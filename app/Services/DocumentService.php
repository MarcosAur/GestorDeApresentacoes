<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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
        $path = "documents/{$user->id}/" . $file->hashName();
        Storage::disk('s3')->put($path, file_get_contents($file));

        return UserDocument::create([
            'user_id' => $user->id,
            'type' => $type,
            'file_path' => $path,
        ]);
    }
}
