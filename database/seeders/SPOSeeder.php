<?php

namespace Database\Seeders;

use App\Models\SPO;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WorkUnit;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SPOSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada data tenant, work unit, dan user
        $tenants = Tenant::all();
        if ($tenants->isEmpty()) {
            $this->command->info('Tidak ada tenant! Membuat tenant baru untuk seeder...');
            $tenant = Tenant::factory()->create();
        } else {
            $tenant = $tenants->first();
        }

        // Mencari atau membuat unit kerja IGD
        $igdUnit = WorkUnit::where('unit_name', 'like', '%IGD%')
            ->where('tenant_id', $tenant->id)
            ->first();

        if (!$igdUnit) {
            $igdUnit = WorkUnit::create([
                'tenant_id' => $tenant->id,
                'unit_code' => 'IGD-01',
                'unit_name' => 'Instalasi Gawat Darurat',
                'unit_type' => 'medical',
                'head_of_unit_id' => User::where('tenant_id', $tenant->id)->first()->id ?? User::factory()->create(['tenant_id' => $tenant->id])->id,
                'description' => 'Unit Instalasi Gawat Darurat',
                'is_active' => true
            ]);
        }

        // Mencari atau membuat unit kerja Keperawatan
        $keperawatanUnit = WorkUnit::where('unit_name', 'like', '%Keperawatan%')
            ->where('tenant_id', $tenant->id)
            ->first();

        if (!$keperawatanUnit) {
            $keperawatanUnit = WorkUnit::create([
                'tenant_id' => $tenant->id,
                'unit_code' => 'KEP-01',
                'unit_name' => 'Instalasi Keperawatan',
                'unit_type' => 'medical',
                'head_of_unit_id' => User::where('tenant_id', $tenant->id)->first()->id ?? User::factory()->create(['tenant_id' => $tenant->id])->id,
                'description' => 'Unit Keperawatan Rumah Sakit',
                'is_active' => true
            ]);
        }

        // Mendapatkan user untuk approved_by dan created_by
        $approver = User::where('tenant_id', $tenant->id)->first() ?? User::factory()->create(['tenant_id' => $tenant->id]);
        $creator = User::where('tenant_id', $tenant->id)->skip(1)->first() ?? User::factory()->create(['tenant_id' => $tenant->id]);

        // Buat SPO Pemasangan Infus
        $documentDate1 = Carbon::now()->subMonths(6);
        $docNumber1 = sprintf(
            '%03d/%s/%s/SPO',
            rand(1, 100),
            $this->getRomanMonth($documentDate1->month),
            $documentDate1->format('Y')
        );

        SPO::create([
            'id' => Str::uuid(),
            'tenant_id' => $tenant->id,
            'work_unit_id' => $igdUnit->id,
            'document_title' => 'SPO Pemasangan Infus',
            'document_type' => 'SPO',
            'document_number' => $docNumber1,
            'document_date' => $documentDate1,
            'document_version' => 'A',
            'confidentiality_level' => 'Internal',
            'file_path' => 'documents/spo/' . Str::uuid() . '.pdf',
            'next_review' => $documentDate1->copy()->addMonths(12),
            'review_cycle_months' => 12,
            'status_validasi' => 'Disetujui',
            'approved_by' => $approver->id,
            'approved_at' => $documentDate1->copy()->addDays(3),
            'definition' => 'Pemasangan infus adalah tindakan memasukkan jarum/kateter ke dalam pembuluh darah vena untuk memberikan cairan, obat, nutrisi, dan produk darah.',
            'purpose' => 'Memberikan akses pembuluh darah vena untuk terapi intravena, mempertahankan keseimbangan cairan dan elektrolit, serta memungkinkan pemberian obat dalam keadaan darurat.',
            'policy' => 'Pemasangan infus harus dilakukan oleh perawat yang terlatih sesuai dengan prinsip aseptik dan memperhatikan keamanan pasien.',
            'procedure' => "1. Persiapan Alat:\n   - Cairan infus sesuai program\n   - Set infus\n   - Abocath ukuran sesuai kebutuhan\n   - Tourniquet\n   - Handscoon\n   - Kassa steril\n   - Plester/Hypafix\n   - Pengalas\n   - Alkohol swab\n   - Tiang infus\n\n2. Persiapan Pasien:\n   - Jelaskan prosedur yang akan dilakukan\n   - Atur posisi pasien senyaman mungkin\n   - Pilih vena yang akan dilakukan penusukan\n\n3. Pelaksanaan:\n   - Cuci tangan dan gunakan handscoon\n   - Pasang tourniquet 10-15 cm di atas area penusukan\n   - Desinfeksi area penusukan dengan alkohol swab\n   - Tusuk vena dengan abocath sudut 30° bevel menghadap ke atas\n   - Lepaskan tourniquet setelah darah tampak pada indikator\n   - Hubungkan selang infus yang sudah diisi cairan\n   - Fiksasi dengan plester\n   - Atur tetesan infus sesuai program terapi\n   - Dokumentasikan tindakan",
            'linked_unit' => json_encode([$keperawatanUnit->id]),
            'created_by' => $creator->id,
            'created_at' => $documentDate1->copy()->subDays(5),
            'updated_at' => $documentDate1->copy()->subDays(1),
        ]);

        // Buat SPO Pemberian Informasi Kepada Pasien
        $documentDate2 = Carbon::now()->subMonths(4);
        $docNumber2 = sprintf(
            '%03d/%s/%s/SPO',
            rand(101, 200),
            $this->getRomanMonth($documentDate2->month),
            $documentDate2->format('Y')
        );

        SPO::create([
            'id' => Str::uuid(),
            'tenant_id' => $tenant->id,
            'work_unit_id' => $keperawatanUnit->id,
            'document_title' => 'SPO Pemberian Informasi Kepada Pasien',
            'document_type' => 'SPO',
            'document_number' => $docNumber2,
            'document_date' => $documentDate2,
            'document_version' => 'B',
            'confidentiality_level' => 'Publik',
            'file_path' => 'documents/spo/' . Str::uuid() . '.pdf',
            'next_review' => $documentDate2->copy()->addMonths(12),
            'review_cycle_months' => 12,
            'status_validasi' => 'Disetujui',
            'approved_by' => $approver->id,
            'approved_at' => $documentDate2->copy()->addDays(2),
            'definition' => 'Pemberian informasi kepada pasien adalah proses komunikasi terapeutik antara petugas kesehatan dengan pasien untuk memberikan edukasi, penjelasan kondisi kesehatan, dan rencana perawatan.',
            'purpose' => 'Memastikan pasien mendapatkan informasi yang jelas, akurat, dan lengkap mengenai kondisi kesehatannya, tindakan medis yang akan dilakukan, serta meningkatkan partisipasi pasien dalam proses perawatan.',
            'policy' => 'Setiap pasien berhak mendapatkan informasi yang jelas dan lengkap tentang kondisi kesehatannya sesuai dengan Peraturan Menteri Kesehatan Nomor 4 Tahun 2018 tentang Hak dan Kewajiban Pasien.',
            'procedure' => "1. Persiapan Pemberian Informasi:\n   - Identifikasi pasien dengan benar (minimal 2 identitas)\n   - Siapkan berkas rekam medis dan informasi terkait pasien\n   - Pastikan ruangan dalam kondisi nyaman dan privasi terjaga\n   - Identifikasi pemberi informasi sesuai kewenangannya\n   - Identifikasi penerima informasi (pasien/keluarga yang berhak)\n\n2. Pelaksanaan Pemberian Informasi:\n   - Perkenalkan diri kepada pasien/keluarga\n   - Jelaskan tujuan pemberian informasi\n   - Sampaikan informasi tentang diagnosis dengan bahasa yang mudah dipahami\n   - Jelaskan rencana perawatan, pengobatan dan tindakan yang akan dilakukan\n   - Jelaskan manfaat, risiko, alternatif, dan prognosis\n   - Beri kesempatan pasien/keluarga untuk bertanya\n   - Jawab pertanyaan dengan jelas dan akurat\n   - Dokumentasikan pemberian informasi pada rekam medis\n\n3. Evaluasi Pemahaman Pasien:\n   - Minta pasien mengulangi informasi penting yang telah disampaikan\n   - Klarifikasi kesalahpahaman jika ada\n   - Tanyakan apakah masih ada informasi yang ingin diketahui\n\n4. Pencatatan dan Pelaporan:\n   - Dokumentasikan seluruh informasi yang telah disampaikan\n   - Catat waktu, tempat, pemberi informasi, dan penerima informasi\n   - Minta tanda tangan pasien/keluarga sebagai bukti telah menerima informasi",
            'linked_unit' => json_encode([$igdUnit->id]),
            'created_by' => $creator->id,
            'created_at' => $documentDate2->copy()->subDays(7),
            'updated_at' => $documentDate2->copy()->subDays(1),
        ]);

        $this->command->info('SPO seeder completed successfully.');
    }

    /**
     * Mengubah angka bulan menjadi angka romawi.
     *
     * @param int $month
     * @return string
     */
    private function getRomanMonth(int $month): string
    {
        $romans = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];

        return $romans[$month] ?? 'I';
    }
}
