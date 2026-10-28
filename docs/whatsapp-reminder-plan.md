# Rencana Bot Pengingat Debitur via WhatsApp

Dokumen ini berisi rencana dan saran implementasi fitur **pengaturan bot jadwal untuk pengingat debitur yang akan jatuh tempo dan yang sudah tertunggak** pada menu WhatsApp API.

Tanggal dibuat: 23 Oktober 2026

---

## 1. Rekomendasi utama

Jangan langsung mengirim pengingat untuk semua angsuran yang belum dibayar.

> **Satu debitur = satu pesan per hari**, berdasarkan **angsuran belum dibayar paling awal**, lalu di dalam pesan disebutkan total tunggakan.

Contoh:

- Debitur punya tunggakan angsuran ke-3, ke-4, ke-5.
- Jangan kirim 3 pesan.
- Kirim 1 pesan: "Angsuran ke-3 sudah lewat 45 hari, total tunggakan 3 angsuran = Rp..."

Pendekatan ini lebih aman dari sisi ban WhatsApp dan lebih nyaman untuk debitur.

---

## 2. Aturan pengingat yang disarankan

| Waktu | Jenis pesan | Aktif default |
|---|---|---|
| H-3 | Pengingat sebelum jatuh tempo | Ya |
| H-1 | Pengingat terakhir | Ya |
| H | Jatuh tempo hari ini | Ya |
| D+3 | Terlambat 3 hari | Ya |
| D+7 | Terlambat 7 hari | Ya |
| D+14 | Terlambat 14 hari | Ya |
| D+30 | Terlambat 30 hari | Ya |
| H-7 | Pengingat awal | Opsional |

Default:

- Jam kirim: **09:00 WIB**
- Maksimal **1 pesan per debitur per hari**
- Jeda antar pesan: **20–30 detik**
- Hanya hari kerja
- Hindari mengirim di luar jam **08:00–20:00 WIB**

---

## 3. Arsitektur teknis

### a. Pisahkan perhitungan jadwal angsuran

Saat ini logika jadwal ada di `DebiturController::buildAngsuranData()` dan bersifat `private`. Sebelum membuat bot, sebaiknya dipindahkan ke service:

```
app/Services/AngsuranScheduleService.php
```

Tujuan:

- Bot dan halaman detail memakai rumus yang sama.
- Tidak ada duplikasi logika.
- Mudah diuji.

### b. Command penjadwalan

```
app/Console/Commands/SendWhatsappReminders.php
```

Contoh pemakaian:

```bash
php artisan reminders:send --dry-run
php artisan reminders:send --date=2026-10-23
php artisan reminders:send --debitur=123
```

`--dry-run` wajib ada supaya bisa melihat siapa saja yang akan dikirimi pesan tanpa benar-benar mengirim.

### c. Queue job untuk pengiriman

```
app/Jobs/SendWhatsappReminderJob.php
```

Alur:

1. Ambil data terbaru debitur.
2. Cek apakah angsuran sudah dibayar setelah job masuk queue.
3. Cek instance WhatsApp masih `connected`.
4. Cek nomor tidak masuk blacklist/opt-out.
5. Render template.
6. Kirim lewat `EvolutionApiService::sendText()`.
7. Simpan log sukses/gagal.

Queue sudah terkonfigurasi di `.env`:

```env
QUEUE_CONNECTION=database
```

Worker:

```bash
php artisan queue:work --queue=default
```

### d. Scheduler

Tambahkan di `routes/console.php`:

```php
Schedule::command('reminders:send')
    ->dailyAt('09:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();
```

Pastikan cron server menjalankan:

```bash
php artisan schedule:run
```

---

## 4. Tabel database yang disarankan

### `whatsapp_bot_settings`

Menyimpan pengaturan bot:

