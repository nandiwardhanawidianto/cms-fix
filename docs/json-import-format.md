# JSON Invitation Import

The JSON importer is a one-time data-entry shortcut. After a successful import, the normal CMS database remains the source of truth and every imported section can be edited manually.

## Mempelai ordering rule

The importer is order-based, not gender-based.

- The first mempelai in the customer form becomes `mempelai_1`.
- The second mempelai becomes `mempelai_2`.
- `mempelai_2` is optional.
- The importer does not infer whether a person is male or female.
- Internally, the current legacy database columns still use `pria` and `wanita`; the importer maps `mempelai_1` to the first legacy slot and `mempelai_2` to the second legacy slot only for compatibility.

## Supported top-level fields

```json
{
  "theme": "sage",
  "mempelai_1": {
    "full_name": "Nandi Wardana",
    "short_name": "Nandi",
    "parents": "Anak ke 2 dari Bapak ... & Ibu ..."
  },
  "mempelai_2": {
    "full_name": "Nama lengkap mempelai kedua",
    "short_name": "Nama pendek",
    "parents": "Anak ke 1 dari Bapak ... & Ibu ..."
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
- Missing `mempelai_1` / `mempelai_2` subfields do not overwrite existing values.
- An explicitly `null` mempelai clears that slot's text fields.
- Photos and gallery files are not imported through JSON.
- Bank names must already exist in Master Bank.
- Event dates use `YYYY-MM-DD`.

## Current schema limitation

The current `cms-fix` database has no dedicated field for `Turut Mengundang`. That value must not be silently mapped into an unrelated column and needs dedicated CMS/database support before it can be part of this importer.
