<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Undangan - {{ $slug->nama }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <h2 class="mb-4">✏️ Edit Undangan: <span class="text-primary">{{ $slug->nama }}</span></h2>

    <!-- Notifikasi -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Navbar Tabs -->
    <ul class="nav nav-tabs" id="editTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="hero-tab" data-bs-toggle="tab" data-bs-target="#hero" type="button" role="tab">
                Hero & Invitation
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="acara-tab" data-bs-toggle="tab" data-bs-target="#acara" type="button" role="tab">
                Acara
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="counting-tab" data-bs-toggle="tab" data-bs-target="#counting" type="button" role="tab">
                Countdown
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="galeri-tab" data-bs-toggle="tab" data-bs-target="#galeri" type="button" role="tab">
                Galeri
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="lovegift-tab" data-bs-toggle="tab" data-bs-target="#lovegift" type="button" role="tab">
                Love Gift
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content mt-3" id="editTabContent">
        <div class="tab-pane fade show active" id="hero" role="tabpanel" aria-labelledby="hero-tab">
            @include('slug.partials.hero')
        </div>
        
        <div class="tab-pane fade" id="acara" role="tabpanel" aria-labelledby="acara-tab">
            @include('slug.partials.acara')
        </div>

        <div class="tab-pane fade" id="counting" role="tabpanel" aria-labelledby="counting-tab">
            @include('slug.partials.counting')
        </div>

        <div class="tab-pane fade" id="galeri" role="tabpanel" aria-labelledby="galeri-tab">
            @include('slug.partials.galeri')
        </div>

        <div class="tab-pane fade" id="lovegift" role="tabpanel" aria-labelledby="lovegift-tab">
            @include('slug.partials.lovegift')
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('slug.index') }}" class="btn btn-secondary">⬅ Kembali ke daftar slug</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
