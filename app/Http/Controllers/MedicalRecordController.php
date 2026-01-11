<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MedicalRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $records = MedicalRecord::all();

        return $records;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('MedicalRecord/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'examined_at' => 'required|date',
            'height' => 'required|int',
            'weight' => 'required|int',
        ]);

        try {
            DB::beginTransaction();

            $medicalRecord = MedicalRecord::create([
                'patient_name' => $validated['patient_name'],
                'examined_at' => $validated['examined_at'],
            ]);

            $medicalRecord->vitalSign()->create([
                'height' => $validated['height'],
                'weight' => $validated['weight'],
            ]);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()
            ->route('rekam-medis')
            ->with('success', 'Rekam medis berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Inertia::render('MedicalRecord/Show', [
            'id' => $id,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $record = MedicalRecord::findOrFail($id);

        return Inertia::render('MedicalRecord/Edit', [
            'record' => $record,
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // update logic
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // delete logic
        MedicalRecord::destroy($id);

        return MedicalRecord::all();
    }
}
