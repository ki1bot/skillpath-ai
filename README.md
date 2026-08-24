# SkillPath AI

SkillPath AI adalah aplikasi web yang membantu mahasiswa memahami kemampuan yang sudah dimiliki, melihat bagian yang masih perlu diperkuat, dan menentukan urutan belajar berdasarkan jurusan yang sedang dijalani.

Aplikasi dapat diakses melalui:

https://skillpath-ai.my.id

Repository:

https://github.com/ki1bot/skillpath-ai

## Tentang SkillPath AI

Belajar menjadi lebih sulit ketika seseorang tidak tahu kemampuan mana yang sudah cukup kuat dan bagian mana yang sebaiknya dipelajari lebih dahulu.

SkillPath AI dibuat untuk membantu mahasiswa memahami kondisi kemampuan mereka secara lebih terarah.

Pengguna memulai dengan memilih jurusan dan mengisi profil belajar. Setelah itu, pengguna mengerjakan asssement awal yang disesuaikan dengan jurusan tersebut.

Hasil asssement digunakan untuk membuat peta kemampuan. Sistem kemudian membandingkan kemampuan pengguna dengan target penguasaan pada setiap kemampuan dan menyusun jalur belajar berdasarkan bagian yang masih perlu dikembangkan.

Pengguna dapat mengikuti materi belajar, mencatat perkembangan, mengerjakan evaluasi berbasis bukti, mendapatkan materi penguatan ketika diperlukan, serta mengerjakan proyek untuk menerapkan kemampuan yang sudah dipelajari.

SkillPath AI juga memiliki fitur AI yang membantu menjelaskan hasil kemampuan, membuat variasi latihan, merangkum perkembangan belajar, memberikan saran pembagian waktu, dan memberikan umpan balik terhadap proyek.

AI tidak menentukan nilai asssement, kelulusan evaluasi, atau keputusan utama sistem. Perhitungan tersebut tetap dilakukan oleh logika aplikasi berdasarkan data yang tersimpan.

## Tujuan

SkillPath AI dikembangkan untuk membantu mahasiswa:

- memahami kemampuan yang sudah dimiliki;
- mengetahui kemampuan yang masih perlu diperkuat;
- mendapatkan urutan belajar yang lebih terarah;
- memantau perkembangan belajar;
- menerapkan kemampuan melalui proyek;
- memiliki gambaran yang lebih jelas mengenai langkah belajar berikutnya.

## Jurusan yang tersedia

SkillPath AI saat ini menyediakan enam jurusan:

1. Sistem Informasi
2. Manajemen
3. Teknik Informatika
4. Sistem Komputer
5. Psikologi
6. Ilmu Komunikasi

Setiap jurusan memiliki tiga bidang utama dan sembilan kemampuan.

Secara keseluruhan terdapat 54 kemampuan akademik yang digunakan sebagai dasar asssement dan jalur belajar.

## Struktur kemampuan setiap jurusan

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
- Artificial Intelligence Fundamentals

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

#### Psikologi Dasar dan Perkembangan

- General Psychology
- Developmental Psychology
- Personality Psychology

#### Psikologi Sosial dan Organisasi

- Social Psychology
- Industrial and Organizational Psychology
- Organizational Behavior

#### Penelitian dan Asssement Psikologi

- Psychological Assessment
- Research Methods in Psychology
- Statistics for Psychology

### Ilmu Komunikasi

#### Komunikasi dan Media

- Communication Theory
- Media Studies
- Digital Communication

#### Public Relations dan Branding

- Public Relations
- Corporate Communication
- Brand Communication

#### Jurnalistik dan Produksi Konten

- Journalism
- Content Production
- Media Writing

## Fitur utama

### 1. Registrasi dan autentikasi

Pengguna dapat membuat akun, masuk ke aplikasi, keluar dari akun, serta melakukan reset kata sandi.

Sistem autentikasi menggunakan Laravel Fortify.

### 2. Profil belajar

Pengguna dapat mengisi informasi yang digunakan sebagai konteks pembelajaran, seperti:

- jurusan;
- semester;
- bidang yang ingin dipelajari lebih dalam;
- pengalaman belajar atau proyek;
- jumlah waktu belajar setiap minggu.

Profil dapat diperbarui kembali ketika kondisi pengguna berubah.

### 3. Asssement awal

Setelah memilih jurusan, pengguna mengerjakan asssement yang sesuai dengan jurusan tersebut.

