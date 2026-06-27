# Dokumentasi ERD & LRS - CleanUp Shoes

Dokumen ini berisi rancangan basis data lengkap berupa **Entity Relationship Diagram (ERD)** dengan **Notasi Chen** (sesuai standar akademik/buku teks) dan **Logical Record Structure (LRS)** untuk aplikasi **CleanUp Shoes**.

---

## 1. Entity Relationship Diagram (ERD) - Notasi Chen

Berikut adalah diagram ERD dengan notasi Chen klasik (Persegi Panjang sebagai Entitas, Belah Ketupat sebagai Relasi, dan Oval sebagai Atribut) berlatar belakang terang sesuai contoh yang Anda berikan:

![ERD Notasi Chen CleanUp Shoes](C:/Users/ACER/.gemini/antigravity-ide/brain/36a50106-0e13-467e-9a9b-26b559f8f4f1/cleanup_classic_erd_1781668857158.png)

### Diagram ERD Teknis Interaktif (Mermaid)
Jika Anda membutuhkan representasi Crow's Foot ERD yang interaktif untuk pemrograman/database, berikut adalah diagram Mermaid-nya:

```mermaid
erDiagram
    USERS {
        bigint id PK "Kunci Utama"
        string name "Nama Pengguna"
        string email "Alamat Email"
        string phone "Nomor Telepon"
        string address "Alamat Utama"
        string latitude "Garis Lintang GPS"
        string longitude "Garis Bujur GPS"
        string kecamatan "Kecamatan"
        string postal_code "Kode Pos"
        string password "Kata Sandi terenkripsi"
        string password_plain "Kata Sandi teks biasa"
        string role "Peran: pelanggan, karyawan, admin, pemilik"
        string google_id "ID Akun Google"
        timestamp email_verified_at "Waktu Verifikasi Email"
        string remember_token "Token Ingat Saya"
        timestamp created_at "Waktu Dibuat"
        timestamp updated_at "Waktu Diperbarui"
    }

    USER_ADDRESSES {
        bigint id PK "Kunci Utama"
        bigint user_id FK "Kunci Asing ke Users"
        string recipient_name "Nama Penerima"
        string phone "Nomor Telepon Penerima"
        string address_label "Label Alamat (Rumah/Kantor)"
        string province "Provinsi"
        string city "Kota/Kabupaten"
        string kecamatan "Kecamatan"
        string village "Kelurahan/Desa"
        string postal_code "Kode Pos"
        text full_address "Alamat Lengkap"
        string address_landmark "Patokan Alamat"
        string latitude "Garis Lintang GPS"
        string longitude "Garis Bujur GPS"
        boolean is_main_address "Apakah Alamat Utama"
        timestamp created_at "Waktu Dibuat"
        timestamp updated_at "Waktu Diperbarui"
    }

    SERVICES {
        bigint id PK "Kunci Utama"
        string name "Nama Layanan"
        string description "Deskripsi Layanan"
        decimal price "Harga Layanan"
        string estimated_time "Estimasi Durasi Kerja"
        string category "Kategori Cuci"
        string icon "Nama Ikon"
        string image "Gambar Layanan"
        timestamp created_at "Waktu Dibuat"
        timestamp updated_at "Waktu Diperbarui"
    }

    ORDERS {
        bigint id PK "Kunci Utama"
        string group_id "ID Grup Pesanan"
        bigint user_id FK "Kunci Asing ke Users (Pelanggan)"
        bigint service_id FK "Kunci Asing ke Services"
        bigint employee_id FK "Kunci Asing ke Users (Karyawan)"
        json additional_services "Layanan Tambahan (Format JSON)"
        string processing_speed "Kecepatan Proses (Reguler/Ekspres)"
        string order_number "Nomor Pesanan"
        string queue_number "Nomor Antrean"
        string status "Status Pengerjaan"
        decimal total_price "Total Harga"
        decimal delivery_fee "Ongkos Kirim"
        string payment_method "Metode Pembayaran (Cash/Transfer)"
        string payment_status "Status Pembayaran"
        string status_pembayaran "Detail Status Pembayaran"
        string snap_token "Token Midtrans Snap"
        string payment_proof "Bukti Transfer Pembayaran"
        text complaint "Komplain Pelanggan"
        text handling_notes "Catatan Pengerjaan Karyawan"
        string photo_before "Foto Sepatu Sebelum Cuci"
        string photo_before_2 "Foto Sepatu Sebelum Cuci 2"
        string photo_after "Foto Sepatu Setelah Cuci"
        datetime reception_date "Tanggal Penerimaan Sepatu"
        datetime completion_date "Tanggal Selesai Cuci"
        int rating "Bintang Penilaian"
        text review "Ulasan Pelanggan"
        string shoe_name "Nama/Merk Sepatu"
        string shoe_size "Ukuran Sepatu"
        string storage_location "Rak/Lokasi Penyimpanan"
        boolean is_delivery "Apakah Layanan Antar-Jemput"
        text delivery_address "Alamat Pengiriman"
        int shoe_quantity "Jumlah Pasang Sepatu"
        string latitude "Garis Lintang Pengiriman"
        string longitude "Garis Bujur Pengiriman"
        decimal cash_amount "Jumlah Uang Tunai"
        decimal change_amount "Jumlah Uang Kembalian"
        timestamp created_at "Waktu Dibuat"
        timestamp updated_at "Waktu Diperbarui"
    }

    ATTENDANCES {
        bigint id PK "Kunci Utama"
        bigint user_id FK "Kunci Asing ke Users (Karyawan)"
        date date "Tanggal Absensi"
        time clock_in "Jam Masuk"
        time clock_out "Jam Pulang"
        timestamp created_at "Waktu Dibuat"
        timestamp updated_at "Waktu Diperbarui"
    }

    LOANS {
        bigint id PK "Kunci Utama"
        bigint user_id FK "Kunci Asing ke Users (Karyawan)"
        decimal amount "Jumlah Kasbon"
        text reason "Alasan Pinjam"
        string status "Status Persetujuan"
        text admin_note "Catatan Admin/Owner"
        timestamp created_at "Waktu Dibuat"
        timestamp updated_at "Waktu Diperbarui"
    }

    FINANCES {
        bigint id PK "Kunci Utama"
        string type "Jenis: Pemasukan / Pengeluaran"
        string category "Kategori Keuangan"
        decimal amount "Jumlah Uang"
        text description "Keterangan Keuangan"
        date date "Tanggal Transaksi"
        timestamp created_at "Waktu Dibuat"
        timestamp updated_at "Waktu Diperbarui"
    }

    INVENTORIES {
        bigint id PK "Kunci Utama"
        string name "Nama Barang/Bahan Baku"
        int stock "Jumlah Stok"
        string unit "Satuan Ukur (Liter/Pcs)"
        int min_stock "Batas Stok Minimum"
        timestamp created_at "Waktu Dibuat"
        timestamp updated_at "Waktu Diperbarui"
    }

    SETTINGS {
        bigint id PK "Kunci Utama"
        string key "Nama Pengaturan/Kunci"
        text value "Nilai Pengaturan"
        timestamp created_at "Waktu Dibuat"
        timestamp updated_at "Waktu Diperbarui"
    }

    %% Hubungan/Relasi
    USERS ||--o{ USER_ADDRESSES : "memiliki daftar alamat"
    USERS ||--o{ ORDERS : "melakukan pemesanan (sebagai customer)"
    USERS ||--o{ ORDERS : "memproses pesanan (sebagai employee)"
    USERS ||--o{ ATTENDANCES : "melakukan pencatatan absen"
    USERS ||--o{ LOANS : "mengajukan kasbon"
    SERVICES ||--o{ ORDERS : "dipilih dalam pemesanan"
```

