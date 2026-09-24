# JSON Invitation Import

The JSON importer is a one-time data-entry shortcut. After a successful import, the normal CMS database remains the source of truth and every imported section can be edited manually.

## Supported top-level fields

```json
{
  "theme": "sage",
  "groom": {
    "full_name": "Nama lengkap pria",
    "short_name": "Nama pendek pria",
    "parents": "Putra ke 2 dari Bapak ... & Ibu ..."
  },
  "bride": {
    "full_name": "Nama lengkap wanita",
    "short_name": "Nama pendek wanita",
    "parents": "Putri ke 4 dari Bapak ... & Ibu ..."
  },
  "events": [
    {
      "name": "Pemberkatan",
      "date": "2026-10-01",
      "time": "08:00 - selesai",
      "address": "Alamat acara",
      "maps": null
    }
  ],
  "love_gifts": [
    {
      "bank": "BRI",
      "account_number": "031401022091531",
      "account_name": "Nama pemilik rekening"
    }
  ],
  "gift_delivery": {
    "recipient_name": "Nama penerima",
    "phone": "081234567890",
    "address": "Alamat kirim kado"
  }
}
```

## Import behavior

- `theme` may be `null` during preview, but a valid CMS theme must be chosen before the final import.
- `events` replaces the existing event rows only when the key is present.
- `love_gifts` replaces the existing love-gift rows only when the key is present.
- `gift_delivery: null` explicitly removes the existing gift-delivery record.
- Missing groom/bride subfields do not overwrite the existing values; an explicitly `null` groom or bride clears that person's text fields.
- Photos and gallery files are not imported through JSON.
- Bank names must already exist in Master Bank.
- Event dates use `YYYY-MM-DD`.

## Current schema limitation

The current `cms-fix` database has no field for customer-form items such as `Format undangan: pria/wanita` (display ordering) or `Turut Mengundang`. Those values must not be silently mapped into unrelated columns. They need dedicated CMS/database support before they can be part of this importer.
