# SkillPath AI

SkillPath AI adalah aplikasi web yang membantu mahasiswa memahami kemampuan yang sudah mereka miliki, menemukan bagian yang masih perlu diperkuat, lalu menyusun jalur belajar yang lebih terarah berdasarkan jurusan dan hasil assessment.

Aplikasi ini tidak hanya memberikan daftar materi. SkillPath AI menyimpan perkembangan pengguna, menghitung kesenjangan kemampuan, menentukan prioritas belajar, menyediakan evaluasi, merekomendasikan proyek, dan menggunakan AI untuk membantu menjelaskan hasil yang sudah dihitung oleh sistem.

**Website:**
https://skillpath-ai.my.id

**Repository:**
https://github.com/ki1bot/skillpath-ai

## Tentang SkillPath AI

Tidak semua mahasiswa kesulitan belajar karena kekurangan materi. Sering kali masalahnya justru lebih sederhana: tidak tahu kemampuan mana yang sudah cukup baik, kemampuan mana yang masih kurang, dan apa yang sebaiknya dipelajari lebih dahulu.

SkillPath AI dibuat untuk membantu menjawab masalah tersebut.

Alur dasarnya dimulai ketika pengguna memilih jurusan dan mengisi profil belajar. Setelah itu, pengguna mengikuti assessment awal yang sesuai dengan jurusannya.

Hasil assessment digunakan untuk membentuk nilai kemampuan pengguna. Sistem kemudian membandingkan nilai tersebut dengan target kemampuan yang tersedia dan menghitung bagian mana yang masih memiliki kesenjangan.

Dari hasil tersebut, SkillPath AI dapat menyusun prioritas belajar, memberikan materi, mencatat perkembangan, melakukan evaluasi, memberikan materi penguatan jika diperlukan, dan membantu pengguna memilih proyek yang sesuai dengan kesiapan mereka.

AI digunakan sebagai pendamping untuk menjelaskan data dan memberikan masukan tambahan. Keputusan utama seperti nilai assessment, kelulusan evaluasi, kesiapan proyek, dan perubahan progres tetap ditentukan oleh logika aplikasi.

## Alur Penggunaan

Secara umum, alur penggunaan SkillPath AI adalah:

1. Membuat akun dan masuk ke aplikasi.
2. Mengisi profil belajar dan memilih jurusan.
3. Mengerjakan assessment awal.
4. Sistem menghitung kemampuan pengguna.
5. Sistem mencari kesenjangan antara kemampuan saat ini dan target.
6. Pengguna melihat prioritas kemampuan yang perlu diperkuat.
7. Sistem menyusun jalur belajar.
8. Pengguna mempelajari materi yang tersedia.
9. Pengguna mencatat aktivitas dan progres belajar.
10. Pengguna mengerjakan evaluasi materi.
11. Jika belum memenuhi hasil yang dibutuhkan, sistem dapat memberikan materi penguatan.
12. Pengguna dapat mengerjakan proyek untuk menerapkan kemampuan yang sudah dipelajari.
13. Perkembangan dapat dipantau kembali melalui dashboard dan halaman progres.

## Jurusan yang Tersedia

SkillPath AI saat ini menyediakan enam jurusan:

1. Sistem Informasi
2. Manajemen
3. Teknik Informatika
4. Sistem Komputer
5. Psikologi
6. Ilmu Komunikasi

Setiap jurusan memiliki:

- 3 bidang utama;
- 9 kemampuan;
- target kemampuan yang digunakan dalam pemetaan dan assessment.

Secara keseluruhan terdapat **54 kemampuan** yang digunakan oleh sistem.

## Struktur Kemampuan

### Sistem Informasi

#### Analisis Data

- SQL dan Pengolahan Data
- Spreadsheet dan Analisis Data
- Business Intelligence dan Visualisasi Data

#### Pengembangan Sistem

- Database Management
- Web Development
- System Analysis and Design

#### UI/UX

- UI Design
- Wireframing dan Prototyping
- User Research

### Manajemen

#### Marketing

- Branding
- Digital Marketing
- Market Research

#### Keuangan

- Financial Planning
- Financial Analysis
- Investment Management

#### Human Resources

- Recruitment and Selection
- Performance Management
- Talent Management

### Teknik Informatika

#### Pemrograman dan Rekayasa Perangkat Lunak

