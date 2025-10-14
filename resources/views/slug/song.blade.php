<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>🎵 Master Lagu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="m-0">🎵 Master Lagu</h2>
    <a href="{{ route('slug.index') }}" class="btn btn-secondary">
      ⬅️ Kembali ke Slug Management
    </a>
  </div>

  <!-- Notifikasi -->
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Form Upload Lagu -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">
      Upload Lagu Baru
    </div>
    <div class="card-body">
      <form action="{{ route('song.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Judul Lagu</label>
            <input type="text" name="title" class="form-control" placeholder="Contoh: Lagu Pernikahan" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">File Lagu (.mp3 / .wav / .ogg)</label>
            <input type="file" name="file" class="form-control" accept=".mp3,.wav,.ogg" required>
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Upload</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Daftar Lagu -->
  <div class="card shadow-sm">
    <div class="card-header bg-light">
      <strong>Daftar Lagu</strong>
    </div>
    <div class="card-body">
      @if($songs->count())
        <div class="table-responsive">
          <table class="table table-striped align-middle">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Judul</th>
                <th>Preview</th>
                <th>URL</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($songs as $index => $song)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <2026-01-01td>{{ $song->title }}</2026-01-01td>
                  <td>
                    <audio controls style="width: 180px;">
                      <source src="{{ $song->url }}" type="audio/mpeg">
                    </audio>
                  </td>
                  <td>
                    <div class="input-group">
                      <input type="text" id="url-{{ $song->id }}" class="form-control" value="{{ $song->url }}" readonly>
                      <button class="btn btn-outline-secondary btn-sm" onclick="copyUrl({{ $song->id }})">Copy</button>
                    </div>
                  </td>
                  <td>
                    <form action="{{ route('song.destroy', $song->id) }}" method="POST" onsubmit="return confirm('Yakin hapus lagu ini?')" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <p class="text-muted">Belum ada lagu yang diupload.</p>
      @endif
    </div>
  </div>
</div>

<!-- Bootstrap JS + Copy function -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function copyUrl(id) {
  const input = document.getElementById(`url-${id}`);
  input.select();
  document.execCommand('copy');
  alert('URL berhasil disalin!');
}
</script>

</body>
</html>
