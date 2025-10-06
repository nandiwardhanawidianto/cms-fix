<div class="card shadow-sm">
    <div class="card-header">Hero & Invitation</div>
    <div class="card-body">
        <form action="{{ route('hero.store', $slug->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Panggilan Pria</label>
                    <input type="text" name="nama_panggilan_pria" value="{{ $heroInvitation->nama_panggilan_pria ?? '' }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap Pria</label>
                    <input type="text" name="nama_lengkap_pria" value="{{ $heroInvitation->nama_lengkap_pria ?? '' }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Foto Pria</label>
                    <input type="file" name="foto_pria" class="form-control">
                    @if(!empty($heroInvitation->foto_pria))
                        <img src="{{ asset('storage/'.$heroInvitation->foto_pria) }}" class="mt-2" height="100">
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">Orang Tua Pria</label>
                    <input type="text" name="orangtua_pria" value="{{ $heroInvitation->orangtua_pria ?? '' }}" class="form-control">
                </div>
                <hr>
                <div class="col-md-6">
                    <label class="form-label">Nama Panggilan Wanita</label>
                    <input type="text" name="nama_panggilan_wanita" value="{{ $heroInvitation->nama_panggilan_wanita ?? '' }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap Wanita</label>
                    <input type="text" name="nama_lengkap_wanita" value="{{ $heroInvitation->nama_lengkap_wanita ?? '' }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Foto Wanita</label>
                    <input type="file" name="foto_wanita" class="form-control">
                    @if(!empty($heroInvitation->foto_wanita))
                        <img src="{{ asset('storage/'.$heroInvitation->foto_wanita) }}" class="mt-2" height="100">
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">Orang Tua Wanita</label>
                    <input type="text" name="orangtua_wanita" value="{{ $heroInvitation->orangtua_wanita ?? '' }}" class="form-control">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">💾 Simpan</button>
            </div>
        </form>
    </div>
</div>
