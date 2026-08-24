# SkillPath AI

SkillPath AI adalah aplikasi web yang membantu mahasiswa memahami kemampuan yang sudah dikuasai, melihat bagian yang masih perlu diperkuat, dan menentukan urutan belajar berdasarkan jurusan yang sedang dijalani.

Website dapat diakses melalui:

https://skillpath-ai.my.id/

## Tentang SkillPath AI

Belajar sering terasa membingungkan ketika kita tidak tahu harus mulai dari mana. SkillPath AI dibuat untuk membantu menjawab masalah tersebut.

Alurnya dimulai dengan memilih jurusan dan mengisi profil belajar. Setelah itu, mahasiswa mengerjakan assesment awal sesuai jurusan. Hasil assesment digunakan untuk membuat peta kemampuan dan menyusun jalur belajar berdasarkan kemampuan yang paling perlu dikembangkan.

Mahasiswa kemudian dapat mempelajari materi, mengerjakan latihan, mencatat perkembangan, menyelesaikan evaluasi berbasis bukti, dan mengerjakan proyek yang sesuai dengan kemampuan mereka.

SkillPath AI juga menyediakan bantuan AI untuk menjelaskan hasil, membuat variasi latihan, merangkum perkembangan belajar, dan memberikan umpan balik pada proyek.

AI tidak menentukan nilai assesment atau keputusan utama sistem. Perhitungan kemampuan, kelulusan evaluasi, kesiapan proyek, dan penyusunan jalur belajar tetap dikendalikan oleh aturan aplikasi.

## Jurusan yang tersedia

Saat ini SkillPath AI menyediakan enam jurusan:

- Sistem Informasi
- Manajemen
- Teknik Informatika
- Sistem Komputer
- Psikologi
- Ilmu Komunikasi

Setiap jurusan memiliki tiga bidang utama dan sembilan kemampuan.

Secara keseluruhan, SkillPath AI menggunakan 54 kemampuan akademik sebagai dasar assesment dan jalur belajar.

## Fitur utama

### Profil belajar

Mahasiswa dapat mengisi informasi seperti semester, bidang yang ingin dipelajari lebih dalam, pengalaman sebelumnya, dan jumlah waktu belajar yang tersedia setiap minggu.

Informasi ini membantu SkillPath menyusun pengalaman belajar yang lebih sesuai dengan kondisi pengguna.

### Assesment awal

Setiap jurusan memiliki sembilan pertanyaan yang mewakili sembilan kemampuan utama.

Hasil assesment digunakan untuk mengetahui kemampuan yang sudah kuat dan kemampuan yang masih perlu dikembangkan.

### Peta kemampuan

SkillPath membandingkan nilai kemampuan pengguna dengan target penguasaan setiap kemampuan pada jurusan yang dipilih.

Kemampuan dengan selisih terbesar akan mendapatkan prioritas lebih tinggi.

### Jalur belajar

Jalur belajar disusun berdasarkan hasil assesment dan kebutuhan penguatan pengguna.

Materi yang paling penting dapat ditempatkan lebih awal. Urutan belajar juga dapat berubah setelah pengguna menyelesaikan evaluasi atau mengalami perubahan kemampuan.

### Materi dan evaluasi

Setiap materi dapat berisi:

- ringkasan materi;
- tujuan pembelajaran;
- referensi;
- latihan praktik;
- pertanyaan evaluasi;
- bukti praktik;
- refleksi hasil belajar.

Materi hanya dianggap benar-benar selesai setelah pengguna memenuhi aturan evaluasi yang ditentukan sistem.

Jika evaluasi belum berhasil, SkillPath dapat menambahkan materi penguatan sebelum pengguna mencoba kembali materi utama.

### Proyek

Proyek digunakan untuk menerapkan kemampuan yang sudah dipelajari.

SkillPath menghitung kesiapan pengguna berdasarkan kemampuan yang dibutuhkan masing-masing proyek.

Proyek kemudian dikelompokkan menjadi proyek yang bisa dikerjakan sekarang, proyek yang masih membutuhkan penguatan, dan proyek yang dapat dipilih sebagai tantangan.

### Riwayat perkembangan

Pengguna dapat melihat riwayat:

- assesment;
- aktivitas belajar;
- evaluasi;
- proyek;
- perubahan jalur belajar;
- kesiapan belajar.

Dengan begitu, perkembangan tidak hanya dilihat dari satu nilai terakhir.

### Pendamping Belajar AI

Jika layanan AI dikonfigurasi, SkillPath dapat memberikan:

- penjelasan hasil kemampuan;
- ringkasan perkembangan;
- saran pembagian waktu belajar;
- analisis kendala belajar;
- variasi latihan;
- umpan balik proyek.

AI hanya menggunakan data yang sudah tersedia di aplikasi dan tidak menggantikan logika assesment utama.

### Halaman administrator

Administrator dapat mengelola data utama SkillPath, termasuk:

- jurusan;
- kemampuan;
- hubungan prasyarat;
- assesment;
- pertanyaan assesment;
- materi belajar;
- proyek;
- masukan pengguna.

Akun dengan izin khusus juga dapat mengelola peran pengguna.

## Teknologi yang digunakan

### Backend

- PHP 8.4+
- Laravel 13
- PostgreSQL
- Inertia.js
- Laravel Fortify
- Laravel Wayfinder
- Resend

### Frontend

- React 19
- TypeScript
- Tailwind CSS 4
- Vite 8
- Recharts
- Lucide React
- Radix UI

### Pemeriksaan kualitas kode

Project menggunakan:

- PHPUnit
- Larastan / PHPStan
- Laravel Pint
- ESLint
- Prettier
- TypeScript type checking

### Integrasi AI

SkillPath mendukung:

- Google Gemini
- OpenRouter

Integrasi AI bersifat opsional. Fitur utama SkillPath tetap dapat berjalan tanpa respons AI.

## Menjalankan project di komputer lokal

Pastikan komputer sudah memiliki:

- PHP 8.4.1 atau versi yang lebih baru;
- Composer;
- Node.js dan npm;
- PostgreSQL.

Clone repository:

```bash
git clone https://github.com/ki1bot/skillpath-ai.git
cd skillpath-ai
```
