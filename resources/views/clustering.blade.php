@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <h5 class="card-header">Clustering Data</h5>
                <div class="card-body">
                    {{-- Tampilkan Pesan Peringatan --}}
                    @verbatim
                        <!-- Alert Message -->
                        <div class="alert alert-warning alert-dismissible fade show" role="alert" v-if="error">
                            <strong>{{ error }}</strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                    @endverbatim

                    @isset($fileId)
                        <!-- Pilih Centroid Secara Acak -->
                        <template v-if="is_random">
                            <form method="POST" @submit="process_random_centroid" enctype="multipart/form-data">
                                @csrf

                                <!-- <div class="form-group row">
                                    <label for="jumlah_cluster" class="col-sm-2 col-form-label">Jumlah Cluster</label>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" name="jumlah_cluster" v-model="jumlah_cluster" id="jumlah_cluster" placeholder="Masukkan Jumlah Cluster" required>
                                        <div class="invalid-feedback">
                                            Masukkan Jumlah Cluster.
                                        </div>
                                    </div>
                                </div> -->

                                <button type="submit" class="btn btn-primary my-1" id="processBtn">Proses</button>

                                <button class="btn btn-primary" type="button" disabled style="display:none;" id="processing">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Processing...
                                </button>
                            </form>
                        </template>

                        <!-- Pilih Centroid Secara Manual -->
                        <template v-if="!is_random">
                            <!-- Keterangan -->
                            <div class="alert alert-info" role="alert">
                                <li>Jika menginginkan n Cluster, maka masukan Nilai Centroid sebanyak n kali</li>
                                <li>Jika ingin membagi transaksi penjualan menjadi 3 kategori, maka masukan 3 nilai  Centroid</li>
                            </div>

                            <!-- Forms -->
                            <form method="POST" @submit="process" enctype="multipart/form-data">
                                @csrf

                                <label class="my-1 mr-2">Titik Centroid Awal</label>
                                @verbatim
                                    <template v-for="(centroid, i) in centroids">
                                        <div class="form-inline mb-2">
                                            <select class="custom-select my-1 mr-sm-2 centroid" name="centroids[]" required v-bind:key="i" v-on:change="selectedUniqueCentroid(i, $event)">
                                                <option value="" selected>Pilih</option>
                                                <option v-for="(data, data_index) in centroid" v-bind:key="data.id" v-bind:dataid="data.id" v-bind:value="data.transform_umur + ',' + data.transform_jenis_kelamin + ',' + data.transform_diagnosa + ',' + data.jumlah_penderita">{{ data.transform_umur }},{{ data.transform_jenis_kelamin }},{{ data.transform_diagnosa }},{{ data.jumlah_penderita }}</option>
                                            </select>

                                            <span class="ml-2">
                                                <i class="fas fa-minus-circle" @click="remove(i)" v-show="i || ( !i && centroids.length > 1)">Hapus</i>
                                                <i class="fas fa-plus-circle" @click="add(i)" v-show="i == centroids.length-1">Tambah</i>
                                            </span>
                                        </div>
                                    </template>
                                @endverbatim

                                <button type="submit" class="btn btn-primary my-1" id="processBtn">Proses</button>

                                <button class="btn btn-primary" type="button" disabled style="display:none;" id="processing">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Processing...
                                </button>
                            </form>
                        </template>
                    @endisset
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}" />
@endpush

