@php
    $savedCarouselAtas = !empty($galeri?->carousel_atas)
        ? json_decode($galeri->carousel_atas, true)
        : [];

    $savedCarouselBawah = !empty($galeri?->carousel_bawah)
        ? json_decode($galeri->carousel_bawah, true)
        : [];

    $savedCarouselAtas = is_array($savedCarouselAtas) ? $savedCarouselAtas : [];
    $savedCarouselBawah = is_array($savedCarouselBawah) ? $savedCarouselBawah : [];
@endphp

@if(count($savedCarouselAtas) || count($savedCarouselBawah))
    <div class="card mt-3 shadow-sm">
        <div class="card-header">
            <h6 class="mb-0">Hapus Foto Galeri Satu per Satu</h6>
        </div>

        <div class="card-body">
            @if(count($savedCarouselAtas))
                <h6>Carousel Atas</h6>

                <div class="d-flex flex-wrap gap-2 mb-4">
                    @foreach($savedCarouselAtas as $index => $img)
                        <div class="border rounded p-2 text-center bg-white" style="width: 150px;">
                            <img
                                src="{{ asset('storage/' . $img) }}"
                                alt="Carousel Atas Foto {{ $index + 1 }}"
                                class="img-thumbnail mb-2"
                                style="width: 130px; height: 130px; object-fit: cover;"
                            >

                            <div class="small text-muted mb-2">
                                Foto {{ $index + 1 }}
                            </div>

                            <form
                                action="{{ route('galeri.photo.destroy', $slug_id) }}"
                                method="POST"
                                onsubmit="return confirm('Hapus Foto {{ $index + 1 }} dari Carousel Atas?');"
                            >
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="carousel" value="atas">
                                <input type="hidden" name="image_path" value="{{ $img }}">

                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                    Hapus Foto Ini
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif

            @if(count($savedCarouselBawah))
                <h6>Carousel Bawah</h6>

                <div class="d-flex flex-wrap gap-2">
                    @foreach($savedCarouselBawah as $index => $img)
                        <div class="border rounded p-2 text-center bg-white" style="width: 150px;">
                            <img
                                src="{{ asset('storage/' . $img) }}"
                                alt="Carousel Bawah Foto {{ $index + 1 }}"
                                class="img-thumbnail mb-2"
                                style="width: 130px; height: 130px; object-fit: cover;"
                            >

                            <div class="small text-muted mb-2">
                                Foto {{ $index + 1 }}
                            </div>

                            <form
                                action="{{ route('galeri.photo.destroy', $slug_id) }}"
                                method="POST"
                                onsubmit="return confirm('Hapus Foto {{ $index + 1 }} dari Carousel Bawah?');"
                            >
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="carousel" value="bawah">
                                <input type="hidden" name="image_path" value="{{ $img }}">

                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                    Hapus Foto Ini
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif
