# # Modul Pelajaran

## 📌 Fitur Create Product: 

## 🎯 Daftar Isi 
- [ ] _Membuat Controller, Model dan Migration_
- [ ] _Setting Product Migration_
- [ ] _Membuat Function store product_
- [ ] _Setting `StoreProductRequest`_
- [ ] _Membuat Routing api product_



# 📚 Materi Pelajaran
## *1. Membuat Controller, Model dan Migration*
Kita akan membuat Controller, Model dan Migration sekaligus dalam satu kali perintah menggunakan
######
`php artisan make:model Product -a`
######
dengan command diatas maka semua class yang kita butuhkan akan dibuat dan di dalam Controller otomatis terbuat function RESTful API yang akan kita gunakan nanti untuk CRUD product.

## *2. Setting Product Migration*
```php
Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('price');
            $table->integer('stock');
            $table->string('image')->nullable();
            $table->boolean('favorite')->default(false);
            $table->enum('status', ['draft', 'published', 'archived'])->default('published');
            $table->timestamps();
        });
```
**Keterangan Syntax** : 
Kode di atas digunakan untuk membuat tabel products dengan struktur sebagai berikut:

- `id()` : Kolom primary key dengan auto increment.

- `string('name')` : Kolom nama produk dengan tipe data string.

- `text('description')->nullable()` : Kolom deskripsi yang bersifat opsional.

- `integer('price')` : Kolom harga produk bertipe integer.

- `integer('stock')` : Kolom stok produk bertipe integer.

- `string('image')->nullable()` : Kolom gambar produk, opsional.

- `boolean('favorite')->default(false)` : Kolom status favorit dengan default false.

- `enum('status', ['draft', 'published', 'archived'])->default('published')` : Kolom status produk dengan tiga opsi.

- `timestamps()` : Menambahkan kolom created_at dan updated_at secara otomatis.


## *3. Membuat Function Store Product*
Kita akan membuat function store yang bertanggung jawab untuk menerima data dari request pengguna, memvalidasi data, menyimpan produk ke dalam database, menangani upload gambar jika ada, dan mengembalikan respons dalam bentuk JSON. Berikut adalah penjelasan rinci untuk setiap bagian dari function tersebut:

```php
public function store(StoreProductRequest $request){
    #Code for store product to database...
}

```

#### a. Validasi Data
```php
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'stock' => 'required',
        ]);

```

Bagian ini memastikan bahwa field `name`, `description`, `price` dan `stock` wajib diisi (required).

Jika salah satu field ini kosong, Laravel akan otomatis mengembalikan error response tanpa melanjutkan proses penyimpanan.


#### b. Membuat dan Menyimpan Data Produk ke Database

```php
$product = new Product;
$product->name = $request->name;
$product->description = $request->description;
$product->price = $request->price;
$product->stock = $request->stock;
$product->favorite = false;
$product->status = 'published';
$product->save();
```

- Membuat instance baru dari model Product.
- Mengisi properti model dengan data yang dikirim dalam request:
  - `name`: Nama produk.
  - `description`: Deskripsi produk (bisa kosong/null).
  - `price`: Harga produk.
  - `stock`: Stok produk.
  - `favorite`: Secara default diatur false.
  - `status`: Secara default diatur ke 'published'.
- Menyimpan data ke dalam database dengan save().

#### c. Menangani Upload Gambar (Jika Ada)

```php
if ($request->file('image')) {
    $image = $request->file('image');
    $image->storeAs('public/products', $product->id . '.png');
    $product->image = $product->id . '.png';
    $product->save();
}
```

- Mengecek apakah request mengandung file image (gambar produk).
- Jika ada:
  - `$image = $request->file('image');`  Mengambil file gambar dari request.
  - `$image->storeAs('public/products', $product->id . '.png');` Menyimpannya dalam direktori storage/app/public/products/ dengan nama sesuai id produk (product_id.png).
  - ` $product->image = $product->id . '.png';` Menyimpan nama file gambar dalam kolom image produk.
  - Menyimpan kembali perubahan produk `$product->save();`.

#### d.Mengembalikan Response dalam Format JSON

##### Penjelasan,
```php
return response()->json([
    'status' => true,
    'message' => 'Berhasil Create Product'
], 200);
```
- Mengembalikan respons HTTP dalam format JSON dengan:
  - `status: true` → Menunjukkan bahwa operasi berhasil.
  - `message: 'Berhasil Create Product'` → Memberikan pesan sukses kepada pengguna.
- HTTP status code 200 berarti operasi berhasil.

## 4. Setting `StoreProductRequest`
`StoreProductRequest`  yang berada di dalam parameter `function store` digunakan untuk validasi data request, sehingga controller tetap bersih dan rapi.

Kita bisa masuk ke dalam class `StoreProductRequest` lalu mengganti isi function `authorize()` yang tujuannya agar hanya user yang sudah login saja yang bisa mengakses api itu

```php
public function authorize(): bool
{
    return Auth::check();
}
```
**Penjelasan Kode :**
- `Auth::check()` akan mengembalikan true jika pengguna sudah login, sehingga hanya pengguna yang terautentikasi yang bisa mengakses API ini.
- Jika `authorize()` mengembalikan `false`, Laravel akan otomatis memberikan respons 403 Forbidden.

Selain itu juga fungsi dari class `StoreProductRequest` bisa untuk validasi input yang membuat controller akan lebih rapih, kita bisa membuat validasi di dalam function `rules ()`

```php
public function rules(): array
{
    return [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|integer|min:0',
        'stock' => 'required|integer|min:0',
        'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
    ];
}
```
**Penjelasan Aturan Validasi:**

- `'name' => 'required|string|max:255'` → Nama produk wajib diisi, berupa string, dengan panjang maksimal 255 karakter.
- `'description' => 'nullable|string'` → Deskripsi boleh kosong, tetapi jika diisi harus berupa string.
- `'price' => 'required|integer|min:0'` → Harga wajib diisi, harus bilangan bulat positif.
- `'stock' => 'required|integer|min:0'` → Stok wajib diisi, harus bilangan bulat positif.
- `'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'` → Gambar boleh kosong, harus berupa file gambar, dengan format jpg, png, atau jpeg, serta maksimal 2MB.

## 4. Membuat Routing
Kode berikut adalah cara mendefinisikan route API di Laravel menggunakan apiResource, dengan middleware autentikasi auth:sanctum:

```php 
Route::apiResource('/product', ProductController::class)->middleware('auth:sanctum');
```

- `Route::apiResource()` adalah cara cepat untuk membuat route RESTful secara otomatis.
- Secara default, apiResource akan menghasilkan lima endpoint CRUD berikut:


| HTTP Method  | URI              | Controller Method | Keterangan                          |
|-------------|-----------------|-------------------|-------------------------------------|
| **GET**     | `/product`       | `index()`         | Menampilkan semua produk           |
| **POST**    | `/product`       | `store()`         | Menyimpan produk baru              |
| **GET**     | `/product/{id}`  | `show($id)`       | Menampilkan produk berdasarkan ID  |
| **PUT/PATCH** | `/product/{id}` | `update($id)`     | Memperbarui produk berdasarkan ID  |
| **DELETE**  | `/product/{id}`  | `destroy($id)`    | Menghapus produk berdasarkan ID    |

- Perbedaan dengan `Route::resource()`
  - `apiResource()` hanya membuat endpoint API tanpa route untuk create dan edit karena API tidak memerlukan halaman formulir.
  - `resource()` mencakup semua route termasuk create dan edit.