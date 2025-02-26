# # Modul Pelajaran

## 📌 Fitur Register: 
_Kita akan membuat fitur login yang dimana kita akan memproses input dari pengguna berupa email dan password dengan ketentuan sebagai berikut :_

1. Validasi input name, email dan password
   a. **Name** : Wajib diisi
   a. **Email** : Wajib diisi dan menggunakan format email yang sah dan email yang unique
   b. **Password**-> Wajib diisi 
#### 

2. Create new user

3. Return response succes

## 🎯 Daftar Isi 
- [ ] _Membuat function register di AuthController_
- [ ] _Membuat Routing API Register_


# 📚 Materi Pelajaran
## *1. Membuat function register di AuthController*
Masih di dalam AuthController kita membuat sebuah function baru yaitu register
##
##### a. Membuat function/feature register
Kita bisa membuat function seperti berikut

```php
    function register(Request $request){
       // Logic autentikasi akan ditambahkan disini
    }
  ```
Variable parameter $request merupakan data yang dikirimkan dari API


  #### 1. Validasi Input Name, Email dan Password
  ```php
    function login(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ]);
    }
  ```
  **Keterangan**: 
  * wajib mengisi name
  * request yang kita tangkap akan kita validasi, untuk validasi email nya `required|email|unique:users,email` menunjukkan wajib mengisi email dengan format email yang sah dan email nya wajib unique di dalam table user di kolom email
  * untuk password maka kita validasi hanya wajib diisi
  
  #### 2. Membuat user baru
```php
    User::create([
        "name" => $request->name,
        "email" => $request->email,
        "password" => Hash::make($request->password)
    ]);
```
**Keterangan Syntax** : 
* `User::create` artinya Query ke database pada tabel users untuk membuat data user baru  yang dikirim dalam request.

#### 3. Return Response Success
```php
return response()->json([
            'message' => 'Berhasil Register',
        ]);
```


## *2. Membuat Routing API Register*
lalu selanjutnya kita buat routing di dalam folder routes/api.php
```php
Route::post('/register', [AuthController::class, 'register']);
```
**Keterangan Syntax** : 
- Metode HTTP: 
  - `post('/register', ...)` menunjukkan bahwa ini adalah rute dengan metode POST, digunakan untuk mengirimkan data (seperti name, email dan password) ke server.
- Endpoint:
  - `/register` adalah path yang digunakan dalam API. Ini berarti pengguna atau aplikasi klien harus mengirim permintaan ke http://your-domain.com/api/register untuk melakukan proses register.
- Controller & Method:
  - `[AuthController::class, 'register']` berarti ketika endpoint `/register` diakses dengan metode POST, Laravel akan menjalankan method login yang ada di dalam `AuthController .