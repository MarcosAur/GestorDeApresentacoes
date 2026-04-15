<?php

namespace App\Http\Controllers;

use App\Models\UserDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    /**
     * Download a document.
     *
     * @param UserDocument $document
     * @return \Illuminate\Http\RedirectResponse
     */
    public function download(UserDocument $document)
    {
        // Security check: only the owner or an admin can download
        if (Auth::id() !== $document->user_id && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        if (!Storage::disk('s3')->exists($document->file_path)) {
            abort(404);
        }

        $url = Storage::disk('s3')->temporaryUrl(
            $document->file_path, 
            now()->addMinutes(5)
        );

        return redirect($url);
    }
}
