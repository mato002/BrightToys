{{-- Enhanced Pagination Component --}}
@if(isset($paginator) && $paginator->hasPages())
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mt-4 pt-4 border-t border-slate-200">
        {{-- Results Info --}}
        <div class="text-xs text-slate-600">
            <span class="font-semibold">Showing</span>
            <span class="font-medium text-slate-900">{{ $paginator->firstItem() ?? 0 }}</span>
            <span class="font-semibold">to</span>
            <span class="font-medium text-slate-900">{{ $paginator->lastItem() ?? 0 }}</span>
            <span class="font-semibold">of</span>
            <span class="font-medium text-slate-900">{{ $paginator->total() }}</span>
            <span class="font-semibold">results</span>
        </div>

        {{-- Pagination Links --}}
        <div class="flex items-center gap-2">
            {{-- Previous Button --}}
            @if($paginator->onFirstPage())
                <span class="px-3 py-1.5 text-xs text-slate-400 border border-slate-200 rounded-md cursor-not-allowed bg-slate-50">
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" 
                   class="px-3 py-1.5 text-xs font-medium text-slate-700 border border-slate-300 rounded-md hover:bg-slate-50 hover:border-slate-400 transition-colors">
                    Previous
                </a>
            @endif

            {{-- Page Numbers --}}
            <div class="flex items-center gap-1">
                @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                    @if($page == $paginator->currentPage())
                        <span class="px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 border border-emerald-600 rounded-md">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" 
                           class="px-3 py-1.5 text-xs font-medium text-slate-700 border border-slate-300 rounded-md hover:bg-slate-50 hover:border-slate-400 transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            </div>

            {{-- Next Button --}}
            @if($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" 
                   class="px-3 py-1.5 text-xs font-medium text-slate-700 border border-slate-300 rounded-md hover:bg-slate-50 hover:border-slate-400 transition-colors">
                    Next
                </a>
            @else
                <span class="px-3 py-1.5 text-xs text-slate-400 border border-slate-200 rounded-md cursor-not-allowed bg-slate-50">
                    Next
                </span>
            @endif
        </div>

        {{-- Per Page Selector --}}
        <form method="GET" class="flex items-center gap-2">
            <label class="text-xs text-slate-600 font-medium">Per page:</label>
            <select name="per_page" 
                    onchange="this.form.submit()" 
                    class="px-2 py-1.5 text-xs border border-slate-300 rounded-md focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                @foreach([10, 20, 50, 100] as $perPage)
                    <option value="{{ $perPage }}" @selected(request('per_page', 20) == $perPage)>
                        {{ $perPage }}
                    </option>
                @endforeach
            </select>
            {{-- Preserve other query parameters --}}
            @foreach(request()->except(['per_page', 'page']) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
        </form>
    </div>
@endif
