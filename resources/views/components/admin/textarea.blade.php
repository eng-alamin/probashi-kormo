@props(['label', 'model', 'rows' => 4, 'required' => false, 'hint' => null, 'span' => true])
@php($id = 'f-'.str_replace('.', '-', $model))
<div class="form-field {{ $span ? 'span-2' : '' }}">
  <label class="form-label" for="{{ $id }}">{{ $label }}@if ($required) <span aria-hidden="true">*</span>@endif</label>
  <textarea id="{{ $id }}" rows="{{ $rows }}" wire:model="{{ $model }}" {{ $attributes->merge(['class' => 'form-control '.($errors->has($model) ? 'is-invalid' : '')]) }}></textarea>
  @if ($hint)<div class="form-hint">{{ $hint }}</div>@endif
  @error($model)<div class="field-error show" role="alert"><i class="bi bi-exclamation-circle" aria-hidden="true"></i>{{ $message }}</div>@enderror
</div>
