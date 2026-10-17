@use('App\Support\SafeRoute')
<nav class="sidebar" id="sidebar" aria-label="Main navigation">
  <a href="{{ SafeRoute::url('admin.dashboard') }}" class="brand"><span class="dot" aria-hidden="true"></span><span class="nav-label">{{ config('site.short_name') }}</span></a>
  <div class="nav-scroll">
    @foreach (config('admin_menu.sections') as $section)
      <div class="nav-section-label">{{ $section['label'] }}</div>
      @foreach ($section['items'] as $item)
      @continue(! empty($item['can']) && ! auth()->user()?->can($item['can']))
        @if (SafeRoute::exists($item['route']))
          @php($active = request()->routeIs($item['match'] ?? $item['route']))
          <a class="nav-item {{ $active ? 'active' : '' }}" href="{{ route($item['route']) }}" title="{{ $item['label'] }}" @if ($active) aria-current="page" @endif>
            <i class="bi {{ $item['icon'] }}" aria-hidden="true"></i><span class="nav-label">{{ $item['label'] }}</span>
          </a>
        @else
          <span class="nav-item is-disabled" title="{{ $item['label'] }} (coming soon)" aria-disabled="true">
            <i class="bi {{ $item['icon'] }}" aria-hidden="true"></i><span class="nav-label">{{ $item['label'] }}<small class="nav-soon">Soon</small></span>
          </span>
        @endif
      @endforeach
    @endforeach

    <div class="sidebar-foot">
      <a class="nav-item" href="{{ SafeRoute::url('home') }}" target="_blank" rel="noopener" title="View website">
        <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i><span class="nav-label">View website</span>
      </a>
    </div>
  </div>
</nav>
