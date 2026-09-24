<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css"
>

<style>
    .crop-image-container {
        width: 100%;
        max-height: 70vh;
        overflow: hidden;
    }

    .crop-image-container img {
        display: block;
        max-width: 100%;
    }

    .foto-preview {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #ddd;
    }

    .mempelai-section {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 20px;
    }

    .picture-choice {
        width: 110px;
    }

    .picture-choice img {
        width: 100%;
        height: 100px;
        object-fit: cover;
    }
</style>

@php
    $mempelaiConfigs = [
        'pria' => [
            'label' => 'Mempelai 1',
            'short' => 'nama_panggilan_pria',
            'full' => 'nama_lengkap_pria',
            'parents' => 'orangtua_pria',
            'photo' => 'foto_pria',
            'source' => 'foto_pria_source',
            'cropped' => 'foto_pria_cropped',
            'inputId' => 'fotoPriaInput',
            'previewId' => 'previewPria',
            'sourceId' => 'fotoPriaSource',
            'croppedId' => 'fotoPriaCropped',
            'statusId' => 'fotoPriaStatus',
            'optional' => false,
        ],
        'wanita' => [
            'label' => 'Mempelai 2',
            'short' => 'nama_panggilan_wanita',
            'full' => 'nama_lengkap_wanita',
            'parents' => 'orangtua_wanita',
            'photo' => 'foto_wanita',
            'source' => 'foto_wanita_source',
            'cropped' => 'foto_wanita_cropped',
            'inputId' => 'fotoWanitaInput',
            'previewId' => 'previewWanita',
            'sourceId' => 'fotoWanitaSource',
            'croppedId' => 'fotoWanitaCropped',
            'statusId' => 'fotoWanitaStatus',
            'optional' => true,
        ],
    ];
@endphp

