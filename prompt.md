# Add Messages, Courses, and Documents Features with Notifications

I need to implement 3 new features for students: Messages, Courses (Lessons), and Documents, with a notification badge system in the sidebar.

## Requirements Overview:

### 1. Messages

- Students can **read messages** sent by Administration or Teachers
- Message types: Broadcast (all students), Per-Specialty, or Individual
- Students **cannot reply** (read-only)
- Mark messages as read/unread
- Show unread count badge in sidebar

### 2. Courses (Lessons)

- Lessons organized by **Module**
- Main page shows all modules → Click module → See lessons list
- Students can **download** lesson files
- Show new lessons badge in sidebar

### 3. Documents

- **Administration only** can send documents
- Important documents for students
- Students can **download** documents
- Show new documents badge in sidebar

### 4. Notifications

- **Badge on sidebar items** showing unread count (orange color)
- Badge disappears when student opens the page
- No notification bell in TopBar

---

## Backend Implementation

### Step 1: Create Message Controller

**File: `backend/app/Http/Controllers/Api/MessageController.php`**

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    /**
     * Get all messages for authenticated student
     * GET /api/student/messages
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        if (!$student) {
            return response()->json(['message' => 'Student profile not found'], 404);
        }

        // Get messages:
        // 1. Broadcast messages (recipient_type = 'all')
        // 2. Specialty messages (recipient_type = 'specialty' AND specialty_id matches)
        // 3. Individual messages (recipient_type = 'individual' AND recipient_id = student.user_id)

        $messages = Message::where(function($query) use ($student, $user) {
                $query->where('recipient_type', 'all')
                    ->orWhere(function($q) use ($student) {
                        $q->where('recipient_type', 'specialty')
                          ->where('specialty_id', $student->specialty_id);
                    })
                    ->orWhere(function($q) use ($user) {
                        $q->where('recipient_type', 'individual')
                          ->where('recipient_id', $user->id);
                    });
            })
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $messages->getCollection()->transform(function($message) use ($user) {
            $isRead = $message->readBy()->where('user_id', $user->id)->exists();

            return [
                'id' => $message->id,
                'subject' => $message->subject,
                'content' => $message->content,
                'sender' => [
                    'name' => $message->sender->name ?? 'System',
                    'role' => $message->sender->role ?? 'system',
                ],
                'recipient_type' => $message->recipient_type,
                'is_read' => $isRead,
                'created_at' => $message->created_at->format('Y-m-d H:i'),
            ];
        });

        return response()->json($messages);
    }

    /**
     * Get single message details
     * GET /api/student/messages/{id}
     */
    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        $message = Message::where(function($query) use ($student, $user) {
                $query->where('recipient_type', 'all')
                    ->orWhere(function($q) use ($student) {
                        $q->where('recipient_type', 'specialty')
                          ->where('specialty_id', $student->specialty_id);
                    })
                    ->orWhere(function($q) use ($user) {
                        $q->where('recipient_type', 'individual')
                          ->where('recipient_id', $user->id);
                    });
            })
            ->with('sender')
            ->findOrFail($id);

        // Mark as read
        if (!$message->readBy()->where('user_id', $user->id)->exists()) {
            $message->readBy()->attach($user->id, ['read_at' => now()]);
        }

        return response()->json([
            'id' => $message->id,
            'subject' => $message->subject,
            'content' => $message->content,
            'sender' => [
                'name' => $message->sender->name ?? 'System',
                'role' => $message->sender->role ?? 'system',
            ],
            'recipient_type' => $message->recipient_type,
            'created_at' => $message->created_at->format('Y-m-d H:i'),
        ]);
    }

    /**
     * Get unread messages count
     * GET /api/student/messages/unread/count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        $unreadCount = Message::where(function($query) use ($student, $user) {
                $query->where('recipient_type', 'all')
                    ->orWhere(function($q) use ($student) {
                        $q->where('recipient_type', 'specialty')
                          ->where('specialty_id', $student->specialty_id);
                    })
                    ->orWhere(function($q) use ($user) {
                        $q->where('recipient_type', 'individual')
                          ->where('recipient_id', $user->id);
                    });
            })
            ->whereDoesntHave('readBy', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->count();

        return response()->json(['unread_count' => $unreadCount]);
    }
}
```

---

### Step 2: Create Lesson Controller

**File: `backend/app/Http/Controllers/Api/LessonController.php`**

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    /**
     * Get all modules with lesson count for student
     * GET /api/student/lessons/modules
     */
    public function modules(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        if (!$student) {
            return response()->json(['message' => 'Student profile not found'], 404);
        }

        // Get modules for student's specialty and semester
        $modules = Module::where('specialty_id', $student->specialty_id)
            ->where('semester', $student->current_semester)
            ->withCount('lessons')
            ->with('teachers')
            ->get()
            ->map(function($module) {
                return [
                    'id' => $module->id,
                    'name' => $module->name,
                    'code' => $module->code,
                    'lessons_count' => $module->lessons_count,
                    'teachers' => $module->teachers->map(fn($t) => $t->name),
                ];
            });

        return response()->json(['modules' => $modules]);
    }

    /**
     * Get lessons for a specific module
     * GET /api/student/lessons/modules/{moduleId}
     */
    public function moduleDetails(Request $request, $moduleId): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        $module = Module::where('id', $moduleId)
            ->where('specialty_id', $student->specialty_id)
            ->with(['lessons' => function($query) {
                $query->orderBy('lesson_order')->orderBy('created_at', 'desc');
            }])
            ->firstOrFail();

        $lessons = $module->lessons->map(function($lesson) use ($user) {
            $isViewed = $lesson->viewedBy()->where('user_id', $user->id)->exists();

            return [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'description' => $lesson->description,
                'lesson_order' => $lesson->lesson_order,
                'file_path' => $lesson->file_path,
                'file_name' => basename($lesson->file_path),
                'file_size' => $lesson->file_size,
                'is_viewed' => $isViewed,
                'created_at' => $lesson->created_at->format('Y-m-d'),
            ];
        });

        return response()->json([
            'module' => [
                'id' => $module->id,
                'name' => $module->name,
                'code' => $module->code,
            ],
            'lessons' => $lessons,
        ]);
    }

    /**
     * Download lesson file
     * GET /api/student/lessons/{id}/download
     */
    public function download(Request $request, $id): mixed
    {
        $user = $request->user();
        $lesson = Lesson::findOrFail($id);

        // Mark as viewed
        if (!$lesson->viewedBy()->where('user_id', $user->id)->exists()) {
            $lesson->viewedBy()->attach($user->id, ['viewed_at' => now()]);
        }

        if (!Storage::exists($lesson->file_path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return Storage::download($lesson->file_path, basename($lesson->file_path));
    }

    /**
     * Get new lessons count
     * GET /api/student/lessons/new/count
     */
    public function newCount(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        $newCount = Lesson::whereHas('module', function($q) use ($student) {
                $q->where('specialty_id', $student->specialty_id)
                  ->where('semester', $student->current_semester);
            })
            ->whereDoesntHave('viewedBy', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->count();

        return response()->json(['new_count' => $newCount]);
    }
}
```

---

### Step 3: Create Document Controller

**File: `backend/app/Http/Controllers/Api/DocumentController.php`**

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Get all documents for students
     * GET /api/student/documents
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get documents accessible to all students or specific specialty
        $student = $user->student;

        $documents = Document::where(function($query) use ($student) {
                $query->whereNull('specialty_id')
                    ->orWhere('specialty_id', $student->specialty_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $documents->getCollection()->transform(function($document) use ($user) {
            $isViewed = $document->viewedBy()->where('user_id', $user->id)->exists();

            return [
                'id' => $document->id,
                'title' => $document->title,
                'description' => $document->description,
                'file_path' => $document->file_path,
                'file_name' => basename($document->file_path),
                'file_size' => $document->file_size,
                'is_viewed' => $isViewed,
                'created_at' => $document->created_at->format('Y-m-d'),
            ];
        });

        return response()->json($documents);
    }

    /**
     * Download document
     * GET /api/student/documents/{id}/download
     */
    public function download(Request $request, $id): mixed
    {
        $user = $request->user();
        $document = Document::findOrFail($id);

        // Mark as viewed
        if (!$document->viewedBy()->where('user_id', $user->id)->exists()) {
            $document->viewedBy()->attach($user->id, ['viewed_at' => now()]);
        }

        if (!Storage::exists($document->file_path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return Storage::download($document->file_path, basename($document->file_path));
    }

    /**
     * Get new documents count
     * GET /api/student/documents/new/count
     */
    public function newCount(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        $newCount = Document::where(function($query) use ($student) {
                $query->whereNull('specialty_id')
                    ->orWhere('specialty_id', $student->specialty_id);
            })
            ->whereDoesntHave('viewedBy', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->count();

        return response()->json(['new_count' => $newCount]);
    }
}
```

---

### Step 4: Update API Routes

**File: `backend/routes/api.php`**

Add these routes inside the student middleware group (after line 38):

```php
// Student routes
Route::middleware(['auth:sanctum', 'approved', 'role:student'])->prefix('student')->group(function () {
    // ... existing routes ...

    // Messages
    Route::get('/messages', [MessageController::class, 'index']);
    Route::get('/messages/unread/count', [MessageController::class, 'unreadCount']);
    Route::get('/messages/{id}', [MessageController::class, 'show']);

    // Lessons
    Route::get('/lessons/modules', [LessonController::class, 'modules']);
    Route::get('/lessons/modules/{moduleId}', [LessonController::class, 'moduleDetails']);
    Route::get('/lessons/{id}/download', [LessonController::class, 'download']);
    Route::get('/lessons/new/count', [LessonController::class, 'newCount']);

    // Documents
    Route::get('/documents', [DocumentController::class, 'index']);
    Route::get('/documents/{id}/download', [DocumentController::class, 'download']);
    Route::get('/documents/new/count', [DocumentController::class, 'newCount']);
});
```

Add controller imports at the top:

```php
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\LessonController;
use App\Http\Controllers\Api\DocumentController;
```

---

## Frontend Implementation

### Step 1: Update Student API Endpoints

**File: `frontend/src/api/endpoints/student.js`**

Add these methods at the end of the export:

```javascript
// Messages
async getMessages(page = 1) {
  try {
    const response = await apiClient.get(`/api/student/messages?page=${page}`)
    return response.data
  } catch (error) {
    console.error('Student API Error (getMessages):', error)
    throw error
  }
},

async getMessage(id) {
  try {
    const response = await apiClient.get(`/api/student/messages/${id}`)
    return response.data
  } catch (error) {
    console.error('Student API Error (getMessage):', error)
    throw error
  }
},

async getUnreadMessagesCount() {
  try {
    const response = await apiClient.get('/api/student/messages/unread/count')
    return response.data
  } catch (error) {
    console.error('Student API Error (getUnreadMessagesCount):', error)
    throw error
  }
},

// Lessons
async getLessonModules() {
  try {
    const response = await apiClient.get('/api/student/lessons/modules')
    return response.data
  } catch (error) {
    console.error('Student API Error (getLessonModules):', error)
    throw error
  }
},

async getModuleLessons(moduleId) {
  try {
    const response = await apiClient.get(`/api/student/lessons/modules/${moduleId}`)
    return response.data
  } catch (error) {
    console.error('Student API Error (getModuleLessons):', error)
    throw error
  }
},

async downloadLesson(lessonId) {
  try {
    const response = await apiClient.get(`/api/student/lessons/${lessonId}/download`, {
      responseType: 'blob'
    })
    return response
  } catch (error) {
    console.error('Student API Error (downloadLesson):', error)
    throw error
  }
},

async getNewLessonsCount() {
  try {
    const response = await apiClient.get('/api/student/lessons/new/count')
    return response.data
  } catch (error) {
    console.error('Student API Error (getNewLessonsCount):', error)
    throw error
  }
},

// Documents
async getDocuments(page = 1) {
  try {
    const response = await apiClient.get(`/api/student/documents?page=${page}`)
    return response.data
  } catch (error) {
    console.error('Student API Error (getDocuments):', error)
    throw error
  }
},

async downloadDocument(documentId) {
  try {
    const response = await apiClient.get(`/api/student/documents/${documentId}/download`, {
      responseType: 'blob'
    })
    return response
  } catch (error) {
    console.error('Student API Error (downloadDocument):', error)
    throw error
  }
},

async getNewDocumentsCount() {
  try {
    const response = await apiClient.get('/api/student/documents/new/count')
    return response.data
  } catch (error) {
    console.error('Student API Error (getNewDocumentsCount):', error)
    throw error
  }
}
```

---

### Step 2: Create Messages Page

**Create File: `frontend/src/views/student/Messages.vue`**

```vue
<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import studentApi from "@/api/endpoints/student";
import Card from "@/components/common/Card.vue";
import LoadingSpinner from "@/components/common/LoadingSpinner.vue";
import {
  EnvelopeIcon,
  EnvelopeOpenIcon,
  UserIcon,
  CalendarIcon,
} from "@heroicons/vue/24/outline";

const router = useRouter();
const loading = ref(true);
const messages = ref([]);
const selectedMessage = ref(null);
const error = ref(null);

const loadMessages = async () => {
  try {
    loading.value = true;
    const data = await studentApi.getMessages();
    messages.value = data.data || [];
  } catch (err) {
    console.error("Failed to load messages:", err);
    error.value = "Failed to load messages";
  } finally {
    loading.value = false;
  }
};

const openMessage = async (message) => {
  try {
    const data = await studentApi.getMessage(message.id);
    selectedMessage.value = data;

    // Mark as read in list
    const index = messages.value.findIndex((m) => m.id === message.id);
    if (index !== -1) {
      messages.value[index].is_read = true;
    }
  } catch (err) {
    console.error("Failed to load message:", err);
    error.value = "Failed to load message details";
  }
};

const closeMessage = () => {
  selectedMessage.value = null;
};

const getRecipientTypeLabel = (type) => {
  const labels = {
    all: "All Students",
    specialty: "Specialty",
    individual: "Personal",
  };
  return labels[type] || type;
};

const getRecipientTypeColor = (type) => {
  const colors = {
    all: "bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400",
    specialty:
      "bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400",
    individual:
      "bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400",
  };
  return colors[type] || colors.all;
};

onMounted(() => {
  loadMessages();
});
</script>

<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
      Messages
    </h1>

    <div v-if="loading" class="flex justify-center items-center h-64">
      <LoadingSpinner size="large" />
    </div>

    <div v-else-if="error" class="text-center py-12">
      <p class="text-red-600 dark:text-red-400">{{ error }}</p>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Messages List -->
      <div class="lg:col-span-1">
        <Card title="Inbox">
          <div class="space-y-2">
            <button
              v-for="message in messages"
              :key="message.id"
              @click="openMessage(message)"
              :class="[
                'w-full text-left p-4 rounded-lg transition-colors',
                message.is_read
                  ? 'bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700'
                  : 'bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 border-l-4 border-blue-500',
                selectedMessage?.id === message.id && 'ring-2 ring-blue-500',
              ]"
            >
              <div class="flex items-start justify-between mb-2">
                <div class="flex items-center gap-2">
                  <component
                    :is="message.is_read ? EnvelopeOpenIcon : EnvelopeIcon"
                    class="w-5 h-5 text-gray-400"
                  />
                  <span
                    class="font-semibold text-sm text-gray-900 dark:text-white"
                  >
                    {{ message.sender.name }}
                  </span>
                </div>
                <span
                  :class="[
                    'px-2 py-1 rounded text-xs font-medium',
                    getRecipientTypeColor(message.recipient_type),
                  ]"
                >
                  {{ getRecipientTypeLabel(message.recipient_type) }}
                </span>
              </div>
              <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                {{ message.subject }}
              </p>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ message.created_at }}
              </p>
            </button>

            <div
              v-if="messages.length === 0"
              class="text-center py-8 text-gray-500 dark:text-gray-400"
            >
              <EnvelopeIcon class="w-16 h-16 mx-auto mb-4 opacity-50" />
              <p>No messages yet</p>
            </div>
          </div>
        </Card>
      </div>

      <!-- Message Detail -->
      <div class="lg:col-span-2">
        <Card v-if="selectedMessage">
          <template #title>
            <div class="flex items-center justify-between">
              <span>Message Details</span>
              <button
                @click="closeMessage"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
              >
                ✕
              </button>
            </div>
          </template>

          <div class="space-y-4">
            <!-- Header -->
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
              <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                {{ selectedMessage.subject }}
              </h2>

              <div
                class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400"
              >
                <div class="flex items-center gap-2">
                  <UserIcon class="w-4 h-4" />
                  <span>From: {{ selectedMessage.sender.name }}</span>
                  <span
                    :class="[
                      'px-2 py-0.5 rounded text-xs capitalize',
                      selectedMessage.sender.role === 'administration'
                        ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400'
                        : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                    ]"
                  >
                    {{ selectedMessage.sender.role }}
                  </span>
                </div>
                <div class="flex items-center gap-2">
                  <CalendarIcon class="w-4 h-4" />
                  <span>{{ selectedMessage.created_at }}</span>
                </div>
              </div>
            </div>

            <!-- Content -->
            <div class="prose dark:prose-invert max-w-none">
              <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                {{ selectedMessage.content }}
              </p>
            </div>
          </div>
        </Card>

        <div
          v-else
          class="flex items-center justify-center h-96 text-gray-500 dark:text-gray-400"
        >
          <div class="text-center">
            <EnvelopeIcon class="w-20 h-20 mx-auto mb-4 opacity-30" />
            <p class="text-lg">Select a message to read</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
```

---

### Step 3: Create Courses Page

**Create File: `frontend/src/views/student/Courses.vue`**

```vue
<script setup>
import { ref, onMounted } from "vue";
import studentApi from "@/api/endpoints/student";
import Card from "@/components/common/Card.vue";
import LoadingSpinner from "@/components/common/LoadingSpinner.vue";
import {
  BookOpenIcon,
  FolderIcon,
  DocumentArrowDownIcon,
  ArrowLeftIcon,
} from "@heroicons/vue/24/outline";

const loading = ref(true);
const modules = ref([]);
const selectedModule = ref(null);
const lessons = ref([]);
const error = ref(null);

const loadModules = async () => {
  try {
    loading.value = true;
    const data = await studentApi.getLessonModules();
    modules.value = data.modules || [];
  } catch (err) {
    console.error("Failed to load modules:", err);
    error.value = "Failed to load modules";
  } finally {
    loading.value = false;
  }
};

const openModule = async (module) => {
  try {
    loading.value = true;
    selectedModule.value = module;
    const data = await studentApi.getModuleLessons(module.id);
    lessons.value = data.lessons || [];
  } catch (err) {
    console.error("Failed to load lessons:", err);
    error.value = "Failed to load lessons";
  } finally {
    loading.value = false;
  }
};

const backToModules = () => {
  selectedModule.value = null;
  lessons.value = [];
};

const downloadLesson = async (lesson) => {
  try {
    const response = await studentApi.downloadLesson(lesson.id);

    // Create blob link to download
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", lesson.file_name);
    document.body.appendChild(link);
    link.click();
    link.remove();

    // Mark as viewed in list
    const index = lessons.value.findIndex((l) => l.id === lesson.id);
    if (index !== -1) {
      lessons.value[index].is_viewed = true;
    }
  } catch (err) {
    console.error("Failed to download lesson:", err);
    error.value = "Failed to download lesson";
  }
};

const formatFileSize = (bytes) => {
  if (!bytes) return "N/A";
  const mb = bytes / (1024 * 1024);
  return mb.toFixed(2) + " MB";
};

onMounted(() => {
  loadModules();
});
</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
        {{ selectedModule ? selectedModule.name : "Courses" }}
      </h1>
      <button
        v-if="selectedModule"
        @click="backToModules"
        class="flex items-center gap-2 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
      >
        <ArrowLeftIcon class="w-5 h-5" />
        Back to Modules
      </button>
    </div>

    <div v-if="loading" class="flex justify-center items-center h-64">
      <LoadingSpinner size="large" />
    </div>

    <div v-else-if="error" class="text-center py-12">
      <p class="text-red-600 dark:text-red-400">{{ error }}</p>
    </div>

    <!-- Modules Grid -->
    <div
      v-else-if="!selectedModule"
      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
    >
      <Card
        v-for="module in modules"
        :key="module.id"
        no-padding
        class="cursor-pointer hover:shadow-lg transition-shadow"
        @click="openModule(module)"
      >
        <div class="p-6">
          <div class="flex items-center justify-between mb-4">
            <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30">
              <FolderIcon class="w-8 h-8 text-blue-600 dark:text-blue-400" />
            </div>
            <span
              class="px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300"
            >
              {{ module.lessons_count }} lessons
            </span>
          </div>

          <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
            {{ module.name }}
          </h3>

          <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
            Code: {{ module.code }}
          </p>

          <div class="flex flex-wrap gap-2">
            <span
              v-for="teacher in module.teachers"
              :key="teacher"
              class="text-xs px-2 py-1 rounded bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400"
            >
              {{ teacher }}
            </span>
          </div>
        </div>
      </Card>

      <div
        v-if="modules.length === 0"
        class="col-span-full text-center py-12 text-gray-500 dark:text-gray-400"
      >
        <BookOpenIcon class="w-16 h-16 mx-auto mb-4 opacity-50" />
        <p>No modules available</p>
      </div>
    </div>

    <!-- Lessons List -->
    <div v-else>
      <div class="space-y-4">
        <Card
          v-for="lesson in lessons"
          :key="lesson.id"
          no-padding
          :class="[
            'transition-all',
            !lesson.is_viewed && 'border-l-4 border-orange-500',
          ]"
        >
          <div class="p-6">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ lesson.title }}
                  </h3>
                  <span
                    v-if="!lesson.is_viewed"
                    class="px-2 py-1 rounded text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400"
                  >
                    New
                  </span>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                  {{ lesson.description }}
                </p>

                <div
                  class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400"
                >
                  <span>📄 {{ lesson.file_name }}</span>
                  <span>💾 {{ formatFileSize(lesson.file_size) }}</span>
                  <span>📅 {{ lesson.created_at }}</span>
                </div>
              </div>

              <button
                @click="downloadLesson(lesson)"
                class="ml-4 flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
              >
                <DocumentArrowDownIcon class="w-5 h-5" />
                Download
              </button>
            </div>
          </div>
        </Card>

        <div
          v-if="lessons.length === 0"
          class="text-center py-12 text-gray-500 dark:text-gray-400"
        >
          <BookOpenIcon class="w-16 h-16 mx-auto mb-4 opacity-50" />
          <p>No lessons available for this module</p>
        </div>
      </div>
    </div>
  </div>
</template>
```

---

### Step 4: Create Documents Page

**Create File: `frontend/src/views/student/Documents.vue`**

```vue
<script setup>
import { ref, onMounted } from "vue";
import studentApi from "@/api/endpoints/student";
import Card from "@/components/common/Card.vue";
import LoadingSpinner from "@/components/common/LoadingSpinner.vue";
import {
  DocumentTextIcon,
  DocumentArrowDownIcon,
} from "@heroicons/vue/24/outline";

const loading = ref(true);
const documents = ref([]);
const error = ref(null);

const loadDocuments = async () => {
  try {
    loading.value = true;
    const data = await studentApi.getDocuments();
    documents.value = data.data || [];
  } catch (err) {
    console.error("Failed to load documents:", err);
    error.value = "Failed to load documents";
  } finally {
    loading.value = false;
  }
};

const downloadDocument = async (document) => {
  try {
    const response = await studentApi.downloadDocument(document.id);

    // Create blob link to download
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", document.file_name);
    document.body.appendChild(link);
    link.click();
    link.remove();

    // Mark as viewed in list
    const index = documents.value.findIndex((d) => d.id === document.id);
    if (index !== -1) {
      documents.value[index].is_viewed = true;
    }
  } catch (err) {
    console.error("Failed to download document:", err);
    error.value = "Failed to download document";
  }
};

const formatFileSize = (bytes) => {
  if (!bytes) return "N/A";
  const mb = bytes / (1024 * 1024);
  return mb.toFixed(2) + " MB";
};

onMounted(() => {
  loadDocuments();
});
</script>

<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
      Documents
    </h1>

    <div v-if="loading" class="flex justify-center items-center h-64">
      <LoadingSpinner size="large" />
    </div>

    <div v-else-if="error" class="text-center py-12">
      <p class="text-red-600 dark:text-red-400">{{ error }}</p>
    </div>

    <div v-else class="space-y-4">
      <Card
        v-for="document in documents"
        :key="document.id"
        no-padding
        :class="[
          'transition-all',
          !document.is_viewed && 'border-l-4 border-orange-500',
        ]"
      >
        <div class="p-6">
          <div class="flex items-start justify-between">
            <div class="flex items-start gap-4 flex-1">
              <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/30">
                <DocumentTextIcon
                  class="w-6 h-6 text-red-600 dark:text-red-400"
                />
              </div>

              <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ document.title }}
                  </h3>
                  <span
                    v-if="!document.is_viewed"
                    class="px-2 py-1 rounded text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400"
                  >
                    New
                  </span>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                  {{ document.description }}
                </p>

                <div
                  class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400"
                >
                  <span>📄 {{ document.file_name }}</span>
                  <span>💾 {{ formatFileSize(document.file_size) }}</span>
                  <span>📅 {{ document.created_at }}</span>
                </div>
              </div>
            </div>

            <button
              @click="downloadDocument(document)"
              class="ml-4 flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
              <DocumentArrowDownIcon class="w-5 h-5" />
              Download
            </button>
          </div>
        </div>
      </Card>

      <div
        v-if="documents.length === 0"
        class="text-center py-12 text-gray-500 dark:text-gray-400"
      >
        <DocumentTextIcon class="w-16 h-16 mx-auto mb-4 opacity-50" />
        <p>No documents available</p>
      </div>
    </div>
  </div>
</template>
```

---

### Step 5: Update Sidebar with Badges

**File: `frontend/src/components/layout/Sidebar.vue`**

Update the Sidebar to include badge counts:

```vue
<script setup>
import { computed, ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import studentApi from "@/api/endpoints/student";
import {
  HomeIcon,
  CalendarIcon,
  ClipboardDocumentCheckIcon,
  AcademicCapIcon,
  UserCircleIcon,
  ArrowLeftOnRectangleIcon,
  EnvelopeIcon,
  BookOpenIcon,
  DocumentTextIcon,
} from "@heroicons/vue/24/outline";

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const unreadMessagesCount = ref(0);
const newLessonsCount = ref(0);
const newDocumentsCount = ref(0);

// Load badge counts
const loadBadgeCounts = async () => {
  if (authStore.isStudent) {
    try {
      const [messages, lessons, documents] = await Promise.all([
        studentApi.getUnreadMessagesCount(),
        studentApi.getNewLessonsCount(),
        studentApi.getNewDocumentsCount(),
      ]);

      unreadMessagesCount.value = messages.unread_count || 0;
      newLessonsCount.value = lessons.new_count || 0;
      newDocumentsCount.value = documents.new_count || 0;
    } catch (err) {
      console.error("Failed to load badge counts:", err);
    }
  }
};

// Dynamic navigation items based on user role
const navigationItems = computed(() => {
  const role = authStore.user?.role;

  // Student navigation
  if (role === "student") {
    return [
      { name: "Dashboard", icon: HomeIcon, path: "/student/dashboard" },
      {
        name: "Messages",
        icon: EnvelopeIcon,
        path: "/student/messages",
        badge: unreadMessagesCount.value,
      },
      {
        name: "Courses",
        icon: BookOpenIcon,
        path: "/student/courses",
        badge: newLessonsCount.value,
      },
      {
        name: "Documents",
        icon: DocumentTextIcon,
        path: "/student/documents",
        badge: newDocumentsCount.value,
      },
      { name: "Schedule", icon: CalendarIcon, path: "/student/schedule" },
      {
        name: "Attendance",
        icon: ClipboardDocumentCheckIcon,
        path: "/student/attendance",
      },
      { name: "Exams", icon: AcademicCapIcon, path: "/student/exams" },
      { name: "Profile", icon: UserCircleIcon, path: "/student/profile" },
    ];
  }

  // Teacher navigation
  if (role === "teacher") {
    return [
      { name: "Dashboard", icon: HomeIcon, path: "/teacher/dashboard" },
      { name: "Schedule", icon: CalendarIcon, path: "/teacher/schedule" },
      {
        name: "Attendance",
        icon: ClipboardDocumentCheckIcon,
        path: "/teacher/attendance",
      },
      { name: "Grades", icon: AcademicCapIcon, path: "/teacher/grades" },
      { name: "Profile", icon: UserCircleIcon, path: "/teacher/profile" },
    ];
  }

  // Admin navigation
  if (role === "administration") {
    return [
      { name: "Dashboard", icon: HomeIcon, path: "/admin/dashboard" },
      { name: "Students", icon: AcademicCapIcon, path: "/admin/students" },
      { name: "Teachers", icon: UserCircleIcon, path: "/admin/teachers" },
      {
        name: "Specialties",
        icon: ClipboardDocumentCheckIcon,
        path: "/admin/specialties",
      },
      { name: "Reports", icon: CalendarIcon, path: "/admin/reports" },
    ];
  }

  // Default fallback
  return [];
});

const isActive = (path) => {
  return route.path === path;
};

const handleLogout = async () => {
  await authStore.logout();
  router.push("/login");
};

onMounted(() => {
  loadBadgeCounts();

  // Refresh badge counts every 2 minutes
  setInterval(() => {
    loadBadgeCounts();
  }, 120000);
});

// Refresh badges when returning to a page
watch(
  () => route.path,
  () => {
    loadBadgeCounts();
  }
);
</script>

<template>
  <aside
    class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col"
  >
    <!-- Logo -->
    <div
      class="h-16 flex items-center justify-center border-b border-gray-200 dark:border-gray-700"
    >
      <h1 class="text-xl font-bold text-blue-600 dark:text-blue-400">INSFP</h1>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
      <router-link
        v-for="item in navigationItems"
        :key="item.path"
        :to="item.path"
        :class="[
          'flex items-center justify-between gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors',
          isActive(item.path)
            ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
        ]"
      >
        <div class="flex items-center gap-3">
          <component :is="item.icon" class="w-5 h-5" />
          {{ item.name }}
        </div>

        <!-- Badge -->
        <span
          v-if="item.badge && item.badge > 0"
          class="px-2 py-0.5 text-xs font-bold text-white bg-orange-500 rounded-full min-w-[20px] text-center"
        >
          {{ item.badge > 99 ? "99+" : item.badge }}
        </span>
      </router-link>
    </nav>

    <!-- User Info & Logout -->
    <div class="border-t border-gray-200 dark:border-gray-700 p-4">
      <div class="flex items-center gap-3 mb-3">
        <div
          class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center"
        >
          <span class="text-blue-600 dark:text-blue-400 font-semibold text-sm">
            {{ authStore.user?.name?.charAt(0)?.toUpperCase() || "U" }}
          </span>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
            {{ authStore.user?.name || "User" }}
          </p>
          <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">
            {{ authStore.user?.role }}
          </p>
        </div>
      </div>
      <button
        @click="handleLogout"
        class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
      >
        <ArrowLeftOnRectangleIcon class="w-5 h-5" />
        Logout
      </button>
    </div>
  </aside>
</template>
```

Don't forget to add `watch` import:

```javascript
import { computed, ref, onMounted, watch } from "vue";
```

---

### Step 6: Update Router

**File: `frontend/src/router/index.js`**

Add the new routes inside the student children array:

```javascript
{
  path: '/student',
  component: () => import('@/components/layout/DashboardLayout.vue'),
  meta: { requiresAuth: true, role: 'student' },
  children: [
    {
      path: 'dashboard',
      name: 'StudentDashboard',
      component: () => import('@/views/student/Dashboard.vue')
    },
    {
      path: 'messages',
      name: 'Messages',
      component: () => import('@/views/student/Messages.vue')
    },
    {
      path: 'courses',
      name: 'Courses',
      component: () => import('@/views/student/Courses.vue')
    },
    {
      path: 'documents',
      name: 'Documents',
      component: () => import('@/views/student/Documents.vue')
    },
    {
      path: 'schedule',
      name: 'Schedule',
      component: () => import('@/views/student/Schedule.vue')
    },
    {
      path: 'attendance',
      name: 'Attendance',
      component: () => import('@/views/student/Attendance.vue')
    },
    {
      path: 'exams',
      name: 'Exams',
      component: () => import('@/views/student/Exams.vue')
    },
    {
      path: 'profile',
      name: 'Profile',
      component: () => import('@/views/student/Profile.vue')
    }
  ]
}
```

---

## Summary:

### Backend:

1. ✅ Created MessageController with list, details, mark as read, unread count
2. ✅ Created LessonController with modules list, lessons by module, download, new count
3. ✅ Created DocumentController with list, download, new count
4. ✅ Added API routes for all endpoints

### Frontend:

1. ✅ Created Messages.vue - Inbox with message list and detail view
2. ✅ Created Courses.vue - Modules grid → Lessons list with download
3. ✅ Created Documents.vue - Documents list with download
4. ✅ Updated Sidebar with badge system (orange badges showing unread/new counts)
5. ✅ Added API endpoints in student.js
6. ✅ Updated router with new routes

### Features:

- ✅ Messages: Read-only, broadcast/specialty/individual, mark as read
- ✅ Courses: Organized by module, download files, mark as viewed
- ✅ Documents: Administration only, download files, mark as viewed
- ✅ Badges: Orange badges in sidebar, auto-refresh every 2 minutes

Please implement ALL these changes.
