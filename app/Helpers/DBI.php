<?php

/**
 * Algoritma K-Means Clustering
 *
 * @param  int[] $dataPenyakit, Multidimensional Arrays Dengan Nilai Harus Integer
 * @param  int[] $centroidAwal, Contoh: array( array('transformations' => (1, 2, 3)), )
 * @return array
 *
 * fungsi:
 * - count() adalah untuk menghitung jumlah element yang ada didalam array
 * - pow() adalah untuk melakukan perhitungan pangkat
 * - array_keys() adalah untuk mengembalikan seluruh key (kunci) dari element array, bukan mengembalikan value (nilai) dari element array
 * - min() adalah untuk mengambil nilai terkecil dari suatu array
 * - max() adalah untuk mengambil nilai terbesar dari suatu array
 * - array_sum() adalah menjumlahkan seluruh nilai dari element array
 * - ksort() adalah untuk mengurutkan element array secara ascending order
 * - str_replace() adalah untuk mengganti (replace) sebuah string menjadi string baru yang telah ditentukan
 * - join() adalah untuk menggabungkan seluruh string pada setiap element di sebuah array
 * - array_column() adalah untuk mengambalikan semua nilai pada setiap element sesuah dengan keys yang dipilih
 * - filter_var() adalah untuk memfilter sebuah variable sesuai dengan nama filter yang dipilih. FILTER_SANITIZE_NUMBER_FILTER membuat karakter yang diluar dari type int akan dihilangkan, contoh '5-2-3pp' akan menjadi '5-2-3'
 */

//  Memformat nilai angka menjadi hanya 4 desimal dengan separator titik (.)
function fixed_decimal($number) {
    $is_decimal = is_numeric($number) && floor($number) != $number;
    return ($is_decimal == true ? number_format($number, 4, '.', '') : $number);
}

