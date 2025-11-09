{{-- 
File: resources/views/barang/_tabel_aset.blade.php
(Versi Lengkap 18 Kolom)
--}}

@forelse ($barang as $brg)
<tr>
    {{-- 1. Nomor --}}
    <td>{{ $loop->iteration + ($barang->currentPage() - 1) * $barang->perPage() }}</td>
    
    {{-- 2. Nama Barang --}}
    <td>{{ $brg->nama_barang }}</td>
    
    {{-- 3. Dinas / Bidang --}}
    <td>
        <small>
            {{ $brg->dinas->nama_dinas ?? 'N/A' }} <br>
            - {{ $brg->bidang->nama_bidang ?? 'N/A' }}
        </small>
    </td>

    {{-- 4. Kode Barang --}}
    <td>{{ $brg->kode_barang ?? '-' }}</td>
    {{-- 5. Register --}}
    <td>{{ $brg->register ?? '-' }}</td>
    {{-- 6. Merk/Type --}}
    <td>{{ $brg->merk_type ?? '-' }}</td>
    {{-- 7. No. Spek --}}
    <td>{{ $brg->nomor_spek ?? '-' }}</td>
    {{-- 8. Bahan --}}
    <td>{{ $brg->bahan ?? '-' }}</td>
    {{-- 9. Tahun --}}
    <td>{{ $brg->tahun_pembelian ?? '-' }}</td>
    {{-- 10. Ukuran --}}
    <td>{{ $brg->ukuran ?? '-' }}</td>
    {{-- 11. Satuan --}}
    <td>{{ $brg->satuan ?? '-' }}</td>
    {{-- 12. Harga --}}
    <td>{{ number_format($brg->harga ?? 0, 0, ',', '.') }}</td>
    {{-- 13. Lokasi (Teks) --}}
    <td>{{ $brg->lokasi ?? '-' }}</td> 
    {{-- 14. Kondisi --}}
    <td>{{ $brg->kondisi->nama_kondisi ?? 'N/A' }}</td>
    {{-- 15. Status --}}
    <td>
        @php
            $statusClass = 'bg-secondary'; // Default
            if (optional($brg->statusAset)->nama_status == 'Tersedia') $statusClass = 'bg-success';
            if (optional($brg->statusAset)->nama_status == 'Dipinjam') $statusClass = 'bg-warning text-dark';
            if (in_array(optional($brg->statusAset)->nama_status, ['Hilang', 'Dihapuskan', 'Rusak Berat (RB)', 'Kurang Baik (KB)'])) $statusClass = 'bg-danger';
        @endphp
        <span class="badge {{ $statusClass }}">{{ $brg->statusAset->nama_status ?? 'N/A' }}</span>
    </td>
    {{-- 16. Pengguna --}}
    <td>{{ $brg->pengguna ?? '-' }}</td>
    {{-- 17. Keterangan --}}
    <td>{{ \Illuminate\Support\Str::limit($brg->keterangan, 30) }}</td>
    
    {{-- 18. Aksi --}}
    <td>
        <a href="{{ route('barang.show', $brg->id) }}" class="btn btn-sm btn-info">Detail</a>
        <a href="{{ route('barang.edit', $brg->id) }}" class="btn btn-sm btn-warning">Edit</a>
        <form action="{{ route('barang.destroy', $brg->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin hapus aset ini?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
        </form>
    </td>
</tr>
@empty
<tr>
    {{-- (Pastikan colspan-nya 18, sesuai jumlah <th> baru) --}}
    <td colspan="18" class="text-center">Data aset tidak ditemukan.</td>
</tr>
@endforelse