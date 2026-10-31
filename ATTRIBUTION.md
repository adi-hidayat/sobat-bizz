# Attribution — Data Wilayah

Aplikasi ini memakai dua dataset wilayah. Atribusi berikut wajib dipertahankan
ketika data didistribusikan ulang.

## Countries States Cities Database (dr5hn)

- Sumber: https://github.com/dr5hn/countries-states-cities-database
- Lisensi: **Open Database License (ODbL) v1.0**
- Versi data: `v5.0.0-export.1` (19 Oktober 2026)
- Digunakan untuk: daftar negara, serta state dan kota di luar Indonesia.
- Atribusi:

  > Data by Countries States Cities Database
  > https://github.com/dr5hn/countries-states-cities-database | ODbL v1.0

- Tabel `regions` pada aplikasi ini merupakan **database turunan** dari data
  dr5hn dan cahyadsn, sehingga bagian yang berasal dari dr5hn tetap tunduk pada
  ODbL 1.0.

## Wilayah Indonesia (cahyadsn)

- Sumber utama: https://github.com/cahyadsn/wilayah
- Sumber kode pos: https://github.com/cahyadsn/wilayah_kodepos
- Lisensi: **MIT**
- Dasar data: Kepmendagri No. 300.2.2-2430 Tahun 2025
- Digunakan untuk: provinsi, kabupaten/kota, kecamatan, desa/kelurahan, dan
  kode pos.

## Catatan

Data unduhan tidak disertakan di repository. Jalankan:

```bash
php artisan crm:download-regions
php artisan crm:import-regions
```
