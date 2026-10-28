# Dokumentasi Alur Sistem Ecommer-Template-Laravel

## 1. Ringkasan Sistem
Sistem ini adalah aplikasi manajemen pipeline debitur dari pengajuan sampai keputusan akhir, dilengkapi:
- workflow approval bertahap (SPV -> Analis -> Risk -> Mitra),
- manajemen pembayaran angsuran,
- dashboard performa cabang/marketing/SPV,
- role & permission management,
- master data (cabang, perusahaan, jabatan, parameter produk),
- pelacakan histori status debitur.

Ruang lingkup utama aplikasi berada pada modul debitur dan analisis kredit.

## 2. Aktor dan Tanggung Jawab
1. Marketing
- Input data debitur baru.
- Upload dokumen awal.
- Menindaklanjuti data revisi dari SPV/Analis/Risk/Mitra.

2. SPV
- Review tahap awal data pengajuan marketing.
- Approve, revisi, atau reject pada tahap SPV.

3. Analis
- Melakukan analisis keuangan dan kelayakan.
- Menentukan usulan plafon, tenor, bunga, dan catatan analisis.

4. Risk
- Review hasil analis dari sisi risiko.
- Generate/validasi nomor SPK saat approval.

5. Mitra
- Final approval (atau revisi/reject) sebelum status akhir.

6. Admin
- Kelola user, role, permission, cabang, perusahaan, jabatan, parameter produk.
- Dapat memonitor proses lintas modul.

## 3. Peta Modul Utama
1. Auth & Lock Screen
- Login, register, lockscreen, unlock.

2. Dashboard
- Ringkasan plafon, pembayaran, outstanding, pencapaian marketing, summary SPV.

3. Debitur
- CRUD pengajuan, tahapan review, payment, tracking, download dokumen, export.

4. Analisis Kredit
- Pengelolaan data analisa keuangan (MPP), persetujuan multi-role, print.

5. Master Data & Akses
- User/Role/Permission/Cabang + master referensi bisnis.

## 4. Alur End-to-End Debitur

### 4.1 Pengajuan Awal (Marketing)
1. Marketing membuka form create debitur.
2. Sistem validasi data utama (identitas, kontak, perusahaan, bank, dll).
3. Dokumen wajib diunggah (KTP, BPJS, selfie KTP).
4. Sistem membuat data debitur dengan status awal `pending_spv`.
5. Sistem mengisi otomatis relasi:
- `marketing_id` = user login,
- `spv_id` = SPV dari user marketing,
- `cabang_id` = cabang user marketing.
6. Sistem mencatat histori submit.

### 4.2 Review SPV (Tahap 1)
1. SPV melihat data debitur yang menjadi tanggung jawabnya.
2. SPV melakukan aksi cepat:
- approve -> status lanjut ke `pending_analis`,
- revisi -> status `revisi_spv` (kembali ke marketing),
- reject -> status `reject_spv`.
3. Sistem menyimpan catatan/riwayat aksi.

### 4.3 Analisis Keuangan (Tahap 3 - Analis)
1. Analis membuka data dengan status `pending_analis`.
2. Sistem menyiapkan/membuat record analisa keuangan (MPP) bila belum ada.
3. Analis mengisi parameter analisa:
- usulan plafon,
- tenor,
- bunga,
- jaminan,
- rasio analisis,
- catatan persetujuan.
4. Hasil aksi analis:
- approve -> `pending_risk`,
- revisi -> `revisi_analis`,
- reject -> `reject_analis`.

### 4.4 Review Risk (Tahap 3 - Risk)
1. Risk memproses data dengan status `pending_risk`.
2. Risk menentukan aksi:
- approve -> `pending_mitra`,
- revisi -> `revisi_risk`,
- reject -> `reject_risk`.
3. Saat approve, sistem menyiapkan nomor SPK (jika belum tersedia) sesuai pola sistem.

