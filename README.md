# SkillPath AI (Galaxy Space)

SkillPath AI adalah aplikasi web yang membantu mahasiswa memahami kemampuan mereka, melihat skill yang masih perlu dikembangkan, dan menyusun jalur belajar berdasarkan jurusan, hasil assessment, progres belajar, serta evaluasi.

Aplikasi ini menggunakan pendekatan berbasis data untuk menentukan skill gap, prioritas belajar, roadmap, progres, dan kesiapan proyek. AI digunakan sebagai fitur pendamping untuk membantu menjelaskan hasil yang sudah dihitung oleh sistem, bukan sebagai penentu keputusan utama.

**Website:** https://skillpath-ai.my.id  
**Repository:** https://github.com/ki1bot/skillpath-ai

---

## Tentang SkillPath AI

Mahasiswa sering memiliki banyak pilihan materi belajar, tetapi belum tentu tahu materi mana yang perlu dipelajari lebih dahulu.

SkillPath AI dibuat untuk membantu proses tersebut menjadi lebih terarah.

Secara umum alurnya adalah:

1. Pengguna membuat akun dan melakukan verifikasi email.
2. Pengguna melengkapi profil dan memilih jurusan.
3. Pengguna mengerjakan assessment sesuai jurusan.
4. Sistem menghitung nilai kemampuan dari hasil assessment.
5. Nilai kemampuan dibandingkan dengan target skill.
6. Sistem menghitung skill gap dan menentukan prioritas.
7. Roadmap belajar dibuat berdasarkan kondisi pengguna.
8. Pengguna mempelajari materi dan mencatat progres.
9. Pengguna mengerjakan evaluasi pada materi.
10. Hasil tugas dikumpulkan melalui folder Google Drive.
11. Admin memeriksa jawaban dan bukti tugas.
12. Hasil review memperbarui progres dan kemampuan pengguna.
13. Jika hasil belum memenuhi nilai yang dibutuhkan, materi penguatan dapat diberikan.
14. Pengguna dapat mengerjakan proyek sesuai tingkat kesiapan skill.
15. Perkembangan dapat dipantau melalui dashboard dan halaman progres.

SkillPath AI tidak dimaksudkan sebagai pengganti penilaian akademik. Nilai skill dan rekomendasi yang diberikan digunakan sebagai indikator internal aplikasi untuk membantu proses belajar.

---

## Fitur Utama

### 1. Authentication

SkillPath AI menyediakan sistem autentikasi yang mencakup:

- registrasi akun;
- login;
- logout;
- verifikasi email;
- reset password;
- perubahan password;
- penghapusan akun;
- idle session timeout;
- login menggunakan Google;
- login menggunakan Facebook;
- menghubungkan akun Google atau Facebook ke akun yang sudah ada.

Pengguna yang belum menyelesaikan verifikasi email tidak dapat menggunakan fitur utama aplikasi.

---

### 2. Onboarding

Setelah masuk ke aplikasi, pengguna perlu melengkapi profil belajar dan memilih jurusan.

Data onboarding digunakan sebagai dasar untuk:

- menentukan assessment;
- menentukan daftar skill;
- membentuk roadmap;
- menampilkan rekomendasi proyek;
- menyesuaikan informasi pada dashboard.

---

### 3. Assessment

Assessment digunakan untuk mendapatkan gambaran awal kemampuan pengguna berdasarkan jurusan yang dipilih.

Setiap jurusan memiliki:

- 50 soal;
- 9 skill yang dinilai;
- soal yang disesuaikan dengan jurusan;
- sesi assessment yang disimpan selama pengguna masih berada dalam proses pengerjaan.

Sistem assessment juga memiliki mekanisme session persistence.

Jika halaman di-refresh atau koneksi terputus, sesi assessment yang sedang aktif dapat dilanjutkan.

Jika pengguna meninggalkan assessment melalui alur pembatalan, sesi aktif akan dihapus sehingga assessment berikutnya dapat dimulai dari awal.

