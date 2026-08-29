
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Master Lagu</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f5f6f8;
        }

        .page-title {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .page-subtitle {
            color: #6c757d;
            margin-bottom: 0;
        }

        .song-card {
            border: 0;
            border-radius: 12px;
        }

        .song-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 0;
            border-bottom: 1px solid #eee;
        }

        .song-item:last-child {
            border-bottom: 0;
        }

        .song-number {
            width: 38px;
            height: 38px;
            min-width: 38px;
            border-radius: 50%;
            background: #f1f3f5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #6c757d;
        }

        .song-info {
            flex: 1;
            min-width: 0;
        }

        .song-title {
            font-weight: 600;
            margin-bottom: 8px;
            word-break: break-word;
        }

        .song-audio {
            width: 100%;
            max-width: 420px;
            height: 38px;
        }

        .upload-card {
            border: 0;
            border-radius: 12px;
        }

        @media (max-width: 767px) {
            .song-item {
                align-items: flex-start;
                flex-wrap: wrap;
            }

            .song-info {
                width: calc(100% - 55px);
            }

            .song-actions {
                width: 100%;
                padding-left: 54px;
            }

            .song-audio {
                max-width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="container py-5">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h2 class="page-title">
                Master Lagu
            </h2>

            <p class="page-subtitle">
                Kelola lagu yang tersedia untuk undangan.
            </p>
        </div>

        <a
            href="{{ route('slug.index') }}"
            class="btn btn-outline-secondary"
        >
            Kembali ke Management
        </a>

    </div>


    {{-- SUCCESS --}}
    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- ERROR --}}
    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif



    {{-- ===================================================== --}}
    {{-- UPLOAD LAGU --}}
    {{-- ===================================================== --}}

    <div class="card upload-card shadow-sm mb-4">

        <div class="card-body p-4">

            <h5 class="mb-3">
                Tambah Lagu Baru
            </h5>


            <form
                action="{{ route('song.store') }}"
                method="POST"
                enctype="multipart/form-data"
            >

                @csrf


                <div class="row g-3">

                    <div class="col-md-5">

                        <label class="form-label">
                            Nama Lagu
                        </label>

                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            value="{{ old('title') }}"
                            placeholder="Contoh: Beautiful In White"
                            required
                        >

                    </div>


                    <div class="col-md-5">

                        <label class="form-label">
                            File Lagu
                        </label>

                        <input
                            type="file"
                            name="file"
                            class="form-control"
                            accept=".mp3,.wav,.ogg,audio/mpeg,audio/wav,audio/ogg"
                            required
                        >

                        <small class="text-muted">
                            MP3, WAV, atau OGG.
                        </small>

                    </div>


                    <div class="col-md-2 d-flex align-items-end">

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Upload
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>



    {{-- ===================================================== --}}
    {{-- DAFTAR LAGU --}}
    {{-- ===================================================== --}}

    <div class="card song-card shadow-sm">

        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <div>

                    <h5 class="mb-1">
                        Daftar Lagu
                    </h5>

                    <small class="text-muted">
                        Total {{ $songs->count() }} lagu
                    </small>

                </div>

            </div>


            @forelse($songs as $index => $song)

                <div class="song-item">

                    {{-- NOMOR --}}

                    <div class="song-number">
                        {{ $index + 1 }}
                    </div>


                    {{-- LAGU --}}

                    <div class="song-info">

                        <div class="song-title">
                            {{ $song->title }}
                        </div>


                        <audio
                            controls
                            preload="none"
                            class="song-audio"
                        >

                            <source
                                src="{{ $song->url }}"
                            >

                            Browser tidak mendukung audio.

                        </audio>

                    </div>


                    {{-- AKSI --}}

                    <div class="song-actions">

                        <form
                            action="{{ route('song.destroy', $song->id) }}"
                            method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus lagu {{ addslashes($song->title) }}?')"
                        >

                            @csrf
                            @method('DELETE')


                            <button
                                type="submit"
                                class="btn btn-outline-danger btn-sm"
                            >
                                Hapus
                            </button>

                        </form>

                    </div>

                </div>

            @empty

                <div class="text-center py-5">

                    <h6 class="text-muted">
                        Belum ada lagu
                    </h6>

                    <p class="text-muted mb-0">
                        Upload lagu pertama melalui form di atas.
                    </p>

                </div>

            @endforelse

        </div>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>