/*
    Perhitungan Evaluasi Clustering
    Evaluasi Hasil Clustering Yang Digunakan Adalah Davies-Bouldin Index.
*/
function dbi($data, $centroidClusterAkhir) {
    $dataPenyakit = $data;
    $clusters = $centroidClusterAkhir;

    /*
        Menghitung Nilai Sum of Square Within-cluster (SSW)
        Nilai SSW Diperoleh Dari Perhitungan Jarak Setiap Data Terhadap Titik Pusat (Centroid) Cluster Akhir Menggunakan Euclidean Distance.
    */
    // Hitung Jarak Data Dengan Centroid Menggunakan Euclidean distance

    $hasilPerhitunganJarak = array();
    $c_i = 0;
    foreach ($clusters as $cluster_key => $cluster) {
        foreach ($dataPenyakit as $key => $value) {
            $euclidean = 0;
            $rumus = 'sqrt(';
            for ($i = 0; $i < count($value['transformations']); $i++) {
                $euclidean += pow($value['transformations'][$i] - $cluster[$i], 2);
                $rumus .= "({$value['transformations'][$i]} - {$cluster[$i]}) ** 2";
                if ($i != count($value['transformations']) - 1) $rumus .= ' + ';
            }
            $rumus .= ')';
            $hasilPerhitunganJarak[$c_i]['jarak'][] = sqrt($euclidean);
            $hasilPerhitunganJarak[$c_i]['rumus'][] = $rumus;
        }
        $hasilPerhitunganJarak[$c_i]['cluster'] = $cluster_key;
        $c_i++;
    }
    $c_i = 0;

    // Kelompokkan Sesuai Jarak Terpendek
    $counter = count($hasilPerhitunganJarak[0]['jarak']);
    for ($i = 0; $i < $counter; $i++) {
        $jarak = array();

        // Ambil Jarak Data
        for ($x = 0; $x < count($clusters); $x++) {
            $jarak[] = $hasilPerhitunganJarak[$x]['jarak'][$i];
        }

        // Cari Jarak Data Terkecil
        $jarakTerpendek = min($jarak);

        // Kelompokkan Data Penyakit Yang Terdekat Dengan Cluser
        for ($z = 0; $z < count($clusters); $z++) {
            if ($jarakTerpendek == $hasilPerhitunganJarak[$z]['jarak'][$i]) {
                // Kelompokkan Data Penyakit
                $dataPenyakit[$i]['cluster'] = array_keys($clusters)[$z];
                $sswName = str_replace("cluster_", "SSW", array_keys($clusters)[$z]);
                $dataPenyakit[$i]['ssw']['name'] = $sswName;
                $dataPenyakit[$i]['ssw']['value'][] = $hasilPerhitunganJarak[$z]['jarak'][$i];
                $dataPenyakit[$i]['ssw']['jarak'] = $jarak;
                // Keluar Dari Loop Setelah Menemukan Data Yang Terdekat Dengan Cluster
                break;
            }
        }
    }

    // Hitung Rata-Rata Dari Data Pada Tiap Cluster
    $data_clusters = array();
    $SSW = array();
    $SSW_i = 0;

    foreach ($dataPenyakit as $key => $value) {
        $data_clusters[$value['cluster']][] = $value['ssw']['value'][0];
    }
    ksort($data_clusters);

    foreach ($data_clusters as $data_cluster_key => $data_cluster_value) {
        $SSW[$SSW_i]['hasil'] = array_sum($data_cluster_value) / count($data_cluster_value);
        $SSW[$SSW_i]['perhitungan'] = '(' . join(' + ', $data_cluster_value) . ' / ' .  count($data_cluster_value) . ')';
        $SSW_i++;
    }
    $SSW_i = 0;
    ksort($SSW);


    /*
        Menghitung Nilai Sum of Square Between-cluster (SSB)
        Untuk Mendapatkan Nilai SSB Adalah Dengan Menghitung Jarak Antar Titik Pusat Cluster (Centroid) Dari Setiap Cluster
    */
    ksort($clusters);

    $titikPerhitungan = array();
    $c_i = 1;

    // Generate Titik Perhitungan SSB
    while(true) {
        if ($c_i == count($clusters)) {
            break;
        }

        for($c_j = 2; $c_j <= count($clusters); $c_j++) {
            if ($c_j > $c_i) {
                $titikPerhitungan[] = array('cluster_' . $c_i, 'cluster_' . $c_j);
            }
        }

        $c_i++;
    }

    // Hitung SSB
    $SSB = array();
    for ($i = 0; $i < count($titikPerhitungan); $i++) {
        $euclidean = 0;
        $rumus = 'sqrt(';
        for ($x = 0; $x < count($clusters['cluster_1']); $x++) {
            $euclidean += pow($clusters[$titikPerhitungan[$i][0]][$x] - $clusters[$titikPerhitungan[$i][1]][$x], 2);
            // Memasukkan Cara Perhitungan
            $rumus .= '('. fixed_decimal($clusters[$titikPerhitungan[$i][0]][$x]) . ' - ' . fixed_decimal($clusters[$titikPerhitungan[$i][1]][$x]) . ') ** 2';
            if ($x != count($clusters['cluster_1']) - 1) $rumus .= ' + ';
        }
        $SSBName = 'SSB' .
            (int) filter_var($titikPerhitungan[$i][0], FILTER_SANITIZE_NUMBER_INT)
            . ',' .
            (int) filter_var($titikPerhitungan[$i][1], FILTER_SANITIZE_NUMBER_INT);
        $rumus .= ')';
        $SSB[$SSBName]['nilai'] = sqrt($euclidean);
        $SSB[$SSBName]['rumus'] = $rumus;
    }
    ksort($SSB);


    /*
        Menghitung Nilai Ratio (Rasio) Yang Bertujuan Untuk Mendapatkan Nilai DBI Untuk Tiap Cluster
    */
    // Perhitungan Rumus Ratio
    $hasilPerhitunganRatio = array();
    for ($r_i = 1; $r_i < count($clusters); $r_i++) {
        for($r_j = 2; $r_j <= count($clusters); $r_j++) {
            $SSB_key = 'SSB' . ($r_i) . ',' . ($r_j);

            if ($r_j > $r_i) {
                // Memasukkan Rumus Ratio
                $hasilPerhitunganRatio['R' . $r_i . ',' . $r_j] = '(' . fixed_decimal($SSW[$r_i-1]['hasil']) . ' + ' . fixed_decimal($SSW[$r_j-1]['hasil']) . ')' . ' / ' . fixed_decimal($SSB[$SSB_key]['nilai']) .  ' = ' . fixed_decimal(($SSW[$r_i-1]['hasil'] + $SSW[$r_j-1]['hasil']) / $SSB[$SSB_key]['nilai']);
            }
        }
    }
    //print_r($hasilPerhitunganRatio);

    // Table Perhitungan Ratio
    $ratio = array();
    for ($r_i = 1; $r_i <= count($clusters); $r_i++) {
        for($r_j = 1; $r_j <= count($clusters); $r_j++) {
            $SSB_key = 'SSB' . ($r_i) . ',' . ($r_j);
            $SSB_key_reversed = 'SSB' . ($r_j) . ',' . ($r_i);

            if ($r_i != $r_j) {
                $ratio[$r_i]['data'][] = ($SSW[$r_i-1]['hasil'] + $SSW[$r_j-1]['hasil']) / ($SSB[$SSB_key]['nilai'] ?? $SSB[$SSB_key_reversed]['nilai']);
            } else {
                $ratio[$r_i]['data'][] = 0;
            }
            $ratio[$r_i]['max'] = max($ratio[$r_i]['data']);
        }
    }

    /*
         Menghitung Nilai Davies-Bouldin Index (DBI). Nilai Davies-Bouldin Index (DBI) Diperoleh Dengan Menghitung Rata-Rata Dari Nilai Rasio Terbesar (R-Max)
    */
    $DBI = 1 * array_sum(array_column($ratio, 'max')) / count(array_column($ratio, 'max'));

    return array($dataPenyakit, $SSW, $SSB, $hasilPerhitunganRatio, $ratio, $DBI);
}