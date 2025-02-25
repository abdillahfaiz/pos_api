# # Modul Pelajaran

## 📌 Fitur login dan logout: 
_Kita akan membuat fitur login yang dimana kita akan memproses input dari pengguna berupa email dan password dengan ketentuan sebagai berikut :_

1. Validasi input email dan password
   a. **Email** : Wajib diisi dan menggunakan format email yang sah
   b. **Password**-> Wajib diisi 
#### 
2. Validasi Email terdaftar
####
3. Validasi Password 
####
4. Jika sukses maka akan mengembalikan response json sukses

## 🎯 Daftar Isi 
- [ ] _Membuat Autentikasi Controller_
- [ ] _Membuat Routing API Autentikasi_
- [ ] _Membuat dummy user menggunakan Seeder_



# 📚 Materi Pelajaran
## *1. Membuat Auth Controller*
Auth Controller yang akan kita buat, kita simpan di folder terpisah dengan controller yang lainnya yaitu folder khusus untuk API
`php artisan make:controller Api/AuthController`
##
##### a. Membuat function/feature login
Kita bisa membuat function seperti berikut

```php
    function login(Request $request){
       // Logic autentikasi akan ditambahkan disini
    }
  ```
Variable parameter $request merupakan data yang dikirimkan dari API


  #### 1. Validasi Input Email dan Password
  ```php
    function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    }
  ```
  **Keterangan**: 
  * request yang kita tangkap akan kita validasi, untuk validasi email nya `required|email` menunjukkan wajib mengisi email dengan format email yang sah
  * untuk password maka kita validasi hanya wajib diisi
  
  #### 2. Validasi Email Terdaftar
```php
    $user = User::where('email', $request->email)->first()
```
**Keterangan Syntax** : 
* `User::where('email', $request->email)` artinya Query ke database pada tabel users untuk mencari data user berdasarkan email yang dikirim dalam request.
* `first()` artinya Mengambil satu record pertama yang cocok dengan kondisi pencarian.
* Hasil pencarian diatas akan disimpan di variable `$user` yang jika data user nya ditemukan maka variable tersebut berisikan data user jika tidak ditemukan maka variable tersebut berisikan `null`

#### 3. Validasi Password
```php
if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password salah'
            ], 401);
        }
```

**Keterangan Syntax** :
- `Hash::check($request->password, $user->password)`
  
  - Fungsi `Hash::check()` digunakan untuk membandingkan password yang diinput pengguna `($request->password)` dengan password yang tersimpan di database `($user->password)`.
  - Password di database biasanya sudah di-hash menggunakan bcrypt(), sehingga tidak bisa dibandingkan langsung dengan password dalam bentuk teks biasa.
  - Jika password tidak cocok, maka `Hash::check()` akan mengembalikan false, dan blok kode dalam if akan dieksekusi 

- Kode di dalam if
  ```php
  return response()->json([
    'status' => 'error',
    'message' => 'Password salah'
    ], 401);
  ``` 
  -   Jika password salah, sistem akan mengembalikan response JSON dengan:
    ```
    Status: "error"
    Pesan: "Password salah"
    Kode HTTP: 401 (Unauthorized), yang berarti kredensial pengguna tidak valid.
    ```
#### 4. Membuat Token

Sebelum membuat token, kita perlu meng-import larave-sanctum di dalam model User:

* Kita bisa menambahkan import `use Laravel\Sanctum\HasApiTokens;`. HasApiTokens bisa kita gunakan untuk membuat token, validasi token dan menghapus token
* Tambahkan use HasApiTokens di dalam class User seperti berikut 
```php
    class User extends Authenticatable
    {
        use HasFactory, Notifiable, HasApiTokens;
    }
```

Lalu kita lanjutkan di bagian function login di dalam AuthController dengan membuat sebuah token ketika email dan password valid setelah pengecekan sebelumnya.

```php
$token = $user->createToken('token')->plainTextToken;
```

**Keterangan Syntax**:
- `$user->createToken('token')`
  - Digunakan untuk membuat token baru untuk pengguna yang berhasil login.
  - `'token'` adalah nama token (bisa diganti, misalnya 'auth_token').
  - Laravel Sanctum digunakan untuk mengelola autentikasi berbasis token.

- `->plainTextToken`
  - Menghasilkan token dalam format plain text yang bisa langsung digunakan oleh klien untuk autentikasi.

#### 5. Mengembalikan Response JSON
```php
return response()->json([
    'token' => $token,
    'message' => 'success',
    'user' => $user,
]);
```

**Keterangan Syntax**:
- Mengembalikan respons JSON ke klien yang berisi:
  - `token` → Token yang bisa digunakan untuk autentikasi di request berikutnya.
  - `message` → Pesan "success" untuk menandakan login berhasil.
  - `user` → Data pengguna yang berhasil login.


## *2. Membuat Routing API Login*
Untuk mengakses function yang sudah kita buat sebelumnya, maka kita harus membuat routing nya yaitu dengan cara:

a. Install API
Di laravel 11, kita harus menginstall terlebih dahulu untuk membuat routing api
`php artisan install:api`

b. Membuat routing
lalu selanjutnya kita buat routing di dalam folder routes/api.php
```php
Route::post('/login', [AuthController::class, 'login']);
```
**Keterangan Syntax** : 
- Metode HTTP: 
  - `post('/login', ...)` menunjukkan bahwa ini adalah rute dengan metode POST, digunakan untuk mengirimkan data (seperti email dan password) ke server.
- Endpoint:
  - `/login` adalah path yang digunakan dalam API. Ini berarti pengguna atau aplikasi klien harus mengirim permintaan ke http://your-domain.com/api/login untuk melakukan proses login.
- Controller & Method:
  - `[AuthController::class, 'login']` berarti ketika endpoint `/login` diakses dengan metode POST, Laravel akan menjalankan method login yang ada di dalam `AuthController .


## *3. Membuat UserSeeder*
Kita akan membuat sebuah dummmy data atau initial data di dalam table User yang sudah kita buat.

`php artisan make:seeder UserSeeder`

Lalu akan terbuatlah sebuah UserSeeder di dalam folder database/seeders . Lalu didalamnya kita bisa membuat sebuah user baru

```php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
 
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => Str::random(10),
            'email' => Str::random(10).'@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
```

##### Penjelasan,
```php
DB::table('users')->insert([
    'name' => Str::random(10),
    'email' => Str::random(10).'@example.com',
    'password' => Hash::make('password'),
]);
```
**Keterangan Syntax**:
- `'name' => Str::random(10)` → Menghasilkan nama acak sepanjang 10 karakter.
- `'email' => Str::random(10).'@example.com'` → Menghasilkan email acak dengan domain `@example.com`.
- `'password' => Hash::make('password')` → Mengenkripsi password menggunakan hashing bcrypt, yang digunakan oleh Laravel secara default.

### 💡 Kesimpulan
Kode ini digunakan untuk menambahkan user dummy ke dalam tabel `users`. 
- Data yang dihasilkan akan berbeda setiap kali seeder dijalankan karena menggunakan `Str::random()`.
- Password yang disimpan sudah dalam bentuk hash sehingga aman digunakan.

Lalu selanjutnya kita migrate ulang dan kita seeding seeder yang sudah kita buat

`php artisan migrate --seed`