- Algoritma dan Struktur Data
- Object-Oriented Programming
- Software Engineering

#### Jaringan dan Sistem Komputer

- Computer Networks
- Operating Systems
- Cybersecurity

#### Artificial Intelligence

- Machine Learning
- Data Science
- Computer Vision

### Sistem Komputer

#### Arsitektur dan Organisasi Komputer

- Computer Architecture
- Digital Logic
- Microprocessor and Microcontroller

#### Embedded System dan Internet of Things

- Embedded Systems
- Internet of Things
- Sensor and Actuator Integration

#### Jaringan dan Keamanan Komputer

- Computer Networks
- Network Administration
- Network Security

### Psikologi

#### Psikologi Industri dan Organisasi

- Employee Behavior
- Organizational Development
- Psychological Assessment

#### Konseling

- Counseling Skills
- Interpersonal Communication
- Emotional Intelligence

#### Penelitian Psikologi

- Research Methodology
- Interview dan Observation
- Survey dan Data Analysis

### Ilmu Komunikasi

#### Public Relations

- Media Relations
- Corporate Communication
- Crisis Communication

#### Jurnalistik

- News Writing
- Journalistic Interview
- News Reporting

#### Digital Media

- Content Creation
- Social Media Management
- Video Production

## Fitur Utama

### Autentikasi

SkillPath AI menggunakan Laravel Fortify untuk menangani autentikasi.

Pengguna dapat:

- membuat akun;
- masuk ke aplikasi;
- keluar dari aplikasi;
- meminta reset kata sandi;
- membuat kata sandi baru melalui proses reset.

Setelah berhasil masuk, pengguna diarahkan ke dashboard.

### Profil Belajar

Pengguna dapat mengisi informasi yang membantu sistem memahami konteks belajar mereka.

Informasi tersebut dapat meliputi:

- jurusan;
- semester;
- bidang yang ingin dikembangkan;
- pengalaman belajar atau proyek;
- waktu belajar yang tersedia setiap minggu.

Profil dapat diperbarui ketika kondisi atau tujuan belajar pengguna berubah.

### Assessment Awal

Assessment digunakan untuk mendapatkan gambaran awal mengenai kemampuan pengguna.

Setiap jurusan memiliki sembilan kemampuan utama yang dinilai melalui pertanyaan assessment.

Jawaban pengguna kemudian digunakan oleh sistem untuk memperbarui nilai kemampuan yang tersimpan.

Hasil assessment menjadi salah satu dasar untuk:

- pemetaan kemampuan;
- analisis kesenjangan;
- penentuan prioritas belajar;
- penyusunan roadmap;
- perhitungan kesiapan.

### Peta Kemampuan

SkillPath AI menyimpan nilai kemampuan masing-masing pengguna.

Nilai tersebut digunakan untuk melihat kemampuan yang:

- sudah mendekati atau mencapai target;
- masih berada di bawah target;
- memiliki kesenjangan terbesar;
- sebaiknya menjadi prioritas untuk dipelajari.

Nilai kemampuan pada SkillPath AI digunakan sebagai indikator perkembangan dan bukan nilai akademik resmi.

### Analisis Kesenjangan Kemampuan

Setiap kemampuan dapat memiliki target penguasaan dan bobot kepentingan.

Sistem membandingkan kemampuan pengguna saat ini dengan target tersebut.

Secara sederhana:

```text
kesenjangan = target kemampuan - kemampuan saat ini
```

Kemampuan dengan kesenjangan dan tingkat kepentingan yang lebih besar dapat memperoleh prioritas lebih tinggi.

Perhitungan ini dilakukan oleh logika aplikasi dan tidak diserahkan kepada AI.

### Kesiapan Belajar pada Jurusan

Halaman pilihan jurusan dapat menampilkan persentase kesiapan belajar untuk pengguna yang sudah memiliki data kemampuan.

Sistem membandingkan nilai kemampuan pengguna dengan target kemampuan setiap jurusan.

Hasilnya dapat memiliki status seperti:

- Sangat siap;
- Siap;
- Cukup siap;
- Perlu penguatan;
- Belum dinilai.

Sistem juga dapat menunjukkan kemampuan dengan kesenjangan terbesar sehingga pengguna dapat mengetahui bagian yang masih perlu diperkuat.

### Jalur Belajar Adaptif