@isset($fileId)
    @push('scripts')
    <script src="{{ asset('js/vue.js') }}"></script>
    <script>
        var app = new Vue({
            el: '#app',
            data: {
                error: null,

                // Pilih Centroid Secara Manual
                centroids: [@json($transformations)],
                manual_centroids: [],
                selected_index: null,
                unique_centroids: null,

                // Pilih Centroid Secara Acak
                is_random: true,
                jumlah_cluster: 3
            },
            methods: {
                // Pilih Centroid Secara Manual
                add: function(index) {
                    this.centroids.push(this.unique_centroids ?? @json($transformations))
                },
                remove: function(index) {
                    this.centroids.splice(index, 1)
                    this.manual_centroids.splice(index, 1)
                },
                selectedUniqueCentroid: function(index, event = null) {
                    centroid_index = this.selected_index ?? event.target.options[event.target.selectedIndex].getAttribute('dataid')

                    for (i = 0; i < this.centroids.length; i++) {
                        //console.log(this.centroids[index][event.target.selectedIndex - 1])
                        if (i != index) {
                            console.log(index)
                            console.log(centroid_index)
                            for(x = 0; x < this.centroids[i].length; x++) {
                                if (this.centroids[i][x].id == centroid_index) {
                                    this.centroids[i].splice(x, 1)
                                }
                            }
                        } else {
                            console.log(centroid_index);
                            this.unique_centroids = this.centroids[i].filter(function( data ) {
                                return data.id != centroid_index;
                            });
                        }
                    }

                    selected_centroid = this.centroids[index].filter(function( data ) {
                        return data.id == centroid_index;
                    })[0]
                    this.manual_centroids[index] =
                        selected_centroid.transform_umur + ',' +
                        selected_centroid.transform_jenis_kelamin + ',' +
                        selected_centroid.transform_diagnosa + ',' +
                        selected_centroid.jumlah_penderita

                    console.log(this.manual_centroids)
                    console.log(this.unique_centroids)
                },
                process: function(e) {
                    e.preventDefault()
                    var input = document.getElementsByClassName('centroid');

                    for (i = 0; i < input.length; i++) {
                        if(!input[i].validity.valid){
                            return false
                        }
                    }

                    document.getElementById("processBtn").style.display = "none"
                    document.getElementById("processing").style.display = "block"

                    fetch('{{ url()->current() }}/',
                        {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                centroids: this.manual_centroids
                            })
                        }
                    )
                    .then(response => response.json())
                    .then(data => {
                        if (data.code === 201) {
                            window.location.href = '{{ route('clustering.result', ['fileId' => $fileId]) }}';
                        } else {
                            document.getElementById("processBtn").style.display = "block"
                            document.getElementById("processing").style.display = "none"
                            this.error = data.message
                        }
                    });
                },
                // Pilih Centroid Secara Acak
                process_random_centroid: function(e) {
                    e.preventDefault()
                    document.getElementById("processBtn").style.display = "none"
                    document.getElementById("processing").style.display = "block"

                    random_centroids = [
                        ['1,2,345,1', '3,2,191,17', '2,1,106,4'],
                        ['3,1,68,4', '3,2,139,2', '2,1,147,1'],
                        ['2,1,32,9', '2,1,172,1', '3,1,198,7'],
                        ['2,1,340,2', '1,2,146,1', '2,1,149,2']
                    ];

                    // Generate unique random numbers
                    // var arr = [];
                    // while(arr.length < this.jumlah_cluster){
                    //     var r = Math.floor(Math.random() * this.centroids[0].length);
                    //     if(arr.indexOf(r) === -1) {
                    //         arr.push(r);
                    //         random_centroids.push(
                    //             this.centroids[0][r].transform_umur + ',' +
                    //             this.centroids[0][r].transform_jenis_kelamin + ',' +
                    //             this.centroids[0][r].transform_diagnosa + ',' +
                    //             this.centroids[0][r].jumlah_penderita
                    //         )
                    //     }
                    // }
                    // console.log(arr);
                    console.log(random_centroids);

                    fetch('{{ url()->current() }}/',
                        {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                centroids: random_centroids[Math.floor(Math.random() * random_centroids.length)]
                            })
                        }
                    )
                    .then(response => response.json())
                    .then(data => {
                        if (data.code === 201) {
                            window.location.href = '{{ route('clustering.result', ['fileId' => $fileId]) }}';
                        } else {
                            document.getElementById("processBtn").style.display = "block"
                            document.getElementById("processing").style.display = "none"
                            this.error = data.message
                        }
                    });
                }
            },
        })
    </script>
    @endpush
@endisset