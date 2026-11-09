# 🏨 Booking Ruangan Native

Aplikasi reservasi/booking ruangan berbasis **PHP Native + PDO**. Fokus pada **logika bisnis** — cek konflik jadwal, validasi tanggal, dan query yang lebih pintar. Bukan sekadar CRUD.

> Project ini adalah **fase native** dari rencana 2 fase. Fase Laravel akan menyusul di repo terpisah.

---

## ✨ Fitur

### Sudah Selesai ✅

- [x] Setup database + struktur folder (`public/` sebagai document root)
- [x] Helper functions (`e`, `redirect`, `set_flash`, `get_flash`, validasi dasar)
- [x] CRUD Ruangan (index, create, store, edit, update, delete)
- [x] CRUD User (dengan validasi email unik)
- [x] CRUD Booking (index, create, store, edit, update, delete)
- [x] Flash message (partial view, sekali tampil)
- [x] Proteksi SQL injection (prepared statement)
- [x] Proteksi XSS (`e()` helper)
- [x] Foreign key RESTRICT (cegah hapus data yang masih dipakai)

### Belum Selesai ⏳

- [ ] **Cek konflik jadwal** ⭐ (inti project)
- [ ] Validasi tanggal (tidak boleh masa lalu)
- [ ] Filter booking by ruangan & tanggal
- [ ] Kalender sederhana (tabel per tanggal)
- [ ] CSS polish + responsive
- [ ] README perbandingan native vs Laravel

---

## 🗂️ Struktur Folder

```text
booking-native/
├── config/
│   └── database.php          # koneksi PDO
├── helpers/
│   └── functions.php         # escape, redirect, flash, validasi
├── views/
│   ├── layouts.php           # layout utama (header + footer)
│   └── flash.php             # partial flash message
├── public/                   # document root
│   ├── index.php             # dashboard
│   ├── css/
│   │   └── style.css
│   ├── rooms/                # CRUD ruangan
│   │   ├── index.php
│   │   ├── create.php
│   │   ├── store.php
│   │   ├── edit.php
│   │   ├── update.php
│   │   └── delete.php
│   ├── users/                # CRUD user
│   │   ├── index.php
│   │   ├── create.php
│   │   ├── store.php
│   │   ├── edit.php
│   │   ├── update.php
│   │   └── delete.php
│   └── bookings/             # CRUD booking
│       ├── index.php
│       ├── create.php
│       ├── store.php
│       ├── edit.php
│       ├── update.php
│       └── delete.php
├── database.sql              # skrip setup + seed
└── README.md
```

> **Kenapa `public/`?** File konfigurasi & logika di luar `public/` tidak bisa diakses langsung lewat URL — lebih aman. Pola ini sama dengan Laravel.

---

## 🗄️ Database

Nama database: **`booking_ruangan`**

### Tabel

| Tabel | Kolom Utama | Keterangan |
|-------|-------------|------------|
| `rooms` | `id`, `nama`, `kapasitas`, `lokasi`, `fasilitas` | Data ruangan |
| `users` | `id`, `nama`, `email`, `telepon` | Data user (tanpa password — fokus logika booking) |
| `bookings` | `id`, `room_id`, `user_id`, `judul`, `deskripsi`, `tanggal`, `jam_mulai`, `jam_selesai`, `status` | Data booking |

### Relasi

```text
rooms (1) ───── (N) bookings (N) ───── (1) users
```

- Satu ruangan punya banyak booking
- Satu user punya banyak booking
- Satu booking milik satu ruangan dan satu user

### Enum Status

`pending` → `dikonfirmasi` → `selesai`  
`pending` → `dibatalkan`

### Foreign Key

```sql
FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE RESTRICT
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
```

`RESTRICT` = **tolak hapus** ruangan/user yang masih punya booking. Mencegah data booking hilang diam-diam (yang bisa menyebabkan double booking tak terdeteksi).

### Index untuk Cek Konflik

```sql
KEY idx_room_date (room_id, tanggal)
```

Query cek konflik selalu filter `room_id = ? AND tanggal = ?` — index komposit ini bikin query cepat.

---

## 🚀 Cara Menjalankan

### Prasyarat

