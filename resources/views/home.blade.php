@extends('layouts.app')

@section('content')
<div class="container">
    <div class="text-center mb-4">
        <img src="{{ asset('img/UPTD_PUSKESMAS_WURYANTORO_Logo.png') }}" class="rounded mx-auto d-block h-5 w-5 mb-4" alt="Logo UPTD PUSKESMAS WURYANTORO">
        <h3>VISI & MISI</h3>
        <h3>{{ config('app.name', 'Laravel') }}</h3>
    </div>

    <div class="row">
        <div class="col-sm-6 mb-3" style="cursor: pointer">
            <div class="card">
                
                <div class="card-body">
                    <h5 class="text-center font-weight-bold">Visi</h5>

                    Terwujudnya pelayanan yang prima menuju masyarakat yang sehat dan mandiri.
                </div>
            </div>
        </div>

        <div class="col-sm-6 mb-3" style="cursor: pointer">
            <div class="card">
                
                <div class="card-body">
                    <h5 class="text-center font-weight-bold">Misi</h5>
                    
                    <ol>
                        <li>Terselenggaranya pelayanan kesehatan yang prima meliputi pelayanan promotif, preventif, kuratif, dan rehabilitatif.</li>
                        <li>Terwujudnya sumberdaya manusia yang profesional, berkualitas, dan sarana prasarana kesehatan yang sesuai standar.</li>
                        <li>Terwujudnya kemandirian hidup sehat bagi keluarga dan masyarakat.</li>
                        <li>Tercapainya mutu, pemerataan dan keterjangkaunya pelayanan kesehatan.</li>
                        <li>Tergalangnya kemitraan dengan masyarakat secara lintas program dan lintas sektor dalam pembangunan kesehatan.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 mb-3" >
            <div class="card">
                
                <div class="card-body">
                    <h5 class="text-center font-weight-bold">Pengertian Data Mining</h5>
                    Data mining juga dikenal dengan istilah pattern recognition merupakan suatu metode yang digunakan untuk pengolahan data guna menemukan pola yang tersembunyi dari data yang diolah. Data yang diolah dengan teknik data mining ini kemudian menghasilkan suatu pengetahuan baru yang bersumber dari data lama, hasil dari pengolahan data tersebut dapat digunakan dalam menentukan keputusan di masa depan.
                </div>
            </div>
        </div>

        <div class="col-sm-4 mb-3" >
            <div class="card">
                
                <div class="card-body">
                    <h5 class="text-center font-weight-bold">Pengertian Clustering</h5>
                    Clustering adalah metode yang digunakan untuk membagi rangkaian data menjadi beberapa group berdasarkan kesamaan-kesamaan yang telah ditentukan sebelumnya. Cluster adalah sekelompok atau sekumpulan objek-objek data yang similar satu sama lain dalam cluster yang sama dan disimilar terhadap objek-objek yang berbeda cluster. Objek akan dikelompokkan ke dalam satu atau lebih cluster sehingga objek-objek yang berada dalam satu cluster akan mempunyai kesamaan yang tinggi antara satu dengan yang lainnya.
                    Dengan menggunakan clustering ini, kita dapat mengkalsifikasikan daerah yang padat, menemukan pola-pola distribusi secara keseluruhan, dan menemukan keterkaitan yang menarik antara atribut data.
                </div>
            </div>
        </div>

        <div class="col-sm-4 mb-3" >
            <div class="card">
                
                <div class="card-body">
                    <h5 class="text-center font-weight-bold">Pengertian Algoritma K-Means</h5>
                    Metode K-Means pertama kali diperkenalkan oleh MacQueen JB pada tahun 1976. K-Means adalah suatu metode penganalisaan data atau metode Data Mining yang melakukan proses pemodelan tanpa supervisi (unsupervised) dan merupakan salah satu metode yang melakukan pengelompokan data dengan sistem partisi (Hariyanto & Shita, 2018).
                    K-Means merupakan salah satu metode data clustering non hierarki yang berusaha mempartisi data yang ada ke dalam bentuk satu atau lebih cluster atau kelompok sehingga data yang memiliki karakteristik yang sama dikelompokkan ke dalam satu cluster yang sama dan data yang mempunyai karakteristik yang berbeda dikelompokkan ke dalam kelompok yang lainnya.
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
