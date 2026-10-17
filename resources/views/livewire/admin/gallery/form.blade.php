<div>
  <div class="page-head">
    <div><h2>{{ $isEdit ? 'Edit photo' : 'Add photos' }}</h2><p>{{ $isEdit ? 'Change the details or replace the image' : 'Select one or many photos. JPG, PNG or WebP, up to 4 MB each.' }}</p></div>
    <a href="{{ route('admin.gallery.index') }}" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back</a>
  </div>

  <form wire:submit="save" novalidate class="glass panel">
    <div class="form-grid">
      @if ($isEdit)
        <x-admin.image label="Photo" model="photo" :preview="$photo" :current="\App\Support\ImageStore::url($currentImage)" hint="Leave empty to keep the current photo." />
      @else
        <div class="form-field span-2">
          <label class="form-label" for="f-photos">Photos <span aria-hidden="true">*</span></label>
          <div class="upload-box {{ $errors->has('photos') || $errors->has('photos.*') ? 'is-invalid' : '' }}">
            <div class="upload-controls">
              <input id="f-photos" type="file" multiple wire:model="photos" accept="image/jpeg,image/png,image/webp" class="form-control">
              <div class="form-hint" wire:loading wire:target="photos">Uploading…</div>
            </div>
            @if (count($photos) > 0)
              <div class="upload-grid">
                @foreach ($photos as $index => $file)
                  @php($url = \App\Support\ImageStore::previewUrl($file))
                  <div class="upload-tile" wire:key="tile-{{ $index }}-{{ $file->getFilename() }}">
                    @if ($url)<img src="{{ $url }}" alt="Selected photo {{ $index + 1 }}">@endif
                    <button type="button" wire:click="removePhoto({{ $index }})" aria-label="Remove photo {{ $index + 1 }}"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
                  </div>
                @endforeach
              </div>
            @endif
          </div>
          <div class="form-hint">You can choose up to 20 photos at once. The details below apply to all of them.</div>
          @error('photos')<div class="field-error show" role="alert"><i class="bi bi-exclamation-circle" aria-hidden="true"></i>{{ $message }}</div>@enderror
          @foreach ($errors->get('photos.*') as $messages)
            @foreach ($messages as $message)
              <div class="field-error show" role="alert"><i class="bi bi-exclamation-circle" aria-hidden="true"></i>{{ $message }}</div>
            @endforeach
          @endforeach
        </div>
      @endif

      <x-admin.select label="Category" model="category" hint="The website shows filter buttons for categories that have photos.">
        <option value="">No category</option>
        @foreach ($categories as $c)
          <option value="{{ $c->value }}">{{ $c->label() }}</option>
        @endforeach
      </x-admin.select>
      <x-admin.field label="Display order" model="sort_order" type="number" hint="Smaller number shows first." />

      <x-admin.field label="Title" model="title" maxlength="200" hint="Optional. Shown under the photo." :span="true" />
      <x-admin.textarea label="Caption" model="caption" :rows="2" hint="Optional. Used when there is no title. Up to 500 characters." />

      <x-admin.check label="Show on website" model="is_active" />
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-cobalt" wire:loading.attr="disabled" wire:target="save,photos,photo">
        <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Save changes' : 'Add photos' }}</span>
        <span wire:loading wire:target="save">Saving…</span>
      </button>
      <a href="{{ route('admin.gallery.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
  </form>
</div>
