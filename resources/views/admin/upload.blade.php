<form method="post" action="{{ route('admin.assets.store') }}" enctype="multipart/form-data" class="upload-form">
    @csrf
    @if($materialId)<input type="hidden" name="material_id" value="{{ $materialId }}">@endif
    <div class="field"><label for="asset-files">{{ $materialId ? 'File PDF / foto' : 'Foto publik' }} <span aria-hidden="true">*</span></label><input id="asset-files" type="file" name="files[]" accept="{{ $materialId ? '.pdf,.jpg,.jpeg,.png,.webp' : '.jpg,.jpeg,.png,.webp' }}" multiple required aria-describedby="upload-help"><p class="field-help" id="upload-help">Maksimal 10 file, {{ number_format(config('portal.upload_max_kb', 10240) / 1024, 1, ',', '.') }} MB per file. Kompres foto dan PDF sebelum diunggah.</p></div>
    <input type="hidden" name="allow_download" value="{{ $materialId ? '0' : '1' }}">
    @if($materialId)<label class="checkbox-line"><input type="checkbox" name="allow_download" value="1"> Izinkan unduhan jika hak akses sekolah mengizinkan</label>@endif
    <button type="submit" class="button button-secondary">Unggah {{ $materialId ? 'lampiran' : 'foto' }}</button>
</form>
