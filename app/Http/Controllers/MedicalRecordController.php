<?php

namespace App\Http\Controllers;

use App\Models\MedicalAttachment;
use App\Models\MedicalNote;
use App\Models\MedicalRecord;
use App\Models\ResepDokter;
use App\Models\VitalSign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MedicalRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $records = MedicalRecord::query()
            ->when($request->patient_name, function ($q) use ($request) {
                $q->where('patient_name', 'like', '%' . $request->patient_name . '%');
            })
            ->when($request->examined_at, function ($q) use ($request) {
                $q->whereDate('examined_at', $request->examined_at);
            })
            ->orderByDesc('created_at')
            ->paginate()
            ->withQueryString();

        return Inertia::render('MedicalRecord/Index', [
            'records' => $records,
            'filters' => $request->only(['patient_name', 'examined_at']),
        ]);
    }

    public function getData(Request $request)
    {
        $records = MedicalRecord::query()
            ->when($request->filled('patient_name'), function ($q) use ($request) {
                $q->where('patient_name', 'like', '%' . $request->patient_name . '%');
            })
            ->when($request->filled('id'), function ($q) use ($request) {
                $q->where('id', $request->id);
            })
            ->orderByDesc('created_at')
            ->get();

        return response()->json($records);
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

        foreach (['height', 'weight', 'systole', 'diastole', 'heart_rate', 'respiration_rate', 'body_temperature'] as $field) {
            if ($request->filled($field)) {
                $request->merge([
                    $field => str_replace(',', '.', $request->$field),
                ]);
            }
        }
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'examined_at' => 'required|date',
            //vitalsign
            'height' => 'nullable|numeric|max:999',
            'weight' => 'nullable|numeric|max:999',
            'systole' => 'nullable|numeric',
            'diastole' => 'nullable|numeric',
            'heart_rate' => 'nullable|numeric',
            'respiration_rate' => 'nullable|numeric',
            'body_temperature' => 'nullable|numeric',
            //resepdokter
            'resep_dokter' => 'nullable|array',
            //note
            'notes' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $medicalRecord = MedicalRecord::create([
                'patient_name' => $validated['patient_name'],
                'examined_at' => $validated['examined_at'],
                'is_paid' => $request->is_paid ? 1 : 0,
            ]);

            $medicalRecord->vitalSign()->create([
                'height' => $validated['height'],
                'weight' => $validated['weight'],
                'systole' => $validated['systole'],
                'diastole' => $validated['diastole'],
                'heart_rate' => $validated['heart_rate'],
                'respiration_rate' => $validated['respiration_rate'],
                'body_temperature' => $validated['body_temperature'],
            ]);

            $medicalRecord->resepDokter()->create([
                'examined_at' => $validated['examined_at'],
                'resep_dokter' => $validated['resep_dokter'],
            ]);

            $medicalRecord->note()->create([
                'notes' => $validated['notes'],
            ]);


            DB::commit();
        } catch (\Throwable $e) {
            // dd($e);
            DB::rollBack();
            throw $e;
            // return redirect()->back()
            //     ->withInput()
            //     ->withjson('error', 'failed save medical record: ' . $e->getMessage())
            //     ;
        }

        $medicalRecord->load('vitalSign', 'resepDokter', 'note');

        return response()->json([
            "messages" => "update success",
            "data" => $medicalRecord
        ]);
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

        $medicalRecord = MedicalRecord::findOrFail($id);
        $vitalSign = VitalSign::where('medical_record_id', $medicalRecord->id)->first();
        $medAttachment = MedicalAttachment::where('medical_record_id', $medicalRecord->id)->first();
        $resepDokter = ResepDokter::where('medical_record_id', $medicalRecord->id)->first();
        $medicalNotes = MedicalNote::where('medical_record_id', $medicalRecord->id)->first();

        return Inertia::render('MedicalRecord/Edit', [
            'medicalRecord' => $medicalRecord,
            'vitalSign' => $vitalSign,
            'medAttachment' => $medAttachment,
            'resepDokter' => $resepDokter,
            'medicalNotes' => $medicalNotes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $record = MedicalRecord::find($id);

        if (!$record) {
            return response()->json([
                'message' => 'Record not found'
            ], 404);
        }

        foreach (['height', 'weight', 'systole', 'diastole', 'heart_rate', 'respiration_rate', 'body_temperature'] as $field) {
            if ($request->filled($field)) {
                $request->merge([
                    $field => str_replace(',', '.', $request->$field),
                ]);
            }
        }

        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'examined_at' => 'required|date',
            //vitalsign
            'height' => 'nullable|numeric|max:999',
            'weight' => 'nullable|numeric|max:999',
            'systole' => 'nullable|numeric',
            'diastole' => 'nullable|numeric',
            'heart_rate' => 'nullable|numeric',
            'respiration_rate' => 'nullable|numeric',
            'body_temperature' => 'nullable|numeric',
            //resepdokter
            'resep_dokter' => 'nullable|array',
            //note
            'notes' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $record->update([
                'patient_name' => $validated['patient_name'],
                'examined_at'  => $validated['examined_at'],
                'is_paid' => $request->is_paid ? 1 : 0,
                'updated_at'  => now(),
            ]);

            // vital sign
            $vitalSign = VitalSign::where('medical_record_id', $record->id)->first();
            $vitalSign?->update([
                'height' => $validated['height'],
                'weight' => $validated['weight'],
                'systole' => $validated['systole'],
                'diastole' => $validated['diastole'],
                'heart_rate' => $validated['heart_rate'],
                'respiration_rate' => $validated['respiration_rate'],
                'body_temperature' => $validated['body_temperature'],
                'updated_at'  => now(),
            ]);

            //resep dokter
            $resepDokter = ResepDokter::where('medical_record_id', $record->id)->first();
            $resepDokter?->update([
                'examined_at' => $validated['examined_at'],
                'resep_dokter' => $validated['resep_dokter'],
                'updated_at'  => now(),
            ]);

            $medNote = MedicalNote::where('medical_record_id', $record->id)->first();
            $medNote?->update([
                'notes' => $validated['notes'],
                'updated_at'  => now(),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json([
            "messages" => "update success",
            'data' => [
                'medical_record' => $record->fresh(),
                'vital_sign' => $vitalSign,
                'resep_dokter' => $resepDokter,
                'medical_notes' => $medNote,
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // delete logic
        MedicalRecord::destroy($id);

        $records = MedicalRecord::All();

        return response()->json($records);
    }
}
