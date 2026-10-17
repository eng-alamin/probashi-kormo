@use('App\Support\SafeRoute')
<nav class="mobile-tabbar" aria-label="Primary">
  @foreach (config('admin_menu.sections') as $section)
    @foreach ($section['items'] as $item)
      @continue(empty($item['tab']))
      @continue(! empty($item['can']) && ! auth()->user()?->can($item['can']))
      @if (SafeRoute::exists($item['route']))
        @php($active = request()->routeIs($item['match'] ?? $item['route']))
        <a href="{{ route($item['route']) }}" class="{{ $active ? 'active' : '' }}" @if ($active) aria-current="page" @endif><i class="bi {{ $item['icon'] }}" aria-hidden="true"></i>{{ $item['label'] }}</a>
      @else
        <span class="is-disabled" aria-disabled="true"><i class="bi {{ $item['icon'] }}" aria-hidden="true"></i>{{ $item['label'] }}</span>
      @endif
    @endforeach
  @endforeach
</nav>
