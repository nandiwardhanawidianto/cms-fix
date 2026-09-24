<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Import Data Undangan dari JSON</span>
        <span class="badge bg-secondary">Slug: {{ $slug->slug }}</span>
    </div>

    <div class="card-body">
        <div class="alert alert-info">
            JSON hanya dipakai sebagai jalan cepat untuk input awal. Setelah berhasil diimpor, database menjadi sumber data utama dan semua bagian tetap bisa diedit manual seperti biasa.
        </div>

        @if($errors->has('json_payload') || $errors->has('theme_override'))
            <div class="alert alert-danger">
                <strong>Import belum bisa diproses.</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->get('json_payload') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    @foreach($errors->get('theme_override') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('import.preview', $slug->id) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="json_payload" class="form-label">JSON Undangan</label>
                <textarea
                    id="json_payload"
                    name="json_payload"
                    class="form-control font-monospace"
                    rows="18"
                    spellcheck="false"
                    placeholder='Paste JSON dari ChatGPT di sini...'
                    required
                >{{ old('json_payload', session('import_json')) }}</textarea>
                <div class="form-text">
                    Foto mempelai dan galeri tidak masuk lewat JSON. Foto tetap diatur manual dari menu Hero/Galeri.
                </div>
            </div>

            <div class="mb-3">
                <label for="theme_override" class="form-label">Theme Override <span class="text-muted">(opsional saat preview)</span></label>
                @php
                    $themeValue = old('theme_override', session('import_theme_override'));
                    $themes = ['violet', 'sage', 'brown', 'jawa', 'bali', 'pink', 'biru', 'hitam', 'dayak', 'bugis'];
                @endphp
                <select id="theme_override" name="theme_override" class="form-select">
                    <option value="">Gunakan theme dari JSON</option>
                    @foreach($themes as $theme)
                        <option value="{{ $theme }}" @selected($themeValue === $theme)>
                            {{ ucfirst($theme) }}
                        </option>
                    @endforeach
                </select>
                <div class="form-text">
                    Jika JSON berisi <code>"theme": null</code> atau tidak memiliki theme, pilih theme sebelum menekan Import Sekarang.
                </div>
            </div>

            <button type="submit" class="btn btn-outline-primary">
                Preview JSON
            </button>
        </form>

        <hr class="my-4">

        <details>
            <summary class="fw-semibold">Lihat format JSON yang didukung</summary>
            <pre class="bg-dark text-light rounded p-3 mt-3 mb-0" style="white-space: pre-wrap;">{
  "theme": null,
  "groom": {
    "full_name": "Avensus Mariono Pardede",
    "short_name": "Aven",
    "parents": "Putra ke 2 dari Bapak Sahat Pardede & Ibu Morlince Purba"
  },
  "bride": {
    "full_name": "Angel Poibe Siahaan",
    "short_name": "Poibe",
    "parents": "Putri ke 4 dari Bapak Perodi Siahaan & Ibu Rostinim Sihombing"
  },
  "events": [
    {
      "name": "Pemberkatan",
      "date": "2026-10-01",
      "time": "08:00 - selesai",
      "address": "Jalan Pemandian Sosor Parribuan",
      "maps": null
    },
    {
      "name": "Adat",
      "date": "2026-10-01",
      "time": "12:00 - selesai",
      "address": "Jalan Pemandian Sosor Parribuan",
      "maps": null
    }
  ],
  "love_gifts": [
    {
      "bank": "BRI",
      "account_number": "031401022091531",
      "account_name": "Avensus Mariono Pardede"
    }
  ],
  "gift_delivery": {
    "recipient_name": "Avensus Mariono Pardede",
    "phone": "081375555160",
    "address": "Jalan Pemandian Sosor Parribuan"
  }
}</pre>
        </details>

        @if(session('import_preview'))
            @php($preview = session('import_preview'))

            <hr class="my-4">

            <h5>Preview Sebelum Import</h5>
            <p class="text-muted">Belum ada perubahan database pada tahap ini.</p>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small">Theme</div>
                        @if($preview['theme'])
                            <strong>{{ ucfirst($preview['theme']) }}</strong>
                        @else
                            <span class="text-danger fw-semibold">Belum dipilih</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small">Mempelai Pria</div>
                        <strong>{{ data_get($preview, 'groom.full_name', '-') ?? '-' }}</strong>
                        <div>{{ data_get($preview, 'groom.short_name', '') }}</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small">Mempelai Wanita</div>
                        <strong>{{ data_get($preview, 'bride.full_name', '-') ?? '-' }}</strong>
                        <div>{{ data_get($preview, 'bride.short_name', '') }}</div>
                    </div>
                </div>
            </div>

            @if(is_array($preview['events']))
                <h6>Acara</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Alamat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($preview['events'] as $event)
                                <tr>
                                    <td>{{ $event['name'] }}</td>
                                    <td>{{ $event['date'] }}</td>
                                    <td>{{ $event['time'] }}</td>
                                    <td>{{ $event['address'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted">Acara akan dikosongkan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            @if(is_array($preview['love_gifts']))
                <h6>Love Gift</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Bank</th>
                                <th>No. Rekening</th>
                                <th>Nama Rekening</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($preview['love_gifts'] as $gift)
                                <tr>
                                    <td>{{ $gift['bank'] }}</td>
                                    <td>{{ $gift['account_number'] }}</td>
                                    <td>{{ $gift['account_name'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted">Love Gift akan dikosongkan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            @if(array_key_exists('gift_delivery', $preview) && $preview['gift_delivery'] !== null)
                <h6>Kirim Kado</h6>
                <div class="border rounded p-3 mb-3">
                    <div><strong>{{ $preview['gift_delivery']['recipient_name'] }}</strong></div>
                    <div>{{ $preview['gift_delivery']['phone'] }}</div>
                    <div>{{ $preview['gift_delivery']['address'] }}</div>
                </div>
            @elseif(array_key_exists('gift_delivery', $preview) && $preview['gift_delivery'] === null)
                <div class="alert alert-warning">Data Kirim Kado akan dihapus.</div>
            @endif

            <form action="{{ route('import.store', $slug->id) }}" method="POST" class="border-top pt-3">
                @csrf
                <textarea name="json_payload" class="d-none">{{ session('import_json') }}</textarea>

                <div class="mb-3">
                    <label for="confirm_theme_override" class="form-label">Theme untuk Import</label>
                    <select id="confirm_theme_override" name="theme_override" class="form-select">
                        <option value="">Gunakan theme dari JSON</option>
                        @foreach($themes as $theme)
                            <option value="{{ $theme }}" @selected($themeValue === $theme)>
                                {{ ucfirst($theme) }}
                            </option>
                        @endforeach
                    </select>
                    @if(!$preview['theme'])
                        <div class="text-danger small mt-1">Theme wajib dipilih sebelum import.</div>
                    @endif
                </div>

                <div class="alert alert-warning">
                    Jika bagian <strong>events</strong> atau <strong>love_gifts</strong> ada di JSON, data lama pada bagian tersebut akan diganti. Bagian yang tidak ada di JSON tidak disentuh.
                </div>

                <button type="submit" class="btn btn-primary">
                    Import Sekarang
                </button>
            </form>
        @endif
    </div>
</div>
