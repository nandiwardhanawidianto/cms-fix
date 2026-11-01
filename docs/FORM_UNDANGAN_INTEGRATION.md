# Form Undangan → CMS Royal Wedding Invitation

## Alur
1. Form Undangan Laravel menyimpan pesanan digital, kemudian POST JSON dari backend ke `/api/form-submissions` menggunakan header `X-CMS-Integration-Token`.
2. CMS mencatat satu `form_submissions` per nomor Shopee dengan status `pending_review`. Pengiriman ulang sebelum publish memperbarui record yang sama.
3. Tautan **Form Menunggu Review** di halaman utama CMS mengarah ke `/form-submissions`. Area ini memakai password admin review terpisah.
4. Admin memeriksa dan dapat mengedit JSON, memilih nama undangan, slug, dan tema. Menekan **Simpan Review Saja** tidak membuat undangan publik.
5. **Publish Undangan** adalah satu-satunya tindakan dalam integrasi baru yang memanggil importer dan membuat SlugList + data hero/acara/love gift/kirim kado; setelah itu status menjadi `published`. Klik publish dua kali diblokir.

## Konfigurasi Server (jangan pernah commit nilai rahasia)
Set variabel berikut di `.env` CMS:

```dotenv
FORM_UNDANGAN_INTEGRATION_TOKEN=
CMS_REVIEW_PASSWORD=
```

Isi token acak minimal 32 karakter dan password review minimal 12 karakter. Token harus sama dengan `ROYAL_CMS_INTEGRATION_TOKEN` pada .env Form Undangan. Endpoint lengkap di Form Undangan: `ROYAL_CMS_SUBMISSION_URL=https://CMS_DOMAIN/api/form-submissions`, wajib HTTPS. Perbarui config cache setelah mengubah .env; jalankan `php artisan migrate --force` saat deploy.

**Peringatan:** CMS lama mempunyai endpoint lain dan halaman pengelolaan yang belum memakai auth Laravel. Gate password di atas mengamankan antrean review baru, **bukan seluruh CMS**. Sebelum dipakai dengan data nyata, audit autentikasi CMS secara keseluruhan.

**Foto belum ditransfer:** CMS hanya menerima `photo_count`. Foto tetap privat di Form Undangan dan perlu mekanisme aman terpisah. Publish tidak menyamakan foto dengan transfer selesai.

**Batas kelengkapan:** Antrean menerima data belum lengkap. Publish memakai validasi impor JSON CMS (termasuk nama pendek, tanggal, alamat, Master Bank, dan tema); kesalahan akan ditampilkan dan harus diperbaiki sebelum publikasi.

**Pemetaan urutan mempelai:** Form mengirim `display_order` di metadata permintaan; importer manual lama tetap menggunakan urutan lama, sedangkan antrean Form mengatur pria/wanita sesuai pilihan pengguna.

## Perlindungan dari penimpaan review
Ketika admin menekan **Simpan Review Saja**, status pengajuan berubah menjadi `in_review` dan isinya dikunci dari pengiriman ulang otomatis. Permintaan API berikutnya untuk nomor pesanan yang sama dibalas HTTP 409 agar perubahan pelanggan tidak menimpa edit admin. Admin tetap dapat memperbarui JSON secara manual dan Publish. Jumlah notifikasi CMS mencakup `pending_review` dan `in_review`.
