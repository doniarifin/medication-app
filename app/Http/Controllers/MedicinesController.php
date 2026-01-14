<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\MedicinePrices;
use App\Models\Medicines;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MedicinesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // Api gets all data by filter

    public function getData(Request $request)
    {
        //
        $records = Medicines::where([
            // 'medical_record_id' => $request->medical_record_id,
            // 'is_deleted' => $request->is_deleted ? 1 : 0
        ])->get();

        return response()->json($records);
    }

    //api get med price

    public function getPrice(Request $request)
    {
        // $today = Carbon::today()->endOfDay();
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $records = MedicinePrices::query()
            ->when($request->filled('medicine_id'), function ($q) use ($request) {
                $q->whereIn('medicine_id', $request->medicine_id);
            })
            // ->whereDate('start_date', '<=', $startDate)
            ->where(function ($q) use ($startDate) {
                $q->Where('start_date', '<=', $startDate);
            })
            // end_date harus NULL atau >= tanggal
            ->where(function ($q) use ($endDate) {
                $q
                    // ->whereNull('end_date')
                    ->Where('end_date', '>=', $endDate);
            })
            ->orderByDesc('created_at')
            ->get();

        return response()->json($records);
    }
}
