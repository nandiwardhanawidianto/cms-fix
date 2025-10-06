<div class="container mt-4">
    <h4>📅 Form Acara</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('acara.store', $slug_id) }}" method="POST">
        @csrf

        <div id="acara-wrapper">
            @php $count = max(1, $acaras->count()); @endphp

            @for($i = 0; $i < $count; $i++)
                <div class="card mb-3 p-3 acara-item">
                    <div class="mb-2">
                        <label>Nama Acara</label>
                        <input type="text" name="nama_acara[]" class="form-control"
                               value="{{ $acaras[$i]->nama_acara ?? '' }}" required>
                    </div>
                    <div class="mb-2">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal_acara[]" class="form-control"
                               value="{{ $acaras[$i]->tanggal_acara ?? '' }}" required>
                    </div>
                    <div class="mb-2">
                        <label>Pukul</label>
                        <input type="text" name="pukul_acara[]" class="form-control"
                               value="{{ $acaras[$i]->pukul_acara ?? '' }}" required>
                    </div>
                    <div class="mb-2">
                        <label>Alamat</label>
                        <textarea name="alamat_acara[]" class="form-control" required>{{ $acaras[$i]->alamat_acara ?? '' }}</textarea>
                    </div>
                    <div class="mb-2">
                        <label>Link Maps</label>
                        <input type="text" name="link_acara[]" class="form-control"
                               value="{{ $acaras[$i]->link_acara ?? '' }}" required>
                    </div>
                </div>
            @endfor
        </div>

        <button type="button" id="btnAdd" class="btn btn-sm btn-secondary">+ Tambah Acara</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>

<script>
let maxAcara = 3;
let acaraWrapper = document.getElementById('acara-wrapper');
let btnAdd = document.getElementById('btnAdd');

btnAdd.addEventListener('click', function () {
    let acaraCount = acaraWrapper.querySelectorAll('.acara-item').length;

    if (acaraCount >= maxAcara) {
        alert("Maksimal hanya 3 acara!");
        return;
    }

    let newAcara = document.createElement('div');
    newAcara.classList.add('card', 'mb-3', 'p-3', 'acara-item');
    newAcara.innerHTML = `
        <div class="mb-2">
            <label>Nama Acara</label>
            <input type="text" name="nama_acara[]" class="form-control" required>
        </div>
        <div class="mb-2">
            <label>Tanggal</label>
            <input type="date" name="tanggal_acara[]" class="form-control" required>
        </div>
        <div class="mb-2">
            <label>Pukul</label>
            <input type="text" name="pukul_acara[]" class="form-control" required>
        </div>
        <div class="mb-2">
            <label>Alamat</label>
            <textarea name="alamat_acara[]" class="form-control" required></textarea>
        </div>
        <div class="mb-2">
            <label>Link Maps</label>
            <input type="text" name="link_acara[]" class="form-control" required>
        </div>
    `;

    acaraWrapper.appendChild(newAcara);
});
</script>
