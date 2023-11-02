<?php

namespace App\Imports;

use App\Model\Diagnosis;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
//use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class DiagnosisImport implements ToModel, WithValidation, SkipsOnFailure, WithHeadingRow, WithChunkReading, ShouldQueue, WithCalculatedFormulas
{
    use Importable;

    public $fileId;

    public function __construct($fileId)
    {
        $this->fileId = $fileId;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Diagnosis([
            'file_id' => $this->fileId,
            'tanggal' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal']),
            'no_index' => $row['no_index'],
            'kode_penyakit' => $row['kode_penyakit'],
            'diagnosa' => $row['diagnosa'],
            'alamat' => $row['alamat'] ?? '',
            'jenis_kelamin' => $row['jenis_kelamin'],
            'umur' => $row['umur'],
            'unit' => $row['unit'] ?? '',
        ]);
    }

    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
        dd($failures);
    }

    public function chunkSize(): int
    {
        return 1337;
    }

    public function rules(): array
    {
        return [
            //'tanggal' => 'date_format:d/m/Y|required',
            'no_index' => 'string|required',
            'kode_penyakit' => 'string|required',
            'diagnosa' => 'string|required',
            // 'alamat' => 'string',
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'umur' => 'integer|required',
            // 'unit' => 'string',

            //'*.tanggal' => 'date_format:d/m/Y|required',
            '*.no_index' => 'string|required',
            '*.kode_penyakit' => 'string|required',
            '*.diagnosa' => 'string|required',
            // '*.alamat' => 'string',
            '*.jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            '*.umur' => 'integer|required',
            // '*.unit' => 'string',
        ];
    }
}
