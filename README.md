# SkillPath AI

SkillPath AI adalah aplikasi web untuk membantu mahasiswa memahami kemampuan mereka, menemukan skill yang masih perlu diperkuat, dan menyusun jalur belajar yang lebih terarah berdasarkan jurusan, hasil assessment, progres belajar, dan evaluasi.

Aplikasi ini tidak menyerahkan keputusan utama kepada AI. Assessment, perhitungan skill gap, roadmap, progres, evaluasi, dan kesiapan proyek tetap dihitung oleh sistem. AI digunakan sebagai pendamping untuk menjelaskan hasil dan memberikan insight berdasarkan data yang sudah tersedia.

**Live Website:** https://skillpath-ai.my.id  
**Repository:** https://github.com/ki1bot/skillpath-ai

---

## Tentang Project

Banyak mahasiswa memiliki akses ke materi belajar, tetapi masih kesulitan menentukan apa yang harus dipelajari terlebih dahulu.

SkillPath AI mencoba menyelesaikan masalah tersebut dengan alur yang sederhana:

1. Pengguna memilih jurusan dan mengisi profil belajar.
2. Pengguna mengerjakan assessment awal.
3. Sistem menghitung kemampuan berdasarkan hasil assessment.
4. Nilai kemampuan dibandingkan dengan target skill.
5. Sistem menentukan skill gap dan prioritas belajar.
6. Roadmap belajar dibuat berdasarkan kondisi pengguna.
7. Pengguna mempelajari materi dan mencatat progres.
8. Evaluasi digunakan untuk mengukur perkembangan.
9. Materi penguatan diberikan jika masih diperlukan.
10. Pengguna dapat mengerjakan proyek sesuai tingkat kesiapan.
11. Perkembangan dapat dipantau melalui dashboard dan halaman progres.

Tujuannya bukan menggantikan proses belajar, tetapi memberikan arah belajar yang lebih jelas dan terukur.

---

## Fitur

### Assessment

Assessment digunakan untuk mendapatkan gambaran awal kemampuan pengguna.

Setiap jurusan memiliki:

- 50 soal assessment;
- 5 bagian;
- 10 soal per bagian;
- 9 skill utama yang dinilai.

Sesi assessment dirancang agar tetap dapat dipulihkan ketika halaman di-refresh atau koneksi internet terputus. Jika pengguna meninggalkan atau membatalkan assessment melalui alur yang tersedia, sesi dapat dimulai kembali dari awal.

### Skill Mapping

Hasil assessment digunakan untuk membentuk nilai kemampuan pengguna.

Nilai tersebut digunakan untuk melihat:

- kemampuan yang sudah mendekati target;
- kemampuan yang masih membutuhkan penguatan;
- skill dengan gap terbesar;
- skill yang sebaiknya diprioritaskan.

Nilai kemampuan di SkillPath AI adalah indikator internal dan bukan nilai akademik resmi.

### Skill Gap Analysis

Sistem membandingkan kemampuan saat ini dengan target kemampuan.

Secara sederhana:

```text
skill gap = target kemampuan - kemampuan saat ini
```

Hasil perhitungan tersebut kemudian digunakan untuk menentukan prioritas pengembangan skill.

Perhitungan utama dilakukan oleh backend, bukan oleh model AI.

### Roadmap Belajar

Roadmap membantu pengguna menentukan materi yang perlu dipelajari berdasarkan kondisi skill mereka.

Roadmap dapat berubah ketika pengguna:

- menyelesaikan assessment;
- memperoleh perubahan nilai skill;
- menyelesaikan materi;
- mengerjakan evaluasi;
- membutuhkan materi penguatan.

### Materi Belajar

Materi dapat memiliki:

- judul;
- ringkasan;
- learning objective;
- tingkat kesulitan;
- estimasi waktu belajar;
- referensi;
- latihan praktik;
- evaluasi;
- penjelasan jawaban.

Setiap materi terhubung dengan skill tertentu.

### Materi Penguatan

