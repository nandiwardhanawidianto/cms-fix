<style>
    .countdown-photo-preview {
        width: 240px;
        height: 213px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #dee2e6;
    }

    .countdown-crop-container {
        width: 100%;
        max-height: 70vh;
        overflow: hidden;
    }

    .countdown-crop-container img {
        display: block;
        max-width: 100%;
    }
</style>

<div class="container mt-4">
    <h4>📖 Form Counting</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('counting.store', $slug_id) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Foto Countdown</label>

            <input
                type="hidden"
                name="foto_counting_cropped"
                id="fotoCountingCropped"
                value="{{ old('foto_counting_cropped', '') }}"
            >

            <div class="mb-2">
                @if(!empty($counting?->foto_counting))
                    <img
                        id="fotoCountingPreview"
                        src="{{ asset('storage/' . $counting->foto_counting) }}"
                        alt="Foto Countdown"
                        class="countdown-photo-preview"
                    >
                @else
                    <img
                        id="fotoCountingPreview"
                        alt="Foto Countdown"
                        class="countdown-photo-preview"
                        style="display:none;"
                    >
                @endif
            </div>

            <input
                type="file"
                id="fotoCountingInput"
                class="form-control @error('foto_counting_cropped') is-invalid @enderror"
                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
            >

            <div id="fotoCountingStatus" class="form-text">
                Pilih foto, atur crop, lalu klik Gunakan Foto. Jika tidak memilih foto baru, foto yang sekarang tetap digunakan.
            </div>

            @error('foto_counting_cropped')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Nama Surat <small class="text-muted">(opsional)</small></label>
            <input type="text" name="nama_surat" class="form-control"
                   value="{{ old('nama_surat', $counting->nama_surat ?? '') }}">
        </div>

        <div class="mb-3">
            <label>Surat (Arab) <small class="text-muted">(opsional)</small></label>
            <textarea name="surat_arab" class="form-control" rows="3">{{ old('surat_arab', $counting->surat_arab ?? '') }}</textarea>
        </div>

        <div class="mb-3">
            <label>Deskripsi Surat (Bahasa Indonesia) <small class="text-muted">(opsional)</small></label>
            <textarea name="deskripsi_surat" class="form-control" rows="4">{{ old('deskripsi_surat', $counting->deskripsi_surat ?? '') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>

<div class="modal fade" id="countdownCropModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crop Foto Countdown</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="countdown-crop-container">
                    <img id="countdownCropImage" src="" alt="Crop Foto Countdown">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="countdownZoomOut">Zoom -</button>
                <button type="button" class="btn btn-outline-secondary" id="countdownZoomIn">Zoom +</button>
                <button type="button" class="btn btn-outline-secondary" id="countdownResetCrop">Reset</button>
                <button type="button" class="btn btn-primary" id="countdownUsePhoto">Gunakan Foto</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('fotoCountingInput');
        const croppedInput = document.getElementById('fotoCountingCropped');
        const preview = document.getElementById('fotoCountingPreview');
        const status = document.getElementById('fotoCountingStatus');
        const modalElement = document.getElementById('countdownCropModal');
        const cropImage = document.getElementById('countdownCropImage');

        if (!input || !croppedInput || !preview || !status || !modalElement || !cropImage) {
            return;
        }

        let cropper = null;
        let cropApplied = false;
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);

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

        input.addEventListener('change', function () {
            const file = this.files[0];

            if (!file) {
                return;
            }

            if (!validateImage(file)) {
                this.value = '';
                return;
            }

            if (typeof Cropper === 'undefined') {
                alert('Fitur crop belum termuat. Refresh halaman lalu coba lagi.');
                this.value = '';
                return;
            }

            cropApplied = false;
            const reader = new FileReader();

            reader.onload = function (event) {
                cropImage.src = event.target.result;
                modal.show();
            };

            reader.readAsDataURL(file);
        });

        modalElement.addEventListener('shown.bs.modal', function () {
            if (cropper) {
                cropper.destroy();
            }

            cropper = new Cropper(cropImage, {
                aspectRatio: 9 / 8,
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

        document.getElementById('countdownZoomIn').addEventListener('click', function () {
            if (cropper) cropper.zoom(0.1);
        });

        document.getElementById('countdownZoomOut').addEventListener('click', function () {
            if (cropper) cropper.zoom(-0.1);
        });

        document.getElementById('countdownResetCrop').addEventListener('click', function () {
            if (cropper) cropper.reset();
        });

        document.getElementById('countdownUsePhoto').addEventListener('click', function () {
            if (!cropper) {
                return;
            }

            const canvas = cropper.getCroppedCanvas({
                width: 1080,
                height: 960,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high'
            });

            const result = canvas.toDataURL('image/jpeg', 0.9);

            croppedInput.value = result;
            preview.src = result;
            preview.style.display = 'block';
            status.textContent = 'Foto countdown hasil crop siap. Klik Simpan untuk menerapkan.';
            cropApplied = true;
            input.value = '';
            modal.hide();
        });

        modalElement.addEventListener('hidden.bs.modal', function () {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }

            if (!cropApplied) {
                input.value = '';
            }
        });
    });
</script>
