# Password Security System - Dokumentasi

## Fitur Baru

Sistem ini telah diperbarui dengan fitur keamanan password yang lebih baik:

### 1. **Validasi Password Strength**
Password sekarang harus memenuhi persyaratan keamanan:
- ✓ Minimal 8 karakter
- ✓ Harus memiliki huruf besar (A-Z)
- ✓ Harus memiliki huruf kecil (a-z)
- ✓ Harus memiliki simbol (!@#$%^&* dll)

### 2. **Password Change Required**
Ketika admin membuat password untuk user:
- Password admin harus memenuhi semua persyaratan keamanan
- Saat user login pertama kali, mereka akan diminta untuk membuat password baru mereka sendiri
- User tidak dapat mengakses dashboard sampai password mereka diganti
- Setelah user membuat password baru sesuai persyaratan, mereka dapat mengakses sistem

### 3. **Real-time Password Validation**
- Admin dapat melihat real-time feedback saat memasukkan password di form tambah/edit user
- Indikator visual menunjukkan persyaratan mana yang sudah terpenuhi
- User juga mendapat feedback real-time saat membuat password baru

## File yang Diubah/Ditambah

### File Baru:
1. **`helpers/PasswordValidator.php`** - Class untuk validasi password strength
2. **`views/auth/change_password.php`** - Halaman untuk user mengubah password
3. **`controllers/validate_password.php`** - API endpoint untuk validasi password
4. **`database/migration_add_password_change_required.sql`** - Migration untuk update database

### File yang Diubah:
1. **`controllers/AuthController.php`** - Tambah check untuk password_change_required flag
2. **`controllers/AdminController.php`** - Tambah validasi password strength
3. **`models/User.php`** - Update method untuk handle password_change_required
4. **`views/admin/dashboard.php`** - UI improvement dengan password validation feedback

## Setup Database

1. Buka phpMyAdmin atau MySQL client
2. Jalankan query berikut untuk menambah kolom password_change_required:

```sql
ALTER TABLE `users` ADD COLUMN `password_change_required` TINYINT(1) DEFAULT 0 AFTER `last_activity`;
```

Atau gunakan file migration:
```
database/migration_add_password_change_required.sql
```

## Cara Kerja Sistem

### Saat Admin Membuat User Baru:
1. Admin membuka form "Add New User"
2. Admin mengisi username, email, password, departemen, dan role
3. Password akan di-validate secara real-time dengan indikator visual
4. Saat form disubmit:
   - Password di-validate di backend
   - Jika tidak memenuhi syarat, error ditampilkan
   - Jika valid, user dibuat dengan flag `password_change_required = 1`
5. User menerima notifikasi bahwa akun sudah dibuat

### Saat User Login Pertama Kali:
1. User login dengan username/email dan password yang dibuat admin
2. Sistem memeriksa flag `password_change_required`
3. Jika flag = 1, user diarahkan ke halaman `change_password.php`
4. User tidak bisa akses dashboard sampai membuat password baru
5. User membuat password baru sesuai persyaratan:
   - Minimal 8 karakter
   - Dengan huruf besar, huruf kecil, dan simbol
   - Ada real-time validation dengan checklist
6. Saat password baru valid dan disubmit:
   - Password di-update di database
   - Flag `password_change_required` di-set ke 0
   - User logout dan diarahkan ke login page
   - User bisa login dengan password baru mereka

### Saat Admin Update Password User:
1. Admin membuka form edit user
2. Password field kosong (opsional)
3. Jika admin mengisi password baru:
   - Password di-validate
   - Jika valid, flag `password_change_required` di-set ke 1
   - Saat user login, mereka harus mengubah password
4. Jika admin tidak mengisi password:
   - Password user tidak berubah
   - Flag tidak diubah

## Persyaratan Password

### Valid Password:
- `MyPassword123!` ✓ (8 char, uppercase, lowercase, simbol)
- `SecurePass99@` ✓ (13 char, uppercase, lowercase, simbol)
- `Admin2024#Secure` ✓ (16 char, uppercase, lowercase, simbol)

### Invalid Password:
- `password123` ✗ (no uppercase, no simbol)
- `PASSWORD` ✗ (no lowercase, no number/simbol)
- `Pass1!` ✗ (less than 8 characters)
- `Admin123` ✗ (no simbol)

## Error Messages

Jika password tidak valid, user akan melihat pesan yang jelas:
```
Password tidak memenuhi syarat:
- Password minimal 8 karakter
- Password harus memiliki huruf besar (A-Z)
- Password harus memiliki simbol (!@#$%^&* dll)
```

## Testing

### Test Admin Create User:
1. Login as admin
2. Klik "Add New User"
3. Coba berbagai password:
   - Terlalu pendek
   - Tidak ada uppercase
   - Tidak ada simbol
   - Valid password
4. Lihat real-time validation feedback

### Test User Change Password:
1. Admin create user dengan password valid
2. User login
3. Lihat di-redirect ke change password page
4. Coba berbagai password sesuai requirements
5. Setelah password berhasil diubah, user dapat akses dashboard

## Security Notes

- Semua password di-hash menggunakan `password_hash()` dengan PASSWORD_DEFAULT (bcrypt)
- Validasi dilakukan di backend juga, bukan hanya frontend
- Session dihapus setelah user mengubah password (force re-login)
- URL change_password.php protected dengan session check

## Support

Jika ada pertanyaan atau masalah dengan sistem password baru, silakan hubungi admin.
