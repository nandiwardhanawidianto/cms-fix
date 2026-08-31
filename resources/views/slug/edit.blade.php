<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Undangan - {{ $slug->nama }}</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container py-4">

    <h2 class="mb-4">
        ✏️ Edit Undangan:
        <span class="text-primary">{{ $slug->nama }}</span>
    </h2>

    <!-- Notifikasi -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Navbar Tabs -->
    <ul class="nav nav-tabs" id="editTab" role="tablist">

        <!-- Hero -->
        <li class="nav-item" role="presentation">
            <button
                class="nav-link active"
                id="hero-tab"
                data-bs-toggle="tab"
                data-bs-target="#hero"
                type="button"
                role="tab"
                aria-controls="hero"
                aria-selected="true"
            >
                Hero & Invitation
            </button>
        </li>

        <!-- Acara -->
        <li class="nav-item" role="presentation">
            <button
                class="nav-link"
                id="acara-tab"
                data-bs-toggle="tab"
                data-bs-target="#acara"
                type="button"
                role="tab"
                aria-controls="acara"
                aria-selected="false"
            >
                Acara
            </button>
        </li>

        <!-- Countdown -->
        <li class="nav-item" role="presentation">
            <button
                class="nav-link"
                id="counting-tab"
                data-bs-toggle="tab"
                data-bs-target="#counting"
                type="button"
                role="tab"
                aria-controls="counting"
                aria-selected="false"
            >
                Countdown
            </button>
        </li>

        <!-- Galeri -->
        <li class="nav-item" role="presentation">
            <button
                class="nav-link"
                id="galeri-tab"
                data-bs-toggle="tab"
                data-bs-target="#galeri"
                type="button"
                role="tab"
                aria-controls="galeri"
                aria-selected="false"
            >
                Galeri
            </button>
        </li>

        <!-- Love Story -->
        <li class="nav-item" role="presentation">
            <button
                class="nav-link"
                id="love_story-tab"
                data-bs-toggle="tab"
                data-bs-target="#love_story"
                type="button"
                role="tab"
                aria-controls="love_story"
                aria-selected="false"
            >
                Love Story
            </button>
        </li>

        <!-- Love Gift -->
        <li class="nav-item" role="presentation">
            <button
                class="nav-link"
                id="lovegift-tab"
                data-bs-toggle="tab"
                data-bs-target="#lovegift"
                type="button"
                role="tab"
                aria-controls="lovegift"
                aria-selected="false"
            >
                Love Gift
            </button>
        </li>

        <!-- Kirim Kado -->
        <li class="nav-item" role="presentation">
            <button
                class="nav-link"
                id="kirimkado-tab"
                data-bs-toggle="tab"
                data-bs-target="#kirimkado"
                type="button"
                role="tab"
                aria-controls="kirimkado"
                aria-selected="false"
            >
                Kirim Kado
            </button>
        </li>

        <!-- Song List -->
        <li class="nav-item" role="presentation">
            <button
                class="nav-link"
                id="song_list-tab"
                data-bs-toggle="tab"
                data-bs-target="#song_list"
                type="button"
                role="tab"
                aria-controls="song_list"
                aria-selected="false"
            >
                Song List
            </button>
        </li>

        <!-- Copy Link -->
        <li class="nav-item" role="presentation">
            <button
                class="nav-link"
                id="copy_link-tab"
                data-bs-toggle="tab"
                data-bs-target="#copy_link"
                type="button"
                role="tab"
                aria-controls="copy_link"
                aria-selected="false"
            >
                Copy Link
            </button>
        </li>

    </ul>

    <!-- Tab Content -->
    <div class="tab-content mt-3" id="editTabContent">

        <!-- Hero -->
        <div
            class="tab-pane fade show active"
            id="hero"
            role="tabpanel"
            aria-labelledby="hero-tab"
        >
            @include('slug.partials.hero')
        </div>

        <!-- Acara -->
        <div
            class="tab-pane fade"
            id="acara"
            role="tabpanel"
            aria-labelledby="acara-tab"
        >
            @include('slug.partials.acara')
        </div>

        <!-- Countdown -->
        <div
            class="tab-pane fade"
            id="counting"
            role="tabpanel"
            aria-labelledby="counting-tab"
        >
            @include('slug.partials.counting')
        </div>

        <!-- Galeri -->
        <div
            class="tab-pane fade"
            id="galeri"
            role="tabpanel"
            aria-labelledby="galeri-tab"
        >
            @include('slug.partials.galeri')
        </div>

        <!-- Love Story -->
        <div
            class="tab-pane fade"
            id="love_story"
            role="tabpanel"
            aria-labelledby="love_story-tab"
        >
            @include('slug.partials.love_story')
        </div>

        <!-- Love Gift -->
        <div
            class="tab-pane fade"
            id="lovegift"
            role="tabpanel"
            aria-labelledby="lovegift-tab"
        >
            @include('slug.partials.lovegift')
        </div>

        <!-- Kirim Kado -->
        <div
            class="tab-pane fade"
            id="kirimkado"
            role="tabpanel"
            aria-labelledby="kirimkado-tab"
        >
            @include('slug.partials.kirimkado')
        </div>

        <!-- Song List -->
        <div
            class="tab-pane fade"
            id="song_list"
            role="tabpanel"
            aria-labelledby="song_list-tab"
        >
            @include('slug.partials.song_list')
        </div>

        <!-- Copy Link -->
        <div
            class="tab-pane fade"
            id="copy_link"
            role="tabpanel"
            aria-labelledby="copy_link-tab"
        >
            @include('slug.partials.copy_link')
        </div>

    </div>

    <!-- Kembali -->
    <div class="mt-4">
        <a href="{{ route('slug.index') }}" class="btn btn-secondary">
            ⬅ Kembali ke daftar slug
        </a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>