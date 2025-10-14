<div class="container">
    @if (session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
    @endif

    <form action="{{ route('songlist.store', $slug->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="song_id">Pilih Lagu:</label>
            <select name="song_id" id="song_id" class="form-control">
                @foreach($songs as $song)
                    <option value="{{ $song->id }}">{{ $song->title }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Tambahkan Lagu</button>
    </form>

    <hr>

    <h4>Daftar Lagu yang Dipilih:</h4>
    <ul class="list-group">
        @foreach($selectedSong as $songId)
            @php $song = $songs->firstWhere('id', $songId); @endphp
            <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $song->title }}
                <form action="{{ route('songlist.destroy', $songId) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">Hapus</button>
                </form>
            </li>
        @endforeach
    </ul>
</div>
