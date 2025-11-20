<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Clinical Pathway: {{ $clinicalPathway->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
        }
        .page-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .page-title {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .sub-title {
            font-size: 14pt;
            margin-bottom: 15px;
        }
        .info-box {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .info-item {
            margin-bottom: 5px;
        }
        .label {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .section-title {
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }
        .footer {
            margin-top: 30px;
            font-size: 9pt;
            color: #777;
            text-align: center;
        }
        .category-badge {
            display: inline-block;
            background-color: #e9f5fd;
            color: #2c5282;
            font-size: 9pt;
            padding: 2px 6px;
            border-radius: 3px;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9pt;
        }
        .status-green {
            background-color: #e3fcec;
            color: #276749;
        }
        .status-yellow {
            background-color: #fefcbf;
            color: #744210;
        }
        .status-red {
            background-color: #fed7d7;
            color: #9b2c2c;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="page-header">
        <div class="page-title">CLINICAL PATHWAY</div>
        <div class="sub-title">{{ $clinicalPathway->name }}</div>
    </div>

    <div class="info-box">
        <div class="info-item"><span class="label">RS/Instansi:</span> {{ $clinicalPathway->tenant->name }}</div>
        <div class="info-item"><span class="label">Kategori:</span> {{ $clinicalPathway->category }}</div>
        <div class="info-item"><span class="label">Tanggal Berlaku:</span> {{ $clinicalPathway->start_date->format('d-m-Y') }}</div>
        <div class="info-item"><span class="label">Status:</span> {{ $clinicalPathway->is_active ? 'Aktif' : 'Tidak Aktif' }}</div>
        <div class="info-item"><span class="label">Dibuat Oleh:</span> {{ $clinicalPathway->creator->name }}</div>
        @if($clinicalPathway->description)
        <div class="info-item"><span class="label">Deskripsi:</span> {{ $clinicalPathway->description }}</div>
        @endif
    </div>

    <div class="section-title">Rincian Langkah-langkah</div>
    
    @php
        $currentDay = null;
        $daySteps = [];
        
        // Mengelompokkan langkah berdasarkan hari
        foreach ($clinicalPathway->steps as $step) {
            $day = $step->day ?? 1; // Default ke hari 1 jika tidak ada
            if (!isset($daySteps[$day])) {
                $daySteps[$day] = [];
            }
            $daySteps[$day][] = $step;
        }
        
        ksort($daySteps); // Urutkan berdasarkan hari
    @endphp
    
    @foreach($daySteps as $day => $steps)
        <h3>Hari ke-{{ $day }}</h3>
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="40%">Langkah</th>
                    <th width="15%">Kategori</th>
                    <th width="15%">Unit Terkait</th>
                    <th width="15%">Biaya (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($steps as $index => $step)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        {{ $step->step_name }}
                        @if(isset($step->description) && $step->description)
                        <div style="font-size: 9pt; color: #666; margin-top: 3px;">{{ $step->description }}</div>
                        @endif
                    </td>
                    <td><span class="category-badge">{{ $step->step_category }}</span></td>
                    <td>{{ $step->unit ?? '-' }}</td>
                    <td style="text-align: right;">{{ number_format($step->unit_cost, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <div style="text-align: right; font-weight: bold; margin: 10px 0 20px 0;">
        Total Biaya Langkah: Rp {{ number_format($totalStepsCost, 0, ',', '.') }}
    </div>

    @if(count($clinicalPathway->tariffs) > 0)
    <div class="section-title">Tarif Klaim</div>
    <table>
        <thead>
            <tr>
                <th width="20%">Kode INA-CBG</th>
                <th width="50%">Deskripsi</th>
                <th width="30%">Nilai Klaim (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clinicalPathway->tariffs as $tariff)
            <tr>
                <td>{{ $tariff->code_ina_cbg }}</td>
                <td>{{ $tariff->description }}</td>
                <td style="text-align: right;">{{ number_format($tariff->claim_value, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(count($clinicalPathway->evaluations) > 0)
    <div class="section-title">Riwayat Evaluasi Terbaru</div>
    <table>
        <thead>
            <tr>
                <th width="15%">Tanggal</th>
                <th width="15%">Evaluator</th>
                <th width="15%">Kepatuhan</th>
                <th width="20%">Status</th>
                <th width="35%">Biaya Tambahan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clinicalPathway->evaluations as $evaluation)
            <tr>
                <td>{{ $evaluation->evaluation_date->format('d-m-Y') }}</td>
                <td>{{ $evaluation->evaluator->name }}</td>
                <td style="text-align: center;">{{ number_format($evaluation->compliance_percentage, 1) }}%</td>
                <td>
                    @php
                        $statusClass = 'status-red';
                        if ($evaluation->evaluation_status == 'Hijau') {
                            $statusClass = 'status-green';
                        } elseif ($evaluation->evaluation_status == 'Kuning') {
                            $statusClass = 'status-yellow';
                        }
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $evaluation->evaluation_status }}</span>
                </td>
                <td style="text-align: right;">Rp {{ number_format($evaluation->total_additional_cost, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        Dicetak oleh: {{ $printBy }} | Tanggal: {{ $printDate }}
    </div>
</body>
</html> 