---

### 4. Skill Mapping

Hasil assessment digunakan untuk membentuk nilai kemampuan pada masing-masing skill.

Data tersebut kemudian digunakan untuk melihat:

- kemampuan yang sudah cukup baik;
- kemampuan yang masih membutuhkan penguatan;
- selisih antara kemampuan saat ini dan target;
- skill yang perlu diprioritaskan.

Nilai skill di SkillPath AI merupakan indikator internal aplikasi dan bukan nilai akademik resmi.

---

### 5. Skill Gap Analysis

Skill gap dihitung dengan membandingkan nilai kemampuan saat ini dengan target kemampuan.

Secara sederhana:

```text
skill gap = target skill - kemampuan saat ini
```

Hasil tersebut digunakan untuk menentukan urutan prioritas pengembangan skill.

Perhitungan utama dilakukan oleh backend aplikasi. AI tidak menentukan nilai skill maupun hasil assessment.

---

### 6. Roadmap Belajar

Roadmap dibuat berdasarkan:

- jurusan pengguna;
- hasil assessment;
- skill gap;
- prerequisite;
- progres materi;
- hasil evaluasi;
- materi penguatan.

Roadmap dapat berubah ketika kondisi skill pengguna berubah.

Contohnya setelah:

- menyelesaikan assessment;
- menyelesaikan materi;
- memperoleh hasil evaluasi;
- menyelesaikan reinforcement material;
- mengalami perubahan nilai skill.

---

### 7. Materi Belajar

Setiap skill dapat memiliki materi belajar.

Data materi dapat mencakup:

- judul;
- ringkasan;
- learning objective;
- tingkat kesulitan;
- estimasi waktu belajar;
- referensi;
- practice task;
- evaluasi;
- jawaban evaluasi;
- penjelasan.

Materi terhubung langsung dengan skill sehingga roadmap tetap sesuai dengan jurusan dan kebutuhan pengguna.

---

### 8. Materi Penguatan

Jika hasil evaluasi belum memenuhi nilai yang dibutuhkan, aplikasi dapat memberikan materi penguatan.

Materi penguatan digunakan agar pengguna dapat memperbaiki pemahaman sebelum melanjutkan ke materi berikutnya.

---

## Evaluasi dan Google Drive

Evaluasi materi menggunakan bukti pengerjaan yang dikumpulkan melalui Google Drive.

Pengguna mengirim:

- jawaban evaluasi;
- folder Google Drive yang berisi hasil pengerjaan.

### Validasi Folder Google Drive

Backend melakukan beberapa pemeriksaan terhadap link Google Drive.

Aplikasi juga mendukung link Google Drive yang memiliki parameter:

```text
resourcekey
```

sehingga link sharing Google Drive tertentu tetap dapat diverifikasi.

Untuk proses verifikasi ini digunakan:

```env
GOOGLE_DRIVE_API_KEY=
GOOGLE_DRIVE_VERIFY_SUBMISSION_FOLDER=true
```

---

## Review Evaluasi oleh Admin

Setelah tugas dikumpulkan, evaluasi masuk ke halaman submission admin.

Admin dapat melihat:

- pengguna yang mengirim tugas;
- materi;
- pertanyaan evaluasi;
- jawaban pengguna;
- link folder Google Drive;
- status submission.

Admin kemudian memberikan nilai terhadap hasil evaluasi.

Saat ini nilai minimum kelulusan evaluasi adalah:

```text
70
```

Jika nilai memenuhi syarat:

- evaluasi dinyatakan lulus;
- progres materi diperbarui;
- nilai skill dapat diperbarui;
- roadmap dapat melanjutkan ke materi berikutnya.

Jika nilai belum memenuhi syarat:

- evaluasi dinyatakan belum lulus;
- nilai skill tidak dinaikkan sebagai hasil kelulusan;
- materi penguatan dapat ditambahkan.

Submission yang sudah direview tidak dapat dinilai kembali.

---

## Progress Tracking

