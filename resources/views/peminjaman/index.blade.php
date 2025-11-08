<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ $judul ?? 'Data Peminjaman Aset' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <h3>{{ $judul ?? 'Data Peminjaman Aset' }}</h3>
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
                        <a href="{{ route('peminjaman.create') }}" class="btn btn-success me-2">Catat Peminjaman Baru</a>
                    </div>

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Aset</th>
                                    <th>Register</th>
                                    <th>Merk/Type</th>
                                    <th>Nama Peminjam</th>
                                    <th>Tgl Pinjam</th>
                                    <th>Tgl Kembali</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($peminjaman as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->barang->nama_barang ?? 'Aset Dihapus' }}</td>
                                    <td>{{ $item->barang->register ?? 'N/A' }}</td>
                                    <td>{{ $item->barang->merk_type ?? 'N/A' }}</td>
                                    <td>{{ $item->peminjam_eksternal ?? ($item->userPeminjam->name ?? 'N/A') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                                    <td>
                                        @if($item->tanggal_kembali)
                                            {{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status_pinjam == 'Dikembalikan')
                                            <span class="badge bg-success">Dikembalikan</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Dipinjam</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status_pinjam == 'Dipinjam')
                                            <form action="{{ route('peminjaman.kembali', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin aset ini sudah dikembalikan?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary">Kembalikan</button>
                                            </form>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Belum ada data peminjaman.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>w
            </div>
        </div>
    </div>
</x-app-layout>