### 4.5 Persetujuan Mitra (Final)
1. Mitra memproses data dengan status `pending_mitra`.
2. Opsi hasil:
- approve -> `approve` (final),
- revisi data -> `revisi_mitra` (kembali ke marketing),
- alasan penolakan tertentu (DSR/FDC/Take Out) -> `reject`.

### 4.6 Siklus Revisi Marketing
1. Marketing menerima data dengan status revisi (`revisi_spv`, `revisi_analis`, `revisi_risk`, `revisi_mitra`).
2. Marketing memperbaiki data/dokumen.
3. Sistem kirim balik ke tahap asal revisi:
- dari `revisi_spv` -> `pending_spv`,
- dari `revisi_analis` -> `pending_analis`,
- dari `revisi_risk` -> `pending_risk`,
- dari `revisi_mitra` -> `pending_mitra`.
4. Histori resubmit dicatat.

## 5. Diagram Status Workflow

```text
pending_spv
  -> approve : pending_analis
  -> revisi  : revisi_spv (kembali ke Marketing)
  -> reject  : reject_spv

pending_analis
  -> approve : pending_risk
  -> revisi  : revisi_analis (kembali ke Marketing)
  -> reject  : reject_analis

pending_risk
  -> approve : pending_mitra
  -> revisi  : revisi_risk (kembali ke Marketing)
  -> reject  : reject_risk

pending_mitra
  -> approve : approve (final)
  -> revisi  : revisi_mitra (kembali ke Marketing)
  -> reject  : reject
```

## 6. Alur Dashboard dan Monitoring

### 6.1 Dashboard Home
1. Sistem menghitung total plafon, total pembayaran, total debitur, outstanding.
2. Sistem mengelompokkan pencapaian per marketing/cabang.
3. Sistem menerapkan filter cabang berdasarkan user login:
- user cabang non-pusat hanya lihat cabangnya,
- user pusat dapat melihat lintas cabang (dan filter cabang dari request bila tersedia).

### 6.2 Summary dan Summary SPV
1. Summary menampilkan agregasi performa.
2. Summary SPV menampilkan agregasi per cabang -> SPV -> marketing.
3. Data diurutkan untuk memudahkan evaluasi per cabang.

## 7. Alur Pembayaran Angsuran
1. User membuka detail debitur untuk melihat jadwal angsuran.
2. Sistem hitung komponen angsuran (pokok, jasa, total bulanan, sisa outstanding).
3. Saat input pembayaran:
- mode normal: update bulan tertentu,
- mode pelunasan: tandai bulan berjalan sampai akhir sebagai lunas.
4. Sistem simpan riwayat pembayaran per urutan bulan.

## 8. Alur Tracking dan Audit
1. Modul tracking mengambil data debitur beserta histori status.
2. Sistem memfilter data tracking berdasarkan role dan cabang:
- marketing melihat data miliknya,
- SPV melihat data tanggungannya,
- pembatasan cabang tetap berlaku untuk non-pusat.
3. Histori digunakan untuk audit perjalanan status dan catatan review.

## 9. Alur Analisis Kredit (MPP)
1. Daftar analisis kredit ditampilkan berdasarkan data analisa dan debitur.
2. Analis dapat edit konten analisa.
3. Tersedia persetujuan multi-role:
- marketing,
- analis,
- kadiv bisnis,
- pengelola.
4. Dokumen print hanya tersedia jika semua role berstatus setuju.

## 10. Alur Master Data dan Akses

### 10.1 User, Role, Permission
1. Admin mengelola user.
2. User dapat diberi role tunggal/multi-role sesuai kebutuhan.
3. Role terkait permission untuk kontrol akses menu/halaman/aksi.

### 10.2 Cabang, Perusahaan, Jabatan, Parameter Produk
1. Admin mengelola data referensi bisnis.
2. Data referensi dipakai saat input debitur dan analisis.

### 10.3 API Wilayah
1. Sistem menyediakan endpoint provinsi -> kabupaten -> kecamatan -> kelurahan.
2. Endpoint dipakai untuk dropdown alamat bertingkat pada form.

