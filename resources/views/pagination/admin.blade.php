@if ($paginator->hasPages())
  <nav class="pager" aria-label="Pagination">
    <span class="pager-info">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}</span>
    <span class="pager-btns">
      <button type="button" class="btn btn-ghost btn-sm" wire:click="previousPage('{{ $paginator->getPageName() }}')" @disabled($paginator->onFirstPage())><i class="bi bi-chevron-left" aria-hidden="true"></i> Prev</button>
      <span class="pager-page">Page {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
      <button type="button" class="btn btn-ghost btn-sm" wire:click="nextPage('{{ $paginator->getPageName() }}')" @disabled(! $paginator->hasMorePages())>Next <i class="bi bi-chevron-right" aria-hidden="true"></i></button>
    </span>
  </nav>
@endif
