<!DOCTYPE html>
<html>
<head>
    <title>Detail Laporan Risiko</title>
</head>
<body>
    <h1>Detail Laporan Risiko</h1>
    
    @if(session('success'))
        <div style="padding: 10px; background-color: #d4edda; color: #155724; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div style="padding: 10px; background-color: #f8d7da; color: #721c24; margin-bottom: 15px;">
            {{ session('error') }}
        </div>
    @endif
    
    <div style="margin-bottom: 20px;">
        <a href="{{ route('modules.risk-management.risk-reports.index') }}">Kembali ke Daftar</a>
        
        @if (auth()->user()->role === 'Staf' && $riskReport->status === 'open' && $riskReport->created_by === auth()->id())
            | <a href="{{ route('modules.risk-management.risk-reports.edit', $riskReport->id) }}">Edit</a>
        @elseif(in_array(auth()->user()->role, ['Superadmin', 'Admin RS', 'Manajemen Operasional', 'Manajemen Eksekutif']))
            | <a href="{{ route('modules.risk-management.risk-reports.edit', $riskReport->id) }}">Edit</a>
        @endif
        
        @if(in_array(auth()->user()->role, ['Superadmin', 'Admin RS']))
            <form method="POST" action="{{ route('modules.risk-management.risk-reports.destroy', $riskReport->id) }}" style="display: inline;">
                @csrf
                @method('DELETE')
                | <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')">Hapus</button>
            </form>
        @endif
        
        | <a href="{{ route('modules.risk-management.risk-reports.export-awal', $riskReport->id) }}" target="_blank">Export Laporan Awal</a>
        | <a href="{{ route('modules.risk-management.risk-reports.export-akhir', $riskReport->id) }}" target="_blank">Export Laporan Final</a>
    </div>
    
    <div style="margin-bottom: 20px;">
        <h2>{{ $riskReport->risk_title }}</h2>
        
        <div style="margin-bottom: 10px;">
            <strong>Status:</strong> 
            @if($riskReport->status === 'open')
                <span style="color: red;">Open</span>
            @elseif($riskReport->status === 'in_review')
                <span style="color: orange;">In Review</span>
            @else
                <span style="color: green;">Resolved</span>
            @endif
        </div>
        
        <div style="margin-bottom: 10px;">
            <strong>Unit Pelapor:</strong> {{ $riskReport->reporter_unit }}
        </div>
        
        <div style="margin-bottom: 10px;">
            <strong>Tipe Risiko:</strong> {{ $riskReport->risk_type ?: 'Tidak ditentukan' }}
        </div>
        
        <div style="margin-bottom: 10px;">
            <strong>Kategori Risiko:</strong> {{ $riskReport->risk_category }}
        </div>
        
        <div style="margin-bottom: 10px;">
            <strong>Tanggal Kejadian:</strong> {{ $riskReport->occurred_at->format('d/m/Y') }}
        </div>
        
        <div style="margin-bottom: 10px;">
            <strong>Tingkat Risiko:</strong> {{ $riskReport->risk_level }}
        </div>
        
        <div style="margin-bottom: 10px;">
            <strong>Dampak:</strong> {{ $riskReport->impact }}
        </div>
        
        <div style="margin-bottom: 10px;">
            <strong>Probabilitas:</strong> {{ $riskReport->probability }}
        </div>
    </div>
    
    <div style="margin-bottom: 20px;">
        <h3>Kronologi</h3>
        <p style="white-space: pre-wrap;">{{ $riskReport->chronology }}</p>
    </div>
    
    @if($riskReport->recommendation)
        <div style="margin-bottom: 20px;">
            <h3>Rekomendasi</h3>
            <p style="white-space: pre-wrap;">{{ $riskReport->recommendation }}</p>
        </div>
    @endif
    
    <div style="margin-bottom: 20px;">
        <h3>Informasi Pelapor</h3>
        <div style="margin-bottom: 5px;">
            <strong>Dilaporkan oleh:</strong> {{ $riskReport->creator->name ?? 'Unknown' }}
        </div>
        <div style="margin-bottom: 5px;">
            <strong>Tanggal Laporan:</strong> {{ $riskReport->created_at->format('d/m/Y H:i') }}
        </div>
    </div>
    
    @if($riskReport->reviewed_by)
        <div style="margin-bottom: 20px;">
            <h3>Informasi Review</h3>
            <div style="margin-bottom: 5px;">
                <strong>Ditindaklanjuti oleh:</strong> {{ $riskReport->reviewer->name ?? 'Unknown' }}
            </div>
            <div style="margin-bottom: 5px;">
                <strong>Tanggal Tindak Lanjut:</strong> {{ $riskReport->reviewed_at->format('d/m/Y H:i') }}
            </div>
        </div>
    @endif
    
    @if($riskReport->approved_by)
        <div style="margin-bottom: 20px;">
            <h3>Informasi Persetujuan</h3>
            <div style="margin-bottom: 5px;">
                <strong>Disetujui oleh:</strong> {{ $riskReport->approver->name ?? 'Unknown' }}
            </div>
            <div style="margin-bottom: 5px;">
                <strong>Tanggal Persetujuan:</strong> {{ $riskReport->approved_at->format('d/m/Y H:i') }}
            </div>
            
            <!-- Tanda Tangan Digital (QR Code) -->
            <div style="margin-top: 20px;">
                <h4>Tanda Tangan Digital</h4>
                <img src="{{ route('modules.risk-management.risk-reports.qr-code', $riskReport->id) }}" alt="QR Code Tanda Tangan">
                <div style="font-size: 0.8em; margin-top: 5px;">
                    <p>Scan QR code untuk verifikasi tanda tangan digital.</p>
                </div>
            </div>
        </div>
    @else
        <div style="margin-bottom: 20px;">
            <h3>Tanda Tangan Digital</h3>
            <div style="padding: 10px; background-color: #f8d7da; color: #721c24; display: inline-block;">
                Laporan belum disetujui. QR code tanda tangan akan tersedia setelah laporan disetujui.
            </div>
        </div>
    @endif
    
    @if(auth()->user()->role === 'Manajemen Operasional' && $riskReport->status === 'open')
        <div style="margin-top: 20px;">
            <form method="POST" action="{{ route('modules.risk-management.risk-reports.mark-in-review', $riskReport->id) }}">
                @csrf
                @method('PUT')
                <button type="submit">Tindak Lanjut Laporan</button>
            </form>
        </div>
    @endif
    
    @if(auth()->user()->role === 'Manajemen Eksekutif' && $riskReport->status === 'in_review')
        <div style="margin-top: 20px;">
            <form method="POST" action="{{ route('modules.risk-management.risk-reports.approve', $riskReport->id) }}">
                @csrf
                @method('PUT')
                <button type="submit">Setujui Laporan</button>
            </form>
        </div>
    @endif
</body>
</html> 