Pengguna dapat mencatat perkembangan selama mempelajari materi.

Data progres dapat mencakup:

- persentase progres;
- waktu belajar;
- catatan;
- kendala;
- riwayat aktivitas.

Informasi tersebut juga digunakan untuk menampilkan perkembangan pengguna pada dashboard dan halaman progress.

---

## Project Recommendation

SkillPath AI menyediakan proyek sebagai sarana untuk menerapkan kemampuan yang sudah dipelajari.

Setiap proyek dapat memiliki:

- judul;
- deskripsi;
- problem statement;
- difficulty;
- estimasi waktu;
- minimum features;
- completion criteria;
- skill yang dibutuhkan.

---

# AI Learning Assistant

AI di SkillPath AI digunakan sebagai fitur pendamping.

AI dapat membantu:

- menjelaskan hasil skill gap;
- membuat ringkasan perkembangan;
- memberikan saran pembagian waktu belajar;
- merangkum kendala belajar;
- membuat variasi latihan;
- memberikan feedback proyek.

AI tidak digunakan untuk:

- menentukan jawaban assessment;
- menentukan nilai assessment;
- membuat nilai skill secara langsung;
- menentukan kelulusan evaluasi;
- menentukan hasil review tugas;
- mengubah progress secara sepihak;
- menentukan project readiness secara langsung.

Semua keputusan utama tetap berasal dari data dan aturan yang ada pada aplikasi.

---

## Public Live Chat

Halaman utama SkillPath AI memiliki fitur live chat yang dapat digunakan oleh pengunjung sebelum login.

Live chat digunakan untuk membantu menjawab pertanyaan umum mengenai SkillPath AI, seperti:

- fungsi dan tujuan SkillPath AI;
- cara menggunakan website;
- fitur yang tersedia;
- proses registrasi dan login;
- informasi umum mengenai project;
- teknologi yang digunakan pada aplikasi.

Live chat tidak digunakan untuk mengerjakan bagian pembelajaran pengguna.

Karena itu live chat tidak memberikan:

- jawaban atau kunci assessment;
- penyelesaian soal assessment;
- roadmap personal;
- pengerjaan proyek atau tugas proyek;
- hasil evaluasi;
- nilai atau skor yang seharusnya dihitung oleh aplikasi.

Live chat hanya ditampilkan pada halaman utama ketika pengunjung belum login.

Pengguna yang sudah login tidak dapat menggunakan endpoint public chat.

Akses live chat juga memiliki rate limit untuk mengurangi penggunaan endpoint secara berlebihan.

Public live chat menggunakan Juan Router dengan API key yang dapat dipisahkan dari API key AI internal aplikasi.

---

## AI Provider

SkillPath AI menggunakan empat provider AI:

- Juan Router;
- OpenRouter;
- xKiro;
- Google Gemini.

Konfigurasi model saat ini:

```text
Juan Router
├── Primary  : gpt-6-luna
└── Fallback : gpt-5.6-luna

OpenRouter
├── Primary  : nex-agi/nex-n2.5-pro:free
└── Fallback : openrouter/free

xKiro
├── Primary  : qwen/qwen3.8-max:free
└── Fallback : mistralai/mistral-large-2512

Google Gemini
├── Primary    : gemini-3.6-flash
├── Fallback 1 : gemini-3.5-flash-lite
```

---

## Automatic AI Failover

Pemilihan provider AI tidak menggunakan urutan statis sepenuhnya.

Aplikasi memiliki mekanisme health tracking melalui:

```text
AiProviderHealth
```

Konfigurasi awal provider:

```env
AI_PROVIDER_ORDER=juanrouter,openrouter,xkiro,gemini
```

Urutan tersebut hanya digunakan sebagai tie-breaker saat belum ada data performa yang cukup.

Setelah aplikasi memperoleh data request, provider dapat diurutkan berdasarkan kondisi aktual.

Sistem mencatat antara lain:

- request berhasil atau gagal;
- latency;
- jumlah kegagalan;
- waktu cooldown;
- waktu keberhasilan terakhir.

Jika provider atau model gagal:

```text
Provider gagal
      ↓
masuk cooldown
      ↓
sementara dilewati
      ↓
provider sehat berikutnya dicoba
```

Setelah cooldown selesai:

```text
Provider dicoba kembali
      ↓
berhasil
      ↓
failure counter di-reset
      ↓
provider kembali digunakan
```

Mekanisme ini menggunakan pola circuit breaker dan half-open retry.

---

## AI Cooldown

Konfigurasi default:

```env
AI_HEALTH_COOLDOWN_SECONDS=45
AI_HEALTH_MAX_COOLDOWN_SECONDS=300
AI_HEALTH_STATE_SECONDS=600
```

Cooldown bertambah ketika kegagalan terjadi berulang.

Secara umum:

```text
45 detik
↓
90 detik
↓
180 detik
↓
maksimal 300 detik
```

Jika provider berhasil kembali, status kegagalan di-reset.

Jika seluruh model sedang cooldown, aplikasi tidak memaksa request ke provider yang masih berada dalam masa cooldown.

---

## AI Latency Tracking

Provider yang berhasil digunakan akan menyimpan informasi latency melalui cache.

Latency menggunakan perhitungan yang dihaluskan agar urutan provider tidak berubah hanya karena satu request yang kebetulan sangat cepat atau sangat lambat.

Secara sederhana:

```text
70% latency sebelumnya
+
30% latency request terbaru
```

Dengan mekanisme ini, provider yang lebih stabil dan cepat dapat memperoleh prioritas lebih tinggi secara otomatis.

---

## AI Timeout

Konfigurasi timeout:

```env
AI_REQUEST_TIMEOUT=60
AI_ATTEMPT_TIMEOUT=25
AI_CONNECT_TIMEOUT=5
AI_FAILURE_CACHE_SECONDS=10
```

`AI_REQUEST_TIMEOUT` menentukan batas waktu keseluruhan proses AI.

`AI_ATTEMPT_TIMEOUT` membatasi waktu untuk satu model/provider.

Konfigurasi tersebut memberi waktu lebih panjang kepada model yang membutuhkan proses lebih lama, tetapi tetap membatasi satu percobaan agar provider lain masih dapat digunakan ketika terjadi kegagalan.

---

# Jurusan dan Skill

SkillPath AI saat ini memiliki enam jurusan dengan total 54 skill.

Setiap jurusan memiliki 9 skill utama.

---

## Sistem Informasi

### Analisis Data

- SQL dan Pengolahan Data
- Spreadsheet dan Analisis Data
- Business Intelligence dan Visualisasi Data

### Pengembangan Sistem

- Database Management
- Web Development
- System Analysis and Design

### UI/UX

- UI Design
- Wireframing dan Prototyping
- User Research

---

## Manajemen

### Marketing

- Branding
- Digital Marketing
- Market Research

### Keuangan

- Financial Planning
- Financial Analysis
- Investment Management

### Human Resources

- Recruitment and Selection
- Performance Management
- Talent Management

---

## Teknik Informatika

### Pemrograman dan Rekayasa Perangkat Lunak

- Algoritma dan Struktur Data
- Object-Oriented Programming
- Software Engineering

### Jaringan dan Sistem Komputer

- Computer Networks
- Operating Systems
- Cybersecurity

### Artificial Intelligence

- Machine Learning
- Data Science
- Computer Vision

---

## Sistem Komputer

### Arsitektur dan Organisasi Komputer

- Computer Architecture
- Digital Logic
- Microprocessor and Microcontroller

### Embedded System dan Internet of Things

- Embedded Systems
- Internet of Things
- Sensor and Actuator Integration

### Jaringan dan Keamanan Komputer

- Computer Networks
- Network Administration
- Network Security

---

## Psikologi

### Psikologi Industri dan Organisasi

- Employee Behavior
- Organizational Development
- Psychological Assessment