SkillPath AI memiliki roadmap belajar yang tidak hanya bergantung pada urutan materi tetap.

Roadmap dapat mempertimbangkan kondisi kemampuan pengguna sehingga materi yang lebih relevan dapat ditempatkan sebagai prioritas.

Roadmap dapat berubah ketika kondisi belajar pengguna berubah, misalnya setelah:

- menyelesaikan assessment;
- mendapatkan perubahan nilai kemampuan;
- menyelesaikan evaluasi;
- membutuhkan materi penguatan.

### Materi Belajar

Materi belajar dapat menyimpan informasi seperti:

- judul;
- ringkasan;
- tujuan pembelajaran;
- tingkat kesulitan;
- estimasi waktu;
- referensi;
- latihan praktik;
- pertanyaan evaluasi;
- pilihan jawaban;
- penjelasan evaluasi.

Materi dihubungkan dengan kemampuan yang ingin dikembangkan.

### Materi Penguatan

Jika pengguna belum berhasil menyelesaikan suatu evaluasi, sistem dapat memberikan materi penguatan.

Materi ini digunakan untuk membantu pengguna memahami bagian yang masih kurang sebelum melanjutkan kembali ke tahap berikutnya.

### Evaluasi Berbasis Bukti

Penyelesaian materi tidak hanya bergantung pada tombol selesai.

Sistem dapat menggunakan beberapa bentuk bukti belajar seperti:

- jawaban konsep;
- hasil evaluasi;
- tautan pekerjaan atau praktik;
- refleksi hasil belajar.

Hasil evaluasi digunakan untuk menentukan perkembangan pengguna dan kebutuhan penguatan selanjutnya.

### Catatan Aktivitas Belajar

Pengguna dapat mencatat aktivitas selama menjalani roadmap.

Catatan tersebut dapat berisi:

- persentase progres;
- waktu yang digunakan untuk belajar;
- catatan belajar;
- kendala;
- tautan bukti.

Riwayat ini kemudian dapat digunakan untuk melihat pola perkembangan pengguna.

### Proyek

SkillPath AI menyediakan sistem proyek sebagai sarana untuk menerapkan kemampuan yang sudah dipelajari.

Data proyek dapat berisi:

- judul;
- jurusan;
- deskripsi;
- permasalahan yang ingin diselesaikan;
- tingkat kesulitan;
- estimasi waktu pengerjaan;
- fitur minimum;
- fitur pengembangan;
- kriteria penyelesaian;
- kemampuan yang dibutuhkan.

### Kesiapan Proyek

Sebelum memulai proyek, sistem dapat membandingkan kemampuan pengguna dengan kemampuan minimum yang dibutuhkan proyek tersebut.

Perhitungan ini digunakan untuk memberikan gambaran apakah pengguna:

- sudah cukup siap;
- masih membutuhkan penguatan;
- atau dapat mengambil proyek tersebut sebagai tantangan.

Nilai kesiapan bukan jaminan bahwa proyek akan mudah diselesaikan. Nilai tersebut hanya menggambarkan seberapa dekat kemampuan pengguna dengan kebutuhan proyek.

### Progres Proyek

Setelah proyek dimulai, pengguna dapat memperbarui perkembangannya.

Ketika proyek akan diselesaikan, sistem dapat meminta bukti seperti:

- tautan repository;
- tautan hasil proyek;
- catatan penyelesaian.

### Riwayat Perkembangan

Halaman progres digunakan untuk melihat aktivitas belajar yang sudah dilakukan.

Data yang dapat muncul antara lain:

- hasil assessment;
- perubahan kemampuan;
- aktivitas belajar;
- evaluasi;
- perkembangan roadmap;
- progres proyek;
- riwayat kesiapan pengguna.

### Pendamping Belajar AI

SkillPath AI memiliki layanan AI untuk membantu menjelaskan informasi yang sudah dimiliki sistem.

AI dapat digunakan untuk:

- menjelaskan kondisi kemampuan;
- membuat ringkasan perkembangan;
- memberikan saran pembagian waktu belajar;
- menganalisis kendala yang pernah dicatat;
- membantu membuat variasi latihan;
- memberikan umpan balik terhadap proyek.

AI hanya menjadi lapisan pendamping.

AI tidak digunakan untuk secara langsung:

