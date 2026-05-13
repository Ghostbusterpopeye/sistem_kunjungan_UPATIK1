# Panduan Kontribusi

Terima kasih telah berkontribusi pada proyek ini. Untuk menjaga kualitas kode dan kolaborasi yang efektif, harap ikuti panduan berikut.

## Alur Kerja Git
1. **Fork** repositori ini.
2. Buat branch baru untuk fitur atau perbaikan Anda:
   `git checkout -b feat/nama-fitur` atau `git checkout -b fix/nama-perbaikan`.
3. Lakukan **Commit** dengan pesan yang jelas:
   `git commit -m "Penjelasan singkat mengenai perubahan"`.
4. **Push** ke branch Anda:
   `git push origin nama-branch`.
5. Ajukan **Pull Request** ke branch utama.

## Standar Kode
* Gunakan indentasi yang konsisten sesuai konfigurasi proyek.
* Berikan komentar pada logika yang kompleks.
* Pastikan kode telah melalui proses pengujian mandiri sebelum diajukan.

## Pelaporan Masalah (Issues)
* Gunakan fitur *Issues* untuk melaporkan bug atau saran fitur baru.
* Sertakan deskripsi yang jelas, langkah-langkah reproduksi (untuk bug), dan ekspektasi hasil.

## Quality Assurance & Review
* Setiap Pull Request akan ditinjau oleh pengelola proyek.
* Pastikan perubahan tidak merusak fungsi yang sudah ada (*regression testing*).
* Perubahan pada struktur database harus disertai dengan pembaruan skema atau file migrasi yang relevan.

## Lisensi
Dengan berkontribusi pada proyek ini, Anda menyetujui bahwa kontribusi Anda akan mengikuti lisensi yang digunakan oleh repositori ini.
