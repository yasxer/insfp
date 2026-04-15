<?php

require __DIR__.'/vendor/autoload.php';
\ = require_once __DIR__.'/bootstrap/app.php';
\->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

\ = \App\Models\Lesson::latest()->first();
echo "file_path: " . \->file_path . PHP_EOL;
echo "disk('local')->exists: " . (\Illuminate\Support\Facades\Storage::disk('local')->exists(\->file_path) ? 'YES' : 'NO') . PHP_EOL;
echo "disk('public')->exists: " . (\Illuminate\Support\Facades\Storage::disk('public')->exists(\->file_path) ? 'YES' : 'NO') . PHP_EOL;
echo "disk('public')->exists(str_replace('public/', '', ...)): " . (\Illuminate\Support\Facades\Storage::disk('public')->exists(str_replace('public/', '', \->file_path)) ? 'YES' : 'NO') . PHP_EOL;
