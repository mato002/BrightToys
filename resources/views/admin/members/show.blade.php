@extends('layouts.admin')

@section('page_title', 'Member Details')

@section('content')
    <div class="mb-4">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.members.index') }}" class="text-slate-500 hover:text-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
            <h1 class="text-lg font-semibold">Member: {{ $member->name }}</h1>
        </div>
        <p class="text-xs text-slate-500">Overview of this member's profile and wallet balances.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Welfare Wallet</p>
            <p class="text-2xl font-bold text-slate-900">
                Ksh {{ number_format(optional($welfareWallet)->balance ?? 0, 2) }}
            </p>
        </div>
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Investment Wallet</p>
            <p class="text-2xl font-bold text-emerald-600">
                Ksh {{ number_format(optional($investmentWallet)->balance ?? 0, 2) }}
            </p>
        </div>
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Status</p>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px]
                @if($member->status === 'active') bg-emerald-50 text-emerald-700 border border-emerald-100
                @elseif($member->status === 'pending') bg-amber-50 text-amber-700 border border-amber-100
                @else bg-slate-50 text-slate-700 border border-slate-100 @endif">
                {{ ucfirst($member->status) }}
            </span>
            <p class="text-[11px] text-slate-500 mt-2">
                Onboarding:
                @if($member->biodata_completed_at)
                    Biodata completed {{ $member->biodata_completed_at->diffForHumans() }}
                @elseif($member->onboarding_token)
                    Link active until {{ optional($member->onboarding_token_expires_at)->format('M d, Y') }}
                @else
                    Not started
                @endif
            </p>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm mb-4">
        <h2 class="text-sm font-semibold text-slate-900 mb-3">Member Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div>
                <p class="text-slate-500 text-[11px] mb-1">Email</p>
                <p class="font-semibold text-slate-900">{{ $member->email ?? '—' }}</p>
            </div>
            <div>
                <p class="text-slate-500 text-[11px] mb-1">Phone</p>
                <p class="font-semibold text-slate-900">{{ $member->phone ?? '—' }}</p>
            </div>
            <div>
                <p class="text-slate-500 text-[11px] mb-1">Date of Birth</p>
                <p class="font-semibold text-slate-900">
                    {{ optional($member->date_of_birth)->format('M d, Y') ?? '—' }}
                </p>
            </div>
            <div>
                <p class="text-slate-500 text-[11px] mb-1">National ID / Passport</p>
                <p class="font-semibold text-slate-900">{{ $member->national_id_number ?? '—' }}</p>
            </div>
            <div>
                <p class="text-slate-500 text-[11px] mb-1">Address</p>
                <p class="font-semibold text-slate-900">{{ $member->address ?? '—' }}</p>
            </div>
            @if($member->id_document_path)
                <div>
                    <p class="text-slate-500 text-[11px] mb-1">ID Document</p>
                    <a href="{{ asset('storage/' . $member->id_document_path) }}"
                       target="_blank"
                       class="text-[11px] text-emerald-600 hover:text-emerald-700 underline">
                        View uploaded ID
                    </a>
                </div>
            @endif
            @if($member->approvalDocument)
                <div class="md:col-span-3">
                    <p class="text-slate-500 text-[11px] mb-1">Approval Document</p>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.documents.show', $member->approvalDocument) }}"
                           class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 underline">
                            {{ $member->approvalDocument->title }}
                        </a>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] bg-blue-50 text-blue-700 border border-blue-100">
                            {{ ucfirst(str_replace('_', ' ', $member->approvalDocument->type)) }}
                        </span>
                        <a href="{{ route('admin.documents.download', $member->approvalDocument) }}"
                           class="text-[10px] text-slate-500 hover:text-slate-700 underline">
                            Download
                        </a>
                    </div>
                    @if($member->approvalDocument->description)
                        <p class="text-xs text-slate-600 mt-1">{{ $member->approvalDocument->description }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="text-xs text-slate-500">
        <p>Wallet transactions and detailed statements will be linked here once contribution posting is wired in.</p>
    </div>
@endsection