<div class="card shadow-sm">
    <div class="card-header">
        Hero & Invitation
    </div>

    <div class="card-body">
        <form
            action="{{ route('hero.store', $slug->id) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Data belum bisa disimpan.</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="alert alert-info">
                Untuk setiap mempelai, pilih salah satu jika ingin mengganti foto:
                <strong>Choose File</strong>, <strong>Picture 1</strong>, atau <strong>Picture 2</strong>.
                Jika tidak memilih apa pun, foto yang sekarang tetap dipakai.
            </div>

            <div class="row g-4">
                @foreach($mempelaiConfigs as $slot => $config)
                    @php
                        $currentPhoto = data_get($heroInvitation, $config['photo']);
                        $currentPhotoUrl = $currentPhoto ? asset('storage/' . $currentPhoto) : null;
                    @endphp

                    <div class="col-12">
                        <div class="mempelai-section">
                            <div class="mb-4">
                                <h5 class="mb-1">
                                    {{ $config['label'] }}
                                    @if($config['optional'])
                                        <span class="badge bg-secondary">Opsional</span>
                                    @endif
                                </h5>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Panggilan {{ $config['label'] }}</label>
                                    <input
                                        type="text"
                                        name="{{ $config['short'] }}"
                                        value="{{ old($config['short'], data_get($heroInvitation, $config['short'], '')) }}"
                                        class="form-control @error($config['short']) is-invalid @enderror"
                                    >
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Nama Lengkap {{ $config['label'] }}</label>
                                    <input
                                        type="text"
                                        name="{{ $config['full'] }}"
                                        value="{{ old($config['full'], data_get($heroInvitation, $config['full'], '')) }}"
                                        class="form-control @error($config['full']) is-invalid @enderror"
                                    >
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Orang Tua {{ $config['label'] }}</label>
                                    <input
                                        type="text"
                                        name="{{ $config['parents'] }}"
                                        value="{{ old($config['parents'], data_get($heroInvitation, $config['parents'], '')) }}"
                                        class="form-control @error($config['parents']) is-invalid @enderror"
                                        placeholder="Contoh: Putra ke 2 dari Bapak ... & Ibu ..."
                                    >
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label d-block">Foto {{ $config['label'] }}</label>

                                    <input
                                        type="hidden"
                                        name="{{ $config['source'] }}"
                                        id="{{ $config['sourceId'] }}"
                                        value="keep"
                                    >
                                    <input
                                        type="hidden"
                                        name="{{ $config['cropped'] }}"
                                        id="{{ $config['croppedId'] }}"
                                    >

                                    <div class="mb-3">
                                        @if($currentPhotoUrl)
                                            <img
                                                id="{{ $config['previewId'] }}"
                                                src="{{ $currentPhotoUrl }}"
                                                class="foto-preview"
                                                alt="Foto {{ $config['label'] }}"
                                            >
                                        @else
                                            <img
                                                id="{{ $config['previewId'] }}"
                                                class="foto-preview"
                                                alt="Foto {{ $config['label'] }}"
                                                style="display:none;"
                                            >
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <label for="{{ $config['inputId'] }}" class="form-label fw-semibold">
                                            Choose File
                                        </label>
                                        <input
                                            type="file"
                                            id="{{ $config['inputId'] }}"
                                            accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                            class="form-control"
                                        >
                                        <div class="form-text">Upload foto customer dari perangkat, lalu crop seperti biasa.</div>
                                    </div>

                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="picture-choice">
                                            @if($picture1Path)
                                                <img
                                                    src="{{ asset('storage/' . $picture1Path) }}"
                                                    class="img-thumbnail mb-2"
                                                    alt="Picture 1"
                                                >
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-primary btn-sm w-100 choose-global-picture"
                                                    data-target="{{ $slot }}"
                                                    data-source="picture1"
                                                    data-url="{{ asset('storage/' . $picture1Path) }}"
                                                >
                                                    Picture 1
                                                </button>
                                            @else
                                                <div class="border rounded p-2 text-muted small text-center">
                                                    Picture 1 belum ada
                                                </div>
                                            @endif
                                        </div>

                                        <div class="picture-choice">
                                            @if($picture2Path)
                                                <img
                                                    src="{{ asset('storage/' . $picture2Path) }}"
                                                    class="img-thumbnail mb-2"
                                                    alt="Picture 2"
                                                >
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-primary btn-sm w-100 choose-global-picture"
                                                    data-target="{{ $slot }}"
                                                    data-source="picture2"
                                                    data-url="{{ asset('storage/' . $picture2Path) }}"
                                                >
                                                    Picture 2
                                                </button>
                                            @else
                                                <div class="border rounded p-2 text-muted small text-center">
                                                    Picture 2 belum ada
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div id="{{ $config['statusId'] }}" class="form-text mt-2">
                                        Tidak ada perubahan foto yang dipilih.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="cropModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Atur Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="crop-image-container">
                    <img id="cropImage" src="" alt="Crop">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="zoomOut">Zoom -</button>
                <button type="button" class="btn btn-outline-secondary" id="zoomIn">Zoom +</button>
                <button type="button" class="btn btn-outline-secondary" id="resetCrop">Reset</button>
                <button type="button" class="btn btn-primary" id="gunakanFoto">Gunakan Foto</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let cropper = null;
        let activeTarget = null;

        const cropModalElement = document.getElementById('cropModal');
        const cropModal = bootstrap.Modal.getOrCreateInstance(cropModalElement);
        const cropImage = document.getElementById('cropImage');

        const states = {
            pria: {
                input: document.getElementById('fotoPriaInput'),
                source: document.getElementById('fotoPriaSource'),
                cropped: document.getElementById('fotoPriaCropped'),
                preview: document.getElementById('previewPria'),
                status: document.getElementById('fotoPriaStatus')
            },
            wanita: {
                input: document.getElementById('fotoWanitaInput'),
                source: document.getElementById('fotoWanitaSource'),
                cropped: document.getElementById('fotoWanitaCropped'),
                preview: document.getElementById('previewWanita'),
                status: document.getElementById('fotoWanitaStatus')
            }
        };

        function validateImage(file) {
            const allowedTypes = ['image/jpeg', 'image/png'];
            const maxSize = 10 * 1024 * 1024;

            if (!allowedTypes.includes(file.type)) {
                alert('Format foto harus JPG, JPEG, atau PNG.');
                return false;
            }

            if (file.size > maxSize) {
                alert('Ukuran foto maksimal 10 MB.');
                return false;
            }

            return true;
        }

        function openCropper(file, target) {
            if (!file || !validateImage(file)) {
                return;
            }

            activeTarget = target;
            const reader = new FileReader();

            reader.onload = function (event) {
                cropImage.src = event.target.result;
                cropModal.show();
            };

            reader.readAsDataURL(file);
        }

        Object.entries(states).forEach(([target, state]) => {
            state.input.addEventListener('change', function () {
                const file = this.files[0];

                if (!file) {
                    return;
                }

                if (!validateImage(file)) {
                    this.value = '';
                    return;
                }

                openCropper(file, target);
            });
        });

        document.querySelectorAll('.choose-global-picture').forEach(function (button) {
            button.addEventListener('click', function () {
                const target = this.dataset.target;
                const source = this.dataset.source;
                const url = this.dataset.url;
                const state = states[target];

                state.source.value = source;
                state.cropped.value = '';
                state.input.value = '';
                state.preview.src = url;
                state.preview.style.display = 'block';
                state.status.textContent = (source === 'picture1' ? 'Picture 1' : 'Picture 2') + ' dipilih. Klik Simpan untuk menerapkan.';
            });
        });

        cropModalElement.addEventListener('shown.bs.modal', function () {
            if (cropper) {
                cropper.destroy();
            }

            cropper = new Cropper(cropImage, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.85,
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

        document.getElementById('zoomIn').addEventListener('click', function () {
            if (cropper) cropper.zoom(0.1);
        });

        document.getElementById('zoomOut').addEventListener('click', function () {
            if (cropper) cropper.zoom(-0.1);
        });

        document.getElementById('resetCrop').addEventListener('click', function () {
            if (cropper) cropper.reset();
        });

        document.getElementById('gunakanFoto').addEventListener('click', function () {
            if (!cropper || !activeTarget) {
                return;
            }

            const canvas = cropper.getCroppedCanvas({
                width: 800,
                height: 800,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high'
            });

            const result = canvas.toDataURL('image/jpeg', 0.90);
            const state = states[activeTarget];

            state.cropped.value = result;
            state.source.value = 'upload';
            state.preview.src = result;
            state.preview.style.display = 'block';
            state.status.textContent = 'Foto custom dipilih. Klik Simpan untuk menerapkan.';

            cropModal.hide();
        });

        cropModalElement.addEventListener('hidden.bs.modal', function () {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }

            activeTarget = null;
        });
    });
</script>
