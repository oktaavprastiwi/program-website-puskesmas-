<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Model\Transformation;
use App\Events\FileProcessDone;

class FileProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $fileId;
    public $diseases;
    public $counter;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($fileId)
    {
        $this->fileId = $fileId;

        $this->diseases = DB::table('diseases')->pluck('id', 'kode_penyakit');
        $this->counter = $this->diseases['F30'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::table('files')->where('id', $this->fileId)->update(['upload_status' => 'Done']);

        $select = function(string $rentang_umur, int $transform_umur): string {
            return "kode_penyakit, diagnosa, jenis_kelamin, '$rentang_umur' AS rentang_umur, $transform_umur AS transform_umur, COUNT(*) AS jumlah_penderita";
        };
        $group_by = 'kode_penyakit, jenis_kelamin';

        $umur_0_13 = DB::table('diagnosis')
        ->selectRaw($select('0-13 tahun', 1))
        ->where('file_id', $this->fileId)
        ->whereBetween('umur', [0, 13])
        ->groupByRaw($group_by);

        $umur_14_45 = DB::table('diagnosis')
        ->selectRaw($select('14-45 tahun', 2))
        ->where('file_id', $this->fileId)
        ->whereBetween('umur', [14, 45])
        ->groupByRaw($group_by);

        $diagnosis = DB::table('diagnosis')
        ->selectRaw($select('45 tahun keatas', 3))
        ->where('file_id', $this->fileId)
        ->where('umur', '>', 45)
        ->groupByRaw($group_by)
        ->union($umur_0_13)
        ->union($umur_14_45)
        ->orderBy('diagnosa')
        ->get();

        $bulk_transformations = array();
        foreach ($diagnosis as $index => $diagnosa) {
            $bulk_transformations[$index] = array(
                'file_id' => $this->fileId,
                'umur' => $diagnosa->rentang_umur,
                'jenis_kelamin' => $diagnosa->jenis_kelamin,
                'nama_penyakit' => $diagnosa->diagnosa,
                'transform_umur' => $diagnosa->transform_umur,
                'transform_jenis_kelamin' => $this->transformJenisKelamin($diagnosa->jenis_kelamin),
                'transform_diagnosa' => $this->transformDiagnosa($diagnosa->kode_penyakit, $diagnosa->diagnosa),
                'jumlah_penderita' => $diagnosa->jumlah_penderita,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );
        }

        DB::table('transformations')->insert($bulk_transformations);

        event(new FileProcessDone("File ID $this->fileId Telah Selesai Di Import. Silahkan Refresh Halaman"));
    }

    private function transformJenisKelamin(string $jenis_kelamin): int {
        return ['L' => 1, 'P' => 2][$jenis_kelamin];
    }

    private function transformDiagnosa(string $kode_penyakit, string $diagnosa): int {
        try {
            $this->diseases[$kode_penyakit];
        } catch (\Exception $e) {
            $this->diseases[$kode_penyakit] = ++$this->counter;

            DB::table('diseases')->insertOrIgnore(
                [
                    'id' => $this->counter,
                    'kode_penyakit' => $kode_penyakit,
                    'diagnosa' => $diagnosa
                ]
            );
        }

        return $this->diseases[$kode_penyakit];
    }
}
