<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">

<style>
    .love-story-photo-preview {
        width: 180px;
        height: 225px;
        object-fit: cover;
        border-radius: 8px;
    }

    .love-story-crop-container {
        width: 100%;
        max-height: 70vh;
        overflow: hidden;
        background: #111;
    }

    .love-story-crop-container img {
        display: block;
        max-width: 100%;
    }
</style>

@php
    $loveStoryPhotos = [
        'awal' => [
            'label' => 'Foto Awal Pertemuan',
            'column' => 'gambar_awal',
            'cropped' => 'gambar_awal_cropped',
            'inputId' => 'loveStoryAwalInput',
            'previewId' => 'loveStoryAwalPreview',
            'croppedId' => 'loveStoryAwalCropped',
            'statusId' => 'loveStoryAwalStatus',
        ],
        'hubungan' => [
            'label' => 'Foto Menjalin Hubungan',
            'column' => 'gambar_hubungan',
            'cropped' => 'gambar_hubungan_cropped',
            'inputId' => 'loveStoryHubunganInput',
            'previewId' => 'loveStoryHubunganPreview',
            'croppedId' => 'loveStoryHubunganCropped',
            'statusId' => 'loveStoryHubunganStatus',
        ],
        'lamaran' => [
            'label' => 'Foto Lamaran',
            'column' => 'gambar_lamaran',
            'cropped' => 'gambar_lamaran_cropped',
            'inputId' => 'loveStoryLamaranInput',
            'previewId' => 'loveStoryLamaranPreview',
            'croppedId' => 'loveStoryLamaranCropped',
            'statusId' => 'loveStoryLamaranStatus',
        ],
    ];
@endphp

