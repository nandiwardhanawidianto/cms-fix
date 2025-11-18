<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>🎁 Kirim Kado</h5>

        @if($kirimkado)
            <form action="{{ route('kirimkado.delete', $slug_id) }}" method="POST"
                  onsubmit="return confirm('Yakin hapus data Kirim Kado?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger">Hapus</button>
            </form>
        @endif
    </div>

    <div class="card-body">

        <form action="{{ route('kirimkado.store', $slug_id) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>Nama Penerima Kado</label>
                <input type="text"
                       name="nama_penerima"
                       class="form-control"
                       value="{{ $kirimkado->nama_penerima ?? '' }}"
                       placeholder="contoh: Andi Pratama">
            </div>

            <div class="mb-3">
                <label>No HP Penerima</label>
                <input type="text"
                       name="no_hp_penerima"
                       class="form-control"
                       value="{{ $kirimkado->no_hp_penerima ?? '' }}"
                       placeholder="contoh: 08123456789">
            </div>

            <div class="mb-3">
                <label>Alamat Kirim Kado</label>
                <textarea name="alamat_penerima"
                          class="form-control"
                          rows="4"
                          placeholder="contoh: Jl. Melati No. 22, Bandung">{{ $kirimkado->alamat_penerima ?? '' }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                {{ $kirimkado ? 'Update' : 'Simpan' }}
            </button>
        </form>

    </div>
</div>
