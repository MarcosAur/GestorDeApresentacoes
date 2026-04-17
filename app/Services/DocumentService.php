<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    /**
     * Store a document for a user.
     *
     * @param User $user
     * @param UploadedFile $file
     * @param string $type
     * @return UserDocument
     */
    public static function run(User $user, UploadedFile $file, string $type): UserDocument
    {
        $sanitizedType = Str::slug($type);
        $datetime = now()->format('Ymd_His');
        $extension = $file->extension();

        $path = "documents/{$user->id}/{$sanitizedType}_{$datetime}.{$extension}";
        Storage::disk('s3')->put($path, $file->getContent());

        return UserDocument::create([
            'user_id' => $user->id,
            'type' => $type,
            'file_path' => $path,
        ]);
    }
}