Ketika pengguna belum memenuhi hasil evaluasi yang dibutuhkan, sistem dapat menambahkan materi penguatan sebelum pengguna melanjutkan proses belajar.

### Evaluasi Berbasis Bukti

Evaluasi tidak hanya mengandalkan tombol selesai.

Beberapa bentuk bukti yang dapat digunakan antara lain:

- jawaban evaluasi;
- tautan hasil praktik;
- Google Drive evidence;
- refleksi belajar.

Hasil evaluasi dapat memengaruhi progres dan kebutuhan reinforcement selanjutnya.

### Progress Tracking

Pengguna dapat mencatat aktivitas belajar seperti:

- persentase progres;
- durasi belajar;
- catatan;
- kendala;
- evidence.

Riwayat tersebut digunakan untuk membantu pengguna melihat perkembangan dari waktu ke waktu.

### Project Recommendation

SkillPath AI menyediakan proyek sebagai sarana menerapkan kemampuan yang sudah dipelajari.

Setiap proyek dapat memiliki:

- judul;
- deskripsi;
- problem statement;
- tingkat kesulitan;
- estimasi waktu;
- minimum features;
- completion criteria;
- skill yang dibutuhkan.

Sistem membandingkan skill pengguna dengan kebutuhan proyek untuk menentukan tingkat kesiapan.

### AI Learning Assistant

AI digunakan sebagai lapisan pendamping, bukan sebagai sumber keputusan utama.

AI saat ini digunakan untuk:

- menjelaskan hasil skill gap;
- membuat ringkasan perkembangan belajar;
- memberikan saran pembagian waktu belajar;
- membantu membaca pola kendala pengguna;
- membuat variasi latihan;
- memberikan feedback proyek.

AI tidak digunakan untuk langsung:

- menentukan nilai assessment;
- membuat nilai skill;
- menentukan kelulusan evaluasi;
- mengubah progres;
- menentukan kesiapan proyek secara sepihak;
- membuat data yang tidak tersedia di aplikasi.

### Authentication

Aplikasi menyediakan:

- registrasi;
- login;
- logout;
- email verification;
- reset password;
- remember me;
- idle session timeout;
- login Google;
- login Facebook.

### Feedback

Pengguna dapat mengirim feedback mengenai:

- pengalaman menggunakan aplikasi;
- materi;
- fitur;
- rekomendasi;
- masalah teknis.

Administrator dapat meninjau feedback melalui halaman admin.

### Admin Dashboard

Administrator dapat mengelola data utama seperti:

- jurusan;
- skill;
- assessment;
- pertanyaan assessment;
- materi;
- prerequisite;
- proyek;
- kebutuhan skill proyek;
- feedback;
- pengguna dan role.

---

## Jurusan

SkillPath AI saat ini menyediakan enam jurusan dengan total 54 skill.

### Sistem Informasi

**Analisis Data**

- SQL dan Pengolahan Data
- Spreadsheet dan Analisis Data
- Business Intelligence dan Visualisasi Data

**Pengembangan Sistem**

- Database Management
- Web Development
- System Analysis and Design

**UI/UX**

- UI Design
- Wireframing dan Prototyping
- User Research

### Manajemen

**Marketing**

- Branding
- Digital Marketing
- Market Research

**Keuangan**

- Financial Planning
- Financial Analysis
- Investment Management

**Human Resources**

- Recruitment and Selection
- Performance Management
- Talent Management

### Teknik Informatika

**Pemrograman dan Rekayasa Perangkat Lunak**

- Algoritma dan Struktur Data
- Object-Oriented Programming
- Software Engineering

**Jaringan dan Sistem Komputer**

- Computer Networks
- Operating Systems
- Cybersecurity

**Artificial Intelligence**

- Machine Learning
- Data Science
- Computer Vision

### Sistem Komputer

**Arsitektur dan Organisasi Komputer**

- Computer Architecture
- Digital Logic
- Microprocessor and Microcontroller

**Embedded System dan Internet of Things**

- Embedded Systems
- Internet of Things
- Sensor and Actuator Integration

**Jaringan dan Keamanan Komputer**