## 11. Relasi Data Penting
1. Debitur (`master_pencairan_mekar`) berelasi ke:
- marketing (`users` via `marketing_id`),
- SPV (`users` via `spv_id`),
- cabang (`cabangs` via `cabang_id`),
- analisa keuangan,
- histori debitur,
- pembayaran angsuran,  
- media dokumen.

2. User berelasi ke:
- jabatan,
- cabang,
- role/permission.

3. Lokasi wilayah berelasi hirarkis:
- province -> regency -> district -> village.

## 12. Dokumen dan Media
1. Sistem menggunakan media collection untuk dokumen debitur (KTP, BPJS, selfie, dll).
2. Tersedia fitur unduh seluruh dokumen debitur dalam file ZIP.
3. Dokumen dapat diperbarui saat siklus revisi.

## 13. Aturan Bisnis Kunci
1. Workflow approval harus mengikuti urutan status tahap.
2. Role tertentu dibatasi pada aksi tertentu (contoh: SPV hanya tahap SPV).
3. Pembatasan cabang berlaku untuk visibilitas data non-pusat.
4. Revisi selalu mengembalikan data ke marketing untuk perbaikan.
5. Histori proses menjadi sumber audit utama.

## 14. Daftar Endpoint Inti (Ringkas)

### Auth
- `/login`, `/register`, `/lockscreen`, `/unlock`, `/logout/page`

### Dashboard
- `/home`, `/summary`, `/summary-spv`
- `/home/grafik-pencapaian`
- `/home/grafik-pencapaian-cabang`

### Debitur
- `/apps-ecommerce/debitur`
- `/apps-ecommerce/debitur/create`
- `/apps-ecommerce/debitur/store`
- `/apps-ecommerce/debitur/{id}/edit`
- `/apps-ecommerce/debitur/{id}/edit-tahap1`
- `/apps-ecommerce/debitur/{id}/edit-tahap3-analis`
- `/apps-ecommerce/debitur/{id}/edit-tahap3-risk`
- `/apps-ecommerce/debitur/{id}/edit-tahap-mitra`
- `/apps-ecommerce/debitur/{id}/action`
- `/apps-ecommerce/debitur-payment`
- `/apps-ecommerce/debitur-tracking`
- `/apps-ecommerce/debitur/{id}/download-all`
- `/apps-ecommerce/debitur/export/mitra`

### Analisis Kredit
- `/apps-ecommerce/analisis-kredit`
- `/apps-ecommerce/analisis-kredit-data`
- `/apps-ecommerce/analisis-kredit/{id}/edit`
- `/apps-ecommerce/analisis-kredit/{id}/persetujuan`
- `/apps-ecommerce/analisis-kredit/{id}/print`

### Master Data
- resource `/users`, `/roles`, `/permissions`, `/cabangs`
- admin-only: `/perusahaan`, `/jabatans`, `/parameter-produk`

### API Wilayah
- `/apps-ecommerce/wilayah/provinsi`
- `/apps-ecommerce/wilayah/kabupaten/{province_id}`
- `/apps-ecommerce/wilayah/kecamatan/{regency_id}`
- `/apps-ecommerce/wilayah/kelurahan/{district_id}`

## 15. Catatan Implementasi untuk Tim
1. Jika menambah tahapan baru, pastikan sinkron antara:
- status enum di proses bisnis,
- endpoint controller,
- validasi akses role,
- histori status.
2. Jika menambah role baru, pastikan permission menu/page/action terdefinisi.
3. Jika mengubah logika dashboard, validasi dampak filter cabang dan peran user.
4. Jika mengubah skema data debitur, cek kompatibilitas export, tracking, dan perhitungan pembayaran.

---
Dokumen ini ditujukan sebagai baseline operasional dan teknis. Jika diperlukan, bisa diturunkan lagi menjadi SOP per divisi (Marketing, SPV, Analis, Risk, Mitra, Admin).
