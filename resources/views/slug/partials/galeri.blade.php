<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Galeri</title>

    {{-- Bootstrap --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    {{-- Cropper --}}
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css"
    >

    <style>
        .crop-galeri-container {
            width: 100%;
            max-height: 65vh;
            overflow: hidden;
            background: #111;
        }

        .crop-galeri-container img {
            display: block;
            max-width: 100%;
        }

        .galeri-preview {
            width: 120px;
            height: 120px;
            object-fit: cover;
        }

        .preview-wrapper {
            position: relative;
            margin: 5px;
        }

        .preview-label {
            position: absolute;
            bottom: 5px;
            left: 5px;
            background: rgba(0, 0, 0, 0.65);
            color: white;
            padding: 2px 6px;
            font-size: 11px;
            border-radius: 4px;
        }
    </style>
</head>

<body class="bg-light">

<div class="container py-4">

    <div class="card mt-3 shadow-sm">

        <div class="card-header">
            <h5 class="mb-0">Galeri</h5>
        </div>

        <div class="card-body">

            <form
                action="{{ route('galeri.store', $slug_id) }}"
                method="POST"
                id="formGaleri"
            >
                @csrf


                {{-- ================================================= --}}
                {{-- CAROUSEL ATAS --}}
                {{-- ================================================= --}}

                <div class="mb-4">

                    <label class="form-label">
                        Carousel Atas
                    </label>

                    <input
                        type="file"
                        id="carouselAtasInput"
                        multiple
                        accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                        class="form-control"
                    >

                    <small class="text-muted">
                        JPG, JPEG, atau PNG. Maksimal 5 foto.
                    </small>


                    {{-- HASIL CROP BARU --}}

                    <div
                        id="previewAtasBaru"
                        class="d-flex flex-wrap mt-3"
                    ></div>


                    {{-- BASE64 HASIL CROP --}}

                    <div id="hiddenAtas"></div>

                </div>



                <hr>



                {{-- ================================================= --}}
                {{-- CAROUSEL BAWAH --}}
                {{-- ================================================= --}}

                <div class="mb-4">

                    <label class="form-label">
                        Carousel Bawah
                    </label>

                    <input
                        type="file"
                        id="carouselBawahInput"
                        multiple
                        accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                        class="form-control"
                    >

                    <small class="text-muted">
                        JPG, JPEG, atau PNG. Maksimal 5 foto.
                    </small>


                    {{-- HASIL CROP BARU --}}

                    <div
                        id="previewBawahBaru"
                        class="d-flex flex-wrap mt-3"
                    ></div>


                    {{-- BASE64 HASIL CROP --}}

                    <div id="hiddenBawah"></div>

                </div>



                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Simpan Galeri
                </button>

            </form>



            {{-- ================================================= --}}
            {{-- FOTO YANG SUDAH TERSIMPAN --}}
            {{-- ================================================= --}}

            @if(!empty($galeri))

                <hr class="my-4">

                <h6>
                    Carousel Atas Saat Ini
                </h6>

                <div class="d-flex flex-wrap mb-4">

                    @foreach(
                        json_decode(
                            $galeri->carousel_atas ?? '[]',
                            true
                        ) as $img
                    )

                        <img
                            src="{{ asset('storage/'.$img) }}"
                            class="img-thumbnail m-1 galeri-preview"
                            alt="Carousel Atas"
                        >

                    @endforeach

                </div>



                <h6>
                    Carousel Bawah Saat Ini
                </h6>

                <div class="d-flex flex-wrap">

                    @foreach(
                        json_decode(
                            $galeri->carousel_bawah ?? '[]',
                            true
                        ) as $img
                    )

                        <img
                            src="{{ asset('storage/'.$img) }}"
                            class="img-thumbnail m-1 galeri-preview"
                            alt="Carousel Bawah"
                        >

                    @endforeach

                </div>

            @endif

        </div>

    </div>

</div>



{{-- ================================================= --}}
{{-- MODAL CROPPER --}}
{{-- ================================================= --}}

<div
    class="modal fade"
    id="cropGaleriModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div
        class="modal-dialog modal-lg modal-dialog-centered"
    >

        <div class="modal-content">


            {{-- HEADER --}}

            <div class="modal-header">

                <div>

                    <h5 class="modal-title mb-0">
                        Atur Foto Galeri
                    </h5>

                    <small
                        id="cropCounter"
                        class="text-muted"
                    ></small>

                </div>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>



            {{-- BODY --}}

            <div class="modal-body">


                {{-- PILIH RASIO --}}

                <div class="mb-3">

                    <label class="form-label d-block">
                        Rasio Crop
                    </label>


                    <div
                        class="btn-group"
                        role="group"
                    >

                        <button
                            type="button"
                            class="btn btn-outline-primary crop-ratio active"
                            data-ratio="1"
                        >
                            1:1
                        </button>


                        <button
                            type="button"
                            class="btn btn-outline-primary crop-ratio"
                            data-ratio="0.8"
                        >
                            4:5
                        </button>


                        <button
                            type="button"
                            class="btn btn-outline-primary crop-ratio"
                            data-ratio="free"
                        >
                            Bebas
                        </button>

                    </div>

                </div>



                {{-- FOTO --}}

                <div class="crop-galeri-container">

                    <img
                        id="cropGaleriImage"
                        alt="Crop Galeri"
                    >

                </div>

            </div>



            {{-- FOOTER --}}

            <div class="modal-footer">

                <button
                    type="button"
                    id="galeriZoomOut"
                    class="btn btn-outline-secondary"
                >
                    Zoom -
                </button>


                <button
                    type="button"
                    id="galeriZoomIn"
                    class="btn btn-outline-secondary"
                >
                    Zoom +
                </button>


                <button
                    type="button"
                    id="galeriReset"
                    class="btn btn-outline-secondary"
                >
                    Reset
                </button>


                <button
                    type="button"
                    id="galeriGunakan"
                    class="btn btn-primary"
                >
                    Gunakan Foto
                </button>

            </div>

        </div>

    </div>

</div>



{{-- ================================================= --}}
{{-- SCRIPT --}}
{{-- ================================================= --}}

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
</script>

<script
    src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js">
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {

    let cropper = null;

    let filesAktif = [];
    let indexAktif = 0;
    let targetAktif = null;

    let hasilAtas = [];
    let hasilBawah = [];

    let ratioAktif = 1;


    const inputAtas =
        document.getElementById('carouselAtasInput');

    const inputBawah =
        document.getElementById('carouselBawahInput');


    const modalElement =
        document.getElementById('cropGaleriModal');

    const modal =
        new bootstrap.Modal(modalElement);


    const cropImage =
        document.getElementById('cropGaleriImage');


    const counter =
        document.getElementById('cropCounter');


    const buttonGunakan =
        document.getElementById('galeriGunakan');



    /*
    |--------------------------------------------------------------------------
    | INPUT ATAS
    |--------------------------------------------------------------------------
    */

    inputAtas.addEventListener(
        'change',
        function () {

            prosesFile(
                Array.from(this.files),
                'atas'
            );

        }
    );



    /*
    |--------------------------------------------------------------------------
    | INPUT BAWAH
    |--------------------------------------------------------------------------
    */

    inputBawah.addEventListener(
        'change',
        function () {

            prosesFile(
                Array.from(this.files),
                'bawah'
            );

        }
    );



    /*
    |--------------------------------------------------------------------------
    | VALIDASI + MULAI
    |--------------------------------------------------------------------------
    */

    function prosesFile(files, target) {

        if (!files.length) {
            return;
        }


        if (files.length > 5) {

            alert(
                'Maksimal 5 foto.'
            );

            resetInput(target);

            return;
        }


        const allowedTypes = [
            'image/jpeg',
            'image/png'
        ];


        const maxSize =
            10 * 1024 * 1024;


        for (const file of files) {

            if (
                !allowedTypes.includes(
                    file.type
                )
            ) {

                alert(
                    'Format harus JPG, JPEG, atau PNG.'
                );

                resetInput(target);

                return;
            }


            if (
                file.size >
                maxSize
            ) {

                alert(
                    'Ukuran maksimal setiap foto adalah 10 MB.'
                );

                resetInput(target);

                return;
            }

        }


        filesAktif =
            files;

        indexAktif =
            0;

        targetAktif =
            target;


        /*
        | Reset hasil lama yang belum disimpan.
        */

        if (
            target ===
            'atas'
        ) {

            hasilAtas = [];

            document
                .getElementById(
                    'previewAtasBaru'
                )
                .innerHTML = '';

            document
                .getElementById(
                    'hiddenAtas'
                )
                .innerHTML = '';

        } else {

            hasilBawah = [];

            document
                .getElementById(
                    'previewBawahBaru'
                )
                .innerHTML = '';

            document
                .getElementById(
                    'hiddenBawah'
                )
                .innerHTML = '';

        }


        resetRatio();

        tampilkanFoto();

    }



    /*
    |--------------------------------------------------------------------------
    | RESET INPUT
    |--------------------------------------------------------------------------
    */

    function resetInput(target) {

        if (
            target ===
            'atas'
        ) {

            inputAtas.value =
                '';

        } else {

            inputBawah.value =
                '';

        }

    }



    /*
    |--------------------------------------------------------------------------
    | TAMPILKAN FOTO AKTIF
    |--------------------------------------------------------------------------
    */

    function tampilkanFoto() {

        const file =
            filesAktif[indexAktif];


        if (!file) {
            return;
        }


        const reader =
            new FileReader();


        reader.onload =
            function (event) {

                if (cropper) {

                    cropper.destroy();

                    cropper =
                        null;

                }


                cropImage.src =
                    event.target.result;


                counter.textContent =
                    'Foto ' +
                    (indexAktif + 1) +
                    ' dari ' +
                    filesAktif.length;


                if (
                    indexAktif ===
                    filesAktif.length - 1
                ) {

                    buttonGunakan.textContent =
                        'Gunakan Foto';

                } else {

                    buttonGunakan.textContent =
                        'Gunakan & Lanjut';

                }


                modal.show();

            };


        reader.readAsDataURL(file);

    }



    /*
    |--------------------------------------------------------------------------
    | BUAT CROPPER
    |--------------------------------------------------------------------------
    */

    modalElement.addEventListener(
        'shown.bs.modal',
        function () {

            buatCropper();

        }
    );


    function buatCropper() {

        if (cropper) {

            cropper.destroy();

            cropper =
                null;

        }


        cropper =
            new Cropper(
                cropImage,
                {

                    aspectRatio:
                        ratioAktif,

                    viewMode:
                        1,

                    dragMode:
                        'move',

                    autoCropArea:
                        0.9,

                    responsive:
                        true,

                    background:
                        false,

                    movable:
                        true,

                    zoomable:
                        true,

                    rotatable:
                        false,

                    scalable:
                        false,

                    guides:
                        true,

                    center:
                        true,

                    highlight:
                        true

                }
            );

    }



    /*
    |--------------------------------------------------------------------------
    | RASIO
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll(
            '.crop-ratio'
        )
        .forEach(
            function (button) {

                button.addEventListener(
                    'click',
                    function () {

                        document
                            .querySelectorAll(
                                '.crop-ratio'
                            )
                            .forEach(
                                function (btn) {

                                    btn.classList
                                        .remove(
                                            'active'
                                        );

                                }
                            );


                        this.classList
                            .add(
                                'active'
                            );


                        const value =
                            this.dataset.ratio;


                        if (
                            value ===
                            'free'
                        ) {

                            ratioAktif =
                                NaN;

                        } else {

                            ratioAktif =
                                parseFloat(
                                    value
                                );

                        }


                        if (cropper) {

                            cropper.setAspectRatio(
                                ratioAktif
                            );

                        }

                    }
                );

            }
        );



    /*
    |--------------------------------------------------------------------------
    | RESET RASIO KE 1:1
    |--------------------------------------------------------------------------
    */

    function resetRatio() {

        ratioAktif =
            1;


        document
            .querySelectorAll(
                '.crop-ratio'
            )
            .forEach(
                function (btn) {

                    btn.classList
                        .remove(
                            'active'
                        );

                }
            );


        const defaultButton =
            document.querySelector(
                '.crop-ratio[data-ratio="1"]'
            );


        if (defaultButton) {

            defaultButton.classList
                .add(
                    'active'
                );

        }

    }



    /*
    |--------------------------------------------------------------------------
    | ZOOM
    |--------------------------------------------------------------------------
    */

    document
        .getElementById(
            'galeriZoomIn'
        )
        .addEventListener(
            'click',
            function () {

                if (cropper) {

                    cropper.zoom(
                        0.1
                    );

                }

            }
        );


    document
        .getElementById(
            'galeriZoomOut'
        )
        .addEventListener(
            'click',
            function () {

                if (cropper) {

                    cropper.zoom(
                        -0.1
                    );

                }

            }
        );



    /*
    |--------------------------------------------------------------------------
    | RESET CROPPER
    |--------------------------------------------------------------------------
    */

    document
        .getElementById(
            'galeriReset'
        )
        .addEventListener(
            'click',
            function () {

                if (!cropper) {
                    return;
                }


                cropper.reset();


                cropper.setAspectRatio(
                    ratioAktif
                );

            }
        );



    /*
    |--------------------------------------------------------------------------
    | GUNAKAN FOTO
    |--------------------------------------------------------------------------
    */

    buttonGunakan.addEventListener(
        'click',
        function () {

            if (!cropper) {
                return;
            }


            /*
            | Tidak paksa width / height.
            | Jadi mode Bebas tetap mempertahankan
            | rasio yang dipilih admin.
            */

            const canvas =
                cropper.getCroppedCanvas({

                    maxWidth:
                        1200,

                    maxHeight:
                        1200,

                    imageSmoothingEnabled:
                        true,

                    imageSmoothingQuality:
                        'high'

                });


            if (!canvas) {

                alert(
                    'Gagal memproses foto.'
                );

                return;
            }


            /*
            | Output JPEG supaya lebih ringan.
            */

            const result =
                canvas.toDataURL(
                    'image/jpeg',
                    0.88
                );


            if (
                targetAktif ===
                'atas'
            ) {

                hasilAtas.push(
                    result
                );

            } else {

                hasilBawah.push(
                    result
                );

            }


            cropper.destroy();

            cropper =
                null;


            indexAktif++;


            /*
            | Masih ada foto berikutnya.
            */

            if (
                indexAktif <
                filesAktif.length
            ) {

                resetRatio();

                tampilkanFotoBerikutnya();

            } else {

                modal.hide();

                renderPreview(
                    targetAktif
                );

            }

        }
    );



    /*
    |--------------------------------------------------------------------------
    | FOTO BERIKUTNYA
    |--------------------------------------------------------------------------
    */

    function tampilkanFotoBerikutnya() {

        const file =
            filesAktif[indexAktif];


        const reader =
            new FileReader();


        reader.onload =
            function (event) {

                cropImage.src =
                    event.target.result;


                counter.textContent =
                    'Foto ' +
                    (indexAktif + 1) +
                    ' dari ' +
                    filesAktif.length;


                if (
                    indexAktif ===
                    filesAktif.length - 1
                ) {

                    buttonGunakan.textContent =
                        'Gunakan Foto';

                } else {

                    buttonGunakan.textContent =
                        'Gunakan & Lanjut';

                }


                cropImage.onload =
                    function () {

                        buatCropper();

                    };

            };


        reader.readAsDataURL(file);

    }



    /*
    |--------------------------------------------------------------------------
    | RENDER PREVIEW
    |--------------------------------------------------------------------------
    */

    function renderPreview(target) {

        let hasil;
        let previewContainer;
        let hiddenContainer;
        let inputName;


        if (
            target ===
            'atas'
        ) {

            hasil =
                hasilAtas;

            previewContainer =
                document.getElementById(
                    'previewAtasBaru'
                );

            hiddenContainer =
                document.getElementById(
                    'hiddenAtas'
                );

            inputName =
                'carousel_atas_cropped[]';

        } else {

            hasil =
                hasilBawah;

            previewContainer =
                document.getElementById(
                    'previewBawahBaru'
                );

            hiddenContainer =
                document.getElementById(
                    'hiddenBawah'
                );

            inputName =
                'carousel_bawah_cropped[]';

        }


        previewContainer.innerHTML =
            '';

        hiddenContainer.innerHTML =
            '';


        hasil.forEach(
            function (
                image,
                index
            ) {

                /*
                | PREVIEW
                */

                const wrapper =
                    document.createElement(
                        'div'
                    );


                wrapper.className =
                    'preview-wrapper';


                const img =
                    document.createElement(
                        'img'
                    );


                img.src =
                    image;


                img.className =
                    'img-thumbnail galeri-preview';


                const label =
                    document.createElement(
                        'span'
                    );


                label.className =
                    'preview-label';


                label.textContent =
                    'Foto ' +
                    (index + 1);


                wrapper.appendChild(
                    img
                );


                wrapper.appendChild(
                    label
                );


                previewContainer
                    .appendChild(
                        wrapper
                    );



                /*
                | HIDDEN BASE64
                */

                const hidden =
                    document.createElement(
                        'input'
                    );


                hidden.type =
                    'hidden';


                hidden.name =
                    inputName;


                hidden.value =
                    image;


                hiddenContainer
                    .appendChild(
                        hidden
                    );

            }
        );

    }



    /*
    |--------------------------------------------------------------------------
    | CLEANUP
    |--------------------------------------------------------------------------
    */

    modalElement.addEventListener(
        'hidden.bs.modal',
        function () {

            if (cropper) {

                cropper.destroy();

                cropper =
                    null;

            }

        }
    );

});
</script>

</body>
</html>
