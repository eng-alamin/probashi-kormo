@props(['label', 'value' => null])
<div class="flex justify-between gap-4 border-b border-slate-100 py-2.5 text-sm">
  <span class="text-slate-500">{{ $label }}</span>
  <span class="text-right font-medium text-pk-dark">{{ filled($value) ? $value : 'Contact us' }}</span>
</div>