- Computer Networks
- Network Administration
- Network Security

### Psikologi

**Psikologi Industri dan Organisasi**

- Employee Behavior
- Organizational Development
- Psychological Assessment

**Konseling**

- Counseling Skills
- Interpersonal Communication
- Emotional Intelligence

**Penelitian Psikologi**

- Research Methodology
- Interview dan Observation
- Survey dan Data Analysis

### Ilmu Komunikasi

**Public Relations**

- Media Relations
- Corporate Communication
- Crisis Communication

**Jurnalistik**

- News Writing
- Journalistic Interview
- News Reporting

**Digital Media**

- Content Creation
- Social Media Management
- Video Production

---

## Tech Stack

### Backend

- PHP 8.4+
- Laravel 13
- Inertia.js 3
- Laravel Fortify
- Laravel Socialite
- Laravel Wayfinder
- PostgreSQL
- Resend

### Frontend

- React 19
- TypeScript
- Tailwind CSS 4
- Vite 8
- Radix UI
- Lucide React
- Recharts

### AI Providers

- Google Gemini
- OpenRouter
- xKiro

### Development & Quality

- Docker
- PHPUnit
- PHPStan
- Larastan
- Laravel Pint
- ESLint
- Prettier
- TypeScript type checking
- GitHub Actions

### Deployment

- Railway
- PostgreSQL
- Custom domain
- HTTPS

---

## Arsitektur AI

SkillPath AI memiliki beberapa provider AI agar aplikasi tidak bergantung pada satu model saja.

Konfigurasi saat ini:

```text
Gemini
├── gemini-3.5-flash-lite
└── gemini-3.1-flash-lite

OpenRouter
├── nex-agi/nex-n2.5-pro:free
└── nvidia/nemotron-3-ultra-550b-a55b:free

xKiro
├── qwen/qwen3.8-max:free
└── mistralai/mistral-large-2512
```

Urutan attempt aplikasi adalah:

```text
Gemini primary
        ↓
OpenRouter primary
        ↓
xKiro primary
        ↓
Gemini fallback
        ↓
OpenRouter fallback
        ↓
xKiro fallback
```

Jika salah satu provider gagal, timeout, atau tidak menghasilkan respons yang valid, aplikasi dapat mencoba provider atau model berikutnya.

Untuk OpenRouter, rate limit dari shared upstream model dibedakan dari rate limit akun/provider sehingga fallback OpenRouter masih dapat digunakan ketika hanya model tertentu yang sedang penuh.

Model gratis dari provider eksternal dapat berubah sewaktu-waktu. Karena itu nama model disimpan melalui environment variable agar dapat diganti tanpa mengubah logika utama aplikasi.

---

## Menjalankan Project dengan Docker

Docker merupakan cara yang direkomendasikan untuk menjalankan environment development yang konsisten.

### 1. Clone repository

```bash
git clone https://github.com/ki1bot/skillpath-ai.git
cd skillpath-ai
```

### 2. Buat file environment

Git Bash:

```bash
cp .env.example .env
```

Kemudian isi konfigurasi yang diperlukan pada `.env`.

Jangan commit `.env` karena file tersebut berisi credential dan secret.

### 3. Buat persistent volume PostgreSQL

Project menggunakan external Docker volume untuk database:

```bash
docker volume create skillpath-ai-postgres-persistent
```

Perintah ini hanya perlu dilakukan satu kali.

### 4. Jalankan container

```bash
docker compose up -d --build
```

### 5. Generate application key

```bash
docker compose exec app php artisan key:generate
```

### 6. Jalankan migration

```bash
docker compose exec app php artisan migrate
```

Jika membutuhkan data awal:

```bash
docker compose exec app php artisan db:seed
```

### 7. Bersihkan cache aplikasi

```bash
docker compose exec app php artisan optimize:clear
```

Aplikasi development dapat dibuka di:

```text
http://localhost:8080
```

Vite development server menggunakan port:

```text
http://localhost:5173
```

---

## Tentang Database pada Docker

Pada `.env` local, database dapat menggunakan:

