# Issues to Fix

## Issue 1: Phone Number Pre-fill in Complete Profile

**Problem:** If a student enters phone number during registration, it should be pre-filled in the "Complete Profile" page, not asked again.

**Solution:**
When loading the CompleteProfile page, fetch the current user's phone number and pre-fill the form.

**File: `frontend/src/views/student/CompleteProfile.vue`**

Update the `onMounted` hook to fetch existing user data:

```javascript
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import studentApi from "@/api/endpoints/student";
import { UserCircleIcon, CheckCircleIcon } from "@heroicons/vue/24/outline";

const router = useRouter();
const authStore = useAuthStore();

const form = ref({
  date_of_birth: "",
  address: "",
  phone: "",
});

const loading = ref(false);
const initialLoading = ref(true);
const error = ref(null);
const fieldErrors = ref({});

// Load existing profile data on mount
onMounted(async () => {
  try {
    initialLoading.value = true;
    const data = await studentApi.getProfile();

    // Pre-fill phone if exists
    form.value.phone = data.phone || "";
    form.value.date_of_birth = data.date_of_birth || "";
    form.value.address = data.address || "";
  } catch (err) {
    console.error("Failed to load profile:", err);
  } finally {
    initialLoading.value = false;
  }
});

const handleSubmit = async () => {
  try {
    loading.value = true;
    error.value = null;
    fieldErrors.value = {};

    await studentApi.completeProfile(form.value);

    authStore.setProfileComplete(true);

    // Show success and redirect
    setTimeout(() => {
      router.push("/student/dashboard");
    }, 1500);
  } catch (err) {
    if (err.response?.status === 422) {
      fieldErrors.value = err.response.data.errors;
      error.value = "Please correct the errors below.";
    } else {
      error.value = err.response?.data?.message || "Failed to complete profile";
    }
  } finally {
    loading.value = false;
  }
};
```

Add loading state to template:

```vue
<template>
  <div
    class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 flex items-center justify-center px-4 py-12"
  >
    <div class="max-w-2xl w-full">
      <!-- Loading State -->
      <div v-if="initialLoading" class="text-center">
        <div
          class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-blue-100 dark:bg-blue-900 mb-4"
        >
          <svg
            class="animate-spin h-10 w-10 text-blue-600"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            ></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
          </svg>
        </div>
        <p class="text-gray-600 dark:text-gray-400">Loading your profile...</p>
      </div>

      <!-- Form (rest of the template remains the same) -->
      <div v-else>
        <!-- Header -->
        <div class="text-center mb-8">
          <!-- ... existing header code ... -->
        </div>
        <!-- ... rest of existing template ... -->
      </div>
    </div>
  </div>
</template>
```

---

## Issue 2: Complete Profile Data Not Saving to Database

**Problem:** Need to verify that the complete profile API actually saves data to database.

**Backend File: `backend/app/Http/Controllers/Api/StudentController.php`**

The `completeProfile` method should already be saving to database. Verify it looks like this:

```php
/**
 * Complete student profile (first time)
 * POST /api/student/complete-profile
 */
public function completeProfile(Request $request): JsonResponse
{
    $user = $request->user();
    $student = $user->student()->with('specialty')->first();

    if (!$student) {
        return response()->json(['message' => 'Student profile not found'], 404);
    }

    // Validate required fields
    $validated = $request->validate([
        'date_of_birth' => ['required', 'date', 'before:today', 'after:1950-01-01'],
        'address' => ['required', 'string', 'min:10', 'max:500'],
        'phone' => ['nullable', 'string', 'regex:/^0[5-7][0-9]{8}$/', Rule::unique('users')->ignore($user->id)],
    ]);

    // Update student (saves to database)
    $student->update([
        'date_of_birth' => $validated['date_of_birth'],
        'address' => $validated['address'],
    ]);

    // Update phone in users table (saves to database)
    if (isset($validated['phone'])) {
        $user->update(['phone' => $validated['phone']]);
    }

    // Reload from database to confirm save
    $student->refresh();
    $user->refresh();

    return response()->json([
        'message' => 'Profile completed successfully',
        'profile_complete' => true,
        'student' => [
            'id' => $student->id,
            'registration_number' => $student->registration_number,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'date_of_birth' => $student->date_of_birth,
            'address' => $student->address,
            'email' => $user->email,
            'phone' => $user->phone,
            'specialty' => [
                'id' => $student->specialty->id,
                'name' => $student->specialty->name,
                'code' => $student->specialty->code,
            ],
            'current_semester' => $student->current_semester,
            'study_mode' => $student->study_mode,
        ],
    ]);
}
```

---

## Issue 3: Sidebar Navigation URLs Wrong

**Problem:** Sidebar gives wrong URLs - shows `/dashboard` instead of `/student/dashboard`, `/teacher/dashboard`, or `/admin/dashboard` based on role.

**File: `frontend/src/components/layout/Sidebar.vue`**

The sidebar navigation items need to be dynamic based on user role. Update the sidebar to use role-based routes:

```vue
<script setup>
import { computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import {
  HomeIcon,
  CalendarIcon,
  ClipboardDocumentCheckIcon,
  AcademicCapIcon,
  UserCircleIcon,
  ArrowLeftOnRectangleIcon,
} from "@heroicons/vue/24/outline";

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

// Dynamic navigation items based on user role
const navigationItems = computed(() => {
  const role = authStore.user?.role;

  // Student navigation
  if (role === "student") {
    return [
      { name: "Dashboard", icon: HomeIcon, path: "/student/dashboard" },
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
          'flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors',
          isActive(item.path)
            ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
        ]"
      >
        <component :is="item.icon" class="w-5 h-5" />
        {{ item.name }}
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

---

## Summary:

1. ✅ **Phone Pre-fill:** Load existing profile data on CompleteProfile mount, pre-fill phone field
2. ✅ **Database Save:** Verify completeProfile() method uses `$student->update()` and `$user->update()` which save to database
3. ✅ **Sidebar URLs:** Make navigation items dynamic based on user role (student/teacher/administration)

**Where are the problems:**

- **Problem 1:** In `frontend/src/views/student/CompleteProfile.vue` - missing onMounted to fetch profile
- **Problem 2:** Backend is correct (already saves), just need to verify
- **Problem 3:** In `frontend/src/components/layout/Sidebar.vue` - hardcoded paths instead of role-based

Please implement ALL these fixes.
