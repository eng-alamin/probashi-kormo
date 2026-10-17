@props(['label', 'model', 'preview' => null, 'current' => null, 'hint' => null, 'removeModel' => null, 'shape' => 'rect'])
@php
  $id = 'f-'.str_replace('.', '-', $model);
  $previewUrl = \App\Support\ImageStore::previewUrl($preview);
@endphp
<div class="form-field span-2">
  <label class="form-label" for="{{ $id }}">{{ $label }}</label>
  <div class="upload-box {{ $errors->has($model) ? 'is-invalid' : '' }}">
    @if ($previewUrl)
      <img class="upload-preview {{ $shape }}" src="{{ $previewUrl }}" alt="Selected image preview">
    @elseif ($current)
      <img class="upload-preview {{ $shape }}" src="{{ $current }}" alt="Current image">
    @else
      <div class="upload-empty {{ $shape }}" aria-hidden="true"><i class="bi bi-image"></i></div>
    @endif
    <div class="upload-controls">
      <input id="{{ $id }}" type="file" wire:model="{{ $model }}" accept="image/jpeg,image/png,image/webp" class="form-control">
      <div class="form-hint" wire:loading wire:target="{{ $model }}">Uploading…</div>
      @if ($removeModel && $current && ! $previewUrl)
        <div class="form-check" style="margin-top:8px;">
          <input id="{{ $id }}-remove" type="checkbox" wire:model="{{ $removeModel }}" class="form-check-input">
          <label class="form-check-label" for="{{ $id }}-remove">Remove the current image</label>
        </div>
      @endif
    </div>
  </div>
  @if ($hint)<div class="form-hint">{{ $hint }}</div>@endif
  @error($model)<div class="field-error show" role="alert"><i class="bi bi-exclamation-circle" aria-hidden="true"></i>{{ $message }}</div>@enderror
</div>
