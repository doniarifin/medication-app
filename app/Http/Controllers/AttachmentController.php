<?php

namespace App\Http\Controllers;

use App\Models\MedicalAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class AttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function gets(Request $request)
    {
        $records = MedicalAttachment::where([
            'medical_record_id' => $request->medical_record_id,
            'is_deleted' => $request->is_deleted ? 1 : 0
        ])->first();

        return response()->json($records);
    }

    public function upload(Request $request)
    {
        if ($request->file) {
            $request->validate([
                'file' => 'required|file|max:2048',
            ]);

            $file = $request->file('file');
            $path = $file->store('attachments', 'public');
        }

        if ($request->id) {
            //update
            MedicalAttachment::where('id', $request->id)->update([
                // 'medical_record_id' => $request->medical_record_id,
                'path' =>  $request->file ? $path : $request->path,
                'original_name' => $request->file ? $file->getClientOriginalName() :  $request->original_name,
                'mime_type' => $request->file ? $file->getMimeType() : $request->mime_type,
                'size' => $request->file ? $file->getSize() : $request->size,
                'is_deleted' => $request->is_deleted,
                'updated_at' => now(),
            ]);

            if ($request->file) {
                return response()->json([
                    'path' => $path,
                    'url'  => Storage::disk('public')->path($path),
                ]);
            } else {
                return response()->json([
                    'messages' => 'update success',
                ]);
            };
        } else {
            //create
            MedicalAttachment::create(
                [
                    'medical_record_id' => $request->medical_record_id,
                    'path' => $request->file ? $path : null,
                    'original_name' => $request->file ? $file->getClientOriginalName() : null,
                    'mime_type' => $request->file ? $file->getMimeType() : null,
                    'size' => $request->file ? $file->getSize() : null,
                ],
            );

            if ($request->file) {
                return response()->json([
                    'path' => $path,
                    'url'  => Storage::disk('public')->path($path),
                ]);
            } else {
                return response()->json([
                    'messages' => 'no data uploaded',
                ]);
            };
        }
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
}
