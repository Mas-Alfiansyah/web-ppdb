# Cara Mengaktifkan PHP GD Extension di XAMPP

## Error yang Muncul

```
Fatal error: Call to undefined function imagecreatefromjpeg()
```

## Solusi: Aktifkan GD Extension

### Langkah-langkah:

1. **Buka php.ini**

   - Lokasi: `C:\xampp\php\php.ini`
   - Atau buka XAMPP Control Panel → Apache → Config → php.ini

2. **Cari baris berikut** (gunakan Ctrl+F):

   ```
   ;extension=gd
   ```

   atau

   ```
   ;extension=php_gd2.dll
   ```

3. **Hapus tanda titik koma (;)** di depan baris tersebut:

   ```
   extension=gd
   ```

   atau

   ```
   extension=php_gd2.dll
   ```

4. **Save file php.ini**

5. **Restart Apache** di XAMPP Control Panel

   - Stop Apache
   - Start Apache lagi

6. **Verifikasi GD Extension Aktif**
   - Buat file `phpinfo.php` di `C:\xampp\htdocs\`:
   ```php
   <?php phpinfo(); ?>
   ```
   - Buka di browser: `http://localhost/phpinfo.php`
   - Cari "gd" → Seharusnya muncul GD Support enabled

## Catatan

Sistem sudah diupdate untuk **skip thumbnail generation** jika GD extension tidak tersedia. Upload foto tetap akan berhasil, hanya saja thumbnail tidak akan dibuat.

Namun, **sangat disarankan** untuk mengaktifkan GD extension agar:

- Thumbnail profil dapat dibuat
- Performa lebih baik saat menampilkan foto
- Fitur image processing berfungsi optimal
