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
     * Supported high-level document categories for structured storage.
     */
    protected array $categories = [
        'constitution',
        'member_agreements',
        'loan_agreements',
        'contracts',
        'title_deeds',
        'import_documents',
        'member_biodata_ids',
        'meeting_minutes',
        'other',
    ];

    /**
     * Check if user has permission to access document management (view only for partners).
     */
    protected function checkFinancePermission($allowPartners = false)
    {
        $user = auth()->user();
        if ($allowPartners && $user->is_partner) {
            return; // Partners can view
        }
        // Allow Super Admin, Finance Admin, Treasurer and Chairman to manage documents
        if (
            ! $user->isSuperAdmin()
            && ! $user->hasAdminRole('finance_admin')
            && ! $user->hasAdminRole('treasurer')
            && ! $user->hasAdminRole('chairman')
        ) {
            abort(403, 'You do not have permission to access this resource.');
        }
    }

    public function index()
    {
        $this->checkFinancePermission(true); // Allow partners to view

        $query = Document::with(['uploader', 'archiver']);

        // Filter by category and sub-category
        if ($category = request('category')) {
            $query->where('category', $category);
        }
        if ($subCategory = request('sub_category')) {
            $query->where('sub_category', 'like', "%{$subCategory}%");
        }

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

        $categories = $this->categories;

        return view('admin.documents.index', compact('documents', 'categories'));
    }

    public function create()
    {
        $this->checkFinancePermission();
        $categories = $this->categories;
        return view('admin.documents.create', compact('categories'));
    }

    public function edit(Document $document)
    {
        $this->checkFinancePermission();
        $document->load(['uploader', 'archiver']);
        $categories = $this->categories;

        return view('admin.documents.edit', compact('document', 'categories'));
    }

    public function store(Request $request)
    {
        $this->checkFinancePermission();

        $validated = $request->validate([
            'type' => ['required', 'in:agreement,report,policy,minutes,resolution,other'],
            'category' => ['required', 'in:' . implode(',', $this->categories)],
            'sub_category' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'visibility' => ['required', 'in:internal_admin,partners,public_link'],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:20480'], // 20MB max
            'subject_type' => ['nullable', 'string', 'max:255'],
            'subject_id' => ['nullable', 'integer'],
            'view_roles' => ['nullable', 'array'],
            'view_roles.*' => ['string'],
            'view_users' => ['nullable', 'array'],
            'view_users.*' => ['integer'],
            'blocked_users' => ['nullable', 'array'],
            'blocked_users.*' => ['integer'],
        ]);

        // Normalise comma-separated role/user lists if provided via single text inputs
        $viewRoles = $validated['view_roles'] ?? null;
        if (is_array($viewRoles) && isset($viewRoles[0])) {
            $viewRoles = collect(explode(',', $viewRoles[0]))
                ->map(fn ($v) => trim($v))
                ->filter()
                ->values()
                ->all();
        }

        $blockedUsers = $validated['blocked_users'] ?? null;
        if (is_array($blockedUsers) && isset($blockedUsers[0])) {
            $blockedUsers = collect(explode(',', $blockedUsers[0]))
                ->map(fn ($v) => trim($v))
                ->filter()
                ->map(fn ($v) => (int) $v)
                ->values()
                ->all();
        }

        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        $document = Document::create([
            'type' => $validated['type'],
            'category' => $validated['category'],
            'sub_category' => $validated['sub_category'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'visibility' => $validated['visibility'],
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
            'subject_type' => $validated['subject_type'] ?? null,
            'subject_id' => $validated['subject_id'] ?? null,
            'view_roles' => $viewRoles,
            'view_users' => $validated['view_users'] ?? null,
            'blocked_users' => $blockedUsers,
        ]);

        // Create initial version snapshot so no document is ever stored without a trace
        $document->versions()->create([
            'version' => 1,
            'file_path' => $document->file_path,
            'original_name' => $document->original_name,
            'mime_type' => $document->mime_type,
            'size' => $document->size,
            'metadata' => [
                'title' => $document->title,
                'type' => $document->type,
                'category' => $document->category ?? null,
                'sub_category' => $document->sub_category ?? null,
                'visibility' => $document->visibility,
                'subject_type' => $document->subject_type,
                'subject_id' => $document->subject_id,
                'view_roles' => $document->view_roles,
                'view_users' => $document->view_users,
                'blocked_users' => $document->blocked_users,
            ],
            'created_by' => auth()->id(),
        ]);

        ActivityLogService::logDocument('created', $document, $validated);

        return redirect()->route('admin.documents.index')
            ->with('success', 'Document uploaded successfully.');
    }

    public function show(Document $document)
    {
        $this->checkFinancePermission(true); // Allow partners to view
        if (! $document->canBeViewedBy(auth()->user())) {
            abort(403, 'You are not allowed to view this document.');
        }

        ActivityLogService::logDocument('viewed', $document, [
            'via' => 'admin',
        ]);

        $document->load(['uploader', 'archiver', 'versions.creator']);
        $categories = $this->categories;
        return view('admin.documents.show', compact('document', 'categories'));
    }

    public function download(Document $document)
    {
        $this->checkFinancePermission(true); // Allow partners to download

        if (! $document->canBeViewedBy(auth()->user())) {
            abort(403, 'You are not allowed to download this document.');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        ActivityLogService::logDocument('downloaded', $document, [
            'via' => 'admin',
        ]);

        return Storage::disk('public')->download(
            $document->file_path,
            $document->original_name
        );
    }

    public function update(Request $request, Document $document)
    {
        $this->checkFinancePermission();

        $validated = $request->validate([
            'type' => ['required', 'in:agreement,report,policy,minutes,resolution,other'],
            'category' => ['required', 'in:' . implode(',', $this->categories)],
            'sub_category' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'visibility' => ['required', 'in:internal_admin,partners,public_link'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:20480'], // 20MB max
            'subject_type' => ['nullable', 'string', 'max:255'],
            'subject_id' => ['nullable', 'integer'],
            'view_roles' => ['nullable', 'array'],
            'view_roles.*' => ['string'],
            'view_users' => ['nullable', 'array'],
            'view_users.*' => ['integer'],
            'blocked_users' => ['nullable', 'array'],
            'blocked_users.*' => ['integer'],
        ]);

        // Normalise comma-separated role/user lists if provided via single text inputs
        $viewRoles = $validated['view_roles'] ?? null;
        if (is_array($viewRoles) && isset($viewRoles[0])) {
            $viewRoles = collect(explode(',', $viewRoles[0]))
                ->map(fn ($v) => trim($v))
                ->filter()
                ->values()
                ->all();
        }

        $blockedUsers = $validated['blocked_users'] ?? null;
        if (is_array($blockedUsers) && isset($blockedUsers[0])) {
            $blockedUsers = collect(explode(',', $blockedUsers[0]))
                ->map(fn ($v) => trim($v))
                ->filter()
                ->map(fn ($v) => (int) $v)
                ->values()
                ->all();
        }

        // Before updating, capture a new version snapshot of the current state
        $currentVersionNumber = (int) ($document->versions()->max('version') ?? 1);
        $document->versions()->create([
            'version' => $currentVersionNumber + 1,
            'file_path' => $document->file_path,
            'original_name' => $document->original_name,
            'mime_type' => $document->mime_type,
            'size' => $document->size,
            'metadata' => [
                'title' => $document->title,
                'type' => $document->type,
                'category' => $document->category ?? null,
                'sub_category' => $document->sub_category ?? null,
                'visibility' => $document->visibility,
                'subject_type' => $document->subject_type,
                'subject_id' => $document->subject_id,
                'view_roles' => $document->view_roles,
                'view_users' => $document->view_users,
                'blocked_users' => $document->blocked_users,
            ],
            'created_by' => auth()->id(),
        ]);

        $data = [
            'type' => $validated['type'],
            'category' => $validated['category'],
            'sub_category' => $validated['sub_category'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'visibility' => $validated['visibility'],
            'subject_type' => $validated['subject_type'] ?? null,
            'subject_id' => $validated['subject_id'] ?? null,
            'view_roles' => $viewRoles,
            'view_users' => $validated['view_users'] ?? null,
            'blocked_users' => $blockedUsers,
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('documents', 'public');

            $data['file_path'] = $path;
            $data['original_name'] = $file->getClientOriginalName();
            $data['mime_type'] = $file->getMimeType();
            $data['size'] = $file->getSize();
        }

        $document->update($data);

        ActivityLogService::logDocument('updated', $document, $data);

        return redirect()->route('admin.documents.show', $document)
            ->with('success', 'Document updated successfully.');
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
