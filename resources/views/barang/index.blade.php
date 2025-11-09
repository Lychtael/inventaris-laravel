<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ $judul ?? 'Daftar Aset Barang' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
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
                        <a href="{{ route('barang.exportCsv') . '?' . http_build_query(request()->query()) }}" class="btn btn-primary me-2">Export Sesuai Filter</a>
                        <a href="{{ route('barang.importCsvForm') }}" class="btn btn-info me-2">Import dari File</a> 
                    </div>

                    
                    <div class="card mb-3">
                        <div class="card-body">
                            <form action="{{ route('barang.index') }}" method="get">
                                <div class="row align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label">Filter Dinas:</label>
                                        <select name="id_dinas" class="form-select">
                                            <option value="">Semua Dinas</option>
                                            @foreach($dinas_list as $item)
                                                <option value="{{ $item->id }}" {{ ($current_filters['id_dinas'] ?? '') == $item->id ? 'selected' : '' }}>{{ $item->nama_dinas }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Filter Bidang:</label>
                                        <select name="id_bidang" class="form-select">
                                            <option value="">Semua Bidang</option>
                                            @foreach($bidang_list as $item)
                                                <option value="{{ $item->id }}" {{ ($current_filters['id_bidang'] ?? '') == $item->id ? 'selected' : '' }}>{{ $item->nama_bidang }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary me-2 w-100">Filter</button>
                                        <a href="{{ route('barang.index') }}" class="btn btn-secondary w-100">Reset</a>
                                    </div>
                                    <div class="col-md-2">
                                        {{-- (Search box nonaktif sampai Tahap 3) --}}
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
                                    <th>Nama Barang / Jenis</th>
                                    <th>Dinas / Bidang</th>
                                    <th>Kode Barang</th>
                                    <th>Register</th>
                                    <th>Merk / Type</th>
                                    <th>No. Spek</th>
                                    <th>Bahan</th>
                                    <th>Tahun</th>
                                    <th>Ukuran</th>
                                    <th>Satuan</th>
                                    <th>Harga (Rp)</th>
                                    <th>Lokasi (Teks)</th>
                                    <th>Kondisi</th>
                                    <th>Status</th>
                                    <th>Pengguna</th>
                                    <th>Keterangan</th>
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