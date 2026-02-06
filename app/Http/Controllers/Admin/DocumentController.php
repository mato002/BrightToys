<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Check if user has permission to access financial management.
     */
    protected function checkFinanceAdminPermission()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('finance_admin')) {
            abort(403, 'You do not have permission to access this resource.');
        }
    }

    /**
     * Display a listing of documents.
     */
    public function index()
    {
        $this->checkFinanceAdminPermission();
        
        $query = Document::with(['uploader']);

        if ($type = request('type')) {
            $query->where('type', $type);
        }

        if ($visibility = request('visibility')) {
            $query->where('visibility', $visibility);
        }

        $documents = $query->where('is_archived', false)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.documents.index', compact('documents'));
    }

    /**
     * Show the form for creating a new document.
     */
    public function create()
    {
        $this->checkFinanceAdminPermission();
        return view('admin.documents.create');
    }

    /**
     * Store a newly created document.
     */
    public function store(Request $request)
    {
        $this->checkFinanceAdminPermission();
    {
        $validated = $request->validate([
            'type' => ['required', 'in:agreement,report,policy,other'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
            'visibility' => ['required', 'in:internal_admin,partners,public_link'],
        ]);

        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        $document = Document::create([
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'visibility' => $validated['visibility'],
            'uploaded_by' => auth()->id(),
        ]);

        ActivityLogService::logDocument('uploaded', $document);

        return redirect()->route('admin.documents.index')
            ->with('success', 'Document uploaded successfully.');
    }

    /**
     * Display the specified document.
     */
    public function show(Document $document)
    {
        $this->checkFinanceAdminPermission();
        $document->load(['uploader', 'archiver']);
        return view('admin.documents.show', compact('document'));
    }

    /**
     * Download the document file.
     */
    public function download(Document $document)
    {
        $this->checkFinanceAdminPermission();
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        ActivityLogService::logDocument('downloaded', $document);

        return Storage::disk('public')->download(
            $document->file_path,
            $document->original_name
        );
    }

    /**
     * Archive a document (soft delete).
     */
    public function archive(Document $document)
    {
        $this->checkFinanceAdminPermission();
        $document->update([
            'is_archived' => true,
            'archived_at' => now(),
            'archived_by' => auth()->id(),
        ]);

        ActivityLogService::logDocument('archived', $document);

        return redirect()->route('admin.documents.index')
            ->with('success', 'Document archived successfully.');
    }
}
