<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slug Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4 text-center">📌 Management Undangan</h2>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('banks.index') }}" class="btn btn-success">🏦 Master Bank CMS</a>
        <a href="{{ route('song.index') }}" class="btn btn-success">🎵 Master Lagu</a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#jsonCreateModal">
            Import JSON & Buat Undangan
        </button>
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#defaultPhotosModal">
            Foto Default
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Form Search -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form action="{{ route('slug.index') }}" method="GET" class="d-flex">
                <input type="text" name="search" value="{{ request('search') }}"
                    class="form-control me-2" placeholder="Cari nama, slug, atau keterangan...">
                <button type="submit" class="btn btn-outline-primary me-2">Search</button>

                @if(request('search'))
                    <a href="{{ route('slug.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Form tambah slug manual -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Tambah Undangan Manual</strong>
            <span class="text-muted small">Untuk alur otomatis gunakan Import JSON di atas.</span>
        </div>
        <div class="card-body">
            <form action="{{ route('slug.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Nama Slug</label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="contoh: Nanda Ane" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Keterangan (Opsional)</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="contoh: 260830QBB2WATW">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Tema</label>
                        <input
                            type="text"
                            name="theme"
                            class="form-control @error('theme') is-invalid @enderror"
                            placeholder="Violet"
                            value="{{ old('theme') }}"
                            required
                        >
                        @error('theme')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Daftar slug -->
    <div class="card shadow-sm">
        <div class="card-header">
            <strong>Daftar Nama Undangan</strong>
        </div>
        <div class="card-body">
            @if($slugs->count())
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Slug</th>
                                <th>Theme</th>
                                <th>Keterangan</th>
                                <th>Dibuat</th>
                                <th>Hosting</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($slugs as $index => $slug)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $slug->nama }}</td>
                                    <td><span class="badge bg-secondary">{{ $slug->slug }}</span></td>
                                    <td>{{ $slug->theme ?? '-' }}</td>
                                    <td>{{ $slug->keterangan ?? '-' }}</td>
                                    <td>{{ $slug->created_at ? $slug->created_at->format('d M Y H:i') : '-' }}</td>
                                    <td>{{ $slug->hosting_at ? $slug->hosting_at->format('d M Y H:i') : '-' }}</td>
                                    <td>
                                        <a href="{{ route('slug.edit', $slug->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('slug.destroy', $slug->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Yakin mau hapus slug ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">Belum ada slug yang dibuat.</p>
            @endif
        </div>
    </div>
</div>

@include('slug.partials.home_json_import')
@include('slug.partials.home_default_photos')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hash = window.location.hash;
        const hasJsonState = @json(
            $errors->has('json_payload') ||
            $errors->has('theme_override') ||
            session()->has('create_import_preview')
        );
        const hasDefaultPhotoError = @json($errors->has('default_photo'));

        if (hash === '#json-import-create' || hasJsonState) {
            const jsonModal = document.getElementById('jsonCreateModal');
            if (jsonModal) {
                bootstrap.Modal.getOrCreateInstance(jsonModal).show();
            }
            return;
        }

        if (hash === '#default-photos' || hasDefaultPhotoError) {
            const photoModal = document.getElementById('defaultPhotosModal');
            if (photoModal) {
                bootstrap.Modal.getOrCreateInstance(photoModal).show();
            }
        }
    });
</script>
</body>
</html>
