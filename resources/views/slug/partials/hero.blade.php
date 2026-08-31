<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hero & Invitation</title>

    {{-- Bootstrap --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    {{-- Cropper CSS --}}
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
            width: 140px;
            height: 140px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .mempelai-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
        }

        .section-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
    </style>
</head>

<body class="bg-light">

<div class="container py-5">

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


                {{-- ====================== --}}
                {{-- ERROR --}}
                {{-- ====================== --}}

                @if($errors->has('mempelai'))
                    <div class="alert alert-danger">
                        {{ $errors->first('mempelai') }}
                    </div>
                @endif


                @if($errors->any() && !$errors->has('mempelai'))
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
                    Minimal isi salah satu mempelai.
                    Mempelai 1 atau Mempelai 2 boleh dikosongkan.
                </div>


                <div class="row g-4">


                    {{-- ====================================================== --}}
                    {{-- MEMPELAI 1 --}}
                    {{-- ====================================================== --}}

                    <div class="col-12">

                        <div class="mempelai-section">

                            <div class="mb-4">

                                <h5 class="section-title">
                                    Mempelai 1
                                </h5>

                                <small class="text-muted">
                                    Boleh dikosongkan jika Mempelai 2 diisi.
                                </small>

                            </div>


                            <div class="row g-3">


                                {{-- Nama Panggilan --}}
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Nama Panggilan Mempelai 1
                                    </label>

                                    <input
                                        type="text"
                                        name="nama_panggilan_pria"
                                        value="{{ old('nama_panggilan_pria', $heroInvitation->nama_panggilan_pria ?? '') }}"
                                        class="form-control @error('nama_panggilan_pria') is-invalid @enderror"
                                        placeholder="Contoh: Andra"
                                    >

                                    @error('nama_panggilan_pria')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                                {{-- Nama Lengkap --}}
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Nama Lengkap Mempelai 1
                                    </label>

                                    <input
                                        type="text"
                                        name="nama_lengkap_pria"
                                        value="{{ old('nama_lengkap_pria', $heroInvitation->nama_lengkap_pria ?? '') }}"
                                        class="form-control @error('nama_lengkap_pria') is-invalid @enderror"
                                        placeholder="Contoh: Putra Andra Ramadhan"
                                    >

                                    @error('nama_lengkap_pria')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                                {{-- Foto --}}
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Foto Mempelai 1
                                    </label>

                                    <input
                                        type="file"
                                        id="fotoPriaInput"
                                        accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                        class="form-control"
                                    >

                                    <small class="text-muted">
                                        Opsional. Format JPG, JPEG, atau PNG.
                                    </small>


                                    {{-- Hasil crop --}}
                                    <input
                                        type="hidden"
                                        name="foto_pria_cropped"
                                        id="fotoPriaCropped"
                                    >


                                    <div class="mt-3">

                                        @if(!empty($heroInvitation->foto_pria))

                                            <img
                                                src="{{ asset('storage/'.$heroInvitation->foto_pria) }}"
                                                id="previewPria"
                                                class="foto-preview"
                                                alt="Foto Mempelai 1"
                                            >

                                        @else

                                            <img
                                                id="previewPria"
                                                class="foto-preview"
                                                alt="Foto Mempelai 1"
                                                style="display:none;"
                                            >

                                        @endif

                                    </div>

                                </div>


                                {{-- Orang Tua --}}
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Orang Tua Mempelai 1
                                    </label>

                                    <input
                                        type="text"
                                        name="orangtua_pria"
                                        value="{{ old('orangtua_pria', $heroInvitation->orangtua_pria ?? '') }}"
                                        class="form-control @error('orangtua_pria') is-invalid @enderror"
                                        placeholder="Contoh: Bapak Ahmad & Ibu Siti"
                                    >

                                    @error('orangtua_pria')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                            </div>

                        </div>

                    </div>



                    {{-- ====================================================== --}}
                    {{-- MEMPELAI 2 --}}
                    {{-- ====================================================== --}}

                    <div class="col-12">

                        <div class="mempelai-section">

                            <div class="mb-4">

                                <h5 class="section-title">
                                    Mempelai 2
                                    <span class="badge bg-secondary">
                                        Opsional
                                    </span>
                                </h5>

                                <small class="text-muted">
                                    Tidak perlu diisi jika undangan hanya menggunakan satu mempelai.
                                </small>

                            </div>


                            <div class="row g-3">


                                {{-- Nama Panggilan --}}
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Nama Panggilan Mempelai 2
                                    </label>

                                    <input
                                        type="text"
                                        name="nama_panggilan_wanita"
                                        value="{{ old('nama_panggilan_wanita', $heroInvitation->nama_panggilan_wanita ?? '') }}"
                                        class="form-control @error('nama_panggilan_wanita') is-invalid @enderror"
                                        placeholder="Contoh: Nova"
                                    >

                                    @error('nama_panggilan_wanita')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                                {{-- Nama Lengkap --}}
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Nama Lengkap Mempelai 2
                                    </label>

                                    <input
                                        type="text"
                                        name="nama_lengkap_wanita"
                                        value="{{ old('nama_lengkap_wanita', $heroInvitation->nama_lengkap_wanita ?? '') }}"
                                        class="form-control @error('nama_lengkap_wanita') is-invalid @enderror"
                                        placeholder="Contoh: Nova Suciati"
                                    >

                                    @error('nama_lengkap_wanita')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                                {{-- Foto --}}
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Foto Mempelai 2
                                    </label>

                                    <input
                                        type="file"
                                        id="fotoWanitaInput"
                                        accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                        class="form-control"
                                    >

                                    <small class="text-muted">
                                        Opsional. Format JPG, JPEG, atau PNG.
                                    </small>


                                    {{-- Hasil crop --}}
                                    <input
                                        type="hidden"
                                        name="foto_wanita_cropped"
                                        id="fotoWanitaCropped"
                                    >


                                    <div class="mt-3">

                                        @if(!empty($heroInvitation->foto_wanita))

                                            <img
                                                src="{{ asset('storage/'.$heroInvitation->foto_wanita) }}"
                                                id="previewWanita"
                                                class="foto-preview"
                                                alt="Foto Mempelai 2"
                                            >

                                        @else

                                            <img
                                                id="previewWanita"
                                                class="foto-preview"
                                                alt="Foto Mempelai 2"
                                                style="display:none;"
                                            >

                                        @endif

                                    </div>

                                </div>


                                {{-- Orang Tua --}}
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Orang Tua Mempelai 2
                                    </label>

                                    <input
                                        type="text"
                                        name="orangtua_wanita"
                                        value="{{ old('orangtua_wanita', $heroInvitation->orangtua_wanita ?? '') }}"
                                        class="form-control @error('orangtua_wanita') is-invalid @enderror"
                                        placeholder="Contoh: Bapak Adi & Ibu Yuli"
                                    >

                                    @error('orangtua_wanita')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                            </div>

                        </div>

                    </div>


                </div>


                {{-- ====================== --}}
                {{-- SIMPAN --}}
                {{-- ====================== --}}

                <div class="mt-4">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Simpan
                    </button>

                </div>


            </form>

        </div>

    </div>

</div>



{{-- ====================================================== --}}
{{-- MODAL CROPPER --}}
{{-- ====================================================== --}}

<div
    class="modal fade"
    id="cropModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">


            <div class="modal-header">

                <h5 class="modal-title">
                    Atur Foto
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>


            <div class="modal-body">

                <div class="crop-image-container">

                    <img
                        id="cropImage"
                        src=""
                        alt="Crop"
                    >

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    id="zoomOut"
                >
                    Zoom -
                </button>


                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    id="zoomIn"
                >
                    Zoom +
                </button>


                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    id="resetCrop"
                >
                    Reset
                </button>


                <button
                    type="button"
                    class="btn btn-primary"
                    id="gunakanFoto"
                >
                    Gunakan Foto
                </button>

            </div>


        </div>

    </div>

</div>



{{-- Bootstrap --}}
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
</script>


{{-- Cropper --}}
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js">
</script>


<script>

    let cropper = null;
    let activeTarget = null;


    const cropModalElement =
        document.getElementById('cropModal');

    const cropModal =
        new bootstrap.Modal(cropModalElement);


    const cropImage =
        document.getElementById('cropImage');


    const fotoPriaInput =
        document.getElementById('fotoPriaInput');

    const fotoWanitaInput =
        document.getElementById('fotoWanitaInput');


    const fotoPriaCropped =
        document.getElementById('fotoPriaCropped');

    const fotoWanitaCropped =
        document.getElementById('fotoWanitaCropped');


    const previewPria =
        document.getElementById('previewPria');

    const previewWanita =
        document.getElementById('previewWanita');



    function validateImage(file) {

        const allowedTypes = [
            'image/jpeg',
            'image/png'
        ];

        if (!allowedTypes.includes(file.type)) {

            alert(
                'Format foto harus JPG, JPEG, atau PNG.'
            );

            return false;
        }


        const maxSize =
            10 * 1024 * 1024;


        if (file.size > maxSize) {

            alert(
                'Ukuran foto maksimal 10 MB.'
            );

            return false;
        }


        return true;
    }



    function bukaCropper(file, target) {

        if (!file) {
            return;
        }


        if (!validateImage(file)) {
            return;
        }


        activeTarget = target;


        const reader =
            new FileReader();


        reader.onload = function(event) {

            cropImage.src =
                event.target.result;

            cropModal.show();

        };


        reader.readAsDataURL(file);

    }



    fotoPriaInput.addEventListener(
        'change',
        function() {

            const file =
                this.files[0];


            if (!file) {
                return;
            }


            if (!validateImage(file)) {

                this.value = '';

                return;
            }


            bukaCropper(
                file,
                'pria'
            );

        }
    );



    fotoWanitaInput.addEventListener(
        'change',
        function() {

            const file =
                this.files[0];


            if (!file) {
                return;
            }


            if (!validateImage(file)) {

                this.value = '';

                return;
            }


            bukaCropper(
                file,
                'wanita'
            );

        }
    );



    cropModalElement.addEventListener(
        'shown.bs.modal',
        function() {

            if (cropper) {

                cropper.destroy();

                cropper = null;

            }


            cropper =
                new Cropper(
                    cropImage,
                    {

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

                    }
                );

        }
    );



    document
        .getElementById('zoomIn')
        .addEventListener(
            'click',
            function() {

                if (cropper) {

                    cropper.zoom(0.1);

                }

            }
        );



    document
        .getElementById('zoomOut')
        .addEventListener(
            'click',
            function() {

                if (cropper) {

                    cropper.zoom(-0.1);

                }

            }
        );



    document
        .getElementById('resetCrop')
        .addEventListener(
            'click',
            function() {

                if (cropper) {

                    cropper.reset();

                }

            }
        );



    document
        .getElementById('gunakanFoto')
        .addEventListener(
            'click',
            function() {

                if (!cropper) {
                    return;
                }


                const canvas =
                    cropper.getCroppedCanvas({

                        width: 800,

                        height: 800,

                        imageSmoothingEnabled: true,

                        imageSmoothingQuality: 'high'

                    });


                const result =
                    canvas.toDataURL(
                        'image/jpeg',
                        0.90
                    );


                if (
                    activeTarget === 'pria'
                ) {

                    fotoPriaCropped.value =
                        result;


                    previewPria.src =
                        result;


                    previewPria.style.display =
                        'block';

                }



                if (
                    activeTarget === 'wanita'
                ) {

                    fotoWanitaCropped.value =
                        result;


                    previewWanita.src =
                        result;


                    previewWanita.style.display =
                        'block';

                }


                cropModal.hide();

            }
        );



    cropModalElement.addEventListener(
        'hidden.bs.modal',
        function() {

            if (cropper) {

                cropper.destroy();

                cropper = null;

            }

        }
    );

</script>

</body>
</html>