```env
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=skillpathai
DB_USERNAME=root
DB_PASSWORD=
```

Ketika aplikasi berjalan melalui Docker Compose, `DB_HOST` secara otomatis dioverride menjadi:

```text
postgres
```

karena Laravel harus mengakses PostgreSQL melalui nama service Docker.

Secret database tetap berada di `.env` dan tidak perlu ditulis langsung di `compose.yaml`.

---

## Menjalankan Tanpa Docker

Pastikan sudah tersedia:

- PHP 8.4.1 atau lebih baru
- Composer
- Node.js
- npm
- PostgreSQL

Install dependency:

```bash
composer install
npm install
```

Buat environment:

```bash
cp .env.example .env
php artisan key:generate
```

Jalankan migration:

```bash
php artisan migrate
```

Jalankan development server:

```bash
composer run dev
```

Script tersebut menjalankan Laravel, queue listener, dan Vite secara bersamaan.

Secara default `php artisan serve` berjalan pada:

```text
http://127.0.0.1:8000
```

Jika menjalankan project tanpa Docker, sesuaikan `APP_URL` pada `.env` dengan URL development yang digunakan.

---

## Konfigurasi AI

API key tidak boleh disimpan di source code atau README.

### Google Gemini

```env
GEMINI_API_KEY=
GEMINI_MODEL=gemini-3.5-flash-lite
GEMINI_FALLBACK_MODELS=gemini-3.1-flash-lite
GEMINI_BASE_URL=https://generativelanguage.googleapis.com/v1beta
```

### OpenRouter

```env
OPENROUTER_API_KEY=
OPENROUTER_MODEL=nex-agi/nex-n2.5-pro:free
OPENROUTER_FALLBACK_MODELS=nvidia/nemotron-3-ultra-550b-a55b:free
OPENROUTER_BASE_URL=https://openrouter.ai/api/v1
```

### xKiro

```env
XKIRO_API_KEY=
XKIRO_MODEL=qwen/qwen3.8-max:free
XKIRO_FALLBACK_MODELS=mistralai/mistral-large-2512
XKIRO_BASE_URL=https://api.xkiro.com/v1
```

### Timeout

```env
AI_REQUEST_TIMEOUT=30
AI_ATTEMPT_TIMEOUT=10
AI_CONNECT_TIMEOUT=5
AI_FAILURE_CACHE_SECONDS=10
```

---

## Konfigurasi Email

Email dikirim menggunakan Resend.

```env
MAIL_MAILER=resend
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"

RESEND_API_KEY=
```

Gunakan domain pengirim yang sudah terverifikasi pada akun Resend.

---

## Login Google dan Facebook

### Google

```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://localhost:8080/auth/google/callback
```

### Facebook

```env
FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
FACEBOOK_REDIRECT_URI=http://localhost:8080/auth/facebook/callback
```

Untuk production, redirect URI harus menggunakan domain production.

Contoh:

```text
https://example.com/auth/google/callback
https://example.com/auth/facebook/callback
```

---

## Session dan Idle Timeout

Durasi idle session dapat diatur melalui:

```env
AUTH_IDLE_TIMEOUT=10
```

Nilai tersebut menggunakan satuan menit.

Untuk production gunakan cookie yang aman:

```env
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

---

## Quality Check

Sebelum melakukan commit atau deployment, jalankan:

```bash
docker compose exec app composer ci:check
```

Command tersebut menjalankan pemeriksaan seperti:

```text
ESLint
Prettier
TypeScript
Laravel Pint
PHPStan / Larastan
PHPUnit
```

Kemudian pastikan frontend dapat dibuild:

```bash
docker compose exec app npm run build
```

Jika tidak menggunakan Docker:

```bash
composer ci:check
npm run build
```

---

## Command Development

### Frontend lint

```bash
npm run lint:check
```

### Frontend formatting

```bash
npm run format:check
```

### TypeScript

```bash
npm run types:check
```

### Frontend build

```bash
npm run build
```

### Laravel Pint

```bash
composer lint:check
```

### PHPStan / Larastan

```bash
composer types:check
```

### Test

```bash
composer test
```

atau:

```bash
php artisan test
```

### Semua pemeriksaan CI

```bash
composer ci:check
```

---

## Deployment

Production SkillPath AI berjalan di Railway.

Environment production minimal sebaiknya menggunakan:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://example.com

DB_CONNECTION=pgsql
DB_URL=

SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
```

