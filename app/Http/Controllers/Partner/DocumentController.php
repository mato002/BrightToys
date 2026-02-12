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
        $user = $request->user();

        $query = Document::with('uploader')
            ->where('is_archived', false)
            ->whereIn('visibility', ['partners', 'public_link']);

        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        // Load all candidate documents then filter by ACL
        $allDocuments = $query->latest()->get();
        $visibleDocuments = $allDocuments->filter(function (Document $document) use ($user) {
            return $document->canBeViewedBy($user);
        })->values();

        // Simple manual pagination for filtered collection
        $perPage = 20;
        $page = (int) ($request->get('page', 1));
        $offset = ($page - 1) * $perPage;
        $pagedItems = $visibleDocuments->slice($offset, $perPage);

        $documents = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedItems,
            $visibleDocuments->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        // Categories mirror admin controller for consistent filtering
        $categories = [
            'constitution' => 'Constitution',
            'member_agreements' => 'Member Agreements',
            'loan_agreements' => 'Loan Agreements',
            'contracts' => 'Contracts',
            'title_deeds' => 'Title Deeds',
            'import_documents' => 'Import Documents',
            'member_biodata_ids' => 'Member Biodata & IDs',
            'meeting_minutes' => 'Meeting Minutes',
            'other' => 'Other',
        ];

        return view('partner.documents.index', compact('documents', 'categories'));
    }

    public function download(Document $document)
    {
        $user = auth()->user();

        if ($document->is_archived) {
            abort(404);
        }

        if (! $document->canBeViewedBy($user)) {
            abort(403, 'You are not allowed to download this document.');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        \App\Services\ActivityLogService::logDocument('downloaded', $document, [
            'via' => 'partner',
        ]);

        return Storage::disk('public')->download(
            $document->file_path,
            $document->original_name
        );
    }
}

