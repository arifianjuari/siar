<?php

namespace Database\Factories;

use App\Models\RiskReport;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RiskReport>
 */
class RiskReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $riskTypes = ['KTD', 'KNC', 'KTC', 'KPC'];
        $riskCategories = ['Medis', 'Non-Medis', 'Lingkungan', 'Keselamatan', 'Keuangan'];
        $riskLevels = ['rendah', 'sedang', 'tinggi'];
        $reporterUnits = ['Unit Gawat Darurat', 'Rawat Inap', 'Poliklinik', 'Farmasi', 'Laboratorium', 'Radiologi'];
        $impactLevels = ['ringan', 'sedang', 'berat'];
        $probabilityLevels = ['jarang', 'kadang', 'sering'];

        return [
            'tenant_id' => Tenant::factory(),
            'risk_title' => fake()->sentence(6),
            'chronology' => fake()->paragraphs(3, true),
            'reporter_unit' => fake()->randomElement($reporterUnits),
            'risk_type' => fake()->randomElement($riskTypes),
            'risk_category' => fake()->randomElement($riskCategories),
            'occurred_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'impact' => fake()->randomElement($impactLevels),
            'probability' => fake()->randomElement($probabilityLevels),
            'risk_level' => fake()->randomElement($riskLevels),
            'status' => 'open',
            'recommendation' => fake()->paragraphs(2, true),
            'created_by' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * State for open reports.
     */
    public function open(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'open',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'approved_by' => null,
                'approved_at' => null,
            ];
        });
    }

    /**
     * State for in-review reports.
     */
    public function inReview(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'in_review',
                'reviewed_by' => User::factory()->manajemenOperasional(),
                'reviewed_at' => now()->subDays(fake()->numberBetween(1, 10)),
                'approved_by' => null,
                'approved_at' => null,
            ];
        });
    }

    /**
     * State for resolved reports.
     */
    public function resolved(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'resolved',
                'reviewed_by' => User::factory()->manajemenOperasional(),
                'reviewed_at' => now()->subDays(fake()->numberBetween(5, 30)),
                'approved_by' => User::factory()->manajemenEksekutif(),
                'approved_at' => now()->subDays(fake()->numberBetween(1, 5)),
            ];
        });
    }
}
