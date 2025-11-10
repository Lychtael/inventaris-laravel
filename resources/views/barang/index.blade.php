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
                            {{-- PERUBAHAN BESAR ADA DI BLOK INI --}}
                            <div class="row align-items-end">
                                {{-- Bagian Filter (Kiri) --}}
                                <div class="col-md-8">
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
                                            <div class="col-md-4 d-flex align-items-end">
                                                <button type="submit" class="btn btn-primary me-2 w-100">Filter</button>
                                                <a href="{{ route('barang.index') }}" class="btn btn-secondary w-100">Reset</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                {{-- Bagian Search (Kanan) --}}
                                <div class="col-md-4">
                                    <label for="keyword" class="form-label">Pencarian Cepat (Nama, Kode, Merk)</label>
                                    <input type="text" class="form-control" placeholder="Mulai mengetik..." name="keyword" id="keyword" autocomplete="off">
                                </div>
                            </div>
                            {{-- BATAS PERUBAHAN --}}
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
                                {{-- Pastikan nama file partial ini benar --}}
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

    {{-- TAMBAHKAN BLOK SCRIPT INI DI SINI (DI DALAM <x-app-layout>) --}}
    @push('scripts')
    <script>
        $(document).ready(function() {
            // Script AJAX Search (Logika Aset Baru)
            $('#keyword').on('keyup', function() {
                let keyword = $(this).val();
                
                // Sembunyikan pagination jika user mulai mengetik
                if (keyword.length > 0) {
                    $('.pagination').hide();
                } else {
                    // Jika keyword kosong, refresh halaman untuk data asli (cara mudah)
                    window.location.href = '{{ route("barang.index") }}';
                    return;
                }

                $.ajax({
                    url: '{{ route("barang.cari") }}',
                    data: { 
                        keyword: keyword,
                        _token: '{{ csrf_token() }}' // Tambahkan CSRF token
                    },
                    method: 'post',
                    success: function(data) {
                        $('#table-body').html(data);
                    },
                    error: function() {
                        // Pastikan colspan-nya 18
                        $('#table-body').html('<tr><td colspan="18" class="text-center text-danger">Terjadi error saat mencari...</td></tr>');
                    }
                });
            });
        });
    </script>
    @endpush

</x-app-layout>