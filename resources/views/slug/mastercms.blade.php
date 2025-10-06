<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Bank CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">🏦 Master Bank CMS</h3>
        <a href="{{ route('slug.index') }}" class="btn btn-secondary">⬅ Kembali ke Slug</a>
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Form tambah bank --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="mb-3">Tambah Bank Baru</h5>
            <form action="{{ route('banks.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="nama_bank" class="form-label">Nama Bank</label>
                        <input type="text" name="nama_bank" class="form-control" required>
                    </div>
                    <div class="col-md-5">
                        <label for="logo_bank" class="form-label">Logo Bank (opsional)</label>
                        <input type="file" name="logo_bank" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- List bank --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <strong>Daftar Bank</strong>
        </div>
        <div class="card-body">
            @if($banks->count())
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama Bank</th>
                            <th>Logo</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($banks as $index => $bank)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $bank->nama_bank }}</td>
                                <td>
                                    @if($bank->logo_bank)
                                        <img src="{{ asset('storage/'.$bank->logo_bank) }}" alt="Logo" width="60">
                                    @else
                                        <small>Tidak ada</small>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('banks.destroy', $bank->id) }}" method="POST" onsubmit="return confirm('Hapus bank ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger w-100">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted">Belum ada data bank.</p>
            @endif
        </div>
    </div>
</div>

</body>
</html>
