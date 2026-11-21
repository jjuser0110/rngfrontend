<?php

namespace App\Http\Controllers;

use App\Models\TemporaryUpload;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Licence uploads on Register and My Account. Files always go into the visitor's own temporary upload
 * (never straight onto a customer or any other record); the form moves them to the customer when saved.
 */
class AttachmentController extends Controller
{
    /** Licence and IC / passport files per form (Register, My Account). */
    private const MAX_FILES = 5;

    public function store(Request $request)
    {
        $request->validate([
            'attachments'   => 'required|array|max:' . self::MAX_FILES,
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        // Keep adding to the visitor's own upload; a uuid that isn't theirs starts a new one
        $temp = TemporaryUpload::ofSession($request->uuid) ?? TemporaryUpload::startForSession();
        if ($temp->getMedia('attachments')->count() + count($request->file('attachments')) > self::MAX_FILES) {
            return response()->json([
                'message' => 'You can upload up to ' . self::MAX_FILES . ' files.',
                'errors'  => ['attachments' => ['You can upload up to ' . self::MAX_FILES . ' files.']],
            ], 422);
        }
        $files = [];

        foreach ($request->file('attachments') as $file) {
            $media = $temp->addMedia($file)->toMediaCollection('attachments');

            $files[] = [
                'id' => $media->id,
                'url' => $media->getUrl(),
            ];
        }

        return response()->json([
            'uuid' => $temp->uuid,
            'files' => $files,
        ]);
    }

    /** Removes a file the visitor uploaded but hasn't saved yet. Saved files can't be deleted here. */
    public function destroy(Media $media)
    {
        $temp = $media->model_type === TemporaryUpload::class ? TemporaryUpload::find($media->model_id) : null;
        if (!$temp || !TemporaryUpload::ofSession($temp->uuid)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found.',
            ], 404);
        }

        try {
            $media->delete();

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete file.',
            ], 500);
        }
    }
}
