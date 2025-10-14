<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slug Management</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4 text-center">📌 Slug Management</h2>
    <a href="{{ route('banks.index') }}" class="btn btn-success">
            🏦 Master Bank CMS
    </a>
    <a href="{{ route('song.index') }}" class="btn btn-success">
            🎵 Master Lagu
    </a>
    <!-- Notifikasi -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Form Search -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form action="{{ route('slug.index') }}" method="GET" class="d-flex">
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="form-control me-2" placeholder="Cari slug...">
                <button type="submit" class="btn btn-outline-primary me-2">Search</button>
                
                @if(request('search'))
                    <a href="{{ route('slug.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Form tambah slug -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form action="{{ route('slug.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Nama Slug</label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="contoh: Nandimia" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Keterangan (Opsional)</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="contoh: Undangan Ungu">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tema</label>
                        <select name="theme" class="form-select">
                            <option value="Violet">Violet</option>
                            <option value="Sage">Sage</option>
                            <option value="Brown">Brown</option>
                            <option value="classic">Classic</option>
                            <option value="modern">Modern</option>
                        </select>
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
            <strong>Daftar Slug</strong>
        </div>
        <div class="card-body">
            @if($slugs->count())
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
                                <!-- Tombol Edit -->
                                    <a href="{{ route('slug.edit', $slug->id) }}" class="btn btn-sm btn-warning">
                                        Edit
                                    </a>
                                    <!-- Form Hapus -->
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
            @else
                <p class="text-muted">Belum ada slug yang dibuat.</p>
            @endif
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
