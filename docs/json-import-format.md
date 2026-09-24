# JSON Invitation Import

The JSON importer is a one-time data-entry shortcut. After a successful import, the normal CMS database remains the source of truth and every imported section can be edited manually.

## Primary workflow: create from the homepage

The preferred workflow is now:

1. Open **Management Undangan**.
2. Click **Import JSON & Buat Undangan**.
3. Paste the JSON from ChatGPT.
4. Preview the derived invitation name, slug, order number, theme, mempelai, events, gifts, and delivery data.
5. Choose a theme if the JSON has no theme.
6. Click **Buat Undangan dari JSON**.

The CMS creates the `SlugList` row and supported invitation rows in one database transaction. Staff no longer need to create the slug manually first.

For a new invitation:

- `order_number` is required and is saved to `slug_lists.keterangan`.
- `mempelai_1.short_name` is required.
- If `mempelai_2` is present, `mempelai_2.short_name` is also required.
- The invitation name is generated from the short names in order. Example: `Nanda` + `Ane` becomes `Nanda Ane`.
- The slug is generated from that name, for example `nanda-ane`. Existing slug collisions receive the normal numeric suffix.
- Global default photos are applied automatically when a mempelai has no custom photo and a default is available.

The existing per-invitation JSON importer remains available for updating an already-created invitation.

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
  "order_number": "260830QBB2WATW",
  "theme": "sage",
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
  "events": [
    {
      "name": "Akad",
      "date": "2026-10-09",
      "time": "16.00 WIB",
      "address": "Jalan Bandes RT 2 RW 4, dekat Rony Futsal",
      "maps": null
    }
  ],
  "love_gifts": [
    {
      "bank": "CIMB Niaga",
      "account_number": "705632029400",
      "account_name": "Febri Mananda"
    }
  ],
  "gift_delivery": {
    "recipient_name": "Alrane Mutia Sari",
    "phone": "08985614376",
    "address": "Jalan Bandes RT 2 RW 4, dekat Rony Futsal"
  }
}
```

Unknown top-level JSON keys are rejected rather than silently ignored.

## Import behavior

- `theme` may be `null` during preview, but a valid CMS theme must be chosen before the final import.
- `events` replaces the existing event rows only when the key is present.
- `love_gifts` replaces the existing love-gift rows only when the key is present.
- `gift_delivery: null` explicitly removes the existing gift-delivery record.
- Missing `mempelai_1` / `mempelai_2` subfields do not overwrite existing values.
- An explicitly `null` mempelai clears that slot's text fields on an existing invitation.
- Photos and gallery files are not imported as file data through JSON.
- Bank names must already exist in Master Bank.
- Event dates use `YYYY-MM-DD`.

## Global default photos

Default photo management is on the **Management Undangan** homepage under **Foto Default**.

- Upload one global default for Mempelai 1 and one for Mempelai 2.
- Internal storage still uses the legacy first/second slot names for compatibility.
- New JSON imports use the matching global default automatically when there is no custom photo.
- Saving Hero data manually also applies the matching default automatically when needed.
- Replacing a global default updates invitations that were using the old default, but does not replace custom photos.

## Current schema limitation

The current `cms-fix` database has no dedicated field for `Turut Mengundang`. That value must not be silently mapped into an unrelated column and needs dedicated CMS/database support before it can be part of this importer.
