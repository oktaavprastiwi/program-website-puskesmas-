<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\Diagnosis;

class DiagnosisController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $files = DB::table('files')->get();

        return view('diagnosis', ['files' => $files]);
    }

    public function show($fileId)
    {
        $diagnosis = Diagnosis::where('file_id', $fileId)->paginate(100);

        return view('diagnosis', ['diagnosis' => $diagnosis]);

        /*$seed = DB::table('transactions')
        ->distinct()
        ->select(DB::raw('route_id, route_name'))
        ->where('file_id', $fileId)
        ->groupBy('route_id')
        ->orderBy('route_id')
        ->get();

        echo 'array(';
        echo '<br>';
        foreach ($seed as $d) {

            echo "array(
                'route_id' => '$d->route_id',
                'route_name' => '$d->route_name',
                'created_at' => '2021-04-27 04:41:00',
                'updated_at' => '2021-04-27 04:41:00'),";
            echo '<br>';
        }
        echo ')';*/
    }

    public function destroy($fileId) {
        $file = DB::table('files')->where('id', $fileId)->first();

        // Jika Data Yang Ingin Dihapus Tidak Ada, Tampilkan Status Code 404
        if ($file == null) {
            return response(null, 404);
        }

        // Tidak Dapat Menghapus Data Jika File Excel Masih Diproses
        if ($file->upload_status == 'Processing') {
            return response(null, 403);
        }

        DB::table('transformations')->where('file_id', $fileId)->delete();
        DB::table('centroids')->where('file_id', $fileId)->delete();
        DB::table('diagnosis')->where('file_id', $fileId)->delete();
        DB::table('files')->where('id', $fileId)->delete();
    }
}
