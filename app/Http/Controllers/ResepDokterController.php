<?php

namespace App\Http\Controllers;

use App\Models\MedicalAttachment;
use App\Models\MedicalRecord;
use App\Models\Pembayaran;
use App\Models\ResepDokter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ResepDokterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function getData(Request $request)
    {
        $records = MedicalRecord::query()
            ->when($request->filled('id'), fn($q) => $q->where('id', $request->id))
            ->when(
                $request->filled('patient_name'),
                fn($q) => $q->where('patient_name', 'like', '%' . $request->patient_name . '%')
            )
            ->when(
                $request->filled('start_date') && $request->filled('end_date'),
                fn($q) => $q->whereBetween('examined_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59',
                ])
            )
            ->with(['resepDokter' => function ($q) {
                $q->orderBy('id')->limit(1);
            }])
            ->orderByDesc('created_at')
            ->get();

        $data = $records->map(function ($record) {
            $record->resep_dokter = $record->resepDokter->first();
            $record->notes = $record->note->first();
            $record->file = $record->attachments->first();
            unset($record->resepDokter);
            unset($record->note);
            unset($record->attachments);
            return $record;
        });

        return response()->json($data);
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
    public function update(Request $request)
    {
        $record = MedicalRecord::find($request->id);

        if (!$record) {
            return response()->json([
                'message' => 'Record not found'
            ], 404);
        }

        $validated = $request->validate([
            'is_paid' => 'required|bool',
            'total_price' => 'numeric',
        ]);

        try {
            DB::beginTransaction();

            $record->update([
                'is_paid' => (bool) $validated['is_paid'],
                'updated_at'  => now(),
            ]);

            // pembayaran
            Pembayaran::updateOrCreate(
                ['medical_record_id' => $record->id],
                [
                    'total_price' => $validated['total_price'],
                    'updated_at' => now(),
                ]
            );


            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json([
            "messages" => "update success",
            'data' => [
                'medical_record' => $record->fresh(),
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    //export pdf



    public function exportPdf($id)
    {
        $record = MedicalRecord::with('resepDokter')->findOrFail($id);

        $record->resep_dokter = $record->resepDokter->first();
        unset($record->resepDokter);

        $record->pembayaran = $record->pembayaran->first();

        $data = $record;

        // dd($data);

        $pdf = Pdf::loadView('pdf.resep', [
            'data' => $data
        ])->setPaper('A4', 'portrait');

        return $pdf->download('resi-pembayaran.pdf');
    }

    //download file
    public function downloadFile($id)
    {
        $file = MedicalAttachment::findOrFail($id);

        return response()->streamDownload(function () use ($file) {
            echo Storage::disk('public')->get($file->path);
        }, $file->original_name);
    }
}
