# Product Requirements Document (PRD)

## 1. Informasi Produk
- Nama Produk: University Talent Hub
- Versi: 0.1 MVP
- Deskripsi: Platform digital MVP yang menjadi ekosistem talenta mahasiswa berbasis gamifikasi untuk memetakan, mengembangkan, dan mempertemukan talenta mahasiswa dengan peluang.

## 2. Tujuan Produk
- Menyediakan basis data talenta mahasiswa yang terintegrasi.
- Memudahkan perguruan tinggi dan industri menemukan mahasiswa sesuai kebutuhan.
- Meningkatkan keterlibatan mahasiswa melalui sistem verifikasi, poin, reward, dan leaderboard.
- Membantu mahasiswa membangun profil profesional secara terstruktur.

## 3. Masalah yang Diselesaikan
- Kompetensi mahasiswa tersebar dan belum terdokumentasi.
- Sulit mencari mahasiswa yang sesuai dengan kebutuhan industri.
- Belum ada sistem verifikasi dan pencatatan prestasi yang konsisten.
- Potensi mahasiswa dari unit kegiatan maupun prestasi akademik/non-akademik belum terlihat.
- Belum ada mekanisme reward yang mendorong partisipasi aktif.

## 4. Target Pengguna
### A. Administrator
- Mengelola pengguna, verifikasi, reward, leaderboard, dan opportunity.

### B. Mahasiswa
- Mengelola profil, skill, sertifikat, portfolio, dan melihat perkembangan gamifikasi.

## 5. Scope MVP
### Fitur Utama
1. Autentikasi login untuk admin dan mahasiswa.
2. Profil mahasiswa lengkap.
3. Manajemen skill, sertifikat, dan portfolio.
4. Submit verifikasi untuk data yang diunggah.
5. Review dan keputusan verifikasi oleh admin.
6. Pemberian poin setelah approve.
7. Manajemen reward dan redeem reward.
8. Leaderboard peringkat mahasiswa.
9. Posting opportunity untuk mahasiswa.
10. Rekomendasi berbasis AI sebagai fitur tambahan di tahap berikutnya.

## 6. Fungsionalitas Berdasarkan Peran
### Administrator
- Login
- Melihat dashboard statistik
- Melihat daftar mahasiswa
- Mencari mahasiswa berdasarkan kompetensi
- Meninjau submission skill, sertifikat, dan portfolio
- Approve/Reject submission
- Memberikan poin jika disetujui
- Mengelola reward
- Melihat leaderboard
- Membuat posting opportunity

### Mahasiswa
- Login
- Melengkapi profil akun
- Menambahkan skill
- Mengunggah sertifikat
- Mengunggah portfolio
- Submit data untuk verifikasi
- Melihat status submission
- Melihat leaderboard
- Melihat reward yang tersedia
- Melihat rekomendasi sistem

## 7. Alur Bisnis Utama
1. Mahasiswa melengkapi profil.
2. Mahasiswa menambahkan skill, sertifikat, atau portfolio.
3. Mahasiswa melakukan submit untuk verifikasi.
4. Admin menerima notifikasi dan melakukan review.
5. Jika reject, mahasiswa tidak menerima poin.
6. Jika approve, admin memberikan poin sesuai aturan.
7. Sistem memperbarui leaderboard.
8. Mahasiswa dapat menukarkan reward dengan poin yang dimiliki.

## 8. Aturan Gamifikasi
- Poin diberikan hanya setelah verifikasi admin berhasil.
- Setiap jenis submission dapat memiliki aturan poin berbeda.
- Poin tidak diberikan pada submission yang ditolak.
- Reward dapat ditukarkan sesuai batas poin yang ditentukan.

## 9. Kebutuhan Non-Fungsional
- Keamanan: login aman, role-based access control, proteksi data.
- Performa: respons API cepat untuk dashboard dan pencarian.
- Ketersediaan: sistem dapat diakses 24/7 dengan uptime yang stabil.
- Skalabilitas: arsitektur terpisah memudahkan pengembangan fitur lebih lanjut.
- Maintainability: kode terstruktur dan dokumentasi jelas.

## 10. Arsitektur Sistem yang Disarankan
- Frontend: Laravel
- Backend: NestJS
- Database: PostgreSQL atau MySQL
- Cache/Queue: Redis
- Storage: local storage atau cloud storage untuk file sertifikat/portfolio

## 11. Modul Inti Backend
- Auth Module
- User Module
- Student Profile Module
- Skill Module
- Certificate Module
- Portfolio Module
- Verification Module
- Gamification Module
- Reward Module
- Opportunity Module
- Leaderboard Module

## 12. Data Entity Inti
- User
- StudentProfile
- Skill
- Certificate
- Portfolio
- VerificationSubmission
- PointLedger
- Reward
- Redemption
- Opportunity

## 13. Acceptance Criteria MVP
- Mahasiswa dapat login dan melengkapi profil.
- Mahasiswa dapat menambahkan dan mengajukan skill, sertifikat, portfolio.
- Admin dapat melihat dan memverifikasi submission.
- Admin dapat memberikan poin dan mengelola reward.
- Leaderboard dapat ditampilkan berdasarkan poin.
- Mahasiswa dapat melihat status submission dan reward yang tersedia.

## 14. Timeline MVP (Saran)
- Minggu 1-2: Setup project, auth, role management.
- Minggu 3-4: Profil mahasiswa, skill, sertifikat, portfolio.
- Minggu 5: Submission dan approval workflow.
- Minggu 6: Gamification, reward, leaderboard.
- Minggu 7: Opportunity posting dan dashboard admin.
- Minggu 8: Testing, polish, deployment.

## 15. Catatan Pengembangan
- Fitur AI recommendation dapat dikembangkan setelah MVP stabil.
- Sistem dapat dikembangkan lebih lanjut dengan notifikasi email, analytics, dan integrasi industri.