- `whatsapp_instance_id`
- `is_active`
- `timezone`
- `send_times` (JSON)
- `reminder_rules` (JSON)
- `max_per_day`
- `delay_seconds`
- `active_days` (JSON)
- `quiet_hours_start`, `quiet_hours_end`
- `branch_ids` (JSON, opsional)

### `whatsapp_templates`

- `key` — misalnya `due_reminder`, `overdue_reminder`
- `name`
- `body`
- `is_active`

Contoh variabel template:

```
{{nama}}
{{nomor_debitur}}
{{angsuran_ke}}
{{nominal_angsuran}}
{{tanggal_jatuh_tempo}}
{{hari_terlambat}}
{{total_tunggakan}}
{{nama_cabang}}
{{kontak_cs}}
```

### `whatsapp_reminder_logs`

Tabel paling penting untuk mencegah pesan dobel:

- `debitur_id`
- `pembayaran_angsuran_id`
- `installment_number`
- `reminder_type`
- `reminder_date`
- `phone_number`
- `message`
- `status` — `pending`, `sent`, `failed`, `skipped`, `delivered`, `read`
- `provider_message_id`
- `error_message`
- `sent_at`

Unique index:

```
(debitur_id, installment_number, reminder_type, reminder_date)
```

Dengan begitu, walau command dijalankan dua kali, pesan tidak akan dobel.

### `whatsapp_opt_outs`

Untuk menyimpan nomor yang minta berhenti:

- `phone_number`
- `reason`

---

## 5. Hal penting yang harus dibereskan dulu

### a. Timezone masih UTC

Di `config/app.php`:

```php
'timezone' => 'UTC',
```

Untuk pengingat WIB, jangan mengandalkan `now()` mentah-mentah. Pilih salah satu:

- Set `APP_TIMEZONE=Asia/Jakarta`, atau
- Secara eksplisit gunakan `Carbon::now('Asia/Jakarta')` di service dan scheduler.

Saran: **eksplisit WIB di service bot**, supaya perubahan timezone global tidak mengacaukan data lain.

### b. Tanggal jatuh tempo bisa tidak stabil

Di `buildAngsuranData()` saat ini:

```php
$tglJatuhTempoStart = now()->addDays(1);
```

Jika `tanggal_angsuran_pertama_dibayar` dan `tanggal_jatuh_tempo` sama-sama kosong, tanggal jatuh tempo **berubah setiap hari**. Ini berbahaya untuk bot.

Sebelum bot aktif, perlu audit:

- Berapa debitur approved yang tidak punya `tanggal_angsuran_pertama_dibayar`?
- Berapa yang tidak punya `tanggal_jatuh_tempo`?
- Apakah `tanggal_jatuh_tempo` di sistem berarti jatuh tempo terakhir atau jatuh tempo pertama?

Saran: gunakan `tanggal_akad_kredit + 1 bulan` sebagai fallback, atau wajibkan tanggal jatuh tempo pertama saat approve.

### c. Normalisasi nomor HP

Nomor bisa berupa:

```
08xxxxxxxxxx
+62xxxxxxxxxx
62xxxxxxxxxx
```

Sebelum kirim, normalisasi ke format `62...`. Nomor tidak valid sebaiknya di-skip dan dicatat.

### d. Debitur dengan lebih dari satu pinjaman

Karena ada `previous_loan_id`, satu debitur bisa punya beberapa pinjaman. Jangan kirim pesan terpisah untuk setiap pinjaman ke nomor yang sama. Gabungkan menjadi satu pesan per nomor/debitur.

---

## 6. UI di menu WhatsApp API

Saran struktur menu:

```
WhatsApp API
├── Instance WhatsApp
├── Pengaturan Bot
│   ├── Pengingat Jatuh Tempo
│   ├── Template Pesan
│   ├── Riwayat Pengiriman
│   └── Blacklist / Opt-out
└── ...
```

### Halaman Pengingat Jatuh Tempo

