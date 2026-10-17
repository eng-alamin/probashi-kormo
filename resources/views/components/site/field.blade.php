@props(['label', 'model', 'type' => 'text', 'required' => false, 'placeholder' => null])
@php($id = str_replace('.', '-', $model))
<div>
  <label for="{{ $id }}" class="mb-1 block text-sm font-medium text-slate-700">{{ $label }}@if ($required) *@endif</label>
  <input id="{{ $id }}" type="{{ $type }}" wire:model="{{ $model }}" @if ($placeholder) placeholder="{{ $placeholder }}" @endif @if ($required) required @endif {{ $attributes->merge(['class' => 'w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-pk-green focus:outline-none focus:ring-2 focus:ring-pk-green/30']) }}>
  @error($model)
    <p class="mt-1 text-xs text-pk-red">{{ $message }}</p>
  @enderror
</div>
