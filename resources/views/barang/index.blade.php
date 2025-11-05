<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $judul ?? 'Daftar Barang' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <h3>{{ $judul ?? 'Daftar Barang' }}</h3>
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
                        <a href="{{ route('barang.create') }}" class="btn btn-success me-2">Tambah Barang</a>
                        <a href="{{ route('barang.exportCsv') }}" class="btn btn-primary me-2">Export ke CSV</a>
                        <a href="{{ route('barang.importCsvForm') }}" class="btn btn-success me-2">Import dari CSV</a>
                    </div>

                   
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-md-8">
                                    <form action="{{ route('barang.index') }}" method="get" class="d-flex align-items-end">
                                        <div class="flex-grow-1 me-2">
                                            <label class="form-label">Filter berdasarkan:</label>
                                            <select name="jenis" class="form-select">
                                                <option value="">Semua Jenis</option>
                                                @foreach($jenis_list as $jenis)
                                                    <option value="{{ $jenis->id }}" {{ ($current_filters['jenis'] ?? '') == $jenis->id ? 'selected' : '' }}>
                                                        {{ $jenis->nama_jenis }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="flex-grow-1 me-2">
                                            <label class="form-label">&nbsp;</label>
                                            <select name="sumber" class="form-select">
                                                <option value="">Semua Sumber</option>
                                                @foreach($sumber_list as $sumber)
                                                    <option value="{{ $sumber->id }}" {{ ($current_filters['sumber'] ?? '') == $sumber->id ? 'selected' : '' }}>
                                                        {{ $sumber->nama_sumber }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="me-2">
                                            <button type="submit" class="btn btn-primary">Apply Filter</button>
                                        </div>
                                        <div>
                                            <a href="{{ route('barang.index') }}" class="btn btn-secondary">Reset</a>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-4">
                                    <label for="keyword" class="form-label">Cari Nama Barang</label>
                                    <input type="text" class="form-control" placeholder="Mulai mengetik..." name="keyword" id="keyword" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Jenis Barang</th>
                                    <th>Sumber Barang</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                @include('barang._search_results', ['barang' => $barang])
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

   
    @if (session('csv_import_errors'))
        <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="errorModalLabel">Error Saat Import CSV</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Ditemukan error pada baris berikut dan proses import dibatalkan:</p>
                        <ul class="list-group" id="errorList">
                            @foreach(session('csv_import_errors') as $error)
                                <li class="list-group-item list-group-item-danger">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Script AJAX Search (dari index.php)
            $('#keyword').on('keyup', function() {
                let keyword = $(this).val();
                
                // Jika keyword kosong, jangan lakukan AJAX
                if (keyword.length < 1) {
                    // Anda bisa me-load ulang data asli atau clear table
                    // Untuk tutorial ini, kita biarkan saja
                    return;
                }

                $.ajax({
                    // Gunakan route() helper
                    url: '{{ route("barang.cari") }}',
                    data: { 
                        keyword: keyword,
                        _token: '{{ csrf_token() }}' // Tambahkan CSRF token
                    },
                    method: 'post',
                    success: function(data) {
                        $('#table-body').html(data);
                    }
                });
            });

            // Tampilkan Modal Error CSV jika ada (dari index.php)
            @if (session('csv_import_errors'))
                const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            @endif
        });
    </script>
    @endpush

</x-app-layout>