Setiap jurusan memiliki sembilan pertanyaan yang mewakili sembilan kemampuan utama.

Selain memilih jawaban, pengguna juga memberikan tingkat keyakinan terhadap jawabannya.

Nilai asssement kemudian digunakan untuk memperbarui kemampuan pengguna.

### 4. Peta kemampuan

SkillPath membandingkan nilai kemampuan pengguna dengan target kemampuan pada jurusan yang dipilih.

Hasilnya digunakan untuk melihat:

- kemampuan yang sudah mencapai target;
- kemampuan yang masih perlu ditingkatkan;
- kemampuan yang membutuhkan penguatan lebih besar;
- urutan prioritas kemampuan yang sebaiknya dipelajari.

### 5. Analisis kesenjangan kemampuan

Setiap kemampuan memiliki target penguasaan dan bobot kepentingan.

Sistem menghitung selisih antara kemampuan pengguna saat ini dengan target yang perlu dicapai.

Kemampuan dengan kebutuhan penguatan lebih tinggi dapat ditempatkan lebih awal dalam jalur belajar.

### 6. Jalur belajar adaptif

Jalur belajar dibuat berdasarkan kondisi kemampuan pengguna.

Materi tidak hanya ditampilkan dalam urutan tetap. Sistem dapat mengatur prioritas berdasarkan kemampuan yang masih perlu dikembangkan.

Jalur belajar juga dapat diperbarui setelah:

- pengguna menyelesaikan asssement;
- nilai kemampuan berubah;
- pengguna menyelesaikan evaluasi;
- pengguna membutuhkan materi penguatan.

### 7. Materi belajar

Materi belajar dapat berisi:

- judul materi;
- ringkasan;
- tujuan pembelajaran;
- tingkat kesulitan;
- estimasi waktu belajar;
- referensi;
- latihan praktik;
- pertanyaan evaluasi;
- pilihan jawaban;
- penjelasan evaluasi.

Setiap kemampuan akademik dapat memiliki materi utama dan materi penguatan.

### 8. Materi penguatan

Jika pengguna belum berhasil menyelesaikan evaluasi, sistem dapat menambahkan materi penguatan.

Materi penguatan harus diselesaikan sebelum pengguna kembali mencoba materi utama yang sebelumnya belum berhasil diselesaikan.

### 9. Evaluasi berbasis bukti

Penyelesaian materi tidak hanya bergantung pada tombol selesai.

Evaluasi dapat menggunakan:

- jawaban konsep;
- tautan bukti praktik;
- refleksi hasil belajar.

Nilai evaluasi terdiri dari beberapa bagian yang digunakan untuk menentukan apakah materi benar-benar sudah selesai.

### 10. Catatan aktivitas belajar

Pengguna dapat mencatat:

- persentase perkembangan;
- waktu belajar;
- catatan belajar;
- kendala;
- tautan bukti.

Aktivitas tersebut disimpan sebagai bagian dari riwayat perkembangan pengguna.

### 11. Proyek

SkillPath memiliki sistem proyek yang dapat digunakan untuk menerapkan kemampuan yang sudah dipelajari.

Setiap proyek dapat memiliki:

- jurusan;
- judul;
- ringkasan;
- masalah yang ingin diselesaikan;
- tingkat kesulitan;
- estimasi pengerjaan;
- fitur minimum;
- fitur pengembangan;
- kriteria penyelesaian;
- kemampuan yang dibutuhkan.

### 12. Perhitungan kesiapan proyek

Sistem membandingkan kemampuan pengguna dengan kemampuan minimum yang dibutuhkan proyek.

Berdasarkan hasil tersebut, proyek dapat berada pada kondisi:

- bisa dikerjakan sekarang;
- masih membutuhkan penguatan;
- dapat dikerjakan sebagai tantangan.

Nilai kesiapan proyek bukan jaminan bahwa proyek akan mudah. Nilai tersebut hanya menunjukkan seberapa dekat kemampuan pengguna dengan kebutuhan minimum proyek.

### 13. Perkembangan proyek

Setelah proyek dimulai, pengguna dapat mencatat persentase pengerjaan.

Untuk menyelesaikan proyek sampai 100%, pengguna dapat diminta menyertakan:

- tautan repositori atau bukti eksternal;
- catatan penyelesaian.

### 14. Riwayat perkembangan

Halaman perkembangan menampilkan berbagai aktivitas yang pernah dilakukan pengguna, termasuk:

- hasil asssement;
- riwayat nilai kemampuan;
- aktivitas belajar;
- evaluasi materi;
- perkembangan proyek;
- perubahan jalur belajar;
- riwayat kesiapan.

### 15. Pendamping Belajar AI

Jika penyedia AI telah dikonfigurasi, SkillPath dapat menggunakan AI untuk membantu pengguna.

Fitur AI meliputi:

- penjelasan peta kemampuan;
- ringkasan perkembangan belajar;
- saran pembagian waktu belajar;
- analisis kendala yang pernah dicatat;
- variasi latihan;
- umpan balik proyek.

AI hanya digunakan sebagai lapisan penjelasan dan pendamping.

AI tidak menggantikan perhitungan utama aplikasi.

### 16. Masukan pengguna

Pengguna dapat mengirim masukan mengenai:

- aplikasi secara umum;
- materi;
- rekomendasi;
- pengalaman penggunaan;
- masalah teknis.

Administrator dapat meninjau dan memberikan tanggapan terhadap masukan tersebut.

### 17. Dashboard administrator

Administrator memiliki halaman untuk melihat dan mengelola data utama aplikasi.

Data yang dapat dikelola meliputi:

- jurusan;
- kemampuan;
- hubungan prasyarat;
- asssement;
- pertanyaan asssement;
- materi belajar;
- proyek;
- kebutuhan kemampuan proyek;
- masukan pengguna.

### 18. Pengelolaan pengguna

Akun yang memiliki izin pengelolaan pengguna dapat melihat daftar pengguna dan mengubah peran akun sesuai izin yang tersedia pada sistem.

### 19. Keamanan sesi

SkillPath memiliki batas waktu sesi ketika pengguna tidak melakukan aktivitas.

Nilai bawaan batas waktu tidak aktif adalah 10 menit dan dapat diubah melalui konfigurasi environment.

## Peran AI dalam SkillPath

SkillPath menggunakan pendekatan di mana AI tidak menjadi satu-satunya mesin pengambil keputusan.

Perhitungan utama tetap dilakukan oleh sistem aplikasi.

AI digunakan untuk membantu menjelaskan data yang sudah tersedia.

Contohnya, AI dapat menerima data mengenai hasil kemampuan pengguna kemudian memberikan penjelasan yang lebih mudah dipahami.

AI tidak diperbolehkan membuat nilai asssement baru, mengubah status progres, membuat kemampuan baru, atau mengubah fakta yang tidak tersedia pada data aplikasi.

## Teknologi yang digunakan

### Backend

- PHP 8.4.1+
- Laravel 13
- Inertia.js
- Laravel Fortify
- Laravel Wayfinder
- PostgreSQL
- Resend

### Frontend

- React 19
- TypeScript
- Tailwind CSS 4
- Vite 8
- Recharts
- Lucide React
- Radix UI

### AI

- Google Gemini
- OpenRouter

Aplikasi dapat menggunakan model utama dan model cadangan sesuai konfigurasi environment.

### Pengujian dan kualitas kode

- PHPUnit 12
- Larastan
- PHPStan
- Laravel Pint
- ESLint
- Prettier
- TypeScript type checking

PHPStan pada project dikonfigurasi pada level 7.

## Persyaratan

Sebelum menjalankan project, pastikan komputer memiliki:

- PHP 8.4.1 atau lebih baru;
- Composer;
- Node.js;
- npm;
- PostgreSQL.

Ekstensi PHP yang dibutuhkan Laravel dan PostgreSQL juga harus tersedia.

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

Install dependency JavaScript:

```bash
npm install
```

Buat application key:

```bash
php artisan key:generate
```

## Konfigurasi database

Konfigurasi bawaan project menggunakan PostgreSQL.

Sesuaikan bagian berikut pada `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=skillpath_ai
DB_USERNAME=root
DB_PASSWORD=
```

Buat database PostgreSQL sesuai nilai `DB_DATABASE`.

Setelah itu jalankan migration dan seeder:

```bash
php artisan migrate --seed
```

Seeder utama akan menyiapkan data akademik seperti:

- jurusan;
- kemampuan;
- hubungan jurusan dan kemampuan;
- asssement;
- pertanyaan asssement;
- materi belajar.

## Menjalankan aplikasi

Untuk menjalankan backend Laravel, queue listener, dan Vite secara bersamaan:

```bash
composer run dev
```

Script tersebut menjalankan:

```text
php artisan serve --no-reload
php artisan queue:listen --tries=1
npm run dev
```

