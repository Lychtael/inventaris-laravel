<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ $judul ?? 'Import Aset dari CSV' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <h3>Formulir Impor Aset</h3>
                    <hr class="my-3">

                    <div class="alert alert-warning">
                        <strong>Perhatian!</strong> Fitur impor ini akan membaca file CSV/Excel dan "menempelkan" data Dinas/Bidang yang Anda pilih di bawah ini ke setiap aset yang diimpor.
                    </div>

                    <form action="{{ route('barang.importCsv') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_dinas" class="form-label">1. Impor untuk Dinas mana? <span class="text-danger">*</span></label>
                                    <select class="form-select @error('id_dinas') is-invalid @enderror" id="id_dinas" name="id_dinas" required>
                                        <option value="">-- Pilih Dinas --</option>
                                        @foreach ($dinas as $item)
                                            <option value="{{ $item->id }}" {{ old('id_dinas') == $item->id ? 'selected' : '' }}>{{ $item->nama_dinas }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_dinas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_bidang" class="form-label">2. Impor untuk Bidang mana? <span class="text-danger">*</span></label>
                                    <select class="form-select @error('id_bidang') is-invalid @enderror" id="id_bidang" name="id_bidang" required>
                                        <option value="">-- Pilih Bidang --</option>
                                        {{-- (Idealnya dinamis, tapi untuk sekarang tampilkan semua) --}}
                                        @foreach ($bidang as $item)
                                            <option value="{{ $item->id }}" {{ old('id_bidang') == $item->id ? 'selected' : '' }}>{{ $item->nama_bidang }} (Dinas: {{ $item->dinas->nama_dinas }})</option>
                                        @endforeach
                                    </select>
                                    @error('id_bidang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="csv_file" class="form-label">3. Pilih File CSV/Excel <span class="text-danger">*</span></label>
                            <input class="form-control @error('csv_file') is-invalid @enderror" type="file" 
                                   id="csv_file" name="csv_file" required 
                                   accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                            @error('csv_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr class="my-3">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('barang.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Impor Data</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>