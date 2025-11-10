<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $judul ?? 'Detail Barang' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="mb-0">Detail Barang</h3>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $barang->nama_barang }}</h5>
                            <p class="card-text">
                                <strong>Kuantitas:</strong> {{ $barang->jumlah }} {{ $barang->satuan }}
                            </p>
                            <p class="card-text">
                                <strong>Jenis Barang:</strong> 
                                <span class="badge bg-info">{{ $barang->jenis->nama_jenis ?? 'Tidak ada' }}</span>
                            </p>
                            <p class="card-text">
                                <strong>Sumber Barang:</strong> 
                                <span class="badge bg-success">{{ $barang->sumber->nama_sumber ?? 'Tidak ada' }}</span>
                            </p>
                            <p class="card-text">
                                <strong>Keterangan:</strong>
                                <br>
                                {!! nl2br(e($barang->keterangan ?? 'Tidak ada keterangan.')) !!}
                            </p>
                            <p class="card-text">
                                <small class="text-muted">
                                    Data dibuat pada: 
                                    @if($barang->created_at)
                                        {{ $barang->created_at->format('d M Y, H:i') }}
                                    @elseif($barang->dibuat_pada)
                                        {{ \Carbon\Carbon::parse($barang->dibuat_pada)->format('d M Y, H:i') }}
                                    @else
                                        -
                                    @endif
                                </small>
                            </p>
                            <a href="{{ route('barang.index') }}" class="btn btn-secondary mt-3">Kembali ke Daftar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>