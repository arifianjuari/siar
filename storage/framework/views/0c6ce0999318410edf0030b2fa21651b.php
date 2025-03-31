<?php $__env->startSection('title', 'Dashboard Korespondensi'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <p class="lead">Manajemen surat dan nota dinas internal rumah sakit.</p>
        </div>
    </div>

    <!-- Grafik -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tren Surat/Nota Dinas (12 Bulan Terakhir)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="correspondenceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Aksi -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <a href="<?php echo e(route('modules.correspondence.letters.index')); ?>" class="btn btn-primary btn-block">
                <i class="fas fa-list mr-1"></i> Lihat Semua Surat
            </a>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <a href="<?php echo e(route('modules.correspondence.letters.create')); ?>" class="btn btn-success btn-block">
                <i class="fas fa-plus mr-1"></i> Buat Surat Baru
            </a>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <a href="<?php echo e(route('modules.correspondence.reports.index')); ?>" class="btn btn-info btn-block">
                <i class="fas fa-file-export mr-1"></i> Generate Laporan
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('correspondenceChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chartData['labels']); ?>,
                datasets: [{
                    label: '<?php echo e($chartData['datasets'][0]['label']); ?>',
                    data: <?php echo json_encode($chartData['datasets'][0]['data']); ?>,
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    tension: 0.4
                }]
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
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/Correspondence/dashboard.blade.php ENDPATH**/ ?>