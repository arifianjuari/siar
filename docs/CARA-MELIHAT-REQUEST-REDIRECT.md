# Cara Melihat Request Redirect dan Cookie di Browser

## 📋 Langkah-Langkah

### 1. Buka Developer Tools

Tekan **F12** atau **Right Click** > **Inspect** > Tab **Network**

### 2. Siapkan Network Tab

1. Pastikan **"Preserve log"** dicentang (penting untuk melihat redirect)
2. Pastikan **"Disable cache"** dicentang (opsional, untuk memastikan tidak ada cache)
3. Filter: Pilih **"All"** atau **"Doc"** untuk melihat document requests

### 3. Login

1. Submit form login
2. Perhatikan Network tab - akan muncul request dengan status **302 Found**

### 4. Lihat Request Redirect (302)

**Cari request dengan:**

- **Name:** `login` atau URL login
- **Status:** `302 Found` (atau `302` dengan warna kuning/orange)
- **Type:** `document` atau `fetch`

**Klik request tersebut** untuk melihat detail:

#### Tab Headers

**Request Headers:**

- Lihat **Cookie** header - berisi cookie yang dikirim ke server
- Cari `siar_session=...` - ini adalah session ID yang dikirim

**Response Headers:**

- Lihat **Location** - URL tujuan redirect (misal: `/superadmin/dashboard`)
- **PENTING:** Lihat **Set-Cookie** headers:
  - Harus ada **2 Set-Cookie** headers:
    1. `Set-Cookie: siar_session=; expires=...` (untuk hapus cookie lama)
    2. `Set-Cookie: siar_session=amGvnsfWebXcWfrizkEQghrHlv8948TsT9eU4ipF; ...` (untuk set cookie baru)

#### Tab Preview

Menampilkan preview response (biasanya kosong untuk redirect)

#### Tab Response

Menampilkan response body (biasanya kosong untuk redirect)

### 5. Lihat Request Setelah Redirect (200)

Setelah redirect, akan ada request baru dengan:

- **Name:** `dashboard` atau `/superadmin/dashboard`
- **Status:** `200 OK` (hijau)
- **Type:** `document`

**Klik request ini** untuk melihat:

- **Request Headers** > **Cookie** - harus berisi `siar_session=...` dengan session ID yang sama dengan yang di-set di redirect
- **Response Headers** - untuk melihat response dari server

### 6. Lihat Cookie di Application Tab

1. Buka tab **Application** (di samping Network)
2. Di sidebar kiri, expand **Cookies**
3. Klik domain: `https://siar-main-bot1z9.laravel.cloud`
4. Cari cookie **`siar_session`**

**Perhatikan:**

- **Name:** `siar_session`
- **Value:** Session ID (40 karakter) - harus sama dengan yang di-set di redirect
- **Domain:** `siar-main-bot1z9.laravel.cloud` (tanpa titik di depan)
- **Path:** `/`
- **Expires / Max-Age:** Waktu expire cookie
- **Size:** Ukuran cookie
- **HttpOnly:** ✅ (checked)
- **Secure:** ✅ (checked) - untuk HTTPS
- **SameSite:** `Lax`

## 🔍 Yang Perlu Diperiksa

### Setelah Login (Request 302)

1. **Response Headers** > **Set-Cookie:**

   - ✅ Harus ada 2 Set-Cookie headers
   - ✅ Cookie pertama: `siar_session=; expires=...` (hapus cookie lama)
   - ✅ Cookie kedua: `siar_session=amGvnsfWebXcWfrizkEQghrHlv8948TsT9eU4ipF; ...` (set cookie baru)
   - ✅ Cookie baru harus memiliki:
     - `Secure` flag (untuk HTTPS)
     - `HttpOnly` flag
     - `SameSite=Lax`
     - `Path=/`
     - `Domain` kosong atau sesuai domain

2. **Response Headers** > **Location:**
   - ✅ Harus mengarah ke dashboard (misal: `/superadmin/dashboard`)

### Setelah Redirect (Request 200)

1. **Request Headers** > **Cookie:**

   - ✅ Harus berisi `siar_session=amGvnsfWebXcWfrizkEQghrHlv8948TsT9eU4ipF`
   - ✅ Session ID harus sama dengan yang di-set di redirect

2. **Response:**
   - ✅ Status 200 OK
   - ✅ Halaman dashboard ter-load

### Di Application Tab > Cookies

1. **Cookie `siar_session`:**
   - ✅ Value harus sama dengan session ID yang di-set di redirect
   - ✅ Domain harus sesuai
   - ✅ Secure flag harus checked
   - ✅ HttpOnly flag harus checked
   - ✅ SameSite harus `Lax`

## 🐛 Troubleshooting

### Jika Tidak Ada Set-Cookie di Response Headers

**Masalah:** Cookie tidak ter-set oleh server

**Solusi:**

- Cek log Laravel Cloud untuk error
- Pastikan code sudah di-deploy
- Pastikan `withCookie()` dipanggil dengan benar

### Jika Set-Cookie Ada Tapi Cookie Tidak Ter-Set di Browser

**Masalah:** Browser tidak menerima cookie

**Solusi:**

- Cek domain cookie - harus sesuai dengan domain aplikasi
- Cek Secure flag - harus true untuk HTTPS
- Cek SameSite - harus `Lax` atau `None; Secure`
- Clear cookies dan coba lagi

### Jika Cookie Ter-Set Tapi Request Berikutnya Tidak Mengirim Cookie

**Masalah:** Cookie tidak ter-kirim kembali ke server

**Solusi:**

- Cek domain cookie - harus match dengan domain request
- Cek Path - harus `/` atau sesuai path request
- Cek Secure flag - harus true untuk HTTPS
- Clear cookies dan login ulang

### Jika Session ID Berbeda Antara Redirect dan Request Berikutnya

**Masalah:** Cookie tidak ter-update dengan benar

**Solusi:**

- Pastikan cookie lama dihapus sebelum set cookie baru
- Cek log untuk "Removing old session cookie"
- Clear cookies dan login ulang

## 📸 Screenshot yang Diperlukan untuk Debugging

Jika masih ada masalah, kirimkan screenshot:

1. **Network Tab** > Request `login` (302) > Tab **Headers** > **Response Headers**

   - Tunjukkan semua `Set-Cookie` headers

2. **Network Tab** > Request `dashboard` (200) > Tab **Headers** > **Request Headers**

   - Tunjukkan `Cookie` header

3. **Application Tab** > **Cookies** > `siar_session`

   - Tunjukkan semua attributes cookie

4. **Console Tab**
   - Tunjukkan error atau warning (jika ada)

## 🎯 Quick Check

Setelah login, cek cepat:

1. **Network Tab:**

   - ✅ Request `login` → Status `302 Found`
   - ✅ Response Headers → Ada `Set-Cookie: siar_session=...`
   - ✅ Request `dashboard` → Status `200 OK`
   - ✅ Request Headers → Ada `Cookie: siar_session=...`

2. **Application Tab:**
   - ✅ Cookie `siar_session` ada
   - ✅ Value sama dengan yang di-set di redirect
   - ✅ Secure dan HttpOnly checked

Jika semua ✅, berarti cookie ter-set dengan benar!

