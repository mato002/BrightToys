@extends('layouts.admin')

@section('title', 'Settings & Permissions')
@section('page_title', 'Settings & Permissions')

@section('breadcrumbs')
    <span>Settings</span>
@endsection

@push('styles')
<style>
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
    .tab-button.active {
        background-color: #10b981;
        color: white;
    }
</style>
@endpush

@section('content')
    <div class="space-y-6">
        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        {{-- Tabs Navigation --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200">
            <div class="border-b border-slate-200">
                <nav class="flex flex-wrap gap-2 px-4 md:px-6 py-3" aria-label="Settings tabs">
                    <button type="button" 
                            onclick="switchTab('profile')"
                            class="tab-button px-4 py-2 text-xs font-medium rounded-lg transition-colors {{ $tab === 'profile' ? 'active' : 'text-slate-600 hover:bg-slate-100' }}"
                            data-tab="profile">
                        Profile
                    </button>
                    <button type="button" 
                            onclick="switchTab('security')"
                            class="tab-button px-4 py-2 text-xs font-medium rounded-lg transition-colors {{ $tab === 'security' ? 'active' : 'text-slate-600 hover:bg-slate-100' }}"
                            data-tab="security">
                        Security
                    </button>
                    @if($canManageSettings ?? false)
                    <button type="button" 
                            onclick="switchTab('general')"
                            class="tab-button px-4 py-2 text-xs font-medium rounded-lg transition-colors {{ $tab === 'general' ? 'active' : 'text-slate-600 hover:bg-slate-100' }}"
                            data-tab="general">
                        General Settings
                    </button>
                    <button type="button" 
                            onclick="switchTab('financial')"
                            class="tab-button px-4 py-2 text-xs font-medium rounded-lg transition-colors {{ $tab === 'financial' ? 'active' : 'text-slate-600 hover:bg-slate-100' }}"
                            data-tab="financial">
                        Financial Settings
                    </button>
                    <button type="button" 
                            onclick="switchTab('welfare')"
                            class="tab-button px-4 py-2 text-xs font-medium rounded-lg transition-colors {{ $tab === 'welfare' ? 'active' : 'text-slate-600 hover:bg-slate-100' }}"
                            data-tab="welfare">
                        Welfare Rules
                    </button>
                    <button type="button" 
                            onclick="switchTab('approval')"
                            class="tab-button px-4 py-2 text-xs font-medium rounded-lg transition-colors {{ $tab === 'approval' ? 'active' : 'text-slate-600 hover:bg-slate-100' }}"
                            data-tab="approval">
                        Approval Workflow
                    </button>
                    <button type="button" 
                            onclick="switchTab('notification')"
                            class="tab-button px-4 py-2 text-xs font-medium rounded-lg transition-colors {{ $tab === 'notification' ? 'active' : 'text-slate-600 hover:bg-slate-100' }}"
                            data-tab="notification">
                        Notifications
                    </button>
                    <button type="button" 
                            onclick="switchTab('email')"
                            class="tab-button px-4 py-2 text-xs font-medium rounded-lg transition-colors {{ $tab === 'email' ? 'active' : 'text-slate-600 hover:bg-slate-100' }}"
                            data-tab="email">
                        Email Settings
                    </button>
                    @endif
                    @if($canManageRoles ?? false)
                    <button type="button" 
                            onclick="switchTab('roles')"
                            class="tab-button px-4 py-2 text-xs font-medium rounded-lg transition-colors {{ $tab === 'roles' ? 'active' : 'text-slate-600 hover:bg-slate-100' }}"
                            data-tab="roles">
                        Role Management
                    </button>
                    @endif
                </nav>
            </div>

            <div class="p-4 md:p-6">
                {{-- Profile Tab --}}
                <div id="tab-profile" class="tab-content {{ $tab === 'profile' ? 'active' : '' }}">
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900 mb-1">Profile Information</h2>
                            <p class="text-xs text-slate-500">Update your name and email address</p>
                        </div>
                        <form method="POST" action="{{ route('admin.settings.profile') }}" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Full Name</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    @error('name')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Email Address</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    @error('email')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <button type="submit"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                                Update Profile
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Security Tab --}}
                <div id="tab-security" class="tab-content {{ $tab === 'security' ? 'active' : '' }}">
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900 mb-1">Security Settings</h2>
                            <p class="text-xs text-slate-500">Manage your password and active sessions</p>
                        </div>
                        
                        <div class="border-t border-slate-200 pt-6">
                            <h3 class="text-sm font-medium text-slate-900 mb-3">Change Password</h3>
                            <form method="POST" action="{{ route('admin.settings.password') }}" class="space-y-4">
                                @csrf
                                @method('PUT')
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Current Password</label>
                                    <input type="password" name="current_password" required
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    @error('current_password')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-700 mb-1.5">New Password</label>
                                        <input type="password" name="password" required
                                               class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                        @error('password')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-700 mb-1.5">Confirm New Password</label>
                                        <input type="password" name="password_confirmation" required
                                               class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    </div>
                                </div>
                                <button type="submit"
                                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                                    Update Password
                                </button>
                            </form>
                        </div>

                        <div class="border-t border-slate-200 pt-6">
                            <h3 class="text-sm font-medium text-slate-900 mb-2">Active Sessions</h3>
                            <p class="text-xs text-slate-500 mb-3">Manage your active login sessions across different devices</p>
                            <a href="{{ route('admin.profile.sessions') }}"
                               class="inline-flex items-center gap-2 text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                View Active Sessions
                            </a>
                        </div>
                    </div>
                </div>

                @if($canManageSettings ?? false)
                {{-- General Settings Tab --}}
                <div id="tab-general" class="tab-content {{ $tab === 'general' ? 'active' : '' }}">
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900 mb-1">General Settings</h2>
                            <p class="text-xs text-slate-500">Configure general application settings</p>
                        </div>
                        <form method="POST" action="{{ route('admin.settings.system') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="group" value="general">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Application Name</label>
                                    <input type="text" name="app_name" value="{{ ($systemSettings['general']['app_name'] ?? null) ?: config('app.name') }}"
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    <p class="mt-1 text-[11px] text-slate-400">The name displayed throughout the application</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Currency</label>
                                    <select name="currency" class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                        <option value="KES" {{ (($systemSettings['general']['currency'] ?? null) ?: 'KES') === 'KES' ? 'selected' : '' }}>KES (Kenyan Shilling)</option>
                                        <option value="USD" {{ (($systemSettings['general']['currency'] ?? null) ?: 'KES') === 'USD' ? 'selected' : '' }}>USD (US Dollar)</option>
                                        <option value="EUR" {{ (($systemSettings['general']['currency'] ?? null) ?: 'KES') === 'EUR' ? 'selected' : '' }}>EUR (Euro)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Currency Symbol</label>
                                    <input type="text" name="currency_symbol" value="{{ ($systemSettings['general']['currency_symbol'] ?? null) ?: 'KSh' }}"
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Timezone</label>
                                    <select name="timezone" class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                        <option value="Africa/Nairobi" {{ (($systemSettings['general']['timezone'] ?? null) ?: 'Africa/Nairobi') === 'Africa/Nairobi' ? 'selected' : '' }}>Africa/Nairobi</option>
                                        <option value="UTC" {{ (($systemSettings['general']['timezone'] ?? null) ?: 'Africa/Nairobi') === 'UTC' ? 'selected' : '' }}>UTC</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Date Format</label>
                                    <select name="date_format" class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                        <option value="d/m/Y" {{ (($systemSettings['general']['date_format'] ?? null) ?: 'd/m/Y') === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                        <option value="m/d/Y" {{ (($systemSettings['general']['date_format'] ?? null) ?: 'd/m/Y') === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                        <option value="Y-m-d" {{ (($systemSettings['general']['date_format'] ?? null) ?: 'd/m/Y') === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Items Per Page</label>
                                    <input type="number" name="items_per_page" value="{{ ($systemSettings['general']['items_per_page'] ?? null) ?: 15 }}" min="5" max="100"
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                            </div>
                            <button type="submit"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                                Save General Settings
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Financial Settings Tab --}}
                <div id="tab-financial" class="tab-content {{ $tab === 'financial' ? 'active' : '' }}">
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900 mb-1">Financial Settings</h2>
                            <p class="text-xs text-slate-500">Configure financial rules, penalties, and rates</p>
                        </div>
                        <form method="POST" action="{{ route('admin.settings.system') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="group" value="financial">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Monthly Contribution Amount</label>
                                    <input type="number" name="monthly_contribution" value="{{ $systemSettings['financial']['monthly_contribution'] ?? 55000 }}" step="0.01" min="0"
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    <p class="mt-1 text-[11px] text-slate-400">Default monthly contribution (5,000 welfare + 50,000 investment)</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Welfare Portion</label>
                                    <input type="number" name="welfare_portion" value="{{ $systemSettings['financial']['welfare_portion'] ?? 5000 }}" step="0.01" min="0"
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Investment Portion</label>
                                    <input type="number" name="investment_portion" value="{{ $systemSettings['financial']['investment_portion'] ?? 50000 }}" step="0.01" min="0"
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Penalty Rate (%)</label>
                                    <input type="number" name="penalty_rate" value="{{ $systemSettings['financial']['penalty_rate'] ?? 5 }}" step="0.01" min="0" max="100"
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    <p class="mt-1 text-[11px] text-slate-400">Percentage charged per month for arrears</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Interest Rate (%)</label>
                                    <input type="number" name="interest_rate" value="{{ $systemSettings['financial']['interest_rate'] ?? 0 }}" step="0.01" min="0" max="100"
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Grace Period (Days)</label>
                                    <input type="number" name="grace_period_days" value="{{ $systemSettings['financial']['grace_period_days'] ?? 7 }}" min="0"
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    <p class="mt-1 text-[11px] text-slate-400">Days before penalty is applied</p>
                                </div>
                            </div>
                            <button type="submit"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                                Save Financial Settings
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Welfare Rules Tab --}}
                <div id="tab-welfare" class="tab-content {{ $tab === 'welfare' ? 'active' : '' }}">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-base font-semibold text-slate-900 mb-1">Welfare Rules</h2>
                                <p class="text-xs text-slate-500">Configure welfare benefit rules and eligibility criteria</p>
                            </div>
                            <button onclick="openWelfareRuleModal()"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
                                + Add Rule
                            </button>
                        </div>

                        @if($welfareRules && $welfareRules->count() > 0)
                            <div class="space-y-4">
                                @foreach($welfareRules as $rule)
                                    <div class="border border-slate-200 rounded-lg p-4 hover:bg-slate-50 transition-colors">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h3 class="text-sm font-semibold text-slate-900">{{ $rule->name }}</h3>
                                                    @if($rule->is_active)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-700">
                                                            Active
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600">
                                                            Inactive
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-xs text-slate-500 mb-2">{{ $rule->description }}</p>
                                                <div class="flex flex-wrap gap-3 text-xs text-slate-600">
                                                    @if($rule->max_amount)
                                                        <span><strong>Max Amount:</strong> {{ number_format($rule->max_amount, 2) }}</span>
                                                    @endif
                                                    @if($rule->max_per_year)
                                                        <span><strong>Max/Year:</strong> {{ $rule->max_per_year }}</span>
                                                    @endif
                                                    @if($rule->min_months_membership)
                                                        <span><strong>Min Membership:</strong> {{ $rule->min_months_membership }} months</span>
                                                    @endif
                                                    <span><strong>Type:</strong> {{ ucfirst($rule->rule_type) }}</span>
                                                </div>
                                            </div>
                                            <form method="POST" action="{{ route('admin.settings.welfare-rules.delete', $rule) }}" class="ml-4"
                                                  onsubmit="return confirm('Are you sure you want to delete this rule?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-700 text-xs">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12 border border-slate-200 rounded-lg">
                                <p class="text-sm text-slate-500">No welfare rules configured yet.</p>
                                <button onclick="openWelfareRuleModal()"
                                        class="mt-4 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
                                    Create First Rule
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Approval Workflow Tab --}}
                <div id="tab-approval" class="tab-content {{ $tab === 'approval' ? 'active' : '' }}">
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900 mb-1">Approval Workflow Settings</h2>
                            <p class="text-xs text-slate-500">Configure approval requirements and workflows</p>
                        </div>
                        <form method="POST" action="{{ route('admin.settings.system') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="group" value="approval">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Default Approval Required</label>
                                    <select name="default_approval_required" class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                        <option value="1" {{ ($systemSettings['approval']['default_approval_required'] ?? '1') === '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ ($systemSettings['approval']['default_approval_required'] ?? '1') === '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Auto-approve Amount Threshold</label>
                                    <input type="number" name="auto_approve_threshold" value="{{ $systemSettings['approval']['auto_approve_threshold'] ?? 0 }}" step="0.01" min="0"
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    <p class="mt-1 text-[11px] text-slate-400">Amounts below this are auto-approved</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Require Multiple Approvers</label>
                                    <select name="require_multiple_approvers" class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                        <option value="0" {{ ($systemSettings['approval']['require_multiple_approvers'] ?? '0') === '0' ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ ($systemSettings['approval']['require_multiple_approvers'] ?? '0') === '1' ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Minimum Approvers Required</label>
                                    <input type="number" name="min_approvers" value="{{ $systemSettings['approval']['min_approvers'] ?? 1 }}" min="1" max="10"
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                            </div>
                            <button type="submit"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                                Save Approval Settings
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Notification Settings Tab --}}
                <div id="tab-notification" class="tab-content {{ $tab === 'notification' ? 'active' : '' }}">
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900 mb-1">Notification Settings</h2>
                            <p class="text-xs text-slate-500">Configure notification preferences</p>
                        </div>
                        <form method="POST" action="{{ route('admin.settings.system') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="group" value="notification">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 border border-slate-200 rounded-lg">
                                    <div>
                                        <label class="text-sm font-medium text-slate-900">Email Notifications</label>
                                        <p class="text-xs text-slate-500">Send email notifications for important events</p>
                                    </div>
                                    <input type="checkbox" name="email_notifications" value="1" 
                                           {{ ($systemSettings['notification']['email_notifications'] ?? '1') === '1' ? 'checked' : '' }}
                                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                </div>
                                <div class="flex items-center justify-between p-3 border border-slate-200 rounded-lg">
                                    <div>
                                        <label class="text-sm font-medium text-slate-900">SMS Notifications</label>
                                        <p class="text-xs text-slate-500">Send SMS notifications for critical alerts</p>
                                    </div>
                                    <input type="checkbox" name="sms_notifications" value="1" 
                                           {{ ($systemSettings['notification']['sms_notifications'] ?? '0') === '1' ? 'checked' : '' }}
                                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                </div>
                                <div class="flex items-center justify-between p-3 border border-slate-200 rounded-lg">
                                    <div>
                                        <label class="text-sm font-medium text-slate-900">Contribution Reminders</label>
                                        <p class="text-xs text-slate-500">Send reminders for upcoming contributions</p>
                                    </div>
                                    <input type="checkbox" name="contribution_reminders" value="1" 
                                           {{ ($systemSettings['notification']['contribution_reminders'] ?? '1') === '1' ? 'checked' : '' }}
                                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                </div>
                                <div class="flex items-center justify-between p-3 border border-slate-200 rounded-lg">
                                    <div>
                                        <label class="text-sm font-medium text-slate-900">Arrears Alerts</label>
                                        <p class="text-xs text-slate-500">Notify when contributions are overdue</p>
                                    </div>
                                    <input type="checkbox" name="arrears_alerts" value="1" 
                                           {{ ($systemSettings['notification']['arrears_alerts'] ?? '1') === '1' ? 'checked' : '' }}
                                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                </div>
                            </div>
                            <button type="submit"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                                Save Notification Settings
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Email Settings Tab --}}
                <div id="tab-email" class="tab-content {{ $tab === 'email' ? 'active' : '' }}">
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900 mb-1">Email Settings</h2>
                            <p class="text-xs text-slate-500">Configure email server and template settings</p>
                        </div>
                        <form method="POST" action="{{ route('admin.settings.system') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="group" value="email">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">From Email Address</label>
                                    <input type="email" name="from_email" value="{{ $systemSettings['email']['from_email'] ?? config('mail.from.address') }}"
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">From Name</label>
                                    <input type="text" name="from_name" value="{{ $systemSettings['email']['from_name'] ?? config('mail.from.name') }}"
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Reply To Email</label>
                                    <input type="email" name="reply_to" value="{{ $systemSettings['email']['reply_to'] ?? '' }}"
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Support Email</label>
                                    <input type="email" name="support_email" value="{{ $systemSettings['email']['support_email'] ?? '' }}"
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                            </div>
                            <button type="submit"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                                Save Email Settings
                            </button>
                        </form>
                    </div>
                </div>
                @endif

                @if($canManageRoles ?? false)
                {{-- Role Management Tab --}}
                <div id="tab-roles" class="tab-content {{ $tab === 'roles' ? 'active' : '' }}">
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900 mb-1">Role Management</h2>
                            <p class="text-xs text-slate-500">Assign roles to admin users. Roles determine what permissions users have across the system.</p>
                        </div>

                        @if($adminUsers && $adminUsers->count() > 0)
                            <div class="space-y-4">
                                @foreach($adminUsers as $adminUser)
                                    <div class="border border-slate-200 rounded-lg p-4 hover:bg-slate-50 transition-colors">
                                        <div class="flex items-center justify-between mb-3">
                                            <div>
                                                <h3 class="text-sm font-semibold text-slate-900">{{ $adminUser->name }}</h3>
                                                <p class="text-xs text-slate-500">{{ $adminUser->email }}</p>
                                            </div>
                                            @if($adminUser->isSuperAdmin())
                                                <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-1 text-xs font-medium text-purple-700">
                                                    Super Admin
                                                </span>
                                            @endif
                                        </div>

                                        <form method="POST" action="{{ route('admin.settings.assign-roles', $adminUser) }}" class="space-y-3">
                                            @csrf
                                            <div class="flex flex-wrap gap-3">
                                                @foreach($roles as $role)
                                                    <label class="inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                                               {{ $adminUser->adminRoles->contains($role->id) ? 'checked' : '' }}
                                                               class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                                        <span class="ml-2 text-xs text-slate-700">{{ $role->display_name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                            <button type="submit"
                                                    class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg transition-colors">
                                                Update Roles
                                            </button>
                                        </form>

                                        @if($adminUser->adminRoles->count() > 0)
                                            <div class="mt-3 pt-3 border-t border-slate-100">
                                                <p class="text-xs text-slate-500 mb-2">Current Roles:</p>
                                                <div class="flex flex-wrap gap-1.5">
                                                    @foreach($adminUser->adminRoles as $role)
                                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-medium text-emerald-700 border border-emerald-200">
                                                            {{ $role->display_name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-slate-500">No admin users found.</p>
                        @endif

                        @if($roles)
                        <div class="border-t border-slate-200 pt-6">
                            <h3 class="text-sm font-semibold text-slate-900 mb-4">Available Roles & Permissions</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($roles as $role)
                                    <div class="border border-slate-200 rounded-lg p-4 hover:bg-slate-50 transition-colors">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="text-sm font-semibold text-slate-900">{{ $role->display_name }}</h4>
                                            <code class="text-[10px] text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">{{ $role->name }}</code>
                                        </div>
                                        @if($role->permissions && $role->permissions->count() > 0)
                                            <div class="space-y-1.5">
                                                <p class="text-xs font-medium text-slate-700 mb-1">Permissions:</p>
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($role->permissions->take(5) as $permission)
                                                        <span class="text-[10px] text-slate-600 bg-slate-50 px-2 py-0.5 rounded border border-slate-200">
                                                            {{ $permission->display_name ?? $permission->name }}
                                                        </span>
                                                    @endforeach
                                                    @if($role->permissions->count() > 5)
                                                        <span class="text-[10px] text-slate-500">+{{ $role->permissions->count() - 5 }} more</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-xs text-slate-400">No permissions assigned</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Welfare Rule Modal --}}
    @if($canManageSettings ?? false)
    <div id="welfare-rule-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-slate-900">Add/Edit Welfare Rule</h3>
                    <button onclick="closeWelfareRuleModal()" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.settings.welfare-rules.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="id" id="welfare-rule-id">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">Rule Type</label>
                            <select name="rule_type" required class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="medical">Medical</option>
                                <option value="education">Education</option>
                                <option value="emergency">Emergency</option>
                                <option value="funeral">Funeral</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">Name</label>
                            <input type="text" name="name" required class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">Description</label>
                            <textarea name="description" rows="3" class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">Max Amount</label>
                            <input type="number" name="max_amount" step="0.01" min="0" class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">Max Per Year</label>
                            <input type="number" name="max_per_year" min="0" class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">Min Months Membership</label>
                            <input type="number" name="min_months_membership" value="0" min="0" class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">Priority</label>
                            <input type="number" name="priority" value="0" class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="requires_approval" value="1" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <label class="ml-2 text-xs text-slate-700">Requires Approval</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <label class="ml-2 text-xs text-slate-700">Active</label>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" onclick="closeWelfareRuleModal()" class="px-4 py-2 text-sm text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors">
                            Save Rule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
<script>
    function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        
        // Remove active class from all buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active');
            button.classList.add('text-slate-600', 'hover:bg-slate-100');
        });
        
        // Show selected tab content
        const tabContent = document.getElementById('tab-' + tabName);
        if (tabContent) {
            tabContent.classList.add('active');
        }
        
        // Add active class to selected button
        const tabButton = document.querySelector(`[data-tab="${tabName}"]`);
        if (tabButton) {
            tabButton.classList.add('active');
            tabButton.classList.remove('text-slate-600', 'hover:bg-slate-100');
        }
        
        // Update URL without reload
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.pushState({}, '', url);
    }

    function openWelfareRuleModal() {
        document.getElementById('welfare-rule-modal').classList.remove('hidden');
    }

    function closeWelfareRuleModal() {
        document.getElementById('welfare-rule-modal').classList.add('hidden');
        // Reset form
        document.querySelector('#welfare-rule-modal form').reset();
        document.getElementById('welfare-rule-id').value = '';
    }

    // Close modal on outside click
    document.getElementById('welfare-rule-modal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeWelfareRuleModal();
        }
    });
</script>
@endpush