Selain itu tambahkan credential yang diperlukan untuk:

- Resend;
- Gemini;
- OpenRouter;
- xKiro;
- Google OAuth;
- Facebook OAuth.

Secret production disimpan melalui Railway Variables dan tidak disimpan di repository.

Setelah perubahan environment atau deployment baru, cache Laravel dapat dibersihkan dengan:

```bash
php artisan optimize:clear
```

---

## Verifikasi Production

Beberapa pemeriksaan sederhana dapat dilakukan melalui Railway Console.

### Database

```bash
php artisan tinker --execute='
try {
    Illuminate\Support\Facades\DB::select("SELECT 1");
    echo "DATABASE: OK".PHP_EOL;
} catch (Throwable $e) {
    echo "DATABASE: ERROR".PHP_EOL;
    echo $e->getMessage().PHP_EOL;
}
'
```

### Konfigurasi model AI

```bash
php artisan tinker --execute='
foreach (["gemini", "openrouter", "xkiro"] as $provider) {
    $config = config("services.$provider");

    echo strtoupper($provider).PHP_EOL;
    echo "Primary  : ".$config["model"].PHP_EOL;
    echo "Fallback : ".implode(", ", $config["fallback_models"]).PHP_EOL;
    echo PHP_EOL;
}
'
```

Jangan menampilkan nilai API key ketika melakukan debugging di console atau screenshot.

---

## GitHub Actions

Repository memiliki workflow CI untuk memeriksa kualitas source code.

Pemeriksaan utama dapat direplikasi secara lokal dengan:

```bash
composer ci:check
```

Tujuannya adalah memastikan perubahan tidak menimbulkan masalah pada:

- lint;
- formatting;
- TypeScript;
- static analysis;
- unit test;
- feature test.

---

## Catatan Keamanan

Beberapa hal yang perlu diperhatikan:

- jangan commit `.env`;
- jangan menulis API key langsung di source code;
- jangan menaruh credential di `compose.yaml`;
- gunakan `APP_DEBUG=false` di production;
- gunakan HTTPS di production;
- gunakan cookie secure untuk production;
- rotasi credential jika pernah terekspos;
- simpan production secret melalui Railway Variables atau secret manager lain.

File `.env.example` hanya berisi nama variable dan contoh konfigurasi yang aman untuk dipublikasikan.

---

## Catatan Tentang Hasil SkillPath AI

SkillPath AI adalah alat bantu pembelajaran.

Nilai skill, readiness, rekomendasi roadmap, dan rekomendasi proyek digunakan sebagai indikator internal untuk membantu pengguna menentukan arah belajar.

Hasil tersebut bukan:

- nilai akademik resmi;
- sertifikasi kemampuan;
- jaminan kesiapan kerja;
- pengganti penilaian dosen atau profesional.

---

## Status Project

Fitur utama yang saat ini sudah tersedia antara lain:

- authentication dan email verification;
- login Google dan Facebook;
- onboarding;
- enam jurusan;
- 54 skill;
- assessment 50 soal per jurusan;
- skill mapping;
- skill gap analysis;
- adaptive learning roadmap;
- learning material;
- reinforcement material;
- evidence-based evaluation;
- progress tracking;
- project recommendation;
- project readiness;
- AI learning assistant;
- feedback;
- admin dashboard;
- user management;
- automated testing;
- GitHub Actions;
- Docker development environment;
- Railway deployment.

Project masih dapat terus dikembangkan seiring penambahan data, materi, project, dan peningkatan pengalaman pengguna.

---

## Author

Developed and maintained by [ki1bot](https://github.com/ki1bot).

Live application: [skillpath-ai.my.id](https://skillpath-ai.my.id)
