@extends('layouts.admin')

@section('page_title', 'Add Member')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Register New Member</h1>
            <p class="text-xs text-slate-500">
                Chairperson registers members after group approval. Onboarding is completed by the member via a secure link.
            </p>
        </div>
        <a href="{{ route('admin.members.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
            Back to members
        </a>
    </div>

    <form action="{{ route('admin.members.store') }}" method="POST"
          class="bg-white border border-slate-100 rounded-lg p-4 text-sm space-y-4 shadow-sm max-w-xl">
        @csrf

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">
                Full Name <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                   placeholder="Member's official name as per ID">
        </div>

        <div class="grid md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">
                    Email
                </label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                       placeholder="Will be used for onboarding link">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">
                    Phone
                </label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                       placeholder="e.g. +2547...">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">
                Link to Partner record (optional)
            </label>
            <select name="partner_id"
                    class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
                <option value="">No linked partner</option>
                @foreach($partners as $partner)
                    <option value="{{ $partner->id }}" {{ old('partner_id') == $partner->id ? 'selected' : '' }}>
                        {{ $partner->name }}
                    </option>
                @endforeach
            </select>
            <p class="text-[10px] text-slate-500 mt-1">
                Use this if the member is also one of the existing partners/investors.
            </p>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-[11px] text-amber-800">
            <p class="font-semibold mb-1">Reminder</p>
            <p>
                Only register members after a group meeting has approved them, and ensure the supporting minutes
                and resolutions are uploaded in the Documents module.
            </p>
        </div>

        <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
            <button type="submit"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-6 py-2 rounded shadow-sm">
                Register Member
            </button>
            <a href="{{ route('admin.members.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
                Cancel
            </a>
        </div>
    </form>
@endsection

