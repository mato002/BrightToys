@extends('layouts.partner')

@section('page_title', 'Partner Profile')

@section('partner_content')
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">My Profile</h1>
                <p class="text-sm text-slate-600 mt-1">
                    View your partner account details and ownership information.
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Profile Card --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Account Information --}}
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-4">
                    <h2 class="text-lg font-bold text-white">Account Information</h2>
                    <p class="text-sm text-amber-100">Your login and user account details</p>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div class="flex items-center justify-between py-3 border-b border-slate-100">
                            <dt class="text-sm font-medium text-slate-600">Full Name</dt>
                            <dd class="text-sm font-semibold text-slate-900">{{ $user->name }}</dd>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b border-slate-100">
                            <dt class="text-sm font-medium text-slate-600">Email Address</dt>
                            <dd class="text-sm font-semibold text-slate-900">{{ $user->email }}</dd>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b border-slate-100">
                            <dt class="text-sm font-medium text-slate-600">Account Created</dt>
                            <dd class="text-sm font-semibold text-slate-900">
                                {{ optional($user->created_at)->format('F d, Y') }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <dt class="text-sm font-medium text-slate-600">Last Login</dt>
                            <dd class="text-sm font-semibold text-slate-900">
                                {{ optional($user->updated_at)->format('F d, Y h:i A') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Partner Details --}}
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 px-6 py-4">
                    <h2 class="text-lg font-bold text-white">Partner Details</h2>
                    <p class="text-sm text-emerald-100">Your partnership information and status</p>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div class="flex items-center justify-between py-3 border-b border-slate-100">
                            <dt class="text-sm font-medium text-slate-600">Partner Name</dt>
                            <dd class="text-sm font-semibold text-slate-900">{{ $partner->name ?? 'Not assigned' }}</dd>
                        </div>
                        @if($partner)
                            @if($partner->email)
                                <div class="flex items-center justify-between py-3 border-b border-slate-100">
                                    <dt class="text-sm font-medium text-slate-600">Partner Email</dt>
                                    <dd class="text-sm font-semibold text-slate-900">{{ $partner->email }}</dd>
                                </div>
                            @endif
                            @if($partner->phone)
                                <div class="flex items-center justify-between py-3 border-b border-slate-100">
                                    <dt class="text-sm font-medium text-slate-600">Phone Number</dt>
                                    <dd class="text-sm font-semibold text-slate-900">{{ $partner->phone }}</dd>
                                </div>
                            @endif
                            <div class="flex items-center justify-between py-3 border-b border-slate-100">
                                <dt class="text-sm font-medium text-slate-600">Status</dt>
                                <dd>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                        {{ ($partner->status ?? 'active') === 'active' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-800 border border-slate-200' }}">
                                        {{ ucfirst($partner->status ?? 'active') }}
                                    </span>
                                </dd>
                            </div>
                            @if($partner->notes)
                                <div class="py-3">
                                    <dt class="text-sm font-medium text-slate-600 mb-2">Notes</dt>
                                    <dd class="text-sm text-slate-700 bg-slate-50 rounded-lg p-3">{{ $partner->notes }}</dd>
                                </div>
                            @endif
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Quick Stats --}}
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                <h3 class="text-sm font-semibold text-slate-900 mb-4">Quick Stats</h3>
                <div class="space-y-4">
                    @php
                        $currentOwnership = $partner?->ownerships()
                            ->where('effective_from', '<=', now())
                            ->where(function ($q) {
                                $q->whereNull('effective_to')
                                  ->orWhere('effective_to', '>=', now());
                            })
                            ->first();
                    @endphp
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Current Ownership</p>
                        <p class="text-xl font-bold text-emerald-600">
                            {{ $currentOwnership ? number_format($currentOwnership->percentage, 2) . '%' : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Projects Created</p>
                        <p class="text-xl font-bold text-blue-600">
                            {{ $partner ? \App\Models\Project::where('created_by', $partner->id)->count() : 0 }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Total Contributions</p>
                        <p class="text-xl font-bold text-amber-600">
                            Ksh {{ number_format($partner ? \App\Models\PartnerContribution::where('partner_id', $partner->id)->where('status', 'approved')->where('type', 'contribution')->sum('amount') : 0, 0) }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Ownership History --}}
            @if($partner && $partner->ownerships->count() > 0)
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-slate-900 mb-4">Ownership History</h3>
                    <div class="space-y-3">
                        @foreach($partner->ownerships->sortByDesc('effective_from') as $ownership)
                            <div class="border-l-2 border-amber-500 pl-3 py-2">
                                <p class="text-sm font-semibold text-slate-900">{{ number_format($ownership->percentage, 2) }}%</p>
                                <p class="text-xs text-slate-500">
                                    From {{ $ownership->effective_from->format('M d, Y') }}
                                    @if($ownership->effective_to)
                                        to {{ $ownership->effective_to->format('M d, Y') }}
                                    @else
                                        (Current)
                                    @endif
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Account Actions --}}
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                <h3 class="text-sm font-semibold text-slate-900 mb-4">Account Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('partner.contributions.create') }}" 
                       class="block w-full text-center bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                        Submit Contribution
                    </a>
                    <a href="{{ route('partner.projects.manage') }}" 
                       class="block w-full text-center border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                        Manage Projects
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
