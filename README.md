# Hafiz Monitor

**Hafiz Monitor** adalah aplikasi web untuk **tracking hapalan Al-Qur’an per ayat** dengan sistem skoring komprehensif. Aplikasi ini membantu guru/ustadz/mentor dalam menilai, memantau, dan mendokumentasikan perkembangan hafalan santri secara lebih objektif dan terstruktur.  

## ✨ Fitur Utama  

- 📖 **Tracking Per Ayat**  
  Setiap hafalan dinilai pada level ayat, bukan sekadar surah, sehingga penilaian lebih detail dan spesifik.  

- 📝 **Sistem Skoring Berbobot**  
  Skoring dilakukan per ayat, tetapi **dengan bobot jumlah kata**. Ayat yang panjang akan lebih mempengaruhi nilai total jika ada kesalahan dibanding ayat yang pendek.  

- 👨‍👩‍👧‍👦 **Support Multi Hafidz**  
  Mendukung banyak santri/hafidz dalam satu aplikasi, dengan profil dan riwayat masing-masing.  

- 📊 **Riwayat Penilaian Hafalan**  
  Semua penilaian tersimpan, sehingga bisa ditinjau kembali untuk evaluasi perkembangan.  

- 📈 **Grafik Perkembangan Hafalan**  
  Menyajikan laporan visual perkembangan hafalan setiap santri dari waktu ke waktu.  

## 🛠️ Teknologi yang Digunakan  

- **Frontend:** Vue 3 + Inertia.js (SPA, reactive, mobile-friendly)  
- **Backend:** Laravel (API + Inertia)  
- **Database:** MySQL / MariaDB  
- **Autentikasi:** Session-based cookie (default Laravel)  

## 🚀 Instalasi  

1. **Clone repository**  
   ```bash
   git clone https://github.com/username/hafiz-monitor.git
   cd hafiz-monitor
   ```

2. **Setup Aplikasi (Laravel + Inertia)**  
   ```bash
   cp .env.example .env
   composer install
   npm install
   php artisan key:generate
   php artisan migrate --seed
   npm run dev
   php artisan serve
   ```

3. **Akses aplikasi**  
   Buka browser dan arahkan ke `http://localhost:8000`.  

## 📂 Struktur Proyek  

```
hafiz-monitor/
│── app/           # Laravel (controller, model, service)
│── database/      # Migrations & seeders
│── resources/     # Vue + Inertia frontend
│── routes/        # Web & API routes
│── public/        # Asset publik
└── docs/          # Dokumentasi tambahan
```

## 📖 Panduan Penggunaan  

1. Login sebagai **Admin/Ustadz**.  
2. Tambahkan data **Hafidz/Santri**.  
3. Pilih ayat yang dihafal, lalu lakukan penilaian dengan sistem skoring berbobot.  
4. Lihat riwayat penilaian untuk setiap hafidz.  
5. Pantau perkembangan melalui **grafik progres**.  

## 🎯 Roadmap  

- [ ] Modul notifikasi pengingat hafalan  
- [ ] Integrasi audio untuk memantau tajwid  
- [ ] Export laporan ke PDF/Excel  
- [ ] Dashboard per kelas/grup santri  

## 🤝 Kontribusi  

Kontribusi terbuka untuk siapa saja.  
Silakan fork repository, buat branch baru, lalu kirimkan pull request.  

## 📜 Lisensi  

Proyek ini dirilis di bawah lisensi **MIT**.  
Silakan lihat file [LICENSE](LICENSE) untuk detail lebih lanjut.  
