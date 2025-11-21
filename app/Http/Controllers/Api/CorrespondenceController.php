<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Correspondence;
use Illuminate\Http\Request;

class CorrespondenceController extends Controller
{
    /**
     * Get list of letters for select options
     *
     * @return \Illuminate\Http\Response
     */
    public function getLetters(Request $request)
    {
        $search = $request->input('search', '');

        $letters = Correspondence::query()
            ->select(['id', 'document_number', 'subject', 'document_date'])
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('document_number', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($letters);
    }
}
