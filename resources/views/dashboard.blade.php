<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    {{-- Pesan Notifikasi (untuk hak akses, dll) --}}
    @if (session('error'))
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-red-500 text-white font-bold p-4 rounded shadow-md">
                    {{ session('error') }}
                </div>
            </div>
        </div>
    @endif
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- === 0. FILTER DINAS/BIDANG (ALUR DRILL-DOWN) === --}}
            @if (Auth::user()->id_peran == 1) {{-- Hanya Admin (BMD) yang bisa filter --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Filter Dashboard</h5>
                    <p class="card-text">Tampilkan statistik untuk Dinas atau Bidang tertentu.</p>
                    <form action="{{ route('dashboard') }}" method="GET">
                        <div class="row align-items-end">
                            <div class="col-md-5">
                                <label for="id_dinas" class="form-label">Pilih Dinas</label>
                                <select class="form-select" id="id_dinas" name="id_dinas" onchange="this.form.submit()">
                                    <option value="">-- Tampilkan Semua Dinas --</option>
                                    @foreach ($dinasList as $dinas)
                                        <option value="{{ $dinas->id }}" @selected($selectedDinasId == $dinas->id)>
                                            {{ $dinas->nama_dinas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label for="id_bidang" class="form-label">Pilih Bidang</label>
                                <select class="form-select" id="id_bidang" name="id_bidang" onchange="this.form.submit()">
                                    <option value="">-- Tampilkan Semua Bidang (dari Dinas terpilih) --</option>
                                    @foreach ($bidangList as $bidang)
                                        <option value="{{ $bidang->id }}" @selected($selectedBidangId == $bidang->id)>
                                            {{ $bidang->nama_bidang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('dashboard') }}" class="btn btn-secondary w-100">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif
            
            <div class="row mb-4">
                {{-- Total Barang Baik --}}
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-success">Total Aset (Kondisi Baik)</h5>
                            <p class="card-text fs-3 fw-bold">{{ $totalBarangBaik ?? 0 }}</p>
                            <small class="text-muted">Jumlah unit aset "Baik (B)"</small>
                        </div>
                    </div>
                </div>
                {{-- Total Dipinjam --}}
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-warning">Total Aset Dipinjam</h5>
                            <p class="card-text fs-3 fw-bold">{{ $totalDipinjam ?? 0 }}</p>
                            <small class="text-muted">Aset dengan status "Dipinjam"</small>
                        </div>
                    </div>
                </div>
                {{-- Total Rusak --}}
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-danger">Total Aset Rusak</h5>
                            <p class="card-text fs-3 fw-bold">{{ $totalRusak ?? 0 }}</p>
                            <small class="text-muted">Kondisi "Kurang Baik (KB)" + "Rusak Berat (RB)"</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Grafik Pie Chart --}}
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Grafik Kondisi Aset</h5>
                            <canvas id="kondisiChart" height="200"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Log Aktivitas Terbaru --}}
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Log Aktivitas Terbaru (Global)</h5>
                            <ul class="list-group list-group-flush">
                                @forelse ($logAktivitas as $log)
                                    <li class="list-group-item">
                                        <strong>{{ $log->pengguna->name ?? 'Sistem' }}</strong> 
                                        {{ $log->keterangan }}
                                        <small class="text-muted d-block">{{ $log->dibuat_pada->diffForHumans() }}</small>
                                    </li>
                                @empty
                                    <li class="list-group-item">Belum ada aktivitas.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- PUSH SCRIPT UNTUK CHART.JS --}}
    @push('scripts')
        {{-- 1. Import Library Chart.js dari CDN --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        {{-- 2. Script untuk membuat grafik --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('kondisiChart');
                
                // Ambil data dari PHP
                const labels = @json($chartLabels);
                const data = @json($chartData);

                // Fungsi untuk memetakan label ke warna (sesuai Seeder CSV)
                const getColors = (labels) => {
                    return labels.map(label => {
                        if (label === 'Baik (B)' || label === 'Baik') {
                            return 'rgb(22, 163, 74)'; // Hijau
                        }
                        if (label === 'Kurang Baik (KB)' || label === 'KB') {
                            return 'rgb(249, 115, 22)'; // Oranye
                        }
                        if (label === 'Rusak Berat (RB)' || label === 'RB') {
                            return 'rgb(220, 38, 38)'; // Merah
                        }
                        return 'rgb(107, 114, 128)'; // Abu-abu (default)
                    });
                };

                new Chart(ctx, {
                    type: 'pie', // Tipe grafik
                    data: {
                        labels: labels, // Label dari const
                        datasets: [{
                            label: 'Jumlah Aset',
                            data: data, // Data dari const
                            backgroundColor: getColors(labels), // Panggil fungsi warna
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        }
                    }
                });
            });
        </script>
    @endpush

</x-app-layout>