@extends('layouts.app')

@section('title', 'Dashboard Korespondensi')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Korespondensi</h1>
        {{-- Bisa ditambahkan tombol laporan global jika perlu --}}
        {{-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generate Report</a> --}}
    </div>

    <!-- Content Row - KPI Cards -->
    <div class="row">

        <!-- Total Surat Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Surat & Nota Dinas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Surat Bulan Ini Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Surat Dibuat Bulan Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['thisMonth'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Surat Draft Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Dokumen Draft
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $stats['draft'] ?? 0 }}</div>
                                </div>
                                {{-- Optional: Progress bar jika ada target --}}
                                {{-- <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar"
                                            style="width: 50%" aria-valuenow="50" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pencil-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Tasks Card Example (Placeholder, bisa diganti) -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Perlu Ditinjau (Contoh)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_review'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Charts -->
    <div class="row">

        <!-- Area Chart - Tren Bulanan -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <!-- Card Header -->
                <div
                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tren Surat/Nota Dinas (12 Bulan Terakhir)</h6>
                    {{-- Optional: Dropdown untuk filter chart --}}
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area" style="height: 320px;"> {{-- Tinggi chart diatur di sini --}}
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart - Distribusi Status -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <!-- Card Header -->
                <div
                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Distribusi Status</h6>
                     {{-- Optional: Dropdown --}}
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2" style="height: 320px;"> {{-- Tinggi chart diatur di sini --}}
                        <canvas id="statusDistributionChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small" id="status-legend-container">
                        {{-- Legend akan digenerate oleh Chart.js atau manual di sini --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Quick Actions & Recent Activity -->
    <div class="row">

        <!-- Quick Actions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('modules.correspondence.letters.index') }}" class="btn btn-primary btn-icon-split mb-2">
                        <span class="icon text-white-50">
                            <i class="fas fa-list"></i>
                        </span>
                        <span class="text">Lihat Semua Surat</span>
                    </a>
                     @can('create', App\Models\Correspondence::class)
                    <a href="{{ route('modules.correspondence.letters.create') }}" class="btn btn-success btn-icon-split mb-2">
                        <span class="icon text-white-50">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">Buat Surat Baru</span>
                    </a>
                    @endcan
                    {{-- Tambahkan tombol lain jika perlu, misal untuk laporan --}}
                    {{-- <a href="#" class="btn btn-info btn-icon-split mb-2">
                        <span class="icon text-white-50">
                            <i class="fas fa-file-alt"></i>
                        </span>
                        <span class="text">Generate Laporan</span>
                    </a> --}}
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aktivitas Terbaru</h6>
                </div>
                <div class="card-body">
                    @if(isset($recentLetters) && $recentLetters->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <tbody>
                                @foreach($recentLetters as $letter)
                                    <tr>
                                        <td>
                                            <a href="{{ route('modules.correspondence.letters.show', $letter->id) }}">
                                                {{ Str::limit($letter->document_title, 40) }}
                                            </a>
                                            <br>
                                            <small class="text-muted">No: {{ $letter->document_number ?? '-' }} | {{ $letter->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td class="text-right">
                                            <span class="badge bg-{{ $letter->status == 'Draft' ? 'secondary' : 'success' }}">{{ $letter->status ?? 'Terkirim' }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-2">
                             <a href="{{ route('modules.correspondence.letters.index') }}">Lihat Semua &rarr;</a>
                        </div>
                    @else
                        <p class="text-center text-muted">Tidak ada aktivitas terbaru.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data dari Controller (contoh, sesuaikan dengan variabel yang Anda pass)
        const monthlyTrendData = {!! json_encode($chartDataMonthly ?? ['labels' => [], 'data' => []]) !!};
        const statusDistributionData = {!! json_encode($chartDataStatus ?? ['labels' => [], 'data' => [], 'colors' => []]) !!};

        // Helper function to format number for tooltips
        function number_format(number, decimals, dec_point, thousands_sep) {
            number = (number + '').replace(',', '').replace(' ', '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;)
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }

        // 1. Monthly Trend Chart (Line)
        const ctxLine = document.getElementById('monthlyTrendChart').getContext('2d');
        if (monthlyTrendData.labels.length > 0) { // Hanya render jika ada data
            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: monthlyTrendData.labels,
                    datasets: [{
                        label: "Jumlah Surat",
                        lineTension: 0.3,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: monthlyTrendData.data,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 25,
                            top: 25,
                            bottom: 0
                        }
                    },
                    scales: {
                        xAxes: [{
                            time: {
                                unit: 'date'
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                maxTicksLimit: 7
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                maxTicksLimit: 5,
                                padding: 10,
                                // Include a dollar sign in the ticks
                                callback: function(value, index, values) {
                                return number_format(value);
                                }
                            },
                            gridLines: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: false,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        }],
                    },
                    legend: {
                        display: false
                    },
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        titleMarginBottom: 10,
                        titleFontColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(tooltipItem, chart) {
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': ' + number_format(tooltipItem.yLabel);
                            }
                        }
                    }
                }
            });
        } else {
             ctxLine.font = "16px Arial";
             ctxLine.textAlign = "center";
             ctxLine.fillText("Data tren tidak tersedia.", ctxLine.canvas.width/2, ctxLine.canvas.height/2);
        }

        // 2. Status Distribution Chart (Doughnut)
        const ctxPie = document.getElementById('statusDistributionChart').getContext('2d');
        const legendContainer = document.getElementById('status-legend-container');
        legendContainer.innerHTML = ''; // Kosongkan legend bawaan

        if (statusDistributionData.labels.length > 0 && statusDistributionData.data.reduce((a, b) => a + b, 0) > 0) { // Hanya render jika ada data > 0
            new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: statusDistributionData.labels,
                    datasets: [{
                        data: statusDistributionData.data,
                        backgroundColor: statusDistributionData.colors, // Gunakan warna dari controller
                        hoverBackgroundColor: statusDistributionData.colors, // Bisa dibuat lebih gelap saat hover
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: {
                        display: false // Matikan legend default Chart.js
                    },
                    cutoutPercentage: 80,
                     plugins: { // Generate legend manual jika perlu
                        htmlLegend: {
                            containerID: 'status-legend-container'
                        }
                    }
                },
                // Plugin custom untuk generate legend HTML (jika diperlukan)
                plugins: [{
                    id: 'htmlLegend',
                    afterUpdate(chart, args, options) {
                        const ul = document.createElement('span');
                        chart.data.labels.forEach((label, i) => {
                            ul.innerHTML += `
                                <span class="mr-2">
                                <i class="fas fa-circle" style="color:${chart.data.datasets[0].backgroundColor[i]}"></i> ${label}
                                </span>
                            `;
                        });
                        const container = document.getElementById(options.containerID);
                        // Hapus legend lama sebelum menambah yg baru
                        while (container.firstChild) {
                            container.firstChild.remove();
                        }
                        container.appendChild(ul);
                    }
                }]
            });
        } else {
            ctxPie.font = "16px Arial";
            ctxPie.textAlign = "center";
            ctxPie.fillText("Data status tidak tersedia.", ctxPie.canvas.width/2, ctxPie.canvas.height/2);
            legendContainer.innerHTML = '<p class="text-muted">Tidak ada data untuk ditampilkan.</p>';
        }
    });
</script>
@endpush 