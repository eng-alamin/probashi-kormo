@props(['label', 'model', 'type' => 'text', 'required' => false, 'hint' => null, 'placeholder' => null, 'span' => false])
@php($id = 'f-'.str_replace('.', '-', $model))
<div class="form-field {{ $span ? 'span-2' : '' }}">
  <label class="form-label" for="{{ $id }}">{{ $label }}@if ($required) <span aria-hidden="true">*</span>@endif</label>
  <input id="{{ $id }}" type="{{ $type }}" wire:model="{{ $model }}" @if ($placeholder) placeholder="{{ $placeholder }}" @endif @if ($required) required @endif {{ $attributes->merge(['class' => 'form-control '.($errors->has($model) ? 'is-invalid' : '')]) }}>
  @if ($hint)<div class="form-hint">{{ $hint }}</div>@endif
  @error($model)<div class="field-error show" role="alert"><i class="bi bi-exclamation-circle" aria-hidden="true"></i>{{ $message }}</div>@enderror
</div>