<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>💖 Love Story</h5>

        @if($lovestory)
            <form action="{{ route('LoveStory.delete', $slug_id) }}" method="POST"
                  onsubmit="return confirm('Yakin hapus Love Story?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm">Hapus</button>
            </form>
        @endif
    </div>

    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Love Story belum bisa disimpan.</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('LoveStory.store', $slug_id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card p-3 mb-3">
                <h6 class="fw-bold">📘 Cerita</h6>

                <div class="mb-3">
                    <label class="fw-bold">Judul Awal Pertemuan</label>
                    <input type="text" name="judul_awal_pertemuan" class="form-control mb-2"
                           value="{{ old('judul_awal_pertemuan', $lovestory->judul_awal_pertemuan ?? '') }}"
                           placeholder="Judul awal pertemuan...">

                    <label class="fw-bold">Cerita Awal Pertemuan</label>
                    <textarea name="awal_pertemuan" class="form-control" rows="3"
                              placeholder="Ceritakan awal pertemuan...">{{ old('awal_pertemuan', $lovestory->awal_pertemuan ?? '') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Judul Menjalin Hubungan</label>
                    <input type="text" name="judul_menjalin_hubungan" class="form-control mb-2"
                           value="{{ old('judul_menjalin_hubungan', $lovestory->judul_menjalin_hubungan ?? '') }}"
                           placeholder="Judul perjalanan hubungan...">

                    <label class="fw-bold">Cerita Menjalin Hubungan</label>
                    <textarea name="menjalin_hubungan" class="form-control" rows="3"
                              placeholder="Ceritakan perjalanan hubungan...">{{ old('menjalin_hubungan', $lovestory->menjalin_hubungan ?? '') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Judul Lamaran</label>
                    <input type="text" name="judul_lamaran" class="form-control mb-2"
                           value="{{ old('judul_lamaran', $lovestory->judul_lamaran ?? '') }}"
                           placeholder="Judul momen lamaran...">

                    <label class="fw-bold">Cerita Lamaran</label>
                    <textarea name="lamaran" class="form-control" rows="3"
                              placeholder="Ceritakan momen lamaran...">{{ old('lamaran', $lovestory->lamaran ?? '') }}</textarea>
                </div>
            </div>

            <div class="card p-3">
                <h6 class="fw-bold">📷 Foto</h6>
                <div class="form-text mb-3">
                    Pilih foto, atur crop, lalu klik Gunakan Foto. Jika tidak memilih foto baru, foto lama tetap dipakai.
                </div>

                @foreach($loveStoryPhotos as $slot => $config)
                    @php
                        $currentPhoto = data_get($lovestory, $config['column']);
                        $currentPhotoUrl = $currentPhoto ? asset('storage/' . $currentPhoto) : null;
                    @endphp

                    <div class="mb-4 border rounded p-3">
                        <label class="fw-bold d-block mb-2">{{ $config['label'] }}</label>

                        <input
                            type="hidden"
                            name="{{ $config['cropped'] }}"
                            id="{{ $config['croppedId'] }}"
                            value="{{ old($config['cropped'], '') }}"
                        >

                        @if($currentPhotoUrl)
                            <img
                                id="{{ $config['previewId'] }}"
                                src="{{ $currentPhotoUrl }}"
                                class="img-thumbnail love-story-photo-preview mb-2 d-block"
                                alt="{{ $config['label'] }}"
                            >
                        @else
                            <img
                                id="{{ $config['previewId'] }}"
                                class="img-thumbnail love-story-photo-preview mb-2"
                                alt="{{ $config['label'] }}"
                                style="display:none;"
                            >
                        @endif

                        <input
                            type="file"
                            id="{{ $config['inputId'] }}"
                            class="form-control love-story-photo-input"
                            data-target="{{ $slot }}"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        >

                        <div id="{{ $config['statusId'] }}" class="form-text mt-2">
                            Tidak ada perubahan foto yang dipilih.
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary mt-3">
                {{ $lovestory ? 'Update' : 'Simpan' }}
            </button>
        </form>
    </div>
</div>

<div class="modal fade" id="loveStoryCropModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0">Atur Foto Love Story</h5>
                    <small id="loveStoryCropLabel" class="text-muted"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label d-block">Rasio Crop</label>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary love-story-ratio active" data-ratio="0.8">4:5</button>
                        <button type="button" class="btn btn-outline-primary love-story-ratio" data-ratio="1">1:1</button>
                        <button type="button" class="btn btn-outline-primary love-story-ratio" data-ratio="free">Bebas</button>
                    </div>
                </div>

                <div class="love-story-crop-container">
                    <img id="loveStoryCropImage" src="" alt="Crop Love Story">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="loveStoryZoomOut">Zoom -</button>
                <button type="button" class="btn btn-outline-secondary" id="loveStoryZoomIn">Zoom +</button>
                <button type="button" class="btn btn-outline-secondary" id="loveStoryReset">Reset</button>
                <button type="button" class="btn btn-primary" id="loveStoryUsePhoto">Gunakan Foto</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let cropper = null;
    let activeTarget = null;
    let activeRatio = 0.8;

    const modalElement = document.getElementById('loveStoryCropModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    const cropImage = document.getElementById('loveStoryCropImage');
    const cropLabel = document.getElementById('loveStoryCropLabel');

    const states = {
        awal: {
            label: 'Foto Awal Pertemuan',
            input: document.getElementById('loveStoryAwalInput'),
            cropped: document.getElementById('loveStoryAwalCropped'),
            preview: document.getElementById('loveStoryAwalPreview'),
            status: document.getElementById('loveStoryAwalStatus')
        },
        hubungan: {
            label: 'Foto Menjalin Hubungan',
            input: document.getElementById('loveStoryHubunganInput'),
            cropped: document.getElementById('loveStoryHubunganCropped'),
            preview: document.getElementById('loveStoryHubunganPreview'),
            status: document.getElementById('loveStoryHubunganStatus')
        },
        lamaran: {
            label: 'Foto Lamaran',
            input: document.getElementById('loveStoryLamaranInput'),
            cropped: document.getElementById('loveStoryLamaranCropped'),
            preview: document.getElementById('loveStoryLamaranPreview'),
            status: document.getElementById('loveStoryLamaranStatus')
        }
    };

    function resetRatio() {
        activeRatio = 0.8;
        document.querySelectorAll('.love-story-ratio').forEach(function (button) {
            button.classList.toggle('active', button.dataset.ratio === '0.8');
        });
    }

    function validateImage(file) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        const maxSize = 10 * 1024 * 1024;

        if (!allowedTypes.includes(file.type)) {
            alert('Format foto harus JPG, JPEG, PNG, atau WEBP.');
            return false;
        }

        if (file.size > maxSize) {
            alert('Ukuran foto maksimal 10 MB.');
            return false;
        }

        return true;
    }

    Object.entries(states).forEach(function ([target, state]) {
        state.input.addEventListener('change', function () {
            const file = this.files[0];

            if (!file) {
                return;
            }

            if (!validateImage(file)) {
                this.value = '';
                return;
            }

            activeTarget = target;
            resetRatio();
            cropLabel.textContent = state.label;

            const reader = new FileReader();
            reader.onload = function (event) {
                cropImage.src = event.target.result;
                modal.show();
            };
            reader.readAsDataURL(file);
        });
    });

    modalElement.addEventListener('shown.bs.modal', function () {
        if (cropper) {
            cropper.destroy();
        }

        cropper = new Cropper(cropImage, {
            aspectRatio: activeRatio,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 0.9,
            responsive: true,
            background: false,
            movable: true,
            zoomable: true,
            rotatable: false,
            scalable: false,
            guides: true,
            center: true,
            highlight: true
        });
    });

    document.querySelectorAll('.love-story-ratio').forEach(function (button) {
        button.addEventListener('click', function () {
            document.querySelectorAll('.love-story-ratio').forEach(function (item) {
                item.classList.remove('active');
            });
            this.classList.add('active');

            activeRatio = this.dataset.ratio === 'free'
                ? NaN
                : parseFloat(this.dataset.ratio);

            if (cropper) {
                cropper.setAspectRatio(activeRatio);
            }
        });
    });

    document.getElementById('loveStoryZoomIn').addEventListener('click', function () {
        if (cropper) cropper.zoom(0.1);
    });

    document.getElementById('loveStoryZoomOut').addEventListener('click', function () {
        if (cropper) cropper.zoom(-0.1);
    });

    document.getElementById('loveStoryReset').addEventListener('click', function () {
        if (!cropper) return;
        cropper.reset();
        cropper.setAspectRatio(activeRatio);
    });

    document.getElementById('loveStoryUsePhoto').addEventListener('click', function () {
        if (!cropper || !activeTarget) {
            return;
        }

        const canvas = cropper.getCroppedCanvas({
            maxWidth: 1400,
            maxHeight: 1600,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high'
        });

        if (!canvas) {
            alert('Gagal memproses foto.');
            return;
        }

        const result = canvas.toDataURL('image/jpeg', 0.88);
        const state = states[activeTarget];

        state.cropped.value = result;
        state.preview.src = result;
        state.preview.style.display = 'block';
        state.status.textContent = 'Hasil crop siap. Klik ' + @json($lovestory ? 'Update' : 'Simpan') + ' untuk menerapkan.';

        modal.hide();
    });

    modalElement.addEventListener('hidden.bs.modal', function () {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }

        activeTarget = null;
    });
});
</script>
