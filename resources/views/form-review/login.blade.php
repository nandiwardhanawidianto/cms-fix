<!doctype html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Login Review Form Undangan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light"><main class="container py-5" style="max-width:440px">
<div class="card shadow-sm"><div class="card-body p-4"><h2>Review Form Undangan</h2>
<p class="text-muted">Area khusus admin CMS. Data pelanggan belum dipublikasikan.</p>
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<form action="{{ route('form-review.login') }}" method="POST">@csrf
<label class="form-label" for="password">Kata Sandi Review CMS</label>
<input type="password" id="password" name="password" class="form-control mb-3" required autocomplete="current-password">
<button type="submit" class="btn btn-primary w-100">Masuk</button>
</form></div></div></main></body></html>