### Konseling

- Counseling Skills
- Interpersonal Communication
- Emotional Intelligence

### Penelitian Psikologi

- Research Methodology
- Interview dan Observation
- Survey dan Data Analysis

---

## Ilmu Komunikasi

### Public Relations

- Media Relations
- Corporate Communication
- Crisis Communication

### Jurnalistik

- News Writing
- Journalistic Interview
- News Reporting

### Digital Media

- Content Creation
- Social Media Management
- Video Production

---

# Tech Stack

## Backend

| Teknologi         | Penggunaan                              |
| ----------------- | --------------------------------------- |
| PHP 8.4+          | Runtime backend                         |
| Laravel 13        | Backend framework                       |
| Inertia.js 3      | Penghubung Laravel dan React            |
| Laravel Fortify   | Authentication                          |
| Laravel Socialite | Login Google dan Facebook               |
| Laravel Wayfinder | Integrasi route Laravel dengan frontend |
| PostgreSQL        | Database                                |
| Resend            | Pengiriman email                        |

---

## Frontend

| Teknologi      | Penggunaan                   |
| -------------- | ---------------------------- |
| React 19       | UI                           |
| TypeScript     | Type checking                |
| Tailwind CSS 4 | Styling                      |
| Vite 8         | Development server dan build |
| Radix UI       | UI primitive                 |
| Lucide React   | Icon                         |
| Recharts       | Visualisasi data             |
| Sonner         | Toast notification           |

---

## Development dan Quality Control

Project menggunakan:

- Docker;
- Docker Compose;
- PHPUnit;
- PHPStan;
- Larastan;
- Laravel Pint;
- ESLint;
- Prettier;
- TypeScript type checking.

---

# Menjalankan Project dengan Docker

Docker merupakan cara yang direkomendasikan untuk menjalankan project secara lokal.

## 1. Clone Repository

```bash
git clone https://github.com/ki1bot/skillpath-ai.git
cd skillpath-ai
```

---

## 2. Buat File Environment

```bash
cp .env.example .env
```

Isi variable yang diperlukan pada `.env`.

File `.env` tidak boleh di-commit karena berisi credential dan API key.

---

## 3. Buat PostgreSQL Volume

Project menggunakan external Docker volume untuk menyimpan data PostgreSQL.

Jalankan satu kali:

```bash
docker volume create skillpath-ai-postgres-persistent
```

---

## 4. Jalankan Container

```bash
docker compose up -d --build
```

Container development akan menangani beberapa proses awal secara otomatis, seperti:

- install Composer dependency jika diperlukan;
- install npm dependency jika diperlukan;
- generate `APP_KEY` jika masih kosong;
- menjalankan migration;
- membuat storage link;
- membersihkan cache aplikasi;
- menjalankan Laravel;
- menjalankan queue listener;
- menjalankan Vite development server.

---

## 5. Cek Container

```bash
docker compose ps
```

Aplikasi dapat dibuka melalui:

```text
http://localhost:8080
```

Vite development server menggunakan:

```text
http://localhost:5173
```

---

# PostgreSQL pada Docker

Service PostgreSQL menggunakan:

```text
postgres
```

sebagai hostname di dalam Docker network.

Pada `.env` local Anda dapat tetap menggunakan:

```env
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=skillpathai
DB_USERNAME=root
DB_PASSWORD=
```

Ketika aplikasi berjalan melalui Docker Compose, `DB_HOST` dioverride menjadi:

```text
postgres
```

oleh `compose.yaml`.

PostgreSQL juga dipublish ke host pada port:

```text
5433
```

sehingga jika ingin mengakses database dari aplikasi desktop pada host, koneksi dapat menggunakan:

```text
Host : localhost
Port : 5433
```

---

# Menjalankan Project Tanpa Docker

Kebutuhan utama:

- PHP 8.4.1 atau lebih baru;
- Composer;
- Node.js;
- npm;
- PostgreSQL.

Install dependency:

