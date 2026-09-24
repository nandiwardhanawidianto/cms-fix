@php
    $createPreview = session('create_import_preview');
    $createThemeValue = old('theme_override', session('create_import_theme_override'));
    $createThemes = ['violet', 'sage', 'brown', 'jawa', 'bali', 'pink', 'biru', 'hitam', 'hitam2', 'dayak', 'bugis'];
@endphp

<div class="modal fade" id="jsonCreateModal" tabindex="-1" aria-labelledby="jsonCreateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="jsonCreateModalLabel">Import JSON & Buat Undangan</h5>
                    <div class="text-muted small">Tidak perlu membuat slug manual terlebih dahulu.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-info">
                    CMS mengambil <strong>nama pendek Mempelai 1 + Mempelai 2</strong> untuk membuat Nama dan Slug. Nomor pesanan disimpan ke Keterangan. Mempelai 2 boleh kosong.
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

                <form action="{{ route('import.create.preview') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="create_json_payload" class="form-label">JSON Undangan</label>
                        <textarea
                            id="create_json_payload"
                            name="json_payload"
                            class="form-control font-monospace"
                            rows="18"
                            spellcheck="false"
                            placeholder="Paste JSON dari ChatGPT di sini..."
                            required
                        >{{ old('json_payload', session('create_import_json')) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="create_theme_override" class="form-label">Theme <span class="text-muted">(boleh dipilih saat preview)</span></label>
                        <select id="create_theme_override" name="theme_override" class="form-select">
                            <option value="">Gunakan theme dari JSON</option>
                            @foreach($createThemes as $theme)
                                <option value="{{ $theme }}" @selected($createThemeValue === $theme)>
                                    {{ ucfirst($theme) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">
                            Jika JSON memakai <code>"theme": null</code>, theme wajib dipilih sebelum import final.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-outline-primary">Preview JSON</button>
                </form>

                <details class="mt-4">
                    <summary class="fw-semibold">Lihat format JSON untuk membuat undangan baru</summary>
                    <pre class="bg-dark text-light rounded p-3 mt-3 mb-0" style="white-space: pre-wrap;">{
  "order_number": "260830QBB2WATW",
  "theme": null,
  "mempelai_1": {
    "full_name": "Febri Mananda",
    "short_name": "Nanda",
    "parents": "Anak ke 4 dari Bapak Ali Muzahir (alm) & Ibu Risnawati"
  },
  "mempelai_2": {
    "full_name": "Alrane Mutia Sari",
    "short_name": "Ane",
    "parents": "Anak ke 2 dari Bapak Ramli & Ibu Zelmi Novita Sari"
  },
  "events": [],
  "love_gifts": [],
  "gift_delivery": null
}</pre>
                </details>

                @if($createPreview)
                    <hr class="my-4">

                    <h5>Preview Sebelum Membuat Undangan</h5>
                    <p class="text-muted">Belum ada perubahan database pada tahap preview.</p>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Nama</div>
                                <strong>{{ $createPreview['invitation_name'] }}</strong>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Slug</div>
                                <strong>{{ $createPreview['slug'] }}</strong>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Keterangan / No Pesanan</div>
                                <strong>{{ $createPreview['order_number'] }}</strong>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Theme</div>
                                @if($createPreview['theme'])
                                    <strong>{{ ucfirst($createPreview['theme']) }}</strong>
                                @else
                                    <span class="text-danger fw-semibold">Belum dipilih</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Mempelai 1</div>
                                <strong>{{ data_get($createPreview, 'mempelai_1.full_name', '-') ?? '-' }}</strong>
                                <div>Nama pendek: {{ data_get($createPreview, 'mempelai_1.short_name', '-') ?? '-' }}</div>
                                <div class="small mt-2">{{ data_get($createPreview, 'mempelai_1.parents', '') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Mempelai 2 <span class="text-muted">(opsional)</span></div>
                                <strong>{{ data_get($createPreview, 'mempelai_2.full_name', '-') ?? '-' }}</strong>
                                <div>Nama pendek: {{ data_get($createPreview, 'mempelai_2.short_name', '-') ?? '-' }}</div>
                                <div class="small mt-2">{{ data_get($createPreview, 'mempelai_2.parents', '') }}</div>
                            </div>
                        </div>
                    </div>

                    @if(is_array($createPreview['events']))
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
                                    @forelse($createPreview['events'] as $event)
                                        <tr>
                                            <td>{{ $event['name'] }}</td>
                                            <td>{{ $event['date'] }}</td>
                                            <td>{{ $event['time'] }}</td>
                                            <td>{{ $event['address'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-muted">Tidak ada acara di JSON.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if(is_array($createPreview['love_gifts']))
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
                                    @forelse($createPreview['love_gifts'] as $gift)
                                        <tr>
                                            <td>{{ $gift['bank'] }}</td>
                                            <td>{{ $gift['account_number'] }}</td>
                                            <td>{{ $gift['account_name'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-muted">Tidak ada Love Gift di JSON.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if(array_key_exists('gift_delivery', $createPreview) && $createPreview['gift_delivery'] !== null)
                        <h6>Kirim Kado</h6>
                        <div class="border rounded p-3 mb-3">
                            <strong>{{ $createPreview['gift_delivery']['recipient_name'] }}</strong>
                            <div>{{ $createPreview['gift_delivery']['phone'] }}</div>
                            <div>{{ $createPreview['gift_delivery']['address'] }}</div>
                        </div>
                    @endif

                    <form action="{{ route('import.create.store') }}" method="POST" class="border-top pt-3">
                        @csrf
                        <textarea name="json_payload" class="d-none">{{ session('create_import_json') }}</textarea>

                        <div class="mb-3">
                            <label for="create_confirm_theme_override" class="form-label">Theme untuk Import</label>
                            <select
                                id="create_confirm_theme_override"
                                name="theme_override"
                                class="form-select"
                                @if(!$createPreview['theme']) required @endif
                            >
                                <option value="">{{ $createPreview['theme'] ? 'Gunakan theme dari JSON' : 'Pilih theme' }}</option>
                                @foreach($createThemes as $theme)
                                    <option value="{{ $theme }}" @selected($createThemeValue === $theme)>
                                        {{ ucfirst($theme) }}
                                    </option>
                                @endforeach
                            </select>
                            @if(!$createPreview['theme'])
                                <div class="text-danger small mt-1">Theme wajib dipilih sebelum membuat undangan.</div>
                            @endif
                        </div>

                        <div class="alert alert-secondary">
                            Saat import final, CMS membuat slug, menyimpan nomor pesanan ke Keterangan, dan mengisi data undangan. Foto dipilih setelahnya dari Hero: upload sendiri, Picture 1, atau Picture 2.
                        </div>

                        <button type="submit" class="btn btn-primary">Buat Undangan dari JSON</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