- Toggle aktif/nonaktif
- Pilih instance pengirim
- Jam kirim
- Aturan H- / D+
- Maks pesan per hari
- Jeda antar pesan
- Pilih cabang
- Preview jumlah debitur yang akan dikirimi
- Tombol **Test Kirim ke Nomor Admin**
- Tombol **Dry Run**

### Halaman Template Pesan

- Editor template
- Preview dengan data dummy
- Tombol test kirim

### Halaman Riwayat

- Filter tanggal, status, cabang
- Lihat pesan yang terkirim
- Lihat alasan gagal
- Export CSV

---

## 7. Risiko WhatsApp

Evolution API memakai WhatsApp Web/Baileys, yang **bukan API resmi**. Risiko:

- Nomor bisa dibanned jika kirim massal.
- Pesan identik ke banyak nomor lebih berisiko.
- Nomor baru belum "warm-up".

Mitigasi:

- Rate limit ketat.
- Jeda acak antar pesan.
- Personalisasi pesan.
- Jangan kirim ke nomor invalid.
- Hormati opt-out.
- Kalau volume > 200–300 pesan/hari, pertimbangkan **WhatsApp Cloud API resmi** dengan template yang disetujui.

---

## 7.1 Volume pengiriman per hari

Tidak ada angka "aman" universal untuk Evolution API (WhatsApp Web/Baileys). Rekomendasi konservatif:

| Umur nomor | Saran volume per hari |
|---|---|
| Nomor baru (0–14 hari) | 20–50 pesan/hari |
| Warming (2–4 minggu) | 50–100 pesan/hari |
| Sudah aktif 1–3 bulan | 100–200 pesan/hari |
| Nomor matang (>3 bulan, engagement baik) | 200–300 pesan/hari (maksimum) |

Rekomendasi untuk NSB:

- Mulai **50 pesan/hari** pada minggu pertama.
- Naikkan bertahap: 75 → 100 → 150 → 200.
- **Jangan lebih dari 200 pesan/hari per nomor** untuk Evolution API.
- Jika calon penerima lebih dari 200/hari, gunakan **WhatsApp Cloud API resmi** atau beberapa nomor/instance terpisah per cabang.
- Jeda antar pesan **20–30 detik** (acak).
- Jam kirim **09:00–16:00 WIB**.
- Prioritaskan debitur yang sudah menunggak.
- Hentikan sementara jika failure rate > **5–10%**.

Rumus estimasi:

```
volume harian ≈ jumlah debitur aktif × persentase yang masuk jendela pengingat
```

Contoh:

| Debitur aktif | Masuk jendela/hari | Volume | Catatan |
|---|---|---|---|
| 1.000 | 10% | 100/hari | Aman |
| 3.000 | 10% | 300/hari | Batas atas, sebaiknya official API |
| 5.000 | 10% | 500/hari | Wajib official API / multi-nomor |

Karena aturannya **1 debitur = 1 pesan/hari**, volume harian biasanya jauh lebih kecil dari total debitur.

---

## 7.2 Opt-out otomatis vs bot satu arah

### Bot satu arah

Sistem hanya mengirim pengingat. Balasan debitur tidak diproses.

Contoh:

```
Sistem : "Halo Pak Budi, angsuran Anda jatuh tempo besok..."
Debitur: "STOP"
Sistem : (tidak membaca / tidak merespons)
```

Debitur tetap menerima pesan berikutnya.

Kelebihan: lebih sederhana, tidak perlu webhook pesan masuk.

Kekurangan: debitur yang minta berhenti tetap dikirimi, risiko spam report dan ban lebih tinggi.

### Opt-out otomatis

Sistem membaca pesan masuk. Jika debitur membalas kata tertentu, nomornya otomatis masuk blacklist.

Contoh:

```
Sistem : "Halo Pak Budi, angsuran Anda jatuh tempo besok..."
Debitur: "STOP"
Sistem : "Baik, Bapak tidak akan menerima pengingat lagi."
          → nomor Pak Budi masuk daftar opt-out
```