```bash
composer install
npm install
```

Buat `.env`:

```bash
cp .env.example .env
```

Generate key:

```bash
php artisan key:generate
```

Jalankan migration:

```bash
php artisan migrate
```

Jalankan development environment:

```bash
composer run dev
```

Perintah tersebut menjalankan:

```text
Laravel development server
Queue listener
Vite development server
```

Jika tidak menggunakan Docker, pastikan konfigurasi database dan `APP_URL` sesuai dengan environment lokal.

---

# Konfigurasi Environment

Contoh lengkap tersedia pada:

```text
.env.example
```

---

## Database

Contoh local:

```env
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=skillpathai
DB_USERNAME=root
DB_PASSWORD=
```

Production menggunakan PostgreSQL melalui environment variable deployment.

---

## Session

Local:

```env
SESSION_DRIVER=database
SESSION_EXPIRE_ON_CLOSE=true
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax
```

Production disarankan menggunakan:

```env
SESSION_DRIVER=database
SESSION_EXPIRE_ON_CLOSE=true
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

---

## Idle Timeout

```env
AUTH_IDLE_TIMEOUT=10
```

Nilainya menggunakan satuan menit.

Jika pengguna tidak aktif melebihi batas tersebut, session dapat dihentikan oleh aplikasi.

---

## Cache

Aplikasi menggunakan Laravel Cache untuk beberapa fitur, termasuk health state AI.

Konfigurasi yang digunakan:

```env
CACHE_STORE=database
```

Database cache membuat state AI tetap tersedia pada environment yang menggunakan lebih dari satu proses aplikasi.

---

# Konfigurasi AI

## Juan Router

```env
JUANROUTER_API_KEY=
JUANROUTER_MODEL=gpt-6-luna
JUANROUTER_FALLBACK_MODELS=gpt-5.6-luna
JUANROUTER_BASE_URL=https://router.juan.web.id/v1
JUANROUTER_REASONING_EFFORT=low
```

Juan Router menggunakan `gpt-6-luna` sebagai primary model dan `gpt-5.6-luna` sebagai fallback.

---

## OpenRouter

```env
OPENROUTER_API_KEY=
OPENROUTER_MODEL=nex-agi/nex-n2.5-pro:free
OPENROUTER_FALLBACK_MODELS=openrouter/free
OPENROUTER_BASE_URL=https://openrouter.ai/api/v1
```

---

## Google Gemini

```env
GEMINI_API_KEY=
GEMINI_MODEL=gemini-3.6-flash
GEMINI_FALLBACK_MODELS=gemini-3.5-flash-lite
GEMINI_BASE_URL=https://generativelanguage.googleapis.com/v1beta
```

---

## xKiro

```env
XKIRO_API_KEY=
XKIRO_MODEL=qwen/qwen3.8-max:free
XKIRO_FALLBACK_MODELS=mistralai/mistral-large-2512
XKIRO_BASE_URL=https://api.xkiro.com/v1
```

---

## Public Live Chat

Public live chat menggunakan konfigurasi Juan Router yang terpisah dari konfigurasi AI internal.

```env
JUANROUTER_CHAT_API_KEY=
JUANROUTER_CHAT_MODEL=gpt-6-luna
JUANROUTER_CHAT_FALLBACK_MODELS=gpt-5.6-luna
JUANROUTER_CHAT_BASE_URL=https://router.juan.web.id/v1

PUBLIC_CHAT_ENABLED=true
PUBLIC_CHAT_REQUEST_TIMEOUT=50
PUBLIC_CHAT_MAX_HISTORY_MESSAGES=8
```

Pada production, API key live chat sebaiknya dibuat terpisah dari `JUANROUTER_API_KEY`.

Dengan konfigurasi tersebut, quota public live chat dapat diatur sendiri tanpa memengaruhi API key AI internal.

---

## AI Failover dan Timeout

```env
AI_PROVIDER_ORDER=juanrouter,openrouter,xkiro,gemini

