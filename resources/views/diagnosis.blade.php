@extends('layouts.app')

@section('content')
    {{-- Menampilkan Seluruh File Excel Yang Sudah Diupload --}}
    @isset($files)
        <div class="container">
            <div class="row">
                @foreach ($files as $file)
                    <div class="col-sm-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $loop->iteration }}. {{ $file->filename }}</h5>
                                <p>Update Data Kurun Waktu 1 Tahun</p>
                                <a class="btn btn-primary mr-1" href="{{ route('diagnosis.detail', ['fileId' => $file->id]) }}" target="_blank">Preview</a>

                                {{-- Cek Status File, Apakah Sudah Dapat Dilakukan Clustering --}}
                                @if ($file->upload_status === 'Done')
                                    <a class="btn btn-primary mr-1" href="{{ route('clustering', ['fileId' => $file->id]) }}" target="_blank">
                                    {{-- Cek Apakah Sudah Ada Hasil Clustering --}}
                                    @if ($file->clustering_status === 'No')
                                        Clustering
                                    @else
                                        Hasil Clustering
                                    @endif
                                    </a>
                                    <a class="btn btn-danger deleteData" href="#" data="{{ $file->id }}" target="_blank">Hapus</a>
                                @else
                                    <span class="d-inline-block" data-toggle="popover" data-trigger="hover" data-content="Data Excel Masih Dalam Proses Upload, Belum Bisa Melakukan Clustering">
                                        <a class="btn btn-primary mr-1 disabled" style="pointer-events: none;" type="button" href="#" target="_blank">Clustering</a>
                                    </span>

                                    <span class="d-inline-block" data-toggle="popover" data-trigger="hover" data-content="Data Excel Masih Dalam Proses Upload, Belum Bisa Menghapus Data">
                                        <a class="btn btn-danger disabled" style="pointer-events: none;" type="button" href="#" target="_blank">Hapus</a>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endisset

    {{-- Menampilkan Data Dari File Excel Yang Sudah Diupload --}}
    @isset($diagnosis)
        <div class="container-fluid">
            {{ $diagnosis->render() }}
            <div class="row">
                <div class="col">
                    <div class="card">
                        <h5 class="card-header">Data Diagnosis</h5>
                        <div class="card-body">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Tanggal</th>
                                        <th scope="col">No Index</th>
                                        <th scope="col">Kode Penyakit</th>
                                        <th scope="col">Diagnosa</th>
                                        <th scope="col">Alamat</th>
                                        <th scope="col">Jenis Kelamin</th>
                                        <th scope="col">Umur</th>
                                        <th scope="col">Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($diagnosis as $data)
                                            <tr>
                                                <td>{{ $loop->iteration + ($diagnosis->currentPage() - 1) * $diagnosis->perPage() }}</td>
                                                <td>{{ $data->tanggal }}</td>
                                                <td>{{ $data->no_index }}</td>
                                                <td>{{ $data->kode_penyakit }}</td>
                                                <td>{{ $data->diagnosa }}</td>
                                                <td>{{ $data->alamat }}</td>
                                                <td>{{ $data->jenis_kelamin }}</td>
                                                <td>{{ $data->umur }}</td>
                                                <td>{{ $data->unit }}</td>
                                            </tr>
                                    @endforeach
                                </tbody>
                            <table>
                        </div>
                    </div>
                </div>
            </div>
            {{ $diagnosis->render() }}
        </div>
    @endisset
@endsection

@isset($files)
    @push('scripts')
        <script src="{{ asset('js/jquery-3.5.1.slim.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
        <script>
            $(document).ready(function(){
                $('[data-toggle="popover"]').popover()

                $(".deleteData").click(function(){
                    if (confirm("Yakin ingin menghapus data?")) {
                        var fileId = $(this).attr('data')
                        fetch("{{ url()->current() }}/" + fileId,
                            {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            }
                        )
                            .then(response => response)
                            .then(data => {
                                if (data.status === 200) {
                                    location.reload();
                                    alert('Data Berhasil Dihapus')
                                } else {
                                    alert('Data Gagal Dihapus, Silahkan Coba Lagi')
                                }
                            });
                    }
                    return false
                });
            });
        </script>
    @endpush
@endisset