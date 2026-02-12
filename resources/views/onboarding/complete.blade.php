@extends('layouts.app')

@section('title', 'Onboarding Complete')

@section('content')
    <div class="container mx-auto max-w-xl py-12 px-4">
        <div class="bg-white shadow-sm rounded-lg border border-slate-100 p-6 text-center">
            <h1 class="text-lg font-semibold mb-2">Thank you, {{ $member->name }}</h1>
            <p class="text-sm text-slate-600 mb-4">
                Your biodata and identification details have been submitted successfully.
            </p>
            <p class="text-xs text-slate-500 mb-6">
                The Chairperson and Treasurer will review your information and finalise your membership and contribution terms.
                You will be notified once everything is confirmed.
            </p>
            <a href="{{ route('home') }}"
               class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg bg-amber-500 hover:bg-amber-600 text-white">
                Back to Otto Investments
            </a>
        </div>
    </div>
@endsection

