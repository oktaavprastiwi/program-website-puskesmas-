@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <h5 class="card-header card-green-light text-gray">UPLOAD DATA</h5>
                    <div class="card-body">
                        {{-- Tampilkan Pesan Peringatan --}}
                        @if ($errors->has('file'))
                        <!-- Alert Message -->
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <strong>{{ $errors->first('file') }}</strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        @endif

                        <!-- Forms -->
                        <form method="POST" action="{{ route('upload.process') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <label class="input-group-text text-gray" for="excelFile">Excel File (.xlsx)</label>
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="excelFile" name='file'>
                                    <label class="custom-file-label text-gray-light" for="excelFile">Choose file</label>
                                </div>
                            </div>

                            <!-- <input class="btn btn-success text-black align-middle" type="submit" id="uploadBtn" value="Upload" onclick="upload()" /> -->
                            <button class="btn btn-success text-gray align-middle" type="submit" id="uploadBtn" onclick="upload()">
                                Upload
                            </button>

                            <button class="btn btn-primary" type="button" disabled style="display:none;" id="uploading">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Uploading...
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('js/bs-custom-file-input.min.js') }}"></script>
<script>bsCustomFileInput.init()</script>
<script>
    function upload() {
        document.getElementById("uploadBtn").style.display = "none"
        document.getElementById("uploading").style.display = "block"
    }
</script>
@endpush