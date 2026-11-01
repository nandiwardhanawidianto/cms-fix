<!doctype html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Review {{ $submission->order_number }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light"><main class="container py-4" style="max-width:1000px">
<a href="{{ route('form-review.index') }}" class="btn btn-outline-secondary mb-3">← Antrean Review</a>
<h2>Periksa Pengajuan #{{ $submission->order_number }}</h2>
<p class="text-muted">WhatsApp: {{ $submission->customer_whatsapp }} · Shopee: {{ $submission->shopee_username ?: '-' }} · Urutan: {{ $submission->display_order === 'female_first' ? 'Wanita – Pria' : 'Pria – Wanita' }} · Foto: {{ $submission->photo_count }} (belum ditransfer)</p>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><strong>Belum dapat diterbitkan:</strong><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@if($submission->status==='published')
<div class="alert alert-success">Undangan sudah diterbitkan.
@if($submission->published_slug_id)<a href="{{ route('slug.edit', $submission->published_slug_id) }}">Buka CMS</a>@endif
</div>
@else
<div class="alert alert-warning">Status: <strong>{{ $submission->status === 'in_review' ? 'Sedang Direview' : 'Menunggu Review' }}</strong>. Simpan hanya mengubah data review. Publish membuat slug dan data undangan yang dapat diakses publik.</div>
<form method="POST" id="review-form">@csrf
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Nama Undangan</label>
<input class="form-control" name="invitation_name" value="{{ old('invitation_name', $suggestedName) }}" required>
</div>
<div class="col-md-6"><label class="form-label">Slug (boleh kosong untuk otomatis dari nama)</label>
<input class="form-control" name="invitation_slug" value="{{ old('invitation_slug', $suggestedSlug) }}" placeholder="contoh-nama" pattern="[a-z0-9]+(-[a-z0-9]+)*">
</div>
<div class="col-md-6"><label class="form-label">Theme</label>
<select class="form-select" name="theme" required>
<option value="">Pilih Tema</option>
@foreach($themes as $theme)<option value="{{ $theme }}" @selected(old('theme', data_get($submission->payload, 'theme')) === $theme)>{{ ucfirst($theme) }}</option>@endforeach
</select></div>
<div class="col-md-12"><label class="form-label">JSON Undangan (bisa diedit sebelum publish)</label>
<textarea class="form-control font-monospace" rows="20" name="json_payload" spellcheck="false" required>{{ old('json_payload', json_encode($submission->payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) }}</textarea>
</div></div>
<div class="d-flex flex-wrap gap-2 mt-3">
<button type="submit" class="btn btn-outline-primary" formaction="{{ route('form-review.save', $submission) }}">Simpan Review Saja</button>
</div>
<div class="border rounded p-3 mt-4 bg-white">
<label class="d-flex gap-2 align-items-center mb-3"><input type="checkbox" name="confirmed" value="1"> <span>Saya sudah memeriksa seluruh data dan memahami bahwa Publish akan membuat undangan dapat diakses publik.</span></label>
<button type="submit" class="btn btn-success" formaction="{{ route('form-review.publish', $submission) }}" onclick="return confirm('Terbitkan undangan ini sekarang?')">Publish Undangan</button>
</div></form>
@endif
</main></body></html>
