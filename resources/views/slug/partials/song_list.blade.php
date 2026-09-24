<div class="container px-0">
    @if($errors->has('song_id') || $errors->has('new_song_title') || $errors->has('new_song_file'))
        <div class="alert alert-danger mt-2">
            <strong>Data lagu belum bisa disimpan.</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->get('song_id') as $error)
                    <li>{{ $error }}</li>
                @endforeach
                @foreach($errors->get('new_song_title') as $error)
                    <li>{{ $error }}</li>
                @endforeach
                @foreach($errors->get('new_song_file') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Song List</span>
            <button
                type="button"
                class="btn btn-sm btn-outline-primary"
                data-bs-toggle="modal"
                data-bs-target="#addSongModal"
            >
                + Add New Song
            </button>
        </div>

        <div class="card-body">
            <form action="{{ route('songlist.store', $slug->id) }}" method="POST">
                @csrf

                <div class="row g-2 align-items-end">
                    <div class="col-md-9">
                        <label for="song_id" class="form-label">Pilih Lagu</label>
                        <select name="song_id" id="song_id" class="form-select" required>
                            <option value="">-- Pilih Lagu --</option>
                            @foreach($songs as $song)
                                <option value="{{ $song->id }}" @selected(old('song_id') == $song->id)>
                                    {{ $song->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            Tambahkan Lagu
                        </button>
                    </div>
                </div>
            </form>

            <hr>

            <h5>Daftar Lagu yang Dipilih</h5>
            <ul class="list-group">
                @forelse($selectedSongLists as $songList)
                    <li class="list-group-item d-flex justify-content-between align-items-center gap-3">
                        <div>
                            <strong>{{ $songList->song->title ?? 'Lagu tidak ditemukan' }}</strong>
                            @if($songList->song?->url)
                                <div class="mt-1">
                                    <audio controls preload="none" style="max-width: 280px; height: 32px;">
                                        <source src="{{ $songList->song->url }}">
                                    </audio>
                                </div>
                            @endif
                        </div>

                        <form
                            action="{{ route('songlist.destroy', $songList->id) }}"
                            method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus lagu ini dari undangan?')"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Belum ada lagu yang dipilih.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<div class="modal fade" id="addSongModal" tabindex="-1" aria-labelledby="addSongModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form
                action="{{ route('songlist.upload', $slug->id) }}"
                method="POST"
                enctype="multipart/form-data"
            >
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="addSongModalLabel">Add New Song</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new_song_title" class="form-label">Judul Lagu</label>
                        <input
                            type="text"
                            name="new_song_title"
                            id="new_song_title"
                            class="form-control @error('new_song_title') is-invalid @enderror"
                            value="{{ old('new_song_title') }}"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="new_song_file" class="form-label">File Lagu</label>
                        <input
                            type="file"
                            name="new_song_file"
                            id="new_song_file"
                            class="form-control @error('new_song_file') is-invalid @enderror"
                            accept=".mp3,.wav,.ogg,audio/mpeg,audio/wav,audio/ogg"
                            required
                        >
                        <div class="form-text">MP3, WAV, atau OGG. Maksimal 10 MB.</div>
                    </div>

                    <div class="alert alert-info mb-0">
                        Setelah disimpan, lagu masuk ke Master Lagu dan langsung ditambahkan ke undangan ini.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload & Pilih Lagu</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->has('new_song_title') || $errors->has('new_song_file'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalElement = document.getElementById('addSongModal');
            if (modalElement) {
                bootstrap.Modal.getOrCreateInstance(modalElement).show();
            }
        });
    </script>
@endif
