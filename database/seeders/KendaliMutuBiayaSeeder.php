<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClinicalPathway;
use App\Models\CpStep;
use App\Models\CpTariff;
use App\Models\CpEvaluation;
use App\Models\CpEvaluationStep;
use App\Models\CpEvaluationAdditionalStep;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class KendaliMutuBiayaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "Mulai seeding data Kendali Mutu Biaya...\n";

        // Mendapatkan tenant pertama
        $tenant = Tenant::first();

        if (!$tenant) {
            echo "Tidak ada tenant yang ditemukan. Silahkan buat tenant terlebih dahulu.\n";
            return;
        }

        // Mendapatkan admin dari tenant tersebut
        $admin = User::where('tenant_id', $tenant->id)
            ->whereHas('role', function ($query) {
                $query->where('slug', 'admin');
            })
            ->first();

        if (!$admin) {
            echo "Tidak ada admin tenant yang ditemukan.\n";
            return;
        }

        echo "Menggunakan tenant: " . $tenant->name . "\n";
        echo "Menggunakan admin: " . $admin->name . "\n";

        // Buat Clinical Pathway
        DB::beginTransaction();

        try {
            // Clinical Pathway 1: Demam Typhoid
            $cp1 = ClinicalPathway::create([
                'tenant_id' => $tenant->id,
                'name' => 'Demam Typhoid',
                'category' => 'Penyakit Dalam',
                'description' => 'Clinical Pathway untuk penanganan pasien Demam Typhoid',
                'start_date' => now(),
                'is_active' => true,
                'created_by' => $admin->id,
                'updated_by' => $admin->id
            ]);

            // Langkah-langkah CP1
            $cp1Steps = [
                [
                    'step_name' => 'Anamnesis dan Pemeriksaan Fisik',
                    'step_category' => 'Diagnosis',
                    'step_order' => 1,
                    'day' => 1,
                    'unit' => 'Rawat Inap',
                    'unit_cost' => 100000,
                    'description' => 'Wajib dilakukan pada hari pertama'
                ],
                [
                    'step_name' => 'Pemeriksaan Laboratorium Dasar',
                    'step_category' => 'Pemeriksaan',
                    'step_order' => 2,
                    'day' => 1,
                    'unit' => 'Laboratorium',
                    'unit_cost' => 225000,
                    'description' => 'Darah rutin, kimia darah, dan widal'
                ],
                [
                    'step_name' => 'Pemberian Antibiotik',
                    'step_category' => 'Terapi',
                    'step_order' => 3,
                    'day' => 1,
                    'unit' => 'Farmasi',
                    'unit_cost' => 175000,
                    'description' => 'Ceftriaxone 1x2 gr IV atau sesuai hasil kultur'
                ],
                [
                    'step_name' => 'Monitoring Tanda Vital',
                    'step_category' => 'Perawatan',
                    'step_order' => 4,
                    'day' => 1,
                    'unit' => 'Rawat Inap',
                    'unit_cost' => 50000,
                    'description' => 'Setiap 6 jam'
                ],
                [
                    'step_name' => 'Evaluasi Respons Terapi',
                    'step_category' => 'Perawatan',
                    'step_order' => 1,
                    'day' => 2,
                    'unit' => 'Rawat Inap',
                    'unit_cost' => 75000,
                    'description' => 'Evaluasi respons klinis dan laboratorium'
                ],
                [
                    'step_name' => 'Pemberian Cairan dan Nutrisi',
                    'step_category' => 'Terapi',
                    'step_order' => 2,
                    'day' => 2,
                    'unit' => 'Rawat Inap',
                    'unit_cost' => 125000,
                    'description' => 'Cairan infus dan diet sesuai kondisi pasien'
                ]
            ];

            foreach ($cp1Steps as $stepData) {
                CpStep::create([
                    'clinical_pathway_id' => $cp1->id,
                    'step_name' => $stepData['step_name'],
                    'step_category' => $stepData['step_category'],
                    'step_order' => $stepData['step_order'],
                    'day' => $stepData['day'],
                    'unit' => $stepData['unit'],
                    'description' => $stepData['description'],
                    'unit_cost' => $stepData['unit_cost'],
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id
                ]);
            }

            // Tarif INA-CBG
            CpTariff::create([
                'clinical_pathway_id' => $cp1->id,
                'code_ina_cbg' => 'K-4-15-I',
                'description' => 'Infeksi bakteri ringan-sedang',
                'claim_value' => 3500000,
                'is_active' => true,
                'created_by' => $admin->id,
                'updated_by' => $admin->id
            ]);

            // Clinical Pathway 2: Asma Bronkial
            $cp2 = ClinicalPathway::create([
                'tenant_id' => $tenant->id,
                'name' => 'Asma Bronkial',
                'category' => 'Paru',
                'description' => 'Clinical Pathway untuk penanganan pasien Asma Bronkial',
                'start_date' => now(),
                'is_active' => true,
                'created_by' => $admin->id,
                'updated_by' => $admin->id
            ]);

            // Langkah-langkah CP2
            $cp2Steps = [
                [
                    'step_name' => 'Anamnesis dan Pemeriksaan Fisik',
                    'step_category' => 'Diagnosis',
                    'step_order' => 1,
                    'day' => 1,
                    'unit' => 'Rawat Inap',
                    'unit_cost' => 100000,
                    'description' => 'Anamnesis riwayat asma dan alergi'
                ],
                [
                    'step_name' => 'Pemeriksaan Spirometri',
                    'step_category' => 'Pemeriksaan',
                    'step_order' => 2,
                    'day' => 1,
                    'unit' => 'Paru',
                    'unit_cost' => 250000,
                    'description' => 'Untuk evaluasi fungsi paru'
                ],
                [
                    'step_name' => 'Pemberian Bronkodilator',
                    'step_category' => 'Terapi',
                    'step_order' => 3,
                    'day' => 1,
                    'unit' => 'Farmasi',
                    'unit_cost' => 150000,
                    'description' => 'Nebulisasi atau MDI'
                ],
                [
                    'step_name' => 'Pemberian Kortikosteroid',
                    'step_category' => 'Terapi',
                    'step_order' => 4,
                    'day' => 1,
                    'unit' => 'Farmasi',
                    'unit_cost' => 200000,
                    'description' => 'Sistemik atau inhalasi'
                ],
                [
                    'step_name' => 'Monitoring Saturasi Oksigen',
                    'step_category' => 'Perawatan',
                    'step_order' => 5,
                    'day' => 1,
                    'unit' => 'Rawat Inap',
                    'unit_cost' => 75000,
                    'description' => 'Setiap 4 jam'
                ],
                [
                    'step_name' => 'Edukasi Pasien',
                    'step_category' => 'Konsultasi',
                    'step_order' => 1,
                    'day' => 2,
                    'unit' => 'Rawat Inap',
                    'unit_cost' => 50000,
                    'description' => 'Edukasi penggunaan obat dan pencegahan'
                ]
            ];

            foreach ($cp2Steps as $stepData) {
                CpStep::create([
                    'clinical_pathway_id' => $cp2->id,
                    'step_name' => $stepData['step_name'],
                    'step_category' => $stepData['step_category'],
                    'step_order' => $stepData['step_order'],
                    'day' => $stepData['day'],
                    'unit' => $stepData['unit'],
                    'description' => $stepData['description'],
                    'unit_cost' => $stepData['unit_cost'],
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id
                ]);
            }

            // Tarif INA-CBG
            CpTariff::create([
                'clinical_pathway_id' => $cp2->id,
                'code_ina_cbg' => 'J-4-10-I',
                'description' => 'Asma dan gangguan pernafasan sedang',
                'claim_value' => 4500000,
                'is_active' => true,
                'created_by' => $admin->id,
                'updated_by' => $admin->id
            ]);

            // Buat evaluasi untuk Clinical Pathway 1
            $evaluation = CpEvaluation::create([
                'clinical_pathway_id' => $cp1->id,
                'evaluation_date' => now()->subDays(5),
                'evaluator_user_id' => $admin->id,
                'compliance_percentage' => 83.3, // 5 dari 6 langkah dilakukan
                'total_additional_cost' => 150000,
                'evaluation_status' => 'Kuning',
                'created_by' => $admin->id,
                'updated_by' => $admin->id
            ]);

            // Evaluasi langkah-langkah
            $cpSteps = CpStep::where('clinical_pathway_id', $cp1->id)->get();
            foreach ($cpSteps as $index => $step) {
                // Langkah terakhir tidak dilakukan (untuk simulasi kepatuhan < 100%)
                $isDone = $index < 5;

                CpEvaluationStep::create([
                    'cp_evaluation_id' => $evaluation->id,
                    'cp_step_id' => $step->id,
                    'is_done' => $isDone,
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id
                ]);
            }

            // Langkah tambahan
            CpEvaluationAdditionalStep::create([
                'cp_evaluation_id' => $evaluation->id,
                'additional_step_name' => 'Pemeriksaan USG Abdomen',
                'additional_step_cost' => 150000,
                'justification_status' => 'Justified',
                'created_by' => $admin->id,
                'updated_by' => $admin->id
            ]);

            DB::commit();
            echo "Seeding data Kendali Mutu Biaya berhasil!\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "Seeding gagal: " . $e->getMessage() . "\n";
        }
    }
}
