<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Presentation;
use App\Models\PresentationDocument;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function download(PresentationDocument $document)
    {
        $presentation = $document->presentation;
        
        // Security check: only the owner or an admin can download
        if (Auth::id() !== $presentation->competitor_id && !Auth::user()->hasRole('admin')) {
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

    public function index(Request $request)
    {
        $query = PresentationDocument::query();
        
        if (!Auth::user()->hasRole('admin')) {
            $query->whereHas('presentation', function($q) {
                $q->where('competitor_id', Auth::id());
            });
        }

        if ($request->has('presentation_id')) {
            $query->where('presentation_id', $request->presentation_id);
        }

        return response()->json($query->with('presentation.contest')->latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'presentation_id' => 'required|exists:presentations,id',
            'document_file' => 'required|file|mimes:pdf,png,jpg,jpeg|max:5120',
            'document_type' => 'required|string|max:255',
        ]);

        $presentation = Presentation::findOrFail($request->presentation_id);
        
        if ($presentation->competitor_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Você não tem permissão para enviar documentos para esta apresentação.');
        }

        $document = DocumentService::run($presentation, $request->file('document_file'), $request->document_type);

        return response()->json($document, 201);
    }

    public function destroy(PresentationDocument $document)
    {
        $presentation = $document->presentation;

        if ($presentation->competitor_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $document->delete();
        return response()->json(['message' => 'Documento removido com sucesso!']);
    }
}
