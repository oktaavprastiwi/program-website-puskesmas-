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
 * - array_sum() adalah menjumlahkan seluruh nilai dari element array
 * - ksort() adalah untuk mengurutkan element array secara ascending order
 */
function k_means($dataPenyakit, $centroidAwal)
{
    // Menentukan Centroid Awal (Secara Acak, Centroid Sejumlah K (Cluster))
    $clusters = array();
    for ($centroidIndex = 0; $centroidIndex < count($centroidAwal); $centroidIndex++) {
        $cluster_key = 'cluster_' . ($centroidIndex + 1);
        $clusters[$cluster_key] = $centroidAwal[$centroidIndex]['transformations'];
    }

    // menentukan variable
    $centroidClusterBaru = array();
    $prosesKMeans = array();
    $prosesClustering = array();

    // looping centroid
    while (true) {

        // Hitung Jarak Data Dengan Centroid Menggunakan Euclidean distance
        $hasilPerhitunganJarak = array();
        foreach ($clusters as $cluster_key => $cluster) {
            foreach ($dataPenyakit as $key => $value) {
                $euclidean = 0;
                for ($i = 0; $i < count($value['transformations']); $i++) {
                    $euclidean += pow($value['transformations'][$i] - $cluster[$i], 2);
                }
                $hasilPerhitunganJarak[$cluster_key][] = sqrt($euclidean);
            }
        }

        // Kelompokkan Sesuai Jarak Terpendek
        $counter = count($hasilPerhitunganJarak[array_keys($clusters)[0]]);

        // lopping mencari jarak terpendek
        for ($i = 0; $i < $counter; $i++) {
            $jarak = array();

            // Ambil Jarak Data
            for ($x = 0; $x < count($clusters); $x++) {
                $jarak[] = $hasilPerhitunganJarak[array_keys($clusters)[$x]][$i];
            }

            // Cari Jarak Data Terkecil
            $jarakTerpendek = min($jarak);

            // Kelompokkan Data Penjualan Yang Terdekat Dengan Cluser
            for ($z = 0; $z < count($clusters); $z++) {
                if ($jarakTerpendek == $hasilPerhitunganJarak[array_keys($clusters)[$z]][$i]) {

                    // Kelompokkan Data Penjualan
                    $dataPenyakit[$i]['cluster'] = array_keys($clusters)[$z];

                    // Keluar Dari Loop Setelah Menemukan Data Yang Terdekat Dengan Cluster
                    break;
                }
            }
        }

        // Hitung Rata-Rata Dari Data Pada Tiap Cluster
        $data_clusters = array();
        $rataRata = array();
        foreach ($clusters as $cluster_name => $cluster_value) {
            $data_clusters = array();
            $index = 0;
            for ($i = 0; $i < count($clusters[array_keys($clusters)[0]]); $i++) {
                foreach ($dataPenyakit as $key => $value) {
                    $data_clusters[$value['cluster']][$index][] = $value['transformations'][$index];
                }
                $index++;
            }

            for ($i = 0; $i < count($clusters[array_keys($clusters)[0]]); $i++) {
                foreach ($data_clusters as $data_cluster_key => $data_cluster_value) {

                        $rataRata[$data_cluster_key][$i] = array_sum($data_cluster_value[$i]) / count($data_cluster_value[$i]);
                }
            }
        }
        ksort($rataRata);

        // Hasil Perhitungan Centroid Baru
        $centroidClusterBaru = $rataRata;

        $prosesKMeans[] = array('centroid' => $clusters);
        $prosesClustering[] = $dataPenyakit;

        // Jika centroid sudah sama atau tidak ada data produk yang terpindah, maka proses kmeans dihentikan
        if ($centroidClusterBaru == $clusters) {
            return array($centroidClusterBaru, $prosesClustering, $prosesKMeans);
        }

        // Jika centroid belum sama atau data produk masih ada yang berpindah, maka proses dilanjutkan
        $clusters = $centroidClusterBaru;
    }
}