Kata yang dideteksi:

- `STOP`
- `BERHENTI`
- `JANGAN KIRIM`
- `UNSUBSCRIBE`
- `BATAL`
- `NO`

Kelebihan: lebih sopan, mengurangi risiko ban, ada audit trail.

Kekurangan: perlu menangani webhook `MESSAGES_UPSERT`, tabel opt-out, pencocokan nomor, dan konfirmasi sekali.

### Keputusan saat ini

> **Bot satu arah dulu. Opt-out otomatis ditunda ke Tahap 4.**

Artinya:

- Bot hanya mengirim pengingat.
- Balasan debitur belum diproses otomatis.
- Debitur yang membalas `STOP` belum otomatis berhenti; admin harus menambahkan nomor ke **blacklist manual**.
- Balasan lain tidak dibalas otomatis.

Persiapan minimum sejak awal:

1. Tabel `whatsapp_opt_outs` (`phone_number` unique, `reason`, `created_at`).
2. Menu admin **Blacklist / Opt-out** untuk menambah dan menghapus nomor secara manual.
3. Sebelum kirim pengingat, cek apakah nomor ada di `whatsapp_opt_outs`; jika ada, skip.

Implementasi opt-out otomatis (Tahap 4):

1. Handler webhook `MESSAGES_UPSERT`:
   - Ambil nomor pengirim.
   - Abaikan pesan dari diri sendiri (`fromMe`).
   - Normalisasi nomor.
   - Cek isi pesan.
   - Simpan ke tabel opt-out jika cocok.
   - Kirim konfirmasi sekali.
2. Deteksi kata: `STOP`, `BERHENTI`, `JANGAN KIRIM`, `UNSUBSCRIBE`, `BATAL`, `NO`.

---

## 7.3 Hak akses pengaturan pengiriman

Pengaturan pengiriman **bisa diatur user**, tetapi tidak semua user boleh mengubah semua hal. Saat ini menu WhatsApp API di `config/sidebar.php` masih dibatasi `role => admin`.

Saran pembagian hak akses:

| Role | Boleh mengatur | Tidak boleh |
|---|---|---|
| Admin Pusat / Superadmin | Semua: instance, jadwal, template, batas harian, cabang, test send | — |
| Admin Cabang / SPV | Aktif/nonaktif bot cabang, jam kirim, pilihan aturan H-/D+, pilih template, lihat log cabang | Ganti instance global, ubah batas harian global, ubah template pusat |
| Marketing | Lihat status dan log, mungkin kirim manual | Mengubah jadwal, template, batas harian |
| Debitur | Opt-out (`STOP`/`BERHENTI`) | Mengatur pengiriman |

### Boleh diatur user (business settings)

- Aktif/nonaktif pengingat
- Jam kirim, misalnya `09:00` dan `14:00`
- Aturan pengingat: H-3, H-1, H, D+3, D+7, D+14, D+30
- Template pesan
- Cakupan cabang
- Preview / dry run
- Test kirim ke nomor admin

### Dikunci admin pusat (safety settings)

- Batas maksimal pesan per hari
- Jeda antar pesan
- Jam tenang (misalnya tidak boleh kirim 20:00–08:00)
- Jumlah maksimal pesan per menit
- Instance pengirim
- Aturan opt-out
- Validasi nomor

Batas harian harus **dipaksa di kode**, bukan hanya di tampilan. Contoh: global max 200/hari; cabang hanya boleh memilih 50, 100, atau 150, tidak bisa 1.000.

### Rekomendasi tahap awal

> **Semua pengaturan pengiriman hanya untuk Admin Pusat dulu.**

Setelah stabil 1–2 bulan, buka bertahap:

1. Admin cabang bisa mengatur jam kirim dan aturan H-/D+.
2. Marketing hanya bisa melihat log.
3. Batas harian dan instance tetap admin pusat.

