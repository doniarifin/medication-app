<?php

namespace App\Http\Controllers;

use App\Models\MedicalAttachment;
use App\Models\MedicalRecord;
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

        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'examined_at' => 'required|date',
            'height' => 'required|integer|max:999',
            'weight' => 'required|integer|max:999',
            'systole' => 'required|integer',
            'diastole' => 'required|integer',
            'heart_rate' => 'required|integer',
            'respiration_rate' => 'required|integer',
            'body_temperature' => 'required|numeric',
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
                'systole' => $validated['systole'],
                'diastole' => $validated['diastole'],
                'heart_rate' => $validated['heart_rate'],
                'respiration_rate' => $validated['respiration_rate'],
                'body_temperature' => $validated['body_temperature'],
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'failed save medical record: ' . $e->getMessage());
        }

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

        return Inertia::render('MedicalRecord/Edit', [
            'medicalRecord' => $medicalRecord,
            'vitalSign' => $vitalSign,
            'medAttachment' => $medAttachment,
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

        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'examined_at' => 'required|date',
            'height' => 'required|integer|max:999',
            'weight' => 'required|integer|max:999',
            'systole' => 'required|integer',
            'diastole' => 'required|integer',
            'heart_rate' => 'required|integer',
            'respiration_rate' => 'required|integer',
            'body_temperature' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $record->update([
                'patient_name' => $validated['patient_name'],
                'examined_at'  => $validated['examined_at'],
            ]);

            // vital sign
            VitalSign::where('medical_record_id', $record->id)
                ->update([
                    'height' => $validated['height'],
                    'weight' => $validated['weight'],
                    'systole' => $validated['systole'],
                    'diastole' => $validated['diastole'],
                    'heart_rate' => $validated['heart_rate'],
                    'respiration_rate' => $validated['respiration_rate'],
                    'body_temperature' => $validated['body_temperature'],
                ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json([
            "messages" => "update success",
            "data" => $record
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


    // public function upload(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
    //     ]);

    //     // $path = $request->file('file')->store('uploads', 'public');

    //     $file = $request->file('file');
    //     $path = $file->store('attachments', 'public');

    //     MedicalAttachment::insert([
    //         'id' => $request->id,
    //         'medical_record_id' => $request->medical_record_id,
    //         'path' => $path,
    //         'original_name' => $file->getClientOriginalName(),
    //         'mime_type' => $file->getMimeType(),
    //         'size' => $file->getSize(),
    //         'created_at' => $request->created_at,
    //         'updated_at' => $request->now(),
    //     ]);

    //     return response()->json([
    //         'path' => $path,
    //         'url'  => Storage::disk('public')->path($path),
    //     ]);
    // }
}