AI_REQUEST_TIMEOUT=60
AI_ATTEMPT_TIMEOUT=25
AI_CONNECT_TIMEOUT=5
AI_FAILURE_CACHE_SECONDS=10

AI_HEALTH_COOLDOWN_SECONDS=45
AI_HEALTH_MAX_COOLDOWN_SECONDS=300
AI_HEALTH_STATE_SECONDS=600
```

Urutan pada `AI_PROVIDER_ORDER` digunakan sebagai tie-breaker awal.

Setelah aplikasi memiliki data performa, urutan provider dapat berubah berdasarkan health state dan latency masing-masing provider.

---

# Login Google

```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://localhost:8080/auth/google/callback
```

Untuk production:

```env
GOOGLE_REDIRECT_URI=https://skillpath-ai.my.id/auth/google/callback
```

---

# Login Facebook

```env
FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
FACEBOOK_REDIRECT_URI=http://localhost:8080/auth/facebook/callback
```

Untuk production:

```env
FACEBOOK_REDIRECT_URI=https://skillpath-ai.my.id/auth/facebook/callback
```

---

# Email

Email aplikasi dikirim melalui Resend.

```env
MAIL_MAILER=resend
RESEND_API_KEY=

MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Untuk production, gunakan alamat email dari domain yang sudah dikonfigurasi pada provider email.

---

# Seeder

Secara default:

```env
RUN_SEEDER=false
RUN_ASSESSMENT_SEEDER=false
```

Untuk menjalankan seluruh database seeder saat container dimulai:

```env
RUN_SEEDER=true
```

Untuk melakukan sinkronisasi data assessment dan materi akademik:

```env
RUN_ASSESSMENT_SEEDER=true
```

Gunakan opsi ini dengan hati-hati pada production.

---

# Admin

Admin memiliki akses ke halaman pengelolaan untuk:

- dashboard admin;
- jurusan;
- skill;
- career skill;
- prerequisite;
- assessment;
- pertanyaan assessment;
- learning material;
- project;
- project skill;
- evaluation submission;
- feedback.

---

## User Management

User management memiliki proteksi tambahan selain role admin.

Aksesnya menggunakan konfigurasi:

```env
USER_MANAGER_EMAIL=
```

Akun yang dapat mengelola role pengguna harus memenuhi aturan akses yang diterapkan pada backend.

Pengelola dapat mengubah role pengguna antara:

```text
student
admin
```

Pengelola tidak dapat mengubah role dirinya sendiri melalui alur tersebut.

---

# Public Pages

Beberapa halaman dapat dibuka tanpa login:

```text
/
 /tentang
 /karier
 /karier/{career}
 /privacy-policy
 /terms
 /data-deletion
```

Halaman tersebut digunakan untuk informasi umum project, karier, kebijakan privasi, ketentuan, dan kebutuhan integrasi platform.

Public live chat ditampilkan pada halaman utama (`/`) ketika pengunjung belum login.

Endpoint public live chat:

```text
POST /bantuan/chat
```

Endpoint tersebut memiliki rate limit dan tidak dapat digunakan oleh pengguna yang sudah login.

---

# Quality Check

Sebelum melakukan deployment atau commit perubahan besar, jalankan:

```bash
docker compose exec app composer ci:check
```

Untuk memeriksa production build frontend:

```bash
docker compose exec app npm run build
```

---

## Test AI

Untuk memeriksa fitur AI:

```bash
docker compose exec app php artisan test \
    --filter='AiProviderHealthTest|AiAutomaticFailoverTest|AiInsightServiceTest|AiExplanationServiceTest|JuanRouterIntegrationTest|PublicChatTest'
```

Test tersebut mencakup:

- Juan Router;
- OpenRouter;
- xKiro;
- Gemini;
- public live chat;
- fallback model;
- dynamic provider ordering;
- cooldown;
- automatic failover;
- half-open retry;
- validasi output AI;
- pembatasan public live chat untuk assessment, roadmap, dan pengerjaan proyek.

