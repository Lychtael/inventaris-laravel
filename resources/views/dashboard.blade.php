<x-app-layout> <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">

                <div class="container mt-4">
                    <h3>Dashboard Inventaris</h3>
                    <hr>

                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card text-white bg-success shadow">
                                <div class="card-body">
                                    <h5 class="card-title">Total Jenis Barang</h5>
                                    <p class="card-text fs-4 fw-bold">{{ $total_barang }} Jenis</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card text-white bg-danger shadow">
                                <div class="card-body">
                                    <h5 class="card-title">Barang Habis (Stok 0)</h5>
                                    <p class="card-text fs-4 fw-bold">{{ $barang_habis }} Jenis</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group">
                            @forelse($barang_by_jenis as $jenis)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $jenis->nama_jenis }}
                                    <span class="badge bg-primary rounded-pill">{{ $jenis->jumlah }}</span>
                                </li>
                            @empty
                                <li class="list-group-item">Data jenis tidak ditemukan.</li>
                            @endforelse
                            </ul>
                        </div>
                        </div>
                </div>

            </div>
        </div>
    </div>
</div>
</x-app-layout>