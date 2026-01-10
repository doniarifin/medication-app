<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use Illuminate\Http\Request;
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
            'diagnosis' => 'required|string',
        ]);

        // MedicalRecord::create($validated);

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
    public function edit(string $id)
    {
        return Inertia::render('MedicalRecord/Edit', [
            'id' => $id,
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
        return redirect()->back();
    }
}
