<div class="container">

    @if (session('success'))
        <div class="alert alert-success mt-2">
            {{ session('success') }}
        </div>
    @endif


    <form
        action="{{ route('songlist.store', $slug->id) }}"
        method="POST"
    >
        @csrf

        <div class="form-group">

            <label for="song_id">
                Pilih Lagu:
            </label>

            <select
                name="song_id"
                id="song_id"
                class="form-control"
                required
            >

                <option value="">
                    -- Pilih Lagu --
                </option>

                @foreach($songs as $song)

                    <option value="{{ $song->id }}">
                        {{ $song->title }}
                    </option>

                @endforeach

            </select>

        </div>


        <button
            type="submit"
            class="btn btn-primary mt-2"
        >
            Tambahkan Lagu
        </button>

    </form>


    <hr>


    <h4>
        Daftar Lagu yang Dipilih:
    </h4>


    <ul class="list-group">

        @forelse($selectedSongLists as $songList)

            <li
                class="list-group-item d-flex justify-content-between align-items-center"
            >

                <span>
                    {{ $songList->song->title ?? 'Lagu tidak ditemukan' }}
                </span>


                <form
                    action="{{ route('songlist.destroy', $songList->id) }}"
                    method="POST"
                    onsubmit="return confirm('Yakin ingin menghapus lagu ini?')"
                >

                    @csrf
                    @method('DELETE')


                    <button
                        type="submit"
                        class="btn btn-danger btn-sm"
                    >
                        Hapus
                    </button>

                </form>

            </li>

        @empty

            <li class="list-group-item text-muted">
                Belum ada lagu yang dipilih.
            </li>

        @endforelse

    </ul>

</div>