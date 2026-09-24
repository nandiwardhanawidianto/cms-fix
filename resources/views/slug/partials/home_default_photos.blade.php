<div class="modal fade" id="defaultPhotosModal" tabindex="-1" aria-labelledby="defaultPhotosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="defaultPhotosModalLabel">Picture Default</h5>
                    <div class="text-muted small">Simpan dua gambar global. Keduanya bisa dipakai oleh Mempelai 1 maupun Mempelai 2.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-info">
                    Picture 1 dan Picture 2 tidak terikat gender atau urutan mempelai. Saat edit undangan, setiap mempelai bisa memilih Picture 1, Picture 2, atau upload foto sendiri.
                </div>

                @error('default_photo')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                <div class="row g-4">
                    @foreach([
                        'picture1' => ['label' => 'Picture 1', 'path' => $picture1Path],
                        'picture2' => ['label' => 'Picture 2', 'path' => $picture2Path],
                    ] as $picture => $config)
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="mb-3">{{ $config['label'] }}</h6>

                                @if($config['path'])
                                    <img
                                        src="{{ asset('storage/' . $config['path']) }}"
                                        alt="{{ $config['label'] }}"
                                        class="img-thumbnail mb-3"
                                        style="width: 180px; height: 180px; object-fit: cover;"
                                    >
                                @else
                                    <div class="alert alert-warning py-2">
                                        {{ $config['label'] }} belum diupload.
                                    </div>
                                @endif

                                <form
                                    action="{{ route('hero.default.store', ['picture' => $picture]) }}"
                                    method="POST"
                                    enctype="multipart/form-data"
                                >
                                    @csrf
                                    <label class="form-label">
                                        {{ $config['path'] ? 'Ganti ' . $config['label'] : 'Upload ' . $config['label'] }}
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
                                        {{ $config['path'] ? 'Ganti Gambar' : 'Simpan Gambar' }}
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
