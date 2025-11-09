<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
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
            <div class="row mb-4">
                {{-- Total Barang Baik --}}
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-success">Total Barang (Kondisi Baik)</h5>
                            <p class="card-text fs-3 fw-bold">{{ $totalBarangBaik ?? 0 }}</p>
                            <small class="text-muted">Jumlah barang yang siap dipinjam</small>
                        </div>
                    </div>
                </div>
                {{-- Total Dipinjam --}}
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-warning">Total Barang Dipinjam</h5>
                            <p class="card-text fs-3 fw-bold">{{ $totalDipinjam ?? 0 }}</p>
                            <small class="text-muted">Barang yang sedang tidak ada di stok</small>
                        </div>
                    </div>
                </div>
                {{-- Total Rusak --}}
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-danger">Total Barang Rusak</h5>
                            <p class="card-text fs-3 fw-bold">{{ $totalRusak ?? 0 }}</p>
                            <small class="text-muted">Rusak Ringan + Rusak Berat</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Grafik Pie Chart --}}
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Grafik Kondisi Barang</h5>
                            <canvas id="kondisiChart" height="200"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Log Aktivitas Terbaru --}}
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Log Aktivitas Terbaru</h5>
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

    @push('scripts')
        {{-- 1. Import Library Chart.js dari CDN --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        {{-- 2. Script untuk membuat grafik --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('kondisiChart');

                const labels = @json($chartLabels);
                const data = @json($chartData);

                const getColors = (labels) => {
                    return labels.map(label => {
                        if (label === 'Baik') {
                            return 'rgb(22, 163, 74)'; // Hijau
                        }
                        if (label === 'KB') {
                            return 'rgb(249, 115, 22)'; // Oranye
                        }
                        if (label === 'RB') {
                            return 'rgb(220, 38, 38)'; // Merah
                        }
                        return 'rgb(107, 114, 128)'; // Abu-abu (default)
                    });
                };

                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jumlah Aset',
                            data: data,
                            backgroundColor: getColors(labels),
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