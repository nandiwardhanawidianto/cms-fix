<!doctype html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Antrean Review Form Undangan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light"><main class="container py-4">
<div class="d-flex justify-content-between align-items-center mb-4">
<div><h2>Antrean Review Form Undangan</h2><p class="text-muted mb-0">Menunggu Review: <strong>{{ $pendingCount }}</strong> · Data baru belum membuat slug/website.</p></div>
<form method="POST" action="{{ route('form-review.logout') }}">@csrf<button class="btn btn-outline-secondary">Keluar</button></form>
</div>
<a href="{{ route('slug.index') }}" class="btn btn-outline-primary mb-3">← Kembali ke CMS</a>
<div class="card"><div class="table-responsive"><table class="table table-striped mb-0">
<thead><tr><th>No. Pesanan</th><th>Status</th><th>WhatsApp</th><th>Foto</th><th>Diperbarui</th><th>Aksi</th></tr></thead>
<tbody>@forelse($submissions as $row)<tr>
<td>{{ $row->order_number }}</td><td>{{ $row->status === 'pending_review' ? 'Menunggu Review' : 'Sudah Diterbitkan' }}</td>
<td>{{ $row->customer_whatsapp }}</td><td>{{ $row->photo_count }} (belum ditransfer)</td>
<td>{{ $row->updated_at?->format('d M Y H:i') }}</td>
<td><a href="{{ route('form-review.show', $row) }}" class="btn btn-sm btn-primary">Periksa</a></td>
</tr>@empty<tr><td colspan="6" class="text-muted text-center py-4">Belum ada pengajuan dari Form Undangan.</td></tr>@endforelse</tbody>
</table></div></div><div class="mt-3">{{ $submissions->links() }}</div>
</main></body></html>
