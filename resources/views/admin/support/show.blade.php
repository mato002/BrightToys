@extends('layouts.admin')

@section('page_title', 'Support ticket')

@section('content')
    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl shadow-sm text-sm">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Message from {{ $ticket->name }}</h2>
                    <p class="text-[11px] text-slate-500">
                        {{ $ticket->email }} · {{ $ticket->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] border
                    @class([
                        'bg-amber-50 text-amber-700 border-amber-200' => $ticket->status === 'open',
                        'bg-sky-50 text-sky-700 border-sky-200' => $ticket->status === 'in_progress',
                        'bg-emerald-50 text-emerald-700 border-emerald-200' => $ticket->status === 'resolved',
                    ])">
                    {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
                </span>
            </div>

            <div class="px-4 py-4 space-y-3">
                <div>
                    <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-[0.18em] mb-1">
                        Subject
                    </p>
                    <p class="text-sm text-slate-900">
                        {{ $ticket->subject ?? 'General enquiry' }}
                    </p>
                </div>

                <div>
                    <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-[0.18em] mb-1">
                        Message
                    </p>
                    <p class="whitespace-pre-line text-sm text-slate-800">
                        {{ $ticket->message }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm text-sm p-4 space-y-4">
            <div>
                <h3 class="text-sm font-semibold text-slate-900 mb-1">Update status</h3>
                <p class="text-[11px] text-slate-500 mb-3">
                    Use this to track whether the ticket is awaiting response or has been resolved.
                </p>
                <form method="POST" action="{{ route('admin.support-tickets.update', $ticket) }}">
                    @csrf
                    @method('PUT')
                    <select name="status"
                            class="w-full rounded border border-slate-200 px-3 py-2 text-xs mb-3">
                        <option value="open" @selected($ticket->status === 'open')>Open</option>
                        <option value="in_progress" @selected($ticket->status === 'in_progress')>In progress</option>
                        <option value="resolved" @selected($ticket->status === 'resolved')>Resolved</option>
                    </select>
                    <button type="submit"
                            class="w-full bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded">
                        Save status
                    </button>
                </form>
            </div>

            @if (session('status'))
                <div class="rounded border border-emerald-200 bg-emerald-50 px-3 py-2 text-[11px] text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif
        </div>
    </div>
@endsection

