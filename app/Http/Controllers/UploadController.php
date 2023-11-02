<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Model\File;
use App\Imports\DiagnosisImport;
use App\Jobs\FileProcess;
use Maatwebsite\Excel\Facades\Excel;

class UploadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        return view('upload');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx'
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();

            // Simpan File Ke Directory (generate a unique ID for file name)
            $path = Storage::disk('public')->putFile('excel', $file);

            // Simpan Path File dan Nama File Ke Database
            $fileUpload = File::create([
                'path' => "app/public/$path",
                'filename' => $filename
            ]);

            $inputFileName = storage_path($fileUpload->path);

            //try {
            //Excel::queueImport(new DiagnosisImport($fileUpload->id), $inputFileName);
            /*} catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                $failures = $e->failures();

                foreach ($failures as $failure) {
                    echo $failure->row(); // row that went wrong
                    echo $failure->attribute(); // either heading key (if using heading row concern) or column index
                }
            }*/
            // try {
                (new DiagnosisImport($fileUpload->id))->queue($inputFileName)->chain([
                    new FileProcess($fileUpload->id),
                ]);
            // } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            //      $failures = $e->failures();

            //      foreach ($failures as $failure) {
            //          print_r($failure->row()); // row that went wrong
            //          print_r($failure->attribute()); // either heading key (if using heading row concern) or column index
            //          print_r($failure->errors()); // Actual error messages from Laravel validator
            //          print_r($failure->values()); // The values of the row that has failed.
            //      }
            // }
        }

        return redirect()->route('diagnosis');
    }
}
