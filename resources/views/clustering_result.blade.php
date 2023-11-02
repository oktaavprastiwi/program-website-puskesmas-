@extends('layouts.app')

@section('content')
    <div class="container">
    {{-- Tampilkan Pesan Peringatan --}}
    @isset($message)
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                            <!-- Alert Message -->
                            <div class="alert alert-info" role="alert">
                                <p>{{ $message }}</p>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    @endisset

    {{-- Menampilkan Data Hasil Clustering --}}
    @isset($clusters)
        <div class="row">
            <div class="col">
                <div class="list-group nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="list-group-item nav-link active" id="v-pills-clustering-tab" data-toggle="pill" href="#v-pills-clustering" role="tab" aria-controls="v-pills-clustering" aria-selected="true">Hasil Clustering</a>

                    <a class="list-group-item nav-link" id="v-pills-centroid-tab" data-toggle="pill" href="#v-pills-centroid" role="tab" aria-controls="v-pills-centroid" aria-selected="false">Jumlah Iterasi<a>

                    <a class="list-group-item nav-link" id="v-pills-dbi-tab" data-toggle="pill" href="#v-pills-dbi" role="tab" aria-controls="v-pills-dbi" aria-selected="false">DBI</a>

                    <a class="list-group-item nav-link" id="v-pills-graph-tab" data-toggle="pill" href="#v-pills-graph" role="tab" aria-controls="v-pills-graph" aria-selected="false">Graph</a>
                </div>
            </div>
            <div class="col-10">
                <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane card fade show active" id="v-pills-clustering" role="tabpanel" aria-labelledby="v-pills-clustering-tab">
                        {{-- Menampilkan Hasil Clustering --}}
                        <h5 class="card-header">Hasil Clustering</h5>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="clusterFilter">Tampilkan Cluster</label>
                                <select class="form-control" id="clusterFilter" v-on:change="filteringCluster($event)">
                                    <option value="*">Semua Cluster</option>
                                    @for ($i = 1; $i <= $jumlahKluster; $i++)
                                        <option value="{{ $i }}">Cluster {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            @verbatim
                                <!-- Info Cluster -->
                                <div class="alert alert-info" role="alert">
                                    <li v-for="info in infoCluster">{{ info }}</li>
                                </div>

                                <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">No</th>
                                            <th scope="col">Nama Penyakit</th>
                                            <th scope="col">Umur</th>
                                            <th scope="col">Jenis Kelamin</th>
                                            <th scope="col">Transformasi Umur</th>
                                            <th scope="col">Transformasi Jenis Kelamin</th>
                                            <th scope="col">Transformasi Diagnosa</th>
                                            <th scope="col">Jumlah Penderita</th>
                                            <th scope="col">Cluster</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in filtering_clusters" :key="item.item_code">
                                            <td>{{ index+1 }}</td>
                                            <td>{{ item.nama_penyakit }}</td>
                                            <td>{{ item.umur }}</td>
                                            <td>{{ item.jenis_kelamin }}</td>
                                            <td>{{ item.transformations[0] }}</td>
                                            <td>{{ item.transformations[1] }}</td>
                                            <td>{{ item.transformations[2] }}</td>
                                            <td>{{ item.transformations[3] }}</td>
                                            <td>{{ item.cluster | capitalize }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                </div>
                            @endverbatim
                        </div>
                    </div>

                    <div class="tab-pane card fade" id="v-pills-centroid" role="tabpanel" aria-labelledby="v-pills-centroid-tab">
                        {{-- Menampilkan Proses Jumlah Iterasi Centroid --}}
                        <h5 class="card-header">Jumlah Iterasi</h5>
                        <div class="card-body">
                            @verbatim
                                <template v-for="(item, index) in prosesKMeans">
                                    <h5 class="card-title">Iterasi {{ index + 1 }}</h5>
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 25%" scope="col">Cluster</th>
                                                <th style="width: 25%" scope="col">Transformasi Umur</th>
                                                <th style="width: 25%" scope="col">Transformasi Jenis Kelamin</th>
                                                <th style="width: 25%" scope="col">Transformasi Diagnosa</th>
                                                <th style="width: 25%" scope="col">Jumlah Penderita</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(it_centroid, i_centroid) in item.centroid">
                                                <td>{{ i_centroid | capitalize }}</td>
                                                <td v-for="transformation in it_centroid">
                                                    {{ fixedDecimalNumber(transformation) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </template>
                            @endverbatim
                        </div>
                    </div>

                    {{-- Menampilkan Perhitungan DBI --}}
                    <div class="tab-pane fade" id="v-pills-dbi" role="tabpanel" aria-labelledby="v-pills-dbi-tab">
                        <div class="card mb-3">
                            <h5 class="card-header">Perhitungan Nilai SSW</h5>
                            <div class="card-body">
                                <h5 class="card-title">Menghitung Jarak Setiap Data Pada Centroid</h5>
                                @verbatim
                                    <paginate
                                        v-model="currentX"
                                        :page-count="Math.ceil(kelompokSSW.length / itemsPerPage)"
                                        :page-range="5"
                                        :prev-text="'Prev'"
                                        :next-text="'Next'"
                                        :container-class="'pagination'"
                                        :page-class="'page-item'"
                                        :prev-class="'page-item'"
                                        :next-class="'page-item'"
                                        :page-link-class="'page-link'"
                                        :prev-link-class="'page-link'"
                                        :next-link-class="'page-link'">
                                    </paginate>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th scope="col">No</th>
                                                    <th scope="col">Umur</th>
                                                    <th scope="col">Jenis Kelamin</th>
                                                    <th scope="col">Nama Penyakit</th>
                                                    <th v-for="i in jumlahKluster" scope="col">SSW{{ i }}</th>
                                                    <th scope="col">SSW</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item, index) in paginatedData" :key="item.customer_id">
                                                    <td>{{ (index + 1) + ((currentX - 1) * itemsPerPage) }}</td>
                                                    <td>{{ item.umur }}</td>
                                                    <td>{{ item.jenis_kelamin }}</td>
                                                    <td>{{ item.nama_penyakit }}</td>
                                                    <td v-for="(jarak) in item.ssw.jarak">{{ fixedDecimalNumber(jarak) }}</td>
                                                    <td>{{ item.ssw.name }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <h5 class="card-title">Menghitung Rata-Ratanya</h5>
                                    <p v-for="(item, index) in hasilSSW">SSW{{ index+1 }} = {{ fixedDecimalNumber(item.hasil) }}</p>
                                @endverbatim
                            </div>
                        </div>

                        <div class="card mb-3">
                            <h5 class="card-header">Perhitungan Nilai SSB</h5>
                            <div class="card-body">
                                <h5 class="card-title">Centroid Hasil Proses Clustering</h5>
                                @verbatim
                                    <p v-for="(item, index) in centroidClusterAkhir">
                                        {{ index | capitalize }} =
                                        <template v-for="(it, i) in item">
                                            {{ fixedDecimalNumber(it) }}<template v-if="i != item.length - 1">, </template>
                                        </template>
                                    </p>
                                @endverbatim

                                <h5 class="card-title">Menghitung Jarak Antar Centroid Suatu Cluster</h5>
                                @verbatim
                                    <p v-for="(item, index) in hasilSSB">{{ index }} = {{ item.rumus }} = {{ fixedDecimalNumber(item.nilai) }}</p>
                                @endverbatim
                            </div>
                        </div>

                        <div class="card mb-3">
                            <h5 class="card-header">Perhitungan Nilai Ratio</h5>
                            <div class="card-body">
                                <h5 class="card-title">Menghitung Jarak Antar Titik Pusat Cluster Dari Setiap Cluster</h5>
                                @verbatim
                                    <p v-for="(item, index) in hasilPerhitunganRatio">{{ index }} = {{ item }}</p>
                                @endverbatim

                                <h5 class="card-title">Mendapatkan Nilai Ratio Terbesar (R-Max)</h5>
                                @verbatim
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th scope="col">R</th>
                                                    <th scope="col" v-for="(item, index) in ratio">{{ index }}</th>
                                                    <th scope="col">R-Max</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item, index) in ratio" :key="index">
                                                    <td>{{ index }}</td>
                                                    <td v-for="(data) in item.data">{{ fixedDecimalNumber(data) }}</td>
                                                    <td>{{ fixedDecimalNumber(item.max) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @endverbatim
                            </div>
                        </div>

                        @verbatim
                            <div class="card mb-3">
                                <h5 class="card-header">Perhitungan Nilai Davies-Bouldin Index (DBI)</h5>
                                <div class="card-body">
                                    <h5 class="card-title">Nilai Davies-Bouldin Index (DBI) Diperoleh Dengan Menghitung Rata-Rata Dari Nilai Rasio Terbesar (R-Max)</h5>
                                    <p>{{ fixedDecimalNumber(dbi) }}</p>
                                </div>
                            </div>
                        @endverbatim
                    </div>

                    <div class="tab-pane card fade" id="v-pills-graph" role="tabpanel" aria-labelledby="v-pills-graph-tab">
                        <h5 class="card-header">Graph</h5>
                        <div class="card-body">
                            <h5 class="card-title">Jumlah Anggota Clusters</h5>
                            <cluster-pie-chart></cluster-pie-chart>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">@{{ cluster1 }}</h5>
                            <cluster-1-bar-chart></cluster-1-bar-chart>
                            <!-- <p>Grafik Cluster 1</p>
                            @verbatim
                                <p v-for="(recency, key) in centroidClusterAkhir">
                                    {{ key | capitalize }} = {{ fixedDecimalNumber(recency[0]) }}
                                </p>
                            @endverbatim -->
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">@{{ cluster2 }}</h5>
                            <cluster-2-bar-chart></cluster-2-bar-chart>
                            <!-- <p>Grafik Frequency terbentuk dari nilai cluster berdasarkan rata-rata nilai Frequency</p>
                            @verbatim
                                <p v-for="(frequency, key) in centroidClusterAkhir">
                                    {{ key | capitalize }} = {{ fixedDecimalNumber(frequency[1]) }}
                                </p>
                            @endverbatim -->
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">@{{ cluster3 }}</h5>
                            <cluster-3-bar-chart></cluster-3-bar-chart>
                        </div>
                        <!-- <div class="card-body">
                            <h5 class="card-title">Kesimpulan</h5>
                            @verbatim
                                <p v-for="(monetary, key) in centroidClusterAkhir">
                                    {{ key | capitalize }} =
                                </p>
                            @endverbatim
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    @endisset
    </div>
@endsection

@isset($clusters)
@push('scripts')
    <!-- development version, includes helpful console warnings -->
    <script src="{{ asset('js/vue.js') }}"></script>
    <script src="{{ asset('js/Chart.min.js') }}"></script>
    <script src="{{ asset('js/vue-chartjs.min.js') }}"></script>
    <!-- use the latest release -->
    <script src="{{ asset('js/vuejs-paginate.js') }}"></script>
    <script>
        Vue.component('cluster-pie-chart', {
            extends: VueChartJs.Pie,
            mounted () {
                jumlah_anggota = this.$parent.jumlahAnggota()

                labels = []
                for (i = 1; i <= this.$parent.jumlahKluster; i++) {
                    labels.push('Cluster ' + i)
                }

                data = []
                for (key in jumlah_anggota) {
                    data.push(jumlah_anggota[key])
                }

                this.renderChart({
                    labels: labels,
                    datasets: [
                        {
                        backgroundColor: this.$parent.clusterRandomColor,
                        data: data
                        }
                    ]
                    }, {responsive: false, maintainAspectRatio: true})
            }
        })

        Vue.component('cluster-1-bar-chart', {
            extends: VueChartJs.Bar,
            data: function () {
                return {
                    clusters: []
                }
            },
            mounted () {
                anak_anak = this.$parent.clusters.filter(
                    function (item) {
                        return (item.cluster === "cluster_1" && item.transformations[0] === 1)
                })
                dewasa = this.$parent.clusters.filter(
                    function (item) {
                        return (item.cluster === "cluster_1" && item.transformations[0] === 2)
                })
                lansia = this.$parent.clusters.filter(
                    function (item) {
                        return (item.cluster === "cluster_1" && item.transformations[0] === 3)
                })
                this.clusters.push(anak_anak.length)
                this.clusters.push(dewasa.length)
                this.clusters.push(lansia.length)

                labels = ['Anak-Anak', 'Dewasa', 'Lansia']

                this.renderChart({
                    labels: labels,
                    datasets: [
                        {
                            backgroundColor: this.$parent.ageRandomColor,
                            data: this.clusters
                        }
                    ]
                    }, {
                        responsive: false,
                        maintainAspectRatio: true,
                        legend: {
                            display: false,
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    min: 0,
                                }
                            }]
                        }
                })
            }
        })

        Vue.component('cluster-2-bar-chart', {
            extends: VueChartJs.Bar,
            data: function () {
                return {
                    clusters: []
                }
            },
            mounted () {
                anak_anak = this.$parent.clusters.filter(
                    function (item) {
                        return (item.cluster === "cluster_2" && item.transformations[0] === 1)
                })
                dewasa = this.$parent.clusters.filter(
                    function (item) {
                        return (item.cluster === "cluster_2" && item.transformations[0] === 2)
                })
                lansia = this.$parent.clusters.filter(
                    function (item) {
                        return (item.cluster === "cluster_2" && item.transformations[0] === 3)
                })
                this.clusters.push(anak_anak.length)
                this.clusters.push(dewasa.length)
                this.clusters.push(lansia.length)

                labels = ['Anak-Anak', 'Dewasa', 'Lansia']

                this.renderChart({
                    labels: labels,
                    datasets: [
                        {
                            backgroundColor: this.$parent.ageRandomColor,
                            data: this.clusters
                        }
                    ]
                    }, {
                        responsive: false,
                        maintainAspectRatio: true,
                        legend: {
                            display: false,
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    min: 0,
                                }
                            }]
                        }
                })
            }
        })

        Vue.component('cluster-3-bar-chart', {
            extends: VueChartJs.Bar,
            data: function () {
                return {
                    clusters: []
                }
            },
            mounted () {
                anak_anak = this.$parent.clusters.filter(
                    function (item) {
                        return (item.cluster === "cluster_3" && item.transformations[0] === 1)
                })
                dewasa = this.$parent.clusters.filter(
                    function (item) {
                        return (item.cluster === "cluster_3" && item.transformations[0] === 2)
                })
                lansia = this.$parent.clusters.filter(
                    function (item) {
                        return (item.cluster === "cluster_3" && item.transformations[0] === 3)
                })
                this.clusters.push(anak_anak.length)
                this.clusters.push(dewasa.length)
                this.clusters.push(lansia.length)

                labels = ['Anak-Anak', 'Dewasa', 'Lansia']

                this.renderChart({
                    labels: labels,
                    datasets: [
                        {
                            backgroundColor: this.$parent.ageRandomColor,
                            data: this.clusters
                        }
                    ]
                    }, {
                        responsive: false,
                        maintainAspectRatio: true,
                        legend: {
                            display: false,
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    min: 0,
                                }
                            }]
                        }
                })
            }
        })

        Vue.component('paginate', VuejsPaginate)

        var app = new Vue({
            el: '#app',
            data: {
                clusters: @json($clusters),
                diseases: @json($diseases),
                jumlahKluster: {{ $jumlahKluster }},
                filtering_clusters: null,
                infoCluster: ['Jumlah Cluster = {{ $jumlahKluster }}'],
                prosesKMeans: @json($prosesKMeans),
                centroidClusterAkhir: @json($centroidClusterAkhir),
                kelompokSSW: @json($kelompokSSW),
                hasilSSW: @json($hasilSSW),
                hasilSSB: @json($hasilSSB),
                hasilPerhitunganRatio: @json($hasilPerhitunganRatio),
                ratio: @json($ratio),
                dbi: {{ $dbi }},
                currentX: 1,
                itemsPerPage: 10,
                clusterRandomColor: [],
                ageRandomColor: [],
                jumlah_anggota: null,
                cluster1: null,
                cluster2: null,
                cluster3: null
            },
            beforeMount: function() {
                // Random Color
                for(i = 0; i < this.jumlahKluster; i++) {
                    r1 = Math.floor(Math.random() * 255)
                    g1 = Math.floor(Math.random() * 255)
                    b1 = Math.floor(Math.random() * 255)
                    r2 = Math.floor(Math.random() * 255)
                    g2 = Math.floor(Math.random() * 255)
                    b2 = Math.floor(Math.random() * 255)
                    this.clusterRandomColor.push(`rgb(${r1},${g1},${b1})`)
                    this.ageRandomColor.push(`rgb(${r2},${g2},${b2})`)
                }
            },
            mounted: function() {
                this.filtering_clusters = this.clusters
                this.jumlah_anggota = this.jumlahAnggota()
                this.infoJumlahAnggotaCluster()
                this.cluster1 = this.whichCluster(jumlah_anggota['cluster_1'], jumlah_anggota['cluster_2'], jumlah_anggota['cluster_3'])
                this.cluster2 = this.whichCluster(jumlah_anggota['cluster_2'], jumlah_anggota['cluster_1'], jumlah_anggota['cluster_3'])
                this.cluster3 = this.whichCluster(jumlah_anggota['cluster_3'], jumlah_anggota['cluster_1'], jumlah_anggota['cluster_2'])
            },
            computed: {
                paginatedData () {
                    const start = (this.currentX - 1) * this.itemsPerPage, end = start + this.itemsPerPage

                    return this.kelompokSSW.slice(start, end)
                }
            },
            methods: {
                whichCluster(main_cluster, cluster_1, cluster_2) {
                    text = ""

                    if (main_cluster < cluster_1 && main_cluster < cluster_2) {
                        text = "Penyakit Rendah"
                    }

                    if ((main_cluster > cluster_1 && main_cluster < cluster_2) || (main_cluster > cluster_2 && main_cluster < cluster_1)) {
                        text = "Penyakit Sedang"
                    }

                    if (main_cluster > cluster_1 && main_cluster > cluster_2) {
                        text = "Penyakit Tinggi"
                    }

                    return text
                },
                maxIndexOf: function(numbers) {
                    max_index = 0;

                    for (let i = 0; i < numbers.length; i++) {
                        if (numbers[i] > numbers[max_index]) {
                            max_index = i;
                        }
                    }

                    return max_index;
                },
                fixedDecimalNumber: function(number) {
                    return (number % 1 != 0 ? number.toFixed(4) : number)
                },
                jumlahAnggota: function() {
                    jumlah_anggota = {}

                    for(i = 1; i <= this.jumlahKluster; i++) {
                        jumlah_anggota['cluster_' + i] = 0
                    }

                    for (key in this.clusters) {
                        jumlah_anggota[this.clusters[key].cluster]++
                    }

                    return jumlah_anggota
                },
                infoJumlahAnggotaCluster: function() {
                    for (key in this.jumlah_anggota) {
                        this.infoCluster.push(`${this.$options.filters.capitalize(key)} sebanyak ${jumlah_anggota[key]} data`)
                    }
                },
                filteringCluster: function(event) {
                    this.filtering_clusters = this.clusters.filter(
                        function (item) {
                            return (event.target.value === "*" ? item.cluster : item.cluster === "cluster_" + event.target.value)
                    })

                    this.infoCluster = []

                    if (event.target.value === "*") {
                        this.infoCluster.push(`Jumlah Cluster = ${this.jumlahKluster}`)
                        this.infoJumlahAnggotaCluster()
                    } else {
                        transform_umur = []
                        transform_jenis_kelamin = []
                        transform_diagnosa = []
                        dominant_diagnosa = []

                        for (index in this.filtering_clusters) {
                            transform_umur.push(this.filtering_clusters[index]['transformations'][0])
                            transform_jenis_kelamin.push(this.filtering_clusters[index]['transformations'][1])
                            transform_diagnosa.push(this.filtering_clusters[index]['transformations'][2])
                            dominant_diagnosa.push(0)
                        }

                        dominant_umur = [0, 0, 0]
                        for (const umur of transform_umur) {
                            dominant_umur[umur-1] += 1
                        }

                        dominant_jenis_kelamin = [0, 0]
                        for (const jenis_kelamin of transform_jenis_kelamin) {
                            dominant_jenis_kelamin[jenis_kelamin-1] += 1
                        }

                        for (const diagnosa of transform_diagnosa) {
                            dominant_diagnosa[diagnosa-1] += 1
                        }

                        this.infoCluster.push(`Didapatkan data sebanyak (${this.filtering_clusters.length} item)`)

                        this.infoCluster.push(`Usia paling dominan yaitu ${['Anak-anak (1-13 Tahun)', 'Dewasa (14-45 Tahun)', 'Lansia (46-125 Tahun)'][this.maxIndexOf(dominant_umur)]}`)
                        this.infoCluster.push(`Jenis kelamin paling dominan yaitu ${['Laki-laki', 'Perempuan'][this.maxIndexOf(dominant_jenis_kelamin)]}`)
                        this.infoCluster.push(`Diagnosa paling dominan yaitu ${this.diseases[this.maxIndexOf(dominant_diagnosa) + 1]}`)
                    }
                }
            },
            filters: {
                capitalize: function(value) {
                    return value.charAt(0).toUpperCase() + value.slice(1).replace("_", " ")
                }
            }
        })
    </script>
@endpush
@endisset