- PHP 8.0+ (dengan ekstensi `pdo_mysql`)
- MySQL / MariaDB (Laragon / XAMPP)
- Git (opsional)

### Langkah

1. **Clone repo**

   ```bash
   git clone https://github.com/USERNAME/booking-native.git
   cd booking-native
   ```

2. **Import database**

   Via HeidiSQL: **File → Run SQL file...** → pilih `database.sql`

   Atau via CLI:

   ```bash
   mysql -u root < database.sql
   ```

3. **Sesuaikan koneksi**

   Edit `config/database.php`:

   ```php
   $DB_HOST = 'localhost';
   $DB_NAME = 'booking_ruangan';
   $DB_USER = 'root';
   $DB_PASS = '';          // sesuaikan
   ```

4. **Akses di browser**

   ```
   http://localhost/booking-native/public/
   ```

   Atau, kalau DocumentRoot sudah diarahkan ke `public/`:

   ```
   http://booking-native.test/
   ```

---

## 🧠 Konsep yang Dipelajari

| Konsep | Implementasi |
|--------|--------------|
| **Prepared statement** | Semua query pakai `$pdo->prepare()` + `execute([...])` — cegah SQL injection |
| **Output escaping** | Semua output dari DB/user pakai `e()` — cegah XSS |
| **PRG pattern** | POST → Redirect → GET — cegah resubmit saat refresh |
| **Flash message** | `set_flash()` + `get_flash()` — pesan sekali tampil |
| **Old input** | Simpan ke `$_SESSION['old']` saat validasi gagal — form tidak kosong |
| **Integritas referensial** | Foreign key `RESTRICT` — database menolak hapus data yang masih dipakai |
| **Validasi email unik** | Cek di PHP + UNIQUE constraint di DB (2 lapis) |
| **Validasi unik saat update** | `WHERE email = ? AND id != ?` — kecualikan diri sendiri |

### Konsep yang Akan Datang ⭐

| Konsep | Rencana |
|--------|---------|
| **Query overlap** | `WHERE jam_mulai < ? AND jam_selesai > ?` — deteksi bentrok |
| **Validasi tanggal** | `strtotime($tanggal) < strtotime(date('Y-m-d'))` — tolak masa lalu |
| **Filter tanggal** | `WHERE tanggal = ?` / `BETWEEN ? AND ?` |
| **Kalender** | Tabel per tanggal (loop 2 dimensi) |

---

## 🛡️ Keamanan

| Ancaman | Proteksi |
|---------|----------|
| **SQL Injection** | Prepared statement di semua query |
| **XSS** | `e()` = `htmlspecialchars()` di semua output |
| **CSRF** | ⚠️ Belum — akan ditambahkan (token di form) |
| **Akses langsung `store.php`** | Cek `$_SERVER['REQUEST_METHOD'] === 'POST'` |
| **Hapus via GET** | Delete hanya via POST + konfirmasi |
| **File config terekspos** | `config/`, `helpers/`, `views/` di luar `public/` |

---

## 🗺️ Roadmap

### Fase 1 — Native (sedang berjalan)

- [x] Setup database + struktur folder
- [x] Helper functions
- [x] CRUD Ruangan
- [x] CRUD User
- [x] CRUD Booking
- [ ] **Cek konflik jadwal** ⭐
- [ ] Validasi tanggal
- [ ] Filter + status
- [ ] Kalender sederhana
- [ ] CSS + README final
- [ ] Push GitHub

### Fase 2 — Laravel (nanti)

- [ ] Migration + Model + Relasi
- [ ] Controller + Route resource
- [ ] Form Request (validasi)
- [ ] Cek konflik dengan Eloquent
- [ ] Blade + layout
- [ ] Kalender (FullCalendar.js)
- [ ] Email notifikasi (Mailtrap)
- [ ] README perbandingan

---

## 🛠️ Tech Stack

- **PHP Native** 8.0+ (tanpa framework)
- **MySQL** via **PDO**
- **HTML + CSS** (tanpa JS framework)
- **Git** untuk version control

---

## 📄 Lisensi

MIT — bebas dipakai untuk belajar.

---

## 👤 Author

**Wisnu Wibisono**  
GitHub: [@username](https://github.com/wwibi11)