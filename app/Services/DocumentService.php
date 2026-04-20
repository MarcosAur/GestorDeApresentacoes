<?php

namespace App\Services;

use App\Models\Presentation;
use App\Models\PresentationDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    /**
     * Store a document for a presentation.
     *
     * @param Presentation $presentation
     * @param UploadedFile $file
     * @param string $type
     * @return PresentationDocument
     */
    public static function run(Presentation $presentation, UploadedFile $file, string $type): PresentationDocument
    {
        $sanitizedType = Str::slug($type);
        $datetime = now()->format('Ymd_His');
        $extension = $file->extension();

        // Path organized by presentation instead of user
        $path = "documents/presentation_{$presentation->id}/{$sanitizedType}_{$datetime}.{$extension}";
        
        // In a real environment, this would use S3. For development, we ensure the directory exists.
        Storage::disk('s3')->put($path, $file->getContent());

        return PresentationDocument::create([
            'presentation_id' => $presentation->id,
            'type' => $type,
            'file_path' => $path,
        ]);
    }
}
