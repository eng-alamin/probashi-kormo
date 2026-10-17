@props(['label', 'model', 'hint' => null])
@php($id = 'f-'.str_replace('.', '-', $model))
<div class="form-field">
  <div class="form-check">
    <input id="{{ $id }}" type="checkbox" wire:model="{{ $model }}" class="form-check-input">
    <label class="form-check-label" for="{{ $id }}">{{ $label }}</label>
  </div>
  @if ($hint)<div class="form-hint">{{ $hint }}</div>@endif
</div>
