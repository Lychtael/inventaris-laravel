<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ $judul ?? 'Edit Aset' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <h3>Formulir Edit Aset: {{ $barang->nama_barang }}</h3>
                    <hr class="my-3">

                    <form action="{{ route('barang.update', $barang->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            {{-- KOLOM KIRI (Grup 1: Dropdown Wajib) --}}
                            <div class="col-md-6">
                                <h5>1. Kepemilikan & Kategori (Wajib)</h5>
                                <div class="mb-3">
                                    <label for="id_dinas" class="form-label">Dinas Pemilik</label>
                                    <select class="form-select @error('id_dinas') is-invalid @enderror" id="id_dinas" name="id_dinas" required>
                                        <option value="">-- Pilih Dinas --</option>
                                        @foreach ($dinas as $item)
                                            <option value="{{ $item->id }}" @selected(old('id_dinas', $barang->id_dinas) == $item->id)>{{ $item->nama_dinas }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_dinas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="id_bidang" class="form-label">Bidang Pemilik</label>
                                    <select class="form-select @error('id_bidang') is-invalid @enderror" id="id_bidang" name="id_bidang" required>
                                        <option value="">-- Pilih Bidang --</option>
                                        @foreach ($bidang as $item)
                                            <option value="{{ $item->id }}" @selected(old('id_bidang', $barang->id_bidang) == $item->id)>{{ $item->nama_bidang }} (Dinas: {{ $item->dinas->nama_dinas }})</option>
                                        @endforeach
                                    </select>
                                    @error('id_bidang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="id_jenis" class="form-label">Jenis Barang</label>
                                    <select class="form-select @error('id_jenis') is-invalid @enderror" id="id_jenis" name="id_jenis" required>
                                        <option value="">-- Pilih Jenis --</option>
                                        @foreach ($jenis as $item)
                                            <option value="{{ $item->id }}" @selected(old('id_jenis', $barang->id_jenis) == $item->id)>{{ $item->nama_jenis }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_jenis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="id_sumber" class="form-label">Sumber Perolehan</label>
                                    <select class="form-select @error('id_sumber') is-invalid @enderror" id="id_sumber" name="id_sumber" required>
                                        <option value="">-- Pilih Sumber --</option>
                                        @foreach ($sumber as $item)
                                            <option value="{{ $item->id }}" @selected(old('id_sumber', $barang->id_sumber) == $item->id)>{{ $item->nama_sumber }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_sumber') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="id_kondisi" class="form-label">Kondisi</label>
                                    <select class="form-select @error('id_kondisi') is-invalid @enderror" id="id_kondisi" name="id_kondisi" required>
                                        <option value="">-- Pilih Kondisi --</option>
                                        @foreach ($kondisi as $item)
                                            <option value="{{ $item->id }}" @selected(old('id_kondisi', $barang->id_kondisi) == $item->id)>{{ $item->nama_kondisi }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_kondisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="id_status_aset" class="form-label">Status Aset</label>
                                    <select class="form-select @error('id_status_aset') is-invalid @enderror" id="id_status_aset" name="id_status_aset" required>
                                        <option value="">-- Pilih Status --</option>
                                        @foreach ($status_aset as $item)
                                            <option value="{{ $item->id }}" @selected(old('id_status_aset', $barang->id_status_aset) == $item->id)>{{ $item->nama_status }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_status_aset') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            {{-- KOLOM KANAN (Grup 2 & 3: Input Manual) --}}
                            <div class="col-md-6">
                                <h5>2. Detail Aset (Sesuai CSV)</h5>
                                <div class="mb-3">
                                    <label for="nama_barang" class="form-label">Nama Barang / Jenis Barang</label>
                                    <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" 
                                           id="nama_barang" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" required>
                                    @error('nama_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="kode_barang" class="form-label">Kode Barang</label>
                                            <input type="text" class="form-control @error('kode_barang') is-invalid @enderror" 
                                                   id="kode_barang" name="kode_barang" value="{{ old('kode_barang', $barang->kode_barang) }}">
                                            @error('kode_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="register" class="form-label">Register</label>
                                            <input type="text" class="form-control @error('register') is-invalid @enderror" 
                                                   id="register" name="register" value="{{ old('register', $barang->register) }}">
                                            @error('register') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="merk_type" class="form-label">Merk / Type</label>
                                    <input type="text" class="form-control @error('merk_type') is-invalid @enderror" 
                                           id="merk_type" name="merk_type" value="{{ old('merk_type', $barang->merk_type) }}">
                                    @error('merk_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="nomor_spek" class="form-label">No. Sertifikat / Pabrik / Chasis / Mesin</label>
                                    <input type="text" class="form-control @error('nomor_spek') is-invalid @enderror" 
                                           id="nomor_spek" name="nomor_spek" value="{{ old('nomor_spek', $barang->nomor_spek) }}">
                                    @error('nomor_spek') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="bahan" class="form-label">Bahan</label>
                                            <input type="text" class="form-control @error('bahan') is-invalid @enderror" 
                                                   id="bahan" name="bahan" value="{{ old('bahan', $barang->bahan) }}">
                                            @error('bahan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="satuan" class="form-label">Satuan</label>
                                            <input type="text" class="form-control @error('satuan') is-invalid @enderror" 
                                                   id="satuan" name="satuan" value="{{ old('satuan', $barang->satuan) }}" placeholder="Contoh: Unit, Buah, Rim">
                                            @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="ukuran" class="form-label">Ukuran (P, S, D)</label>
                                    <input type="text" class="form-control @error('ukuran') is-invalid @enderror" 
                                           id="ukuran" name="ukuran" value="{{ old('ukuran', $barang->ukuran) }}">
                                    @error('ukuran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="tahun_pembelian" class="form-label">Tahun Pembelian</label>
                                            <input type="number" class="form-control @error('tahun_pembelian') is-invalid @enderror" 
                                                   id="tahun_pembelian" name="tahun_pembelian" value="{{ old('tahun_pembelian', $barang->tahun_pembelian) }}" placeholder="Contoh: 2024">
                                            @error('tahun_pembelian') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="harga" class="form-label">Harga (Rp)</label>
                                            <input type="number" class="form-control @error('harga') is-invalid @enderror" 
                                                   id="harga" name="harga" value="{{ old('harga', $barang->harga) }}">
                                            @error('harga') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="lokasi" class="form-label">Lokasi (Teks)</label>
                                    <input type="text" class="form-control @error('lokasi') is-invalid @enderror" 
                                           id="lokasi" name="lokasi" value="{{ old('lokasi', $barang->lokasi) }}" placeholder="Contoh: Ruang Rapat Lt. 2">
                                    @error('lokasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="pengguna" class="form-label">Pengguna Aset</label>
                                    <input type="text" class="form-control @error('pengguna') is-invalid @enderror" 
                                           id="pengguna" name="pengguna" value="{{ old('pengguna', $barang->pengguna) }}" placeholder="Contoh: Staff Bidang TIK">
                                    @error('pengguna') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="keterangan" class="form-label">Keterangan</label>
                                    <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                              id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $barang->keterangan) }}</textarea>
                                    @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('barang.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Update Aset</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>