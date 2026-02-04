@extends('layouts.account')

@section('title', 'My Addresses')
@section('page_title', 'Saved Addresses')

@section('content')
    <div class="space-y-6">
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-xl font-bold text-slate-900 mb-1">Saved Addresses</h1>
                        <p class="text-sm text-slate-500">
                            Manage your delivery addresses for faster checkout.
                        </p>
                    </div>
                    <button onclick="document.getElementById('addressForm').classList.toggle('hidden')"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2.5 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Add Address</span>
                    </button>
                </div>

                @if(session('success'))
                    <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Add Address Form --}}
                <div id="addressForm" class="hidden mb-6 border border-slate-200 rounded-xl p-5 bg-slate-50">
                    <h2 class="text-base font-semibold text-slate-900 mb-4">Add New Address</h2>
                    <form action="{{ route('account.addresses.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Full Name *</label>
                                <input type="text" name="full_name" value="{{ old('full_name') }}" required
                                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                                @error('full_name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Address Line 1 *</label>
                            <input type="text" name="address_line_1" value="{{ old('address_line_1') }}" required
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Address Line 2</label>
                            <input type="text" name="address_line_2" value="{{ old('address_line_2') }}"
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div class="grid md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">City *</label>
                                <input type="text" name="city" value="{{ old('city') }}" required
                                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">State/County</label>
                                <input type="text" name="state" value="{{ old('state') }}"
                                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Postal Code</label>
                                <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Country *</label>
                            <input type="text" name="country" value="{{ old('country', 'Kenya') }}" required
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_default" id="is_default" value="1"
                                   class="rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                            <label for="is_default" class="text-xs text-slate-700">Set as default address</label>
                        </div>
                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2.5 transition-colors">
                                Save Address
                            </button>
                            <button type="button"
                                    onclick="document.getElementById('addressForm').classList.add('hidden')"
                                    class="inline-flex items-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium px-4 py-2.5 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Addresses List --}}
                @forelse($addresses as $address)
                    <div class="border border-slate-200 rounded-xl p-5 bg-white hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <h3 class="font-semibold text-slate-900">{{ $address->full_name }}</h3>
                                    @if($address->is_default)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-100 text-amber-700 border border-amber-200">
                                            Default
                                        </span>
                                    @endif
                                </div>
                                @if($address->phone)
                                    <p class="text-xs text-slate-600 mb-1">{{ $address->phone }}</p>
                                @endif
                                <p class="text-sm text-slate-700 mb-1">{{ $address->address_line_1 }}</p>
                                @if($address->address_line_2)
                                    <p class="text-sm text-slate-700 mb-1">{{ $address->address_line_2 }}</p>
                                @endif
                                <p class="text-sm text-slate-700">
                                    {{ $address->city }}{{ $address->state ? ', ' . $address->state : '' }}{{ $address->postal_code ? ' ' . $address->postal_code : '' }}
                                </p>
                                <p class="text-sm text-slate-600 mt-1">{{ $address->country }}</p>
                            </div>
                            <form action="{{ route('account.addresses.destroy', $address->id) }}" method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this address?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-500 hover:text-red-700 text-xs font-medium">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="border-2 border-dashed border-slate-200 rounded-xl p-8 text-center bg-slate-50">
                        <div class="mb-3"><i class="fas fa-map-marker-alt text-5xl text-slate-400"></i></div>
                        <p class="text-base font-semibold text-slate-900 mb-2">No saved addresses yet</p>
                        <p class="text-sm text-slate-500 mb-4">
                            Add your first address to speed up checkout.
                        </p>
                        <button onclick="document.getElementById('addressForm').classList.remove('hidden')"
                                class="inline-flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white font-semibold px-6 py-2.5 rounded-lg shadow-sm shadow-amber-500/30 transition-colors">
                            Add Address
                        </button>
                    </div>
                @endforelse
            </div>
    </div>
@endsection
