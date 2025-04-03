<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;
use App\Models\Tenant;
use App\Models\User;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil tenant RS Bhayangkara
        $tenant = Tenant::where('name', 'RS Bhayangkara Tk.III Batu')->first();

        // Ambil salah satu user dengan role "Staf"
        $user = User::where('tenant_id', $tenant->id)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Staf');
            })
            ->first();

        if (!$user) {
            $this->command->warn('User dengan role "Staf" di RS Bhayangkara belum ada.');
            return;
        }

        $documents = [
            [
                'title' => 'Pedoman Mutu Rumah Sakit',
                'number' => 'DOC/PM/001',
                'type' => 'Pedoman',
                'status' => 'Aktif',
            ],
            [
                'title' => 'SOP Kegiatan Visite Pasien',
                'number' => 'DOC/SOP/014',
                'type' => 'SOP',
                'status' => 'Aktif',
            ],
            [
                'title' => 'Instruksi Kerja Penggunaan Alat USG',
                'number' => 'DOC/IK/007',
                'type' => 'Instruksi Kerja',
                'status' => 'Aktif',
            ],
        ];

        foreach ($documents as $doc) {
            Document::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'number' => $doc['number']
                ],
                [
                    'title' => $doc['title'],
                    'type' => $doc['type'],
                    'status' => $doc['status'],
                    'created_by' => $user->id,
                    'tenant_id' => $tenant->id
                ]
            );
        }
    }
}
