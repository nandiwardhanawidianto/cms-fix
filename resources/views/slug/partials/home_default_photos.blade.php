<div class="modal fade" id="defaultPhotosModal" tabindex="-1" aria-labelledby="defaultPhotosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="defaultPhotosModalLabel">Foto Default Mempelai</h5>
                    <div class="text-muted small">Satu foto global untuk setiap slot mempelai.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-info">
                    Undangan yang memiliki data mempelai tetapi belum punya foto custom akan memakai foto default secara otomatis. Mengganti default tidak mengganti foto custom.
                </div>

                @error('default_photo')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                <div class="row g-4">
                    @foreach([
                        'pria' => ['label' => 'Mempelai 1', 'path' => $defaultFotoMempelai1],
                        'wanita' => ['label' => 'Mempelai 2', 'path' => $defaultFotoMempelai2],
                    ] as $type => $config)
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="mb-3">{{ $config['label'] }}</h6>

                                @if($config['path'])
                                    <img
                                        src="{{ asset('storage/' . $config['path']) }}"
                                        alt="Foto default {{ $config['label'] }}"
                                        class="img-thumbnail mb-3"
                                        style="width: 180px; height: 180px; object-fit: cover;"
                                    >
                                @else
                                    <div class="alert alert-warning py-2">
                                        Belum ada foto default {{ $config['label'] }}.
                                    </div>
                                @endif

                                <form
                                    action="{{ route('hero.default.store', ['type' => $type]) }}"
                                    method="POST"
                                    enctype="multipart/form-data"
                                >
                                    @csrf
                                    <label class="form-label">
                                        {{ $config['path'] ? 'Ganti foto default' : 'Upload foto default' }}
                                    </label>
                                    <input
                                        type="file"
                                        name="default_photo"
                                        class="form-control mb-2"
                                        accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                        required
                                    >
                                    <div class="form-text mb-3">JPG/JPEG/PNG, maksimal 10 MB.</div>
                                    <button type="submit" class="btn btn-primary">
                                        {{ $config['path'] ? 'Ganti Default' : 'Simpan Default' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
