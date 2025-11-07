<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ $judul ?? 'Daftar Aset Barang' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <h3>{{ $judul ?? 'Daftar Aset Barang' }}</h3>
                    <hr class="my-3">

                    
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="d-flex justify-content-start mb-3">
                        <a href="{{ route('barang.create') }}" class="btn btn-success me-2">Tambah Aset Baru</a>
                        {{-- Tombol Impor/Ekspor dinonaktifkan
                        <a href="{{ route('barang.exportCsv') }}" class="btn btn-primary me-2">Export ke CSV</a>
                        <a href="{{ route('barang.importCsvForm') }}" class="btn btn-success me-2">Import dari CSV</a> 
                        --}}
                    </div>

                    
                    <div class="card mb-3">
                        <div class="card-body">
                            <form action="{{ route('barang.index') }}" method="get">
                                <div class="row align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label">Filter Jenis:</label>
                                        <select name="id_jenis" class="form-select">
                                            <option value="">Semua Jenis</option>
                                            @foreach($jenis_list as $item)
                                                <option value="{{ $item->id }}" {{ ($current_filters['id_jenis'] ?? '') == $item->id ? 'selected' : '' }}>{{ $item->nama_jenis }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Filter Kondisi:</label>
                                        <select name="id_kondisi" class="form-select">
                                            <option value="">Semua Kondisi</option>
                                            @foreach($kondisi_list as $item)
                                                <option value="{{ $item->id }}" {{ ($current_filters['id_kondisi'] ?? '') == $item->id ? 'selected' : '' }}>{{ $item->nama_kondisi }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Filter Lokasi:</label>
                                        <select name="id_lokasi" class="form-select">
                                            <option value="">Semua Lokasi</option>
                                            @foreach($lokasi_list as $item)
                                                <option value="{{ $item->id }}" {{ ($current_filters['id_lokasi'] ?? '') == $item->id ? 'selected' : '' }}>{{ $item->nama_lokasi }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Filter Status:</label>
                                        <select name="id_status_aset" class="form-select">
                                            <option value="">Semua Status</option>
                                            @foreach($status_aset_list as $item)
                                                <option value="{{ $item->id }}" {{ ($current_filters['id_status_aset'] ?? '') == $item->id ? 'selected' : '' }}>{{ $item->nama_status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary me-2 w-100">Filter</button>
                                        <a href="{{ route('barang.index') }}" class="btn btn-secondary w-100">Reset</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Kode Barang</th>
                                    <th>Register</th>
                                    <th>Merk/Type</th>
                                    <th>Tahun</th>
                                    <th>Lokasi</th>
                                    <th>Kondisi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                {{-- Kita panggil partial view baru --}}
                                @include('barang._tabel_aset', ['barang' => $barang])
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $barang->appends(request()->query())->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>