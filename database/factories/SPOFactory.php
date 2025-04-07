<?php

namespace Database\Factories;

use App\Models\SPO;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SPO>
 */
class SPOFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SPO::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $documentDate = $this->faker->dateTimeBetween('-2 years', 'now');
        $reviewCycleMonths = $this->faker->randomElement([6, 12, 24, 36]);
        $documentNumber = sprintf(
            '%03d/%s/%s/SPO',
            $this->faker->numberBetween(1, 999),
            $this->getRomanMonth(Carbon::parse($documentDate)->month),
            Carbon::parse($documentDate)->format('Y')
        );

        return [
            'tenant_id' => Tenant::factory(),
            'work_unit_id' => WorkUnit::factory(),
            'document_title' => $this->faker->sentence(4),
            'document_type' => $this->faker->randomElement(['Kebijakan', 'Pedoman', 'SPO', 'Perencanaan', 'Program']),
            'document_number' => $documentNumber,
            'document_date' => $documentDate,
            'document_version' => $this->faker->randomElement(['A', 'B', 'C', '1', '2', '3']),
            'confidentiality_level' => $this->faker->randomElement(['Internal', 'Publik', 'Rahasia']),
            'file_path' => 'documents/spo/' . $this->faker->uuid . '.pdf',
            'next_review' => Carbon::parse($documentDate)->addMonths($reviewCycleMonths),
            'review_cycle_months' => $reviewCycleMonths,
            'status_validasi' => $this->faker->randomElement(['Draft', 'Disetujui', 'Kadaluarsa', 'Revisi']),
            'approved_by' => User::factory(),
            'approved_at' => $this->faker->dateTimeBetween($documentDate, 'now'),
            'definition' => $this->faker->paragraph(),
            'purpose' => $this->faker->paragraph(),
            'policy' => $this->faker->paragraph(3),
            'procedure' => $this->faker->paragraphs(5, true),
            'linked_unit' => json_encode([$this->faker->numberBetween(1, 5), $this->faker->numberBetween(6, 10)]),
            'created_by' => User::factory(),
        ];
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

    /**
     * Menandai SPO sebagai disetujui
     *
     * @return static
     */
    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status_validasi' => 'Disetujui',
                'approved_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
                'approved_by' => User::factory(),
            ];
        });
    }

    /**
     * Menandai SPO sebagai draft
     *
     * @return static
     */
    public function draft()
    {
        return $this->state(function (array $attributes) {
            return [
                'status_validasi' => 'Draft',
                'approved_at' => null,
                'approved_by' => null,
            ];
        });
    }
}
