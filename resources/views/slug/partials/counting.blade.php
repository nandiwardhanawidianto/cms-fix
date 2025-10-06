<div class="container mt-4">
    <h4>📖 Form Counting</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('counting.store', $slug_id) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Nama Surat</label>
            <input type="text" name="nama_surat" class="form-control" 
                   value="{{ $counting->nama_surat ?? '' }}" required>
        </div>

        <div class="mb-3">
            <label>Surat (Arab) <small class="text-muted">(opsional)</small></label>
            <textarea name="surat_arab" class="form-control" rows="3">{{ $counting->surat_arab ?? '' }}</textarea>
        </div>

        <div class="mb-3">
            <label>Deskripsi Surat (Bahasa Indonesia)</label>
            <textarea name="deskripsi_surat" class="form-control" rows="4" required>{{ $counting->deskripsi_surat ?? '' }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>