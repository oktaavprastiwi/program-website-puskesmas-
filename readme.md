# laravel-k-means-app-okta

Aplikasi Mengelompokkan Diagnosa Penyakit, Penyakit Rendah, Sedang dan Tinggi Menggunakan Algoritma Clustering K-Means

Installing
----------
1. Extract file laravel-k-means-app-okta-main.zip
2. Copy direktori hasil extract ke htdocs (XAMPP)
3. Ubah nama file .env.example menjadi .env
4. Apabila belum menginstall composer, maka download disini -> https://getcomposer.org/download/
5. Jalankan perintah pada terminal/cmd -> composer install
6. Jalankan perintah pada terminal/cmd -> php artisan key:generate
7. Atur variable (DB_DATABASE=namadb, DB_USERNAME=username, DB_PASSWORD=password) pada file .env, sesuaikan dengan pengaturan database yang ada
8. Jalankan perintah pada terminal/cmd -> php artisan migrate
9. Jalankan perintah pada terminal/cmd -> php artisan db:seed
10. Jalankan perintah pada terminal/cmd -> php artisan serve dan php artisan queue:work
