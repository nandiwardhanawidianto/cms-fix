<div class="card mt-3">
    <div class="card-header">
        <h5>Galeri</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('galeri.store', $slug_id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">Carousel Atas (max 5)</label>
                <input type="file" name="carousel_atas[]" multiple accept="image/*" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Carousel Bawah (max 5)</label>
                <input type="file" name="carousel_bawah[]" multiple accept="image/*" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>

        @if(!empty($galeri))
            <hr>
            <h6>Preview Carousel Atas</h6>
            <div class="d-flex flex-wrap">
                @foreach(json_decode($galeri->carousel_atas ?? '[]', true) as $img)
                    <img src="{{ asset('storage/'.$img) }}" class="img-thumbnail m-1" width="120">
                @endforeach
            </div>

            <h6 class="mt-3">Preview Carousel Bawah</h6>
            <div class="d-flex flex-wrap">
                @foreach(json_decode($galeri->carousel_bawah ?? '[]', true) as $img)
                    <img src="{{ asset('storage/'.$img) }}" class="img-thumbnail m-1" width="120">
                @endforeach
            </div>
        @endif

    </div>
</div>
