<!DOCTYPE html>
<html>
<head>
    <title>Laporan Monitoring Peralatan - {{ $bulan }} {{ $tahun }}</title>
    <style>
        @page { size: A4 landscape; margin: 30px; }
        body { font-family: Arial, sans-serif; font-size: 9pt; }
        .kop-right { float: right; text-align: left; font-size: 7pt; width: 50%; margin-bottom: 20px; }
        .clear { clear: both; }
        .title { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 5px; }
        .subtitle { text-align: center; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; }
        table.border th, table.border td { border: 1px solid black; padding: 5px; }
        .bg-gray { background-color: #f2f2f2; }
        
        .footer-table { width: 100%; margin-top: 30px; }
        .footer-table td { text-align: center; width: 50%; }
        .spacer { height: 80px; position: relative; vertical-align: middle; }
        
        .dokumentasi-grid { width: 100%; margin-top: 20px; }
        .dokumentasi-item { width: 31%; display: inline-block; vertical-align: top; margin: 1%; text-align: center; border: 1px solid #ddd; padding: 5px; }
        .foto-alat { width: 100%; height: 140px; object-fit: contain; background-color: #f9f9f9; }
        .caption { font-size: 8pt; font-weight: bold; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="kop-right">
        LAMPIRAN II PERATURAN KEPALA BADAN METEOROLOGI, KLIMATOLOGI, DAN GEOFISIKA<br>
        NOMOR 7 TAHUN 2014 TENTANG STANDAR TEKNIS DAN OPERASIONAL<br>
        PEMELIHARAAN PERALATAN METEOROLOGI, KLIMATOLOGI, DAN GEOFISIKA
    </div>
    <div class="clear"></div>

    <div style="text-align: center; font-weight: bold; margin-bottom: 20px;">KOP SURAT UPT</div>

    <div class="title">LAPORAN MONITORING PERALATAN</div>
    <div class="subtitle">Bulan: {{ $bulan }} {{ $tahun }}</div>

    <p><strong>I. Identitas</strong></p>
    <table style="width: 40%; margin-bottom: 15px;">
        <tr><td width="40%">Lokasi/Stasiun</td><td>: Maritim Semarang</td></tr>
        <tr><td>Petugas/Teknis</td><td>: {{ auth()->user()->name }}</td></tr>
        <tr><td>Waktu Pelaksanaan</td><td>: {{ $bulan }} {{ $tahun }}</td></tr>
    </table>

    <p><strong>II. Kondisi Peralatan MKG yang dioperasikan</strong></p>
    <table class="border">
        <thead class="bg-gray">
            <tr>
                <th rowspan="2" width="5%">No</th>
                <th rowspan="2" width="30%">Nama Peralatan</th>
                <th colspan="3">Kondisi</th>
                <th rowspan="2">Keterangan</th>
            </tr>
            <tr>
                <th width="8%">RR</th>
                <th width="8%">RB</th>
                <th width="8%">Baik</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data->unique('alat_id') as $item)
            @php
                $kondisi = strtoupper($item->kondisi_fisik ?? $item->kondisi_akhir ?? '');
            @endphp
            <tr>
                <td align="center">{{ $loop->iteration }}</td>
                <td>{{ $item->alat->nama_alat ?? $item->nama_alat }}</td>
                <td align="center">{{ ($kondisi == 'RR' || $kondisi == 'RUSAK RINGAN') ? 'V' : '' }}</td>
                <td align="center">{{ ($kondisi == 'RB' || $kondisi == 'RUSAK BERAT') ? 'V' : '' }}</td>
                <td align="center">{{ ($kondisi == 'BAIK') ? 'V' : '' }}</td>
                <td>{{ $kondisi == 'BAIK' ? 'Baik' : ($item->catatan_khusus ?? $kondisi) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>III. Catatan Khusus</strong></p>
    <div style="border: 1px solid black; padding: 10px; min-height: 40px; margin-bottom: 20px;">
        {{ $catatan_umum ?? 'Seluruh peralatan dalam kondisi operasional normal.' }}
    </div>

    <p><strong>IV. Dokumentasi Peralatan</strong></p>
    <div class="dokumentasi-grid">
        @php $count = 0; @endphp
        @foreach($data->unique('alat_id') as $item)
            @if($item->dokumen)
                @php
                    // CEK PATH: Jika upload via controller perbaikan, filenya ada di storage/perbaikan
                    // Kita coba dua kemungkinan path yang umum
                    $pathDoc = public_path('storage/' . $item->dokumen);
                    if(!file_exists($pathDoc)) {
                        $pathDoc = public_path('assets/img/pengecekan/' . $item->dokumen);
                    }
                @endphp
                <div class="dokumentasi-item">
                    @if(file_exists($pathDoc))
                        <img src="{{ $pathDoc }}" class="foto-alat">
                    @else
                        <div style="height:140px; background:#eee; padding-top:50px;">Foto tidak ditemukan</div>
                    @endif
                    <div class="caption">{{ $item->alat->nama_alat ?? 'Alat' }}</div>
                </div>
                @php $count++; @endphp
            @endif
        @endforeach
    </div>

    <table class="footer-table">
        <tr>
            <td>
                Mengetahui,<br>Ka. Stasiun<br>
                <div class="spacer"></div>
                <strong>( __________________________ )</strong><br>NIP. .................................
            </td>
            <td>
                Semarang, {{ now()->format('d F Y') }}<br>Petugas Pemeliharaan<br>
                <div class="spacer">
                    @php
                        // Nama file harus sama persis dengan Nama User di Database (misal: Hajirin Arafat.png)
                        $namaUser = auth()->user()->name;
                        $pathTTD = public_path('assets/dist/img/TTD/' . $namaUser . '.png');
                    @endphp

                    @if(file_exists($pathTTD))
                        <img src="{{ $pathTTD }}" style="height: 70px; width: auto;">
                    @else
                        <div style="height:70px;"></div> {{-- Spacer jika TTD tidak ada --}}
                    @endif
                </div>
                <strong>( {{ auth()->user()->name }} )</strong><br>
                NIP. {{ auth()->user()->nip ?? '.................................' }}
            </td>
        </tr>
    </table>
</body>
</html>