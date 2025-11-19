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

        <form action="{{ route('LoveStory.store', $slug_id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- ======================
                TEXT AREA
            ======================= --}}
            <div class="card p-3 mb-3">
                <h6 class="fw-bold">📘 Cerita</h6>

                <div class="mb-3">
                    <label class="fw-bold">Awal Pertemuan</label>
                    <textarea name="awal_pertemuan" class="form-control" rows="3"
                              placeholder="Ceritakan awal pertemuan...">{{ $lovestory->awal_pertemuan ?? '' }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Menjalin Hubungan</label>
                    <textarea name="menjalin_hubungan" class="form-control" rows="3"
                              placeholder="Ceritakan perjalanan hubungan...">{{ $lovestory->menjalin_hubungan ?? '' }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Cerita Lamaran</label>
                    <textarea name="lamaran" class="form-control" rows="3"
                              placeholder="Ceritakan momen lamaran...">{{ $lovestory->lamaran ?? '' }}</textarea>
                </div>
            </div>

            {{-- ======================
                GAMBAR
            ======================= --}}
            <div class="card p-3">
                <h6 class="fw-bold">📷 Foto</h6>

                {{-- FOTO AWAL --}}
                <div class="mb-3">
                    <label class="fw-bold">Foto Awal Pertemuan</label><br>
                    @if($lovestory && $lovestory->gambar_awal)
                        <img src="{{ asset('storage/'.$lovestory->gambar_awal) }}" width="200" class="img-thumbnail mb-2">
                    @endif
                    <input type="file" name="gambar_awal" class="form-control">
                </div>

                {{-- FOTO HUBUNGAN --}}
                <div class="mb-3">
                    <label class="fw-bold">Foto Menjalin Hubungan</label><br>
                    @if($lovestory && $lovestory->gambar_hubungan)
                        <img src="{{ asset('storage/'.$lovestory->gambar_hubungan) }}" width="200" class="img-thumbnail mb-2">
                    @endif
                    <input type="file" name="gambar_hubungan" class="form-control">
                </div>

                {{-- FOTO LAMARAN --}}
                <div class="mb-3">
                    <label class="fw-bold">Foto Lamaran</label><br>
                    @if($lovestory && $lovestory->gambar_lamaran)
                        <img src="{{ asset('storage/'.$lovestory->gambar_lamaran) }}" width="200" class="img-thumbnail mb-2">
                    @endif
                    <input type="file" name="gambar_lamaran" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">
                {{ $lovestory ? 'Update' : 'Simpan' }}
            </button>

        </form>

    </div>
</div>