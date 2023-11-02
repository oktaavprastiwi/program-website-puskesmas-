<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\Transformation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use RenderGraph;

class ClusteringController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Menampilkan Data Hasil Clustering
    public function index($fileId)
    {
        $clustering_status = DB::table('files')->where('id', $fileId)->value('clustering_status');
        $transformations = Transformation::where('file_id', $fileId)->get();
        $data = array();
        $i = 0;

        if ($clustering_status == 'No') {
            return view('clustering_result', ['message' => 'Lakukan Clustering Terlebih Dahulu, Untuk Melihat Data Hasil Clustering']);
        }

        foreach ($transformations as $transformation) {
            //try {
                $data[$i]['umur'] = $transformation->umur;
                $data[$i]['jenis_kelamin'] = $transformation->jenis_kelamin;
                $data[$i]['nama_penyakit'] = $transformation->nama_penyakit;
                $data[$i]['transformations'] = array($transformation->transform_umur, $transformation->transform_jenis_kelamin, $transformation->transform_diagnosa, $transformation->jumlah_penderita);
                $i++;
            /*} catch (\Exception $e) {
                //echo $transformation->umur . '<br>';
                //echo $e->getMessage() . '<br>';
            }*/
        }
        $i = 0;
        //print_r($data);
        //print_r($data[0]['transformations']);

        $centroids = DB::table('centroids')->where('file_id', $fileId)->get();
        $centroidAwal = array();
        foreach($centroids as $centroid) {
            $centroidAwal[$i]['transformations'] = array($centroid->transform_umur, $centroid->transform_jenis_kelamin, $centroid->transform_diagnosa, $centroid->jumlah_penderita);
            $i++;
        }
        $i = 0;

        $clustering = k_means($data, $centroidAwal);
        $centroidClusterAkhir = $clustering[0];
        $dbi = dbi($data, $centroidClusterAkhir);
        //print_r($centroidClusterAkhir);
        //print_r($clustering[2]);

        return view('clustering_result', [
            'prosesKMeans' => $clustering[2],
            'centroidClusterAkhir' => $centroidClusterAkhir,
            'kelompokSSW' => $dbi[0],
            'hasilSSW' => $dbi[1],
            'hasilSSB' => $dbi[2],
            'hasilPerhitunganRatio' => $dbi[3],
            'ratio' => $dbi[4],
            'dbi' => $dbi[5],
            'clusters' => end($clustering[1]),
            'jumlahKluster' => count($centroidAwal),
            'diseases'=> DB::table('diseases')->pluck('diagnosa', 'id')]
        );
    }

    public function create($fileId)
    {
        $file = DB::table('files')->where('id', $fileId)->first();
        $transformations = DB::table('transformations')->where('file_id', $fileId)->select('id', 'transform_umur', 'transform_jenis_kelamin', 'transform_diagnosa', 'jumlah_penderita')->get();

        // Jika Data Excel Tidak Ada, Tampilkan Pesan
        if ($file == null) {
            return view('clustering', ['message' => 'Upload Terlebih Dahulu Data Excel, Agar Bisa Melakukan Clustering']);
        }

        // Jika Data Excel Sudah Dilakukan Clustering Arahkan Ke Halaman Hasil Clustering
        if ($file->clustering_status == 'Yes') {
            return redirect()->route('clustering.result', ['fileId' => $fileId]);
        }

        // Jika Data Excel Belum Dilakukan Clustering, Maka Arahkan Ke Halaman Form
        return ($file->upload_status === 'Done' ?
                view('clustering', ['fileId' => $fileId, 'transformations' => $transformations]) :
                view('clustering', ['fileId' => $fileId, 'transformations' => $transformations, 'message' => 'Data Excel Masih Diproses, Silahkan Tunggu']));
    }

    public function store($fileId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'centroids' => ['required', 'array', 'min:2'],
            //'centroids.*' => ['required', 'integer'],
            //'model' => ['required', 'string', Rule::in(['Transformation'])]
        ]);

        // Cek Status File, Apakah Sudah Dapat Di Clustering
        $fileStatus = DB::table('files')->where('id', $fileId)->value('upload_status');
        $transformations = DB::table('transformations')->where('file_id', $fileId)->select('id', 'transform_umur', 'transform_jenis_kelamin', 'transform_diagnosa', 'jumlah_penderita')->get();

        $message = '';

        if ($validator->fails()) {
            $message = $validator->errors()->first();
        }

        if ($fileStatus == NULL) {
            $message = 'Data Tidak Ditemukan';
        }

        if ($fileStatus === 'Processing') {
            $message = 'Data Excel Masih Diproses, Silahkan Tunggu';
        }

        // Tampilkan Pesan Peringatan Jika Jumlah Data Lebih Kecil Dari K Cluster (3 Cluster)
        if (count($transformations) <= 3) {
            $message = 'Jumlah Data Terlalu Kecil';
        }

        if ($message != '') {
            return response()->json(['message' => $message, 'code' => 422], 422);
        }

        // Simpan Titik Centroid Awal Ke Database
        $centroids = array();
        for ($i = 0; $i < count($request->input('centroids')); $i++) {
            $centroid = explode(",", $request->input('centroids')[$i]);
            $centroids[$i] = array(
                'file_id' => $fileId,
                'transform_umur' => $centroid[0],
                'transform_jenis_kelamin' => $centroid[1],
                'transform_diagnosa' => $centroid[2],
                'jumlah_penderita' => $centroid[3],
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
        }
        DB::table('centroids')->insert($centroids);

        DB::table('files')->where('id', $fileId)->update(['clustering_status' => 'Yes']);

        return response()->json(['message' => 'Berhasil Melakukan Clustering', 'code' => 201], 201);

        /*$monetary = DB::table('sales')
        ->distinct()
        ->select(DB::raw('kd_item, harga_hna, nm_item, kategori_obat'))
        ->where('file_id', $fileId)
        ->groupBy('kd_item')
        ->orderBy('kd_item')
        ->get();

        echo 'array(';
        echo '<br>';
        foreach ($monetary as $r) {

            echo "array(
                'kd_item' => '$r->kd_item',
                'harga_hna' => $r->harga_hna,
                'nm_item' => '$r->nm_item',
                'kategori_obat' => '$r->kategori_obat',
                'created_at' => '2020-12-02 10:40:17',
                'updated_at' => '2020-12-02 10:40:17'),";
            echo '<br>';
        }
        echo ')';*/
    }
}