Jadwal pengiriman sebaiknya **terpusat**, bukan per user marketing, agar tidak ada pesan dobel ke debitur yang sama.

---

## 8. Tahapan implementasi

### Tahap 1 — Fondasi

- Refactor `buildAngsuranData()` ke service.
- Audit data tanggal jatuh tempo dan nomor HP.
- Perbaiki timezone.
- Buat unit test perhitungan jadwal.

### Tahap 2 — UI + Dry Run

- Tabel settings, template, log.
- Menu Pengaturan Bot.
- Menu **Blacklist / Opt-out manual**.
- Preview calon penerima.
- Test kirim ke nomor admin.

### Tahap 3 — Pengiriman terbatas

- Command + queue job.
- Kirim hanya ke 1 cabang / nomor internal.
- Monitor log dan respon.

### Tahap 4 — Rollout

- Aktifkan semua cabang.
- Tambah webhook status delivered/read.
- Tambah opt-out otomatis.

---

## 9. Keputusan yang sudah ditentukan

| No | Topik | Keputusan |
|---|---|---|
| 1 | Instance WhatsApp | **Satu instance pusat** |
| 2 | Dasar jatuh tempo | **Jatuh tempo angsuran bulanan** (angsuran belum dibayar paling awal) |
| 3 | Jadwal pengingat | **Pakai default**: H-3, H-1, H, D+3, D+7, D+14, D+30 |
| 4 | Isi pesan | **Boleh menyebut** nominal tunggakan, denda, dan nomor rekening |
| 5 | Balasan masuk | **Bot satu arah dulu**; opt-out otomatis ditunda ke Tahap 4 |
| 6 | Volume per hari | **Ikuti rekomendasi**: mulai 50/hari, maksimal 200/hari per nomor |
| 7 | Hak akses | **Semua pengaturan hanya Admin Pusat** |

### Catatan keputusan

- Karena masih **bot satu arah**, debitur yang membalas `STOP` belum otomatis berhenti. Sediakan menu **Blacklist manual** sejak awal agar admin bisa menambahkan nomor secara manual.
- Opt-out otomatis tetap direncanakan pada **Tahap 4** setelah pengiriman stabil.
- Semua pengaturan pengiriman (jadwal, template, batas harian, instance) dikelola **Admin Pusat**.
- Admin cabang dan marketing hanya dapat melihat status/log (jika nanti dibutuhkan).

---

## 10. Status implementasi

### Tahap 1 — Fondasi: selesai

| Komponen | Keterangan |
|---|---|
| `app/Services/AngsuranScheduleService.php` | Logika jadwal dipindah dari controller, memakai timezone `Asia/Jakarta`, anchor tanggal stabil, plus `total_tunggakan` dan `oldest_unpaid`. |
| `app/Services/PhoneNumberService.php` | Normalisasi nomor ke `62...` dan validasi. |
| `app/Console/Commands/AuditReminderData.php` | Command `php artisan reminders:audit`. |
| `config/whatsapp.php` | Timezone, jendela kirim, batas harian, jeda, aturan H-/D+. |
| `DebiturController` | Tidak lagi menyimpan logika jadwal; memakai service. |
| Unit test | `AngsuranScheduleServiceTest`, `PhoneNumberServiceTest` — 18 test lulus. |

### Hasil audit data (23 Oktober 2026)

| Metrik | Jumlah |
|---|---|
| Debitur approved | 718 |
| Aktif (belum lunas) | 669 |
| Lunas | 49 |
| Siap dikirim (tanggal `high` + HP valid) | 643 |
| Perlu review | 75 |
| Tanpa tanggal angsuran pertama / jatuh tempo | 26 |
| Nomor HP tidak valid | 0 |
| Grup nomor duplikat | 3 |

Sumber tanggal jatuh tempo: 692 dari `tanggal_angsuran_pertama_dibayar`, 26 dari pembayaran/`created_at` (reliability `medium`).

