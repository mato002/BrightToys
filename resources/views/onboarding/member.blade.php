@extends('layouts.app')

@section('title', 'Member Onboarding')

@section('content')
    <div class="container mx-auto max-w-xl py-8 px-4">
        <div class="bg-white shadow-sm rounded-lg border border-slate-100 p-6">
            <h1 class="text-lg font-semibold mb-2">Welcome, {{ $member->name }}</h1>
            <p class="text-xs text-slate-500 mb-4">
                Please complete your biodata and identification details to finish registration.
            </p>

            <form action="{{ route('onboarding.submit', $token) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-sm">
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
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $member->name) }}" required
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                </div>

                <div class="grid md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-slate-700">Email</label>
                        <input type="email" name="email" value="{{ old('email', $member->email) }}"
                               class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-slate-700">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $member->phone) }}"
                               class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-slate-700">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($member->date_of_birth)->format('Y-m-d')) }}"
                               class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-slate-700">National ID / Passport Number <span class="text-red-500">*</span></label>
                        <input type="text" name="national_id_number" value="{{ old('national_id_number', $member->national_id_number) }}" required
                               class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Postal / Physical Address</label>
                    <input type="text" name="address" value="{{ old('address', $member->address) }}"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Upload ID Document (optional)</label>
                    <input type="file" name="id_document" accept=".pdf,.jpg,.jpeg,.png"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                    <p class="text-[10px] text-slate-500 mt-1">
                        Upload a clear copy of your ID or passport (PDF, JPG, PNG - Max 10MB).
                    </p>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-[11px] text-amber-800">
                    <p class="font-semibold mb-1">Privacy</p>
                    <p>Your information is stored securely and used only for membership and compliance purposes.</p>
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                    <button type="submit"
                            class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-6 py-2 rounded shadow-sm">
                        Submit Details
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

