<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDocument;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
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

    public function index()
    {
        return response()->json(Auth::user()->documents()->latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_file' => 'required|file|mimes:pdf,png,jpg,jpeg|max:5120',
            'document_type' => 'required|string|max:255',
        ]);

        $document = DocumentService::run(Auth::user(), $request->file('document_file'), $request->document_type);

        return response()->json($document, 201);
    }

    public function destroy(UserDocument $document)
    {
        if ($document->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $document->delete();
        return response()->json(['message' => 'Documento removido com sucesso!']);
    }
}
