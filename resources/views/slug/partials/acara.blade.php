<div class="container mt-4">
    <h4>📅 Form Acara</h4>

    <form action="{{ route('acara.store', $slug_id) }}" method="POST">
        @csrf

        <div id="acara-wrapper">

            @php
                $count = max(1, $acaras->count());
            @endphp

            @for($i = 0; $i < $count; $i++)

                <div class="card mb-3 p-3 acara-item">

                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <h6 class="mb-0 acara-title">
                            Acara {{ $i + 1 }}
                        </h6>

                        <button
                            type="button"
                            class="btn btn-sm btn-danger btn-hapus-acara"
                        >
                            Hapus
                        </button>

                    </div>


                    <div class="mb-2">

                        <label class="form-label">
                            Nama Acara
                        </label>

                        <input
                            type="text"
                            name="nama_acara[]"
                            class="form-control"
                            value="{{ $acaras[$i]->nama_acara ?? '' }}"
                            required
                        >

                    </div>


                    <div class="mb-2">

                        <label class="form-label">
                            Tanggal
                        </label>

                        <input
                            type="date"
                            name="tanggal_acara[]"
                            class="form-control"
                            value="{{ $acaras[$i]->tanggal_acara ?? '' }}"
                            required
                        >

                    </div>


                    <div class="mb-2">

                        <label class="form-label">
                            Pukul
                        </label>

                        <input
                            type="text"
                            name="pukul_acara[]"
                            class="form-control"
                            value="{{ $acaras[$i]->pukul_acara ?? '' }}"
                            placeholder="Contoh: 09.00 - Selesai"
                            required
                        >

                    </div>


                    <div class="mb-2">

                        <label class="form-label">
                            Alamat
                        </label>

                        <textarea
                            name="alamat_acara[]"
                            class="form-control"
                            rows="3"
                            required
                        >{{ $acaras[$i]->alamat_acara ?? '' }}</textarea>

                    </div>


                    <div class="mb-2">

                        <label class="form-label">
                            Link Maps
                            <span class="text-muted">
                                (Opsional)
                            </span>
                        </label>

                        <input
                            type="text"
                            name="link_acara[]"
                            class="form-control"
                            value="{{ $acaras[$i]->link_acara ?? '' }}"
                            placeholder="https://maps.app.goo.gl/..."
                        >

                    </div>

                </div>

            @endfor

        </div>


        <button
            type="button"
            id="btnAdd"
            class="btn btn-sm btn-secondary"
        >
            + Tambah Acara
        </button>


        <button
            type="submit"
            class="btn btn-primary"
        >
            Simpan
        </button>

    </form>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const maxAcara = 3;

    const acaraWrapper =
        document.getElementById('acara-wrapper');

    const btnAdd =
        document.getElementById('btnAdd');


    /*
    |--------------------------------------------------------------------------
    | UPDATE NOMOR ACARA
    |--------------------------------------------------------------------------
    */

    function updateNomorAcara() {

        const items =
            acaraWrapper.querySelectorAll('.acara-item');


        items.forEach(function(item, index) {

            const title =
                item.querySelector('.acara-title');

            if (title) {

                title.textContent =
                    'Acara ' + (index + 1);

            }

        });


        /*
        | Kalau cuma tersisa 1 acara,
        | tombol hapus kita disable.
        */

        const deleteButtons =
            acaraWrapper.querySelectorAll('.btn-hapus-acara');


        deleteButtons.forEach(function(button) {

            button.disabled =
                items.length <= 1;

        });


        /*
        | Disable tambah jika sudah 3.
        */

        btnAdd.disabled =
            items.length >= maxAcara;

    }



    /*
    |--------------------------------------------------------------------------
    | TAMBAH ACARA
    |--------------------------------------------------------------------------
    */

    btnAdd.addEventListener('click', function () {

        const acaraCount =
            acaraWrapper
                .querySelectorAll('.acara-item')
                .length;


        if (acaraCount >= maxAcara) {

            alert(
                'Maksimal hanya 3 acara!'
            );

            return;

        }


        const newAcara =
            document.createElement('div');


        newAcara.classList.add(
            'card',
            'mb-3',
            'p-3',
            'acara-item'
        );


        newAcara.innerHTML = `

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h6 class="mb-0 acara-title">
                    Acara ${acaraCount + 1}
                </h6>

                <button
                    type="button"
                    class="btn btn-sm btn-danger btn-hapus-acara"
                >
                    Hapus
                </button>

            </div>


            <div class="mb-2">

                <label class="form-label">
                    Nama Acara
                </label>

                <input
                    type="text"
                    name="nama_acara[]"
                    class="form-control"
                    required
                >

            </div>


            <div class="mb-2">

                <label class="form-label">
                    Tanggal
                </label>

                <input
                    type="date"
                    name="tanggal_acara[]"
                    class="form-control"
                    required
                >

            </div>


            <div class="mb-2">

                <label class="form-label">
                    Pukul
                </label>

                <input
                    type="text"
                    name="pukul_acara[]"
                    class="form-control"
                    placeholder="Contoh: 09.00 - Selesai"
                    required
                >

            </div>


            <div class="mb-2">

                <label class="form-label">
                    Alamat
                </label>

                <textarea
                    name="alamat_acara[]"
                    class="form-control"
                    rows="3"
                    required
                ></textarea>

            </div>


            <div class="mb-2">

                <label class="form-label">
                    Link Maps
                    <span class="text-muted">
                        (Opsional)
                    </span>
                </label>

                <input
                    type="text"
                    name="link_acara[]"
                    class="form-control"
                    placeholder="https://maps.app.goo.gl/..."
                >

            </div>

        `;


        acaraWrapper.appendChild(
            newAcara
        );


        updateNomorAcara();

    });



    /*
    |--------------------------------------------------------------------------
    | HAPUS ACARA
    |--------------------------------------------------------------------------
    |
    | Event delegation supaya acara yang baru
    | ditambahkan juga bisa dihapus.
    |
    */

    acaraWrapper.addEventListener(
        'click',
        function(event) {

            const deleteButton =
                event.target.closest(
                    '.btn-hapus-acara'
                );


            if (!deleteButton) {
                return;
            }


            const items =
                acaraWrapper
                    .querySelectorAll(
                        '.acara-item'
                    );


            if (items.length <= 1) {

                alert(
                    'Minimal harus ada 1 acara.'
                );

                return;

            }


            if (
                !confirm(
                    'Yakin ingin menghapus acara ini?'
                )
            ) {

                return;

            }


            const item =
                deleteButton.closest(
                    '.acara-item'
                );


            if (item) {

                item.remove();

            }


            updateNomorAcara();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | INITIAL
    |--------------------------------------------------------------------------
    */

    updateNomorAcara();

});
</script>

