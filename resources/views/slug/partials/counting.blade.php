<div class="container mt-4">
    <h4>📖 Form Counting</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('counting.store', $slug_id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Foto Countdown</label>

            @if(!empty($counting?->foto_counting))
                <div class="mb-2">
                    <img
                        src="{{ asset('storage/' . $counting->foto_counting) }}"
                        alt="Foto Countdown"
                        class="img-thumbnail"
                        style="max-width: 240px; max-height: 320px; object-fit: cover;"
                    >
                </div>
            @endif

            <input
                type="file"
                name="foto_counting"
                class="form-control @error('foto_counting') is-invalid @enderror"
                accept="image/jpeg,image/png,image/webp"
            >
            <div class="form-text">
                Opsional. Jika tidak memilih file baru, foto countdown yang sekarang tetap digunakan.
            </div>
            @error('foto_counting')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Nama Surat <small class="text-muted">(opsional)</small></label>
            <input type="text" name="nama_surat" class="form-control"
                   value="{{ old('nama_surat', $counting->nama_surat ?? '') }}">
        </div>

        <div class="mb-3">
            <label>Surat (Arab) <small class="text-muted">(opsional)</small></label>
            <textarea name="surat_arab" class="form-control" rows="3">{{ old('surat_arab', $counting->surat_arab ?? '') }}</textarea>
        </div>

        <div class="mb-3">
            <label>Deskripsi Surat (Bahasa Indonesia) <small class="text-muted">(opsional)</small></label>
            <textarea name="deskripsi_surat" class="form-control" rows="4">{{ old('deskripsi_surat', $counting->deskripsi_surat ?? '') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>