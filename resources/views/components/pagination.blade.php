@props(['paginator'])
@if($paginator->hasPages())
    <nav class="mt-12 flex items-center justify-between gap-5 border-t border-hedge-700/20 pt-6" aria-label="Pagination">
        @if($paginator->onFirstPage())<span class="text-hedge-800/45">Previous</span>@else<a class="font-semibold text-barn-700 underline decoration-2 underline-offset-4" href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous</a>@endif
        <p class="text-sm text-hedge-800">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</p>
        @if($paginator->hasMorePages())<a class="font-semibold text-barn-700 underline decoration-2 underline-offset-4" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>@else<span class="text-hedge-800/45">Next</span>@endif
    </nav>
@endif