Secara bawaan aplikasi Laravel dapat diakses melalui:

```text
http://localhost:8000
```

## Konfigurasi sesi

Project menggunakan database untuk menyimpan sesi.

Konfigurasi bawaan:

```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

Batas waktu tidak aktif SkillPath dapat ditentukan dengan:

```env
AUTH_IDLE_TIMEOUT=10
```

Jika variabel tersebut tidak ditentukan, aplikasi menggunakan 10 menit.

## Konfigurasi queue dan cache

Konfigurasi bawaan project menggunakan database:

```env
QUEUE_CONNECTION=database
CACHE_STORE=database
```

Saat menjalankan aplikasi secara manual dan fitur yang membutuhkan queue digunakan, jalankan worker:

```bash
php artisan queue:work --tries=1
```

Pada mode development, `composer run dev` sudah menjalankan `queue:listen`.

## Konfigurasi email

Project menggunakan Resend sebagai mailer.

Isi konfigurasi berikut:

```env
MAIL_MAILER=resend
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"
RESEND_API_KEY=
```

Gunakan alamat pengirim yang sudah sesuai dengan konfigurasi akun dan domain Resend.

## Konfigurasi Google Gemini

Isi:

```env
GEMINI_API_KEY=""
GEMINI_MODEL=gemini-3.5-flash-lite
GEMINI_FALLBACK_MODELS=gemini-3.1-flash-lite
GEMINI_BASE_URL=https://generativelanguage.googleapis.com/v1beta
```

`GEMINI_API_KEY` harus berisi API key yang valid agar layanan dapat digunakan.

## Konfigurasi OpenRouter

Isi:

```env
OPENROUTER_API_KEY=""
OPENROUTER_MODEL=openai/gpt-oss-20b:free
OPENROUTER_FALLBACK_MODELS=openrouter/free
OPENROUTER_BASE_URL=https://openrouter.ai/api/v1
```

Jika Gemini tidak digunakan atau gagal dan OpenRouter dikonfigurasi, aplikasi dapat mencoba penyedia yang tersedia berdasarkan logika layanan AI.

## Konfigurasi waktu permintaan AI

Environment yang tersedia:

```env
AI_REQUEST_TIMEOUT=12
AI_ATTEMPT_TIMEOUT=6
AI_CONNECT_TIMEOUT=3
AI_FAILURE_CACHE_SECONDS=5
```

Nilai tersebut digunakan untuk membatasi waktu tunggu dan kegagalan permintaan AI.

## Keamanan

Beberapa hal yang perlu diperhatikan:

- jangan memasukkan `.env` ke repository;
- jangan menyimpan password database di source code;
- jangan memasukkan API key ke commit;
- gunakan `APP_DEBUG=false` pada production;
- gunakan HTTPS pada production;
- lakukan backup sebelum perubahan database besar;
- batasi akses administrator;
- jalankan pemeriksaan kode sebelum deployment.

## Tentang nilai SkillPath

Nilai 0–100 pada SkillPath digunakan sebagai alat bantu untuk membaca perkembangan kemampuan.

Nilai tersebut bukan nilai akademik resmi.

Nilai tersebut juga bukan jaminan bahwa seseorang akan mendapatkan hasil tertentu di pendidikan maupun dunia kerja.

Tujuan nilai tersebut adalah memberikan gambaran mengenai kondisi kemampuan pengguna agar pengguna dapat menentukan bagian yang lebih masuk akal untuk dipelajari berikutnya.

## Prinsip pengembangan

Ketika melakukan perubahan pada SkillPath AI:

1. jangan mengubah logika penilaian tanpa alasan yang jelas;
2. perbarui test ketika perilaku sistem berubah;
3. pertahankan data pengguna ketika melakukan migration;
4. jangan menyembunyikan error baru dengan baseline;
5. pastikan frontend tetap responsif pada desktop dan mobile;
6. pastikan teks yang ditampilkan kepada pengguna mudah dipahami;
7. pastikan `composer run ci:check` dan `npm run build` berhasil sebelum deployment.

## Status project

SkillPath AI saat ini memiliki fondasi utama untuk:

- pemetaan kemampuan berdasarkan jurusan;
- Asssement kemampuan;
- analisis kesenjangan kemampuan;
- jalur belajar adaptif;
- materi utama dan materi penguatan;
- evaluasi berbasis bukti;
- proyek;
- pemantauan perkembangan;
- integrasi AI;
- masukan pengguna;
- pengelolaan data oleh administrator.
