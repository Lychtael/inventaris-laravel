<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $judul ?? 'Riwayat Aktivitas' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <h3>{{ $judul ?? 'Riwayat Aktivitas' }}</h3>
                    <hr class="my-3">

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Admin</th>
                                    <th>Aksi</th>
                                    <th>Tabel</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($log as $l)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($l->dibuat_pada)->format('d M Y, H:i:s') }}</td>
                                    
                                    <td>{{ $l->pengguna->nama_pengguna ?? ($l->pengguna->name ?? 'User Dihapus') }}</td>
                                    
                                    <td><span class="badge bg-info text-dark">{{ $l->aksi }}</span></td>
                                    <td>{{ $l->tabel }}</td>
                                    <td>{{ $l->keterangan }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada riwayat aktivitas.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>