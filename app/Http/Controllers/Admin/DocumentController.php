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
     * Check if user has permission to access document management (view only for partners).
     */
    protected function checkFinancePermission($allowPartners = false)
    {
        $user = auth()->user();
        if ($allowPartners && $user->is_partner) {
            return; // Partners can view
        }
        // Allow Super Admin, Finance Admin and Chairman to manage documents
        if (! $user->isSuperAdmin() && ! $user->hasAdminRole('finance_admin') && ! $user->hasAdminRole('chairman')) {
            abort(403, 'You do not have permission to access this resource.');
        }
    }

    public function index()
    {
        $this->checkFinancePermission(true); // Allow partners to view

        $query = Document::with(['uploader', 'archiver']);

        // Filter by type
        if ($type = request('type')) {
            $query->where('type', $type);
        }

        // Filter by visibility
        if ($visibility = request('visibility')) {
            $query->where('visibility', $visibility);
        }

        // Filter archived
        if (request('archived') === '1') {
            $query->where('is_archived', true);
        } else {
            $query->where('is_archived', false);
        }

        // Search
        if ($search = request('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $documents = $query->latest()->paginate(20)->withQueryString();

        return view('admin.documents.index', compact('documents'));
    }

    public function create()
    {
        $this->checkFinancePermission();
        return view('admin.documents.create');
    }

    public function store(Request $request)
    {
        $this->checkFinancePermission();

        $validated = $request->validate([
            'type' => ['required', 'in:agreement,report,policy,other'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'visibility' => ['required', 'in:internal_admin,partners,public_link'],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:20480'], // 20MB max
        ]);

        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        $document = Document::create([
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'visibility' => $validated['visibility'],
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        ActivityLogService::logDocument('created', $document, $validated);

        return redirect()->route('admin.documents.index')
            ->with('success', 'Document uploaded successfully.');
    }

    public function show(Document $document)
    {
        $this->checkFinancePermission(true); // Allow partners to view
        $document->load(['uploader', 'archiver']);
        return view('admin.documents.show', compact('document'));
    }

    public function download(Document $document)
    {
        $this->checkFinancePermission(true); // Allow partners to download

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->original_name
        );
    }

    public function archive(Request $request, Document $document)
    {
        $this->checkFinancePermission();

        if ($document->is_archived) {
            return redirect()->back()
                ->with('error', 'This document is already archived.');
        }

        $document->update([
            'is_archived' => true,
            'archived_at' => now(),
            'archived_by' => auth()->id(),
        ]);

        ActivityLogService::logDocument('archived', $document, [
            'archived_by' => auth()->user()->name,
        ]);

        return redirect()->back()
            ->with('success', 'Document archived successfully.');
    }
}
