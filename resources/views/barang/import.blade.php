<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $judul ?? 'Import Data Barang' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <h3>Import Data Barang dari CSV</h3>
                    <hr class="my-3">

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @error('csv_file')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    @if (session('csv_import_errors'))
                        <div class="alert alert-danger">
                            <strong>Import Gagal!</strong> Ditemukan error pada baris berikut dan proses import dibatalkan:
                            <ul>
                                @foreach(session('csv_import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif


                    <form action="{{ route('barang.importCsv') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">Pilih File CSV</label>
                            <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                            <div class="form-text">Pastikan header file CSV (baris pertama) dapat dikenali (mis. nama_barang, qty, satuan, jenis, sumber, keterangan).</div>
                        </div>
                        <a href="{{ route('barang.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>