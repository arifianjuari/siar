<?php

namespace App\Http\Controllers\Modules\KendaliMutuBiaya;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClinicalPathway;
use App\Models\CpStep;
use App\Models\CpTariff;
use App\Models\CpEvaluation;
use App\Models\CpEvaluationStep;
use App\Models\CpEvaluationAdditionalStep;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class KendaliMutuBiayaController extends Controller
{
    /**
     * Display a listing of the Clinical Pathways.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $clinicalPathways = ClinicalPathway::forTenant($user->tenant_id)
            ->with(['creator', 'steps'])
            ->orderBy('name')
            ->paginate(10);

        return view('modules.kendali-mutu-biaya.index', compact('clinicalPathways'));
    }

    /**
     * Show the form for creating a new Clinical Pathway.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('modules.kendali-mutu-biaya.create');
    }

    /**
     * Store a newly created Clinical Pathway in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Debug log all request data
        Log::info('Form submission data:', $request->all());

        // Check if the structured_data is being populated correctly
        Log::info('Structured data value:', ['data' => $request->structured_data]);

        // Relax validation for structured_data temporarily to see if that's the issue
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'structured_data' => 'required', // Removed json validation temporarily
            'code' => 'required|string|max:191|unique:clinical_pathways,code',
            'diagnosis_code' => 'nullable|string|max:191',
            'diagnosis_name' => 'nullable|string|max:191',
            'procedure_code' => 'nullable|string|max:191',
            'procedure_name' => 'nullable|string|max:191',
            'has_steps' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return back()->withErrors($validator)->withInput();
        }

        if ($request->has_steps != '1') {
            Log::error('No steps added to the form');
            return back()->withErrors(['has_steps' => 'Minimal satu langkah harus ditambahkan!'])->withInput();
        }

        DB::beginTransaction();

        try {
            // Generate a unique code if not provided
            $code = $request->code ?? ('CP-' . time());

            // Handle invalid JSON gracefully
            $structuredData = null;
            try {
                if (is_string($request->structured_data)) {
                    $structuredData = json_decode($request->structured_data, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Log::error('JSON decode error: ' . json_last_error_msg());
                        $structuredData = [
                            'error' => 'Invalid JSON format',
                            'original' => $request->structured_data
                        ];
                    }
                } else {
                    $structuredData = $request->structured_data;
                }
            } catch (\Exception $jsonEx) {
                Log::error('Exception decoding JSON: ' . $jsonEx->getMessage());
                $structuredData = [
                    'error' => 'Exception: ' . $jsonEx->getMessage(),
                    'original' => $request->structured_data
                ];
            }

            // Create Clinical Pathway with new schema
            $clinicalPathway = new ClinicalPathway();
            $clinicalPathway->tenant_id = Auth::user()->tenant_id;
            $clinicalPathway->name = $request->name;
            $clinicalPathway->code = $code;
            $clinicalPathway->description = $request->description;
            $clinicalPathway->diagnosis_code = $request->diagnosis_code;
            $clinicalPathway->diagnosis_name = $request->diagnosis_name;
            $clinicalPathway->procedure_code = $request->procedure_code;
            $clinicalPathway->procedure_name = $request->procedure_name;
            $clinicalPathway->structured_data = $structuredData;
            $clinicalPathway->status = 'draft';
            $clinicalPathway->effective_date = $request->effective_date ?? now();
            $clinicalPathway->expiry_date = $request->expiry_date ?? now()->addYear();
            $clinicalPathway->created_by = Auth::id();
            $clinicalPathway->updated_by = Auth::id();
            $clinicalPathway->save();

            Log::info('Clinical pathway created successfully with ID: ' . $clinicalPathway->id);

            DB::commit();

            return redirect()->route('kendali-mutu-biaya.index')
                ->with('success', 'Clinical Pathway berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create clinical pathway: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified Clinical Pathway.
     */
    public function show($id)
    {
        $clinicalPathway = ClinicalPathway::with(['steps' => function ($query) {
            $query->orderByDay();
        }, 'tariffs' => function ($query) {
            $query->active();
        }])->findOrFail($id);

        // Authorization check
        $this->authorize('view', $clinicalPathway);

        return view('modules.kendali-mutu-biaya.show', compact('clinicalPathway'));
    }

    /**
     * Show the form for editing the specified Clinical Pathway.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $clinicalPathway = ClinicalPathway::with(['steps' => function ($query) {
            $query->ordered();
        }])->findOrFail($id);

        // Authorization check
        $this->authorize('update', $clinicalPathway);

        return view('modules.kendali-mutu-biaya.edit', compact('clinicalPathway'));
    }

    /**
     * Update the specified Clinical Pathway in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'steps' => 'required|array|min:1',
            'steps.*.id' => 'nullable|exists:cp_steps,id',
            'steps.*.step_name' => 'required|string|max:255',
            'steps.*.step_category' => 'required|string|max:100',
            'steps.*.step_order' => 'required|integer|min:1',
            'steps.*.unit_cost' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $clinicalPathway = ClinicalPathway::findOrFail($id);

        // Authorization check
        $this->authorize('update', $clinicalPathway);

        DB::beginTransaction();

        try {
            // Update Clinical Pathway
            $clinicalPathway->name = $request->name;
            $clinicalPathway->category = $request->category;
            $clinicalPathway->description = $request->description;
            $clinicalPathway->start_date = $request->start_date;
            $clinicalPathway->is_active = $request->has('is_active');
            $clinicalPathway->updated_by = Auth::id();
            $clinicalPathway->save();

            // Get current step IDs for comparison
            $currentStepIds = $clinicalPathway->steps()->pluck('id')->toArray();
            $updatedStepIds = [];

            // Update or Create Steps
            if ($request->has('steps')) {
                foreach ($request->steps as $stepData) {
                    if (isset($stepData['id'])) {
                        // Update existing step
                        $step = CpStep::findOrFail($stepData['id']);
                        $step->step_name = $stepData['step_name'];
                        $step->step_category = $stepData['step_category'];
                        $step->step_order = $stepData['step_order'];
                        $step->unit_cost = $stepData['unit_cost'];
                        $step->updated_by = Auth::id();
                        $step->save();

                        $updatedStepIds[] = $step->id;
                    } else {
                        // Create new step
                        $step = new CpStep();
                        $step->clinical_pathway_id = $clinicalPathway->id;
                        $step->step_name = $stepData['step_name'];
                        $step->step_category = $stepData['step_category'];
                        $step->step_order = $stepData['step_order'];
                        $step->unit_cost = $stepData['unit_cost'];
                        $step->created_by = Auth::id();
                        $step->updated_by = Auth::id();
                        $step->save();

                        $updatedStepIds[] = $step->id;
                    }
                }
            }

            // Delete steps that are not in the updated list
            $stepsToDelete = array_diff($currentStepIds, $updatedStepIds);
            if (!empty($stepsToDelete)) {
                CpStep::whereIn('id', $stepsToDelete)->delete();
            }

            DB::commit();

            return redirect()->route('kendali-mutu-biaya.index')
                ->with('success', 'Clinical Pathway berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified Clinical Pathway from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $clinicalPathway = ClinicalPathway::findOrFail($id);

        // Authorization check
        $this->authorize('delete', $clinicalPathway);

        DB::beginTransaction();

        try {
            // Check if there are evaluations first
            $hasEvaluations = $clinicalPathway->evaluations()->exists();

            if ($hasEvaluations) {
                return back()->with('error', 'Clinical Pathway tidak dapat dihapus karena sudah memiliki evaluasi.');
            }

            // Delete steps first
            $clinicalPathway->steps()->delete();

            // Delete tariffs
            $clinicalPathway->tariffs()->delete();

            // Delete the clinical pathway
            $clinicalPathway->delete();

            DB::commit();

            return redirect()->route('kendali-mutu-biaya.index')
                ->with('success', 'Clinical Pathway berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show tariff management page for a specific Clinical Pathway.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function manageTariffs($id)
    {
        $clinicalPathway = ClinicalPathway::with('tariffs')->findOrFail($id);

        // Authorization check
        $this->authorize('view', $clinicalPathway);

        return view('modules.kendali-mutu-biaya.tariffs', compact('clinicalPathway'));
    }

    /**
     * Store a new tariff for a Clinical Pathway.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeTariff(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'code_ina_cbg' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'claim_value' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $clinicalPathway = ClinicalPathway::findOrFail($id);

        // Authorization check
        $this->authorize('update', $clinicalPathway);

        DB::beginTransaction();

        try {
            $tariff = new CpTariff();
            $tariff->clinical_pathway_id = $clinicalPathway->id;
            $tariff->code_ina_cbg = $request->code_ina_cbg;
            $tariff->description = $request->description;
            $tariff->claim_value = $request->claim_value;
            $tariff->created_by = Auth::id();
            $tariff->updated_by = Auth::id();
            $tariff->save();

            DB::commit();

            return redirect()->route('kendali-mutu-biaya.tariffs', $clinicalPathway->id)
                ->with('success', 'Tarif klaim berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show evaluation form for a Clinical Pathway.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function evaluateCP($id)
    {
        $clinicalPathway = ClinicalPathway::with(['steps' => function ($query) {
            $query->ordered();
        }])->findOrFail($id);

        // Authorization check
        $this->authorize('view', $clinicalPathway);

        return view('modules.kendali-mutu-biaya.evaluate', compact('clinicalPathway'));
    }

    /**
     * Store a new evaluation for a Clinical Pathway.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeEvaluation(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'evaluation_date' => 'required|date',
            'step_status' => 'required|array',
            'step_status.*' => 'required|boolean',
            'additional_steps' => 'nullable|array',
            'additional_steps.*.additional_step_name' => 'required|string|max:255',
            'additional_steps.*.additional_step_cost' => 'required|numeric|min:0',
            'additional_steps.*.justification_status' => 'required|in:Justified,Tidak Justified',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $clinicalPathway = ClinicalPathway::with('steps')->findOrFail($id);

        // Authorization check
        $this->authorize('create', CpEvaluation::class);

        DB::beginTransaction();

        try {
            // Calculate compliance percentage
            $stepsCount = count($request->step_status);
            $completedSteps = array_sum($request->step_status);
            $compliancePercentage = ($stepsCount > 0) ? ($completedSteps / $stepsCount) * 100 : 0;

            // Calculate total additional cost
            $totalAdditionalCost = 0;
            if ($request->has('additional_steps')) {
                foreach ($request->additional_steps as $additionalStep) {
                    $totalAdditionalCost += $additionalStep['additional_step_cost'];
                }
            }

            // Determine evaluation status based on compliance percentage
            $evaluationStatus = 'Merah';
            if ($compliancePercentage >= 90) {
                $evaluationStatus = 'Hijau';
            } elseif ($compliancePercentage >= 70) {
                $evaluationStatus = 'Kuning';
            }

            // Create evaluation
            $evaluation = new CpEvaluation();
            $evaluation->clinical_pathway_id = $clinicalPathway->id;
            $evaluation->evaluation_date = $request->evaluation_date;
            $evaluation->evaluator_user_id = Auth::id();
            $evaluation->compliance_percentage = $compliancePercentage;
            $evaluation->total_additional_cost = $totalAdditionalCost;
            $evaluation->evaluation_status = $evaluationStatus;
            $evaluation->created_by = Auth::id();
            $evaluation->updated_by = Auth::id();
            $evaluation->save();

            // Create evaluation steps
            foreach ($clinicalPathway->steps as $index => $step) {
                $evaluationStep = new CpEvaluationStep();
                $evaluationStep->cp_evaluation_id = $evaluation->id;
                $evaluationStep->cp_step_id = $step->id;
                $evaluationStep->is_done = $request->step_status[$index] ?? false;
                $evaluationStep->created_by = Auth::id();
                $evaluationStep->updated_by = Auth::id();
                $evaluationStep->save();
            }

            // Create additional steps if any
            if ($request->has('additional_steps')) {
                foreach ($request->additional_steps as $additionalStepData) {
                    $additionalStep = new CpEvaluationAdditionalStep();
                    $additionalStep->cp_evaluation_id = $evaluation->id;
                    $additionalStep->additional_step_name = $additionalStepData['additional_step_name'];
                    $additionalStep->additional_step_cost = $additionalStepData['additional_step_cost'];
                    $additionalStep->justification_status = $additionalStepData['justification_status'];
                    $additionalStep->created_by = Auth::id();
                    $additionalStep->updated_by = Auth::id();
                    $additionalStep->save();
                }
            }

            DB::commit();

            return redirect()->route('kendali-mutu-biaya.show-evaluation', $evaluation->id)
                ->with('success', 'Evaluasi Clinical Pathway berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display evaluation results for a specified evaluation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showEvaluation($id)
    {
        $evaluation = CpEvaluation::with([
            'clinicalPathway',
            'evaluator',
            'evaluationSteps.step',
            'additionalSteps'
        ])->findOrFail($id);

        // Authorization check
        $this->authorize('view', $evaluation);

        return view('modules.kendali-mutu-biaya.show-evaluation', compact('evaluation'));
    }

    /**
     * Display recap of all evaluations.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function rekapEvaluation(Request $request)
    {
        // Filtering options
        $startDate = $request->input('start_date', now()->subMonths(1)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $status = $request->input('status');
        $cpId = $request->input('clinical_pathway_id');

        $user = Auth::user();

        $query = CpEvaluation::with(['clinicalPathway', 'evaluator'])
            ->whereHas('clinicalPathway', function ($query) use ($user) {
                $query->where('tenant_id', $user->tenant_id);
            })
            ->whereBetween('evaluation_date', [$startDate, $endDate]);

        if ($status) {
            $query->where('evaluation_status', $status);
        }

        if ($cpId) {
            $query->where('clinical_pathway_id', $cpId);
        }

        $evaluations = $query->orderBy('evaluation_date', 'desc')->paginate(15);

        // For dropdown filter
        $clinicalPathways = ClinicalPathway::forTenant($user->tenant_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('modules.kendali-mutu-biaya.rekap', compact(
            'evaluations',
            'clinicalPathways',
            'startDate',
            'endDate',
            'status',
            'cpId'
        ));
    }

    /**
     * Generate PDF for a Clinical Pathway.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generatePDF($id)
    {
        $clinicalPathway = ClinicalPathway::with([
            'steps' => function ($query) {
                $query->orderBy('day', 'asc')->orderBy('step_order', 'asc');
            },
            'tariffs',
            'evaluations' => function ($query) {
                $query->latest()->limit(5);
            },
            'evaluations.evaluator',
            'evaluations.evaluationSteps.step',
            'evaluations.additionalSteps',
            'creator',
            'tenant'
        ])->findOrFail($id);

        // Authorization check
        $this->authorize('view', $clinicalPathway);

        // Menghitung total cost langkah-langkah
        $totalStepsCost = $clinicalPathway->steps->sum('unit_cost');

        // Membuat data untuk PDF
        $data = [
            'clinicalPathway' => $clinicalPathway,
            'totalStepsCost' => $totalStepsCost,
            'printDate' => now()->format('d-m-Y H:i:s'),
            'printBy' => Auth::user()->name
        ];

        $pdf = Pdf::loadView('modules.kendali-mutu-biaya.pdf', $data);

        // Menyesuaikan opsi PDF jika perlu
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'dpi' => 150,
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ]);

        $fileName = 'CP_' . str_replace(' ', '_', $clinicalPathway->name) . '_' . date('Ymd') . '.pdf';

        return $pdf->download($fileName);
    }
}