---

## 2. Logical Record Structure (LRS)

LRS (Logical Record Structure) menggambarkan struktur record logis pada tabel-tabel database dengan memperlihatkan relasi kunci utama (Primary Key) ke kunci asing (Foreign Key) secara jelas.

### Visualisasi Konseptual LRS
Berikut adalah visualisasi konseptual dari diagram LRS aplikasi Anda:

![Konsep LRS CleanUp Shoes](C:/Users/ACER/.gemini/antigravity-ide/brain/36a50106-0e13-467e-9a9b-26b559f8f4f1/cleanup_shoes_lrs_1781668611955.png)

### Skema Relasi Tertulis
Relasi struktur tabel secara logis (garis bawah menunjukkan **Primary Key**, tanda bintang/miring menunjukkan **Foreign Key**):

1. **users**
   * ( <u>id</u>, name, email, phone, address, latitude, longitude, kecamatan, postal_code, password, password_plain, role, google_id, email_verified_at, remember_token, created_at, updated_at )

2. **user_addresses**
   * ( <u>id</u>, *user_id* (FK → users.id), recipient_name, phone, address_label, province, city, kecamatan, village, postal_code, full_address, address_landmark, latitude, longitude, is_main_address, created_at, updated_at )

3. **services**
   * ( <u>id</u>, name, description, price, estimated_time, category, icon, image, created_at, updated_at )

4. **orders**
   * ( <u>id</u>, group_id, *user_id* (FK → users.id), *service_id* (FK → services.id), *employee_id* (FK → users.id), additional_services, processing_speed, order_number, queue_number, status, total_price, delivery_fee, payment_method, payment_status, status_pembayaran, snap_token, payment_proof, complaint, handling_notes, photo_before, photo_before_2, photo_after, reception_date, completion_date, rating, review, shoe_name, shoe_size, storage_location, is_delivery, delivery_address, shoe_quantity, latitude, longitude, cash_amount, change_amount, created_at, updated_at )

5. **attendances**
   * ( <u>id</u>, *user_id* (FK → users.id), date, clock_in, clock_out, created_at, updated_at )

6. **loans**
   * ( <u>id</u>, *user_id* (FK → users.id), amount, reason, status, admin_note, created_at, updated_at )

7. **finances**
   * ( <u>id</u>, type, category, amount, description, date, created_at, updated_at )

8. **inventories**
   * ( <u>id</u>, name, stock, unit, min_stock, created_at, updated_at )

9. **settings**
   * ( <u>id</u>, key, value, created_at, updated_at )
