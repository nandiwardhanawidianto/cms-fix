<div class="card shadow-sm mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Foto Default Mempelai</span>
        <span class="badge bg-info text-dark">Dipakai bersama</span>
    </div>

    <div class="card-body">
        <div class="alert alert-info">
            Upload foto ilustrasi default satu kali. Setelah itu, setiap undangan versi tanpa foto cukup menekan tombol <strong>Pakai Foto Default</strong>. File tidak disalin ulang per undangan.
        </div>

        @error('default_photo')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror

        <div class="row g-4">
            @foreach([
                'pria' => ['label' => 'Mempelai Pria', 'path' => $defaultFotoPria],
                'wanita' => ['label' => 'Mempelai Wanita', 'path' => $defaultFotoWanita],
            ] as $type => $config)
                @php
                    $column = $type === 'pria' ? 'foto_pria' : 'foto_wanita';
                    $isUsed = $config['path'] && (($heroInvitation?->{$column}) === $config['path']);
                @endphp

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">{{ $config['label'] }}</h5>
                            @if($isUsed)
                                <span class="badge bg-success">Sedang dipakai</span>
                            @endif
                        </div>

                        @if($config['path'])
                            <img
                                src="{{ asset('storage/' . $config['path']) }}"
                                alt="Foto default {{ $config['label'] }}"
                                class="img-thumbnail mb-3"
                                style="width: 160px; height: 160px; object-fit: cover;"
                            >

                            <form
                                action="{{ route('hero.default.use', ['slug_id' => $slug->id, 'type' => $type]) }}"
                                method="POST"
                                class="mb-3"
                            >
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    Pakai Foto Default untuk Undangan Ini
                                </button>
                            </form>
                        @else
                            <div class="alert alert-warning py-2">
                                Belum ada foto default untuk {{ strtolower($config['label']) }}.
                            </div>
                        @endif

                        <form
                            action="{{ route('hero.default.store', ['type' => $type]) }}"
                            method="POST"
                            enctype="multipart/form-data"
                        >
                            @csrf
                            <label class="form-label">
                                {{ $config['path'] ? 'Ganti foto default global' : 'Upload foto default global' }}
                            </label>
                            <input
                                type="file"
                                name="default_photo"
                                class="form-control"
                                accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                required
                            >
                            <div class="form-text mb-2">
                                JPG/JPEG/PNG, maksimal 10 MB. Jika diganti, undangan yang memakai default lama otomatis diarahkan ke file baru.
                            </div>
                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                {{ $config['path'] ? 'Ganti Default' : 'Simpan Default' }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
