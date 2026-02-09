<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::with('uploader')
            ->where('is_archived', false);

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        $documents = $query->latest()->paginate(20)->withQueryString();

        return view('partner.documents.index', compact('documents'));
    }

    public function download(Document $document)
    {
        if ($document->is_archived) {
            abort(404);
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->original_name
        );
    }
}

