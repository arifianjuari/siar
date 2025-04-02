<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentReference;
use Illuminate\Http\Request;

class DocumentReferenceController extends Controller
{
    /**
     * Get list of document references for select options
     *
     * @return \Illuminate\Http\Response
     */
    public function getReferences(Request $request)
    {
        $query = DocumentReference::query();

        // Filter by search term
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('reference_number', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('title', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Get results
        $references = $query->select('id', 'reference_number', 'title')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($references);
    }
}