Catatan: 230 record punya arti `tanggal_jatuh_tempo` yang tidak konsisten (kadang jatuh tempo terakhir, kadang pertama). Karena itu service memprioritaskan `tanggal_angsuran_pertama_dibayar`.

### Tahap 2 — Data layer + UI + dry run: selesai

- Migrasi: `whatsapp_bot_settings`, `whatsapp_templates`, `whatsapp_reminder_logs`, `whatsapp_opt_outs`.
- Model: `WhatsappBotSetting`, `WhatsappTemplate`, `WhatsappReminderLog`, `WhatsappOptOut`.
- `app/Services/WhatsappReminderService.php`: cari calon penerima, dedup per nomor, pilih milestone, hormati opt-out, batas harian, render template.
- `app/Jobs/SendWhatsappReminderJob.php`: kirim + log status (hanya dipakai saat `--send`).
- Command: `php artisan reminders:send --dry-run --date=2026-10-23`.
- Seeder: template default `due_reminder` dan `overdue_reminder`; setting bot masih **nonaktif**.
- UI Admin Pusat di menu **WhatsApp API**:
  - `Dashboard Bot` (`whatsapp/bot/dashboard`) — status bot, ringkasan hari ini, tren 7 hari, jenis pengingat, dan aktivitas terbaru.
  - `Pengaturan Bot` (`whatsapp/bot`) — instance, jam kirim, batas harian, jeda, hari aktif, jadwal H-/D+, data bank/CS, aktif/nonaktif, pratinjau dry-run.
  - `Template Pesan` (`whatsapp/bot/templates`) — edit isi pesan.
  - `Riwayat Pengingat` (`whatsapp/bot/logs`) — filter tanggal/status/nomor.
  - `Blacklist Nomor` (`whatsapp/bot/opt-outs`) — tambah/hapus opt-out manual.
  - Form **Test Kirim** di halaman Pengaturan Bot.
- Test kirim manual ke **081390844197** (`6281390844197`) via instance `Kheling` berhasil (HTTP 201).

### Tahap 3 — Otomatisasi: selesai (siap diaktifkan)

- `routes/console.php` menjadwalkan:
  - `reminders:send --send` setiap hari **09:00 WIB** (aman karena tetap ditolak selama bot nonaktif).
  - `queue:work --stop-when-empty --max-time=50` setiap menit sebagai fallback tanpa worker permanen.
- `WebhookController` sekarang:
  - Menyinkronkan status pesan (`pending/sent/delivered/read`) ke `whatsapp_reminder_logs` dari event `messages.update`.
  - Menangkap balasan masuk berisi `STOP`/`BERHENTI`/`UNSUBSCRIBE` dari event `messages.upsert` dan otomatis menambahkannya ke blacklist (`source = auto`).
- Verifikasi webhook: status teks & numerik, opt-out masuk, dan pesan keluar (`fromMe`) tidak dianggap opt-out — semua lolos.

### Belum dikerjakan

- Meminta konfirmasi sebelum mengaktifkan bot dan menjalankan pengiriman harian.
- Memantau hasil pengiriman pertama lewat halaman Riwayat.

### Cara menjalankan scheduler

Windows/Laragon tidak punya cron bawaan. Pilih salah satu:

- Saat pengembangan: `php artisan schedule:work` (biarkan terminal terbuka).
- Produksi: buat Task Scheduler tiap 1 menit yang menjalankan `php artisan schedule:run`.

Kalau menjalankan worker permanen, hapus baris `queue:work` dari `routes/console.php` agar tidak dobel.

### Catatan penting

- Bot masih `is_active = false`. Command `--send` akan menolak jalan sampai diaktifkan.
- Milestone `D+30` dikirim **sekali per angsuran**. Tunggakan yang lebih dari 30 hari dan sudah pernah menerima `D+30` tidak akan mendapat pengingat lanjutan kecuali ditambah aturan baru.
- Queue worker harus berjalan: `php artisan queue:work --queue=default`.

