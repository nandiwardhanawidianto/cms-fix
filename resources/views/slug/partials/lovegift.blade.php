<div class="card mt-3">
    <div class="card-header">
        <h5>💝 Love Gift</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('lovegift.store', $slug_id) }}" method="POST">
            @csrf

            <div id="bank-wrapper">
                @php
                    $count = isset($love_gifts) && count($love_gifts) > 0 ? count($love_gifts) : 1;
                @endphp
                @for($i = 0; $i < $count; $i++)
                    @php $gift = $love_gifts[$i] ?? null; @endphp
                    <div class="card mb-3 p-3 bank-item">
                        <h6>Bank {{ $i + 1 }}</h6>

                        <div class="mb-2">
                            <label>Nama Bank</label>
                            <select name="bank_id[]" class="form-control" required>
                                <option value="">-- Pilih Bank --</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}"
                                        {{ $gift && $gift->bank_id == $bank->id ? 'selected' : '' }}>
                                        {{ $bank->nama_bank }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-2">
                            <label>No Rekening</label>
                            <input type="text" name="no_rekening[]" class="form-control"
                                   value="{{ $gift->no_rekening ?? '' }}" required>
                        </div>

                        <div class="mb-2">
                            <label>Nama Pemilik</label>
                            <input type="text" name="pemilik_bank[]" class="form-control"
                                   value="{{ $gift->pemilik_bank ?? '' }}" required>
                        </div>

                        {{-- tampilkan logo jika ada --}}
                        @if($gift && isset($gift->bank) && $gift->bank && $gift->bank->logo)
                            <div class="mb-2">
                                <label>Logo Bank</label><br>
                                <img src="{{ asset('storage/'.$gift->bank->logo) }}" width="100" alt="Logo">
                            </div>
                        @endif
                    </div>
                @endfor
            </div>

            <button type="button" id="btnAddBank" class="btn btn-sm btn-secondary">+ Tambah Bank</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>

<script>
let maxBank = 3;
let bankWrapper = document.getElementById('bank-wrapper');
let btnAddBank = document.getElementById('btnAddBank');

btnAddBank.addEventListener('click', function () {
    let bankCount = bankWrapper.querySelectorAll('.bank-item').length;
    if (bankCount >= maxBank) {
        alert("Maksimal hanya 3 rekening bank!");
        return;
    }

    // Generate options dari server-side (Blade)
    let options = `{!! addslashes(
        collect($banks)->map(fn($b) => "<option value='{$b->id}'>{$b->nama_bank}</option>")->join('')
    ) !!}`;

    let newBank = document.createElement('div');
    newBank.classList.add('card','mb-3','p-3','bank-item');
    newBank.innerHTML = `
        <h6>Bank ${bankCount+1}</h6>
        <div class="mb-2">
            <label>Nama Bank</label>
            <select name="bank_id[]" class="form-control" required>
                <option value="">-- Pilih Bank --</option>
                ${options}
            </select>
        </div>
        <div class="mb-2">
            <label>No Rekening</label>
            <input type="text" name="no_rekening[]" class="form-control" required>
        </div>
        <div class="mb-2">
            <label>Nama Pemilik</label>
            <input type="text" name="pemilik_bank[]" class="form-control" required>
        </div>
    `;
    bankWrapper.appendChild(newBank);
});
</script>