---

## Test Google Drive

```bash
docker compose exec app php artisan test \
    --filter=GoogleDriveSubmissionFolderTest
```

Test tersebut mencakup:

- link file Google Drive ditolak;
- link folder diterima;
- folder private atau tidak tersedia ditolak;
- folder kosong ditolak;
- folder yang memiliki isi diterima;
- Google Drive resource key diteruskan saat diperlukan.

---

# Command Development

## Laravel Pint

Memformat PHP:

```bash
docker compose exec app ./vendor/bin/pint
```

Memeriksa format tanpa mengubah file:

```bash
docker compose exec app ./vendor/bin/pint --test
```

---

## PHPStan

```bash
docker compose exec app ./vendor/bin/phpstan analyse --memory-limit=512M
```

---

## PHPUnit

```bash
docker compose exec app php artisan test
```

atau:

```bash
docker compose exec app composer run test
```

---

## ESLint

```bash
docker compose exec app npm run lint:check
```

---

## Prettier

```bash
docker compose exec app npm run format:check
```

---

## TypeScript

```bash
docker compose exec app npm run types:check
```

---

## Frontend Build

```bash
docker compose exec app npm run build
```

---

# Deployment

Production SkillPath AI berjalan di Railway.

Website production:

```text
https://skillpath-ai.my.id
```

Konfigurasi production utama antara lain:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://skillpath-ai.my.id

DB_CONNECTION=pgsql
DB_URL=
DB_SSLMODE=require

SESSION_DRIVER=database
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

CACHE_STORE=database

QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
```

---

# Catatan Tentang AI

Model gratis pada provider eksternal dapat berubah, mengalami rate limit, atau tidak tersedia untuk sementara.

Karena itu SkillPath AI tidak bergantung pada satu provider.

Jika satu model bermasalah, aplikasi dapat mencoba model atau provider lain yang masih tersedia.

Walaupun AI digunakan pada beberapa bagian aplikasi, hasil AI tidak menjadi sumber utama dalam perhitungan skill, assessment, roadmap, evaluasi, maupun project readiness.

---

# Catatan Tentang Hasil SkillPath AI

SkillPath AI merupakan alat bantu pembelajaran.

Hasil yang ditampilkan tidak dapat dianggap sebagai:

- nilai akademik resmi;
- sertifikasi kemampuan;
- hasil psikotes resmi;
- keputusan akademik;
- jaminan kesiapan kerja;
- pengganti penilaian dosen atau profesional.

Nilai skill, roadmap, dan rekomendasi digunakan untuk membantu pengguna menentukan arah belajar berdasarkan data yang tersedia pada aplikasi.

---

# Status Project

Fitur utama yang sudah tersedia saat ini:

- registrasi dan login;
- verifikasi email;
- reset password;
- login Google;
- login Facebook;
- account linking;
- idle session timeout;
- onboarding;
- enam jurusan;
- 54 skill;
- 50 soal assessment per jurusan;
- persistent assessment session;
- skill mapping;
- skill gap analysis;
- adaptive learning roadmap;
- learning material;
- reinforcement material;
- Google Drive evidence validation;
- admin evaluation review;
- progress tracking;
- project recommendation;
- project readiness;
- AI learning assistant;
- Juan Router;
- OpenRouter;
- xKiro;
- Google Gemini;
- public live chat;
- dedicated API key untuk public live chat;
- automatic AI failover;
- dynamic AI provider health;
- responsive public pages untuk desktop, tablet, dan mobile;
- feedback;
- admin dashboard;
- user management;
- automated testing;
- Docker development environment;
- Railway deployment.

Project masih dapat dikembangkan lebih lanjut seiring penambahan materi, assessment, project, integrasi, dan peningkatan pengalaman pengguna.

---

# Author

Developed and maintained by **ki1bot**.

GitHub:

https://github.com/ki1bot

Repository:

https://github.com/ki1bot/skillpath-ai

Live website:

https://skillpath-ai.my.id