- menentukan nilai assessment;
- membuat nilai kemampuan;
- menentukan kelulusan evaluasi;
- mengubah progres pengguna;
- membuat fakta yang tidak tersedia di dalam data aplikasi.

Dengan pendekatan ini, fitur utama aplikasi tetap dapat dipertanggungjawabkan melalui logika program dan data yang tersimpan.

### Feedback

Pengguna dapat memberikan masukan mengenai aplikasi.

Feedback dapat berkaitan dengan:

- pengalaman menggunakan aplikasi;
- materi;
- rekomendasi;
- fitur;
- masalah teknis.

Administrator dapat melihat dan memberikan tanggapan terhadap feedback tersebut.

### Dashboard Administrator

Administrator memiliki halaman khusus untuk mengelola data utama SkillPath AI.

Data yang dapat dikelola mencakup:

- jurusan;
- kemampuan;
- hubungan kemampuan;
- prasyarat;
- assessment;
- pertanyaan assessment;
- materi belajar;
- proyek;
- kebutuhan kemampuan proyek;
- feedback.

### Pengelolaan Pengguna

Sistem memiliki akses khusus untuk pengelolaan pengguna.

Pengguna yang memiliki izin yang sesuai dapat:

- melihat daftar akun;
- melihat peran pengguna;
- mengubah peran akun.

### Keamanan Sesi

SkillPath AI memiliki mekanisme idle timeout.

Secara bawaan, sesi dianggap tidak aktif setelah:

```text
10 menit
```

Nilai tersebut dapat diubah melalui environment:

```env
AUTH_IDLE_TIMEOUT=10
```

## Teknologi

### Backend

- PHP
- Laravel
- Inertia.js
- Laravel Fortify
- Laravel Wayfinder
- PostgreSQL
- Resend

### Frontend

- React
- TypeScript
- Tailwind CSS
- Vite
- Recharts
- Lucide React
- Radix UI

### AI

- Google Gemini
- OpenRouter

Aplikasi mendukung model utama dan model cadangan melalui konfigurasi environment.

### Pengujian dan Kualitas Kode

Project menggunakan:

- PHPUnit 12
- Larastan
- PHPStan
- Laravel Pint
- ESLint
- Prettier
- TypeScript type checking
- GitHub Actions

## Persyaratan

Sebelum menjalankan project secara lokal, siapkan:

- PHP 8.4.1 atau lebih baru;
- Composer;
- Node.js;
- npm;
- PostgreSQL.

Untuk lingkungan yang mendekati GitHub Actions project ini, Node.js 22 dapat digunakan.

Pastikan ekstensi PHP yang dibutuhkan Laravel dan PostgreSQL juga sudah aktif.

## Instalasi

Clone repository:

```bash
git clone https://github.com/ki1bot/skillpath-ai.git
cd skillpath-ai
```

Install dependency PHP:

```bash
composer install
```

Install dependency frontend:

```bash
npm install
```

## Konfigurasi Database

SkillPath AI menggunakan PostgreSQL.

Contoh konfigurasi development:

```env
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=skillpathai
DB_USERNAME=root
DB_PASSWORD=
```

## Menjalankan Aplikasi

Untuk menjalankan Laravel, queue listener, dan Vite secara bersamaan:

```bash
composer run dev
```

Script tersebut menjalankan:

```text
php artisan serve --no-reload
php artisan queue:listen --tries=1
npm run dev
```

Aplikasi development secara bawaan dapat dibuka melalui:

```text
http://localhost:8000
```

## Setup Otomatis

Repository juga memiliki Composer script:

```bash
composer setup
```

Script tersebut melakukan beberapa proses setup seperti:

- install dependency Composer;
- membuat `.env` jika belum tersedia;
- membuat application key;
- menjalankan migration;
- install dependency npm;
- menjalankan frontend build.

Database tetap harus sudah dibuat dan konfigurasi `.env` harus benar sebelum migration dapat berhasil.

Jika membutuhkan data awal SkillPath AI setelah proses tersebut, jalankan:

```bash
php artisan db:seed
```

## Konfigurasi Email

SkillPath AI menggunakan Resend untuk pengiriman email.

Isi konfigurasi berikut:

```env
MAIL_MAILER=resend
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"
RESEND_API_KEY=
```

## Konfigurasi Google Gemini

Tambahkan API key Gemini:

```env
GEMINI_API_KEY=""
GEMINI_MODEL=gemini-3.5-flash-lite
GEMINI_FALLBACK_MODELS=gemini-3.1-flash-lite
GEMINI_BASE_URL=https://generativelanguage.googleapis.com/v1beta
```

## Konfigurasi OpenRouter

OpenRouter dapat dikonfigurasi dengan:

```env
OPENROUTER_API_KEY=""
OPENROUTER_MODEL=openai/gpt-oss-20b:free
OPENROUTER_FALLBACK_MODELS=openrouter/free
OPENROUTER_BASE_URL=https://openrouter.ai/api/v1
```

## Timeout AI

Beberapa batas waktu request AI dapat diatur melalui:

```env
AI_REQUEST_TIMEOUT=12
AI_ATTEMPT_TIMEOUT=6
AI_CONNECT_TIMEOUT=3
AI_FAILURE_CACHE_SECONDS=5
```

### Frontend

Lint:

```bash
npm run lint:check
```

Format:

```bash
npm run format:check
```

TypeScript:

```bash
npm run types:check
```

Build:

```bash
npm run build
```

### Backend

Laravel Pint:

```bash
composer run lint:check
```

PHPStan:

```bash
composer run types:check
```

Test backend:

```bash
composer run test
```

Atau:

```bash
php artisan test
```

Sebelum melakukan deployment, sebaiknya pastikan setidaknya:

```bash
composer run ci:check
npm run build
```

## GitHub Actions

Repository memiliki workflow GitHub Actions yang berjalan pada:

- push ke branch `main`;
- pull request.

CI menggunakan:

- Ubuntu;
- PHP;
- Node.js;
- PostgreSQL.

Workflow melakukan setup aplikasi kemudian menjalankan:

```bash
composer ci:check
```

## Environment Production

Untuk production, beberapa konfigurasi penting yang perlu diperhatikan antara lain:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-aplikasi
```

Pastikan juga:

- database production sudah dikonfigurasi;
- API key tidak dimasukkan ke repository;
- `.env` tidak di-commit;
- HTTPS aktif;
- queue worker tersedia jika fitur queue digunakan;
- konfigurasi Resend sudah valid;
- API key Gemini atau OpenRouter dikonfigurasi jika fitur AI ingin digunakan.

## Catatan Mengenai AI

SkillPath AI tidak dirancang sebagai aplikasi yang menyerahkan seluruh keputusan kepada model AI.

Perhitungan inti tetap dikerjakan oleh backend menggunakan data yang tersimpan.

AI menerima konteks yang sudah tersedia untuk membantu menjelaskan informasi tersebut dengan bahasa yang lebih mudah dipahami.

Pendekatan ini digunakan agar fitur utama aplikasi tetap dapat berjalan berdasarkan aturan yang jelas dan tidak bergantung sepenuhnya pada hasil generatif.

## Catatan Mengenai Nilai Kemampuan

Nilai kemampuan pada SkillPath AI bukan nilai akademik resmi dan bukan alat untuk menentukan kemampuan seseorang secara mutlak.

Nilai tersebut digunakan sebagai indikator internal untuk membantu:

- membandingkan kemampuan saat ini dengan target;
- menentukan kemampuan yang perlu diperkuat;
- menyusun prioritas belajar;
- memperkirakan kesiapan terhadap materi atau proyek;
- melihat perubahan perkembangan dari waktu ke waktu.

Hasil SkillPath AI sebaiknya digunakan sebagai alat bantu belajar, bukan sebagai pengganti penilaian akademik atau profesional.

## Status Project

Saat ini SkillPath AI sudah memiliki fondasi utama untuk:

- autentikasi pengguna;
- onboarding dan profil belajar;
- enam pilihan jurusan;
- 54 kemampuan;
- assessment kemampuan;
- pemetaan kemampuan;
- analisis kesenjangan kemampuan;
- perhitungan kesiapan belajar;
- roadmap adaptif;
- materi belajar;
- materi penguatan;
- evaluasi berbasis bukti;
- pencatatan aktivitas belajar;
- proyek;
- perhitungan kesiapan proyek;
- progres proyek;
- riwayat perkembangan;
- integrasi Gemini dan OpenRouter;
- feedback pengguna;
- dashboard administrator;
- pengelolaan pengguna;
- CI melalui GitHub Actions.
