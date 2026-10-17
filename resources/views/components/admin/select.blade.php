@props(['label', 'model', 'required' => false, 'hint' => null, 'span' => false])
@php($id = 'f-'.str_replace('.', '-', $model))
<div class="form-field {{ $span ? 'span-2' : '' }}">
  <label class="form-label" for="{{ $id }}">{{ $label }}@if ($required) <span aria-hidden="true">*</span>@endif</label>
  <select id="{{ $id }}" wire:model="{{ $model }}" {{ $attributes->merge(['class' => 'form-select '.($errors->has($model) ? 'is-invalid' : '')]) }}>
    {{ $slot }}
  </select>
  @if ($hint)<div class="form-hint">{{ $hint }}</div>@endif
  @error($model)<div class="field-error show" role="alert"><i class="bi bi-exclamation-circle" aria-hidden="true"></i>{{ $message }}</div>@enderror
</div>
