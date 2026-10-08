<?php

namespace App\Http\Controllers;

use App\Models\TemporaryUpload;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AttachmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'files.*'      => 'required|file|max:5120',
            'collection'   => 'required|string',
        ]);

        $uuid = $request->uuid ?? Str::uuid()->toString();
        $files = [];

        foreach ($request->file('attachments') as $file) {

            if ($request->filled(['model_type', 'model_id'])) {
                $modelClass = $request->model_type;
                abort_unless(class_exists($modelClass), 404);
                $model = $modelClass::findOrFail($request->model_id);

                $media = $model
                    ->addMedia($file)
                    ->toMediaCollection($request->collection);
            } else {
                $temp = TemporaryUpload::firstOrCreate([
                    'uuid' => $uuid
                ]);

                $media = $temp
                    ->addMedia($file)
                    ->toMediaCollection($request->collection);
            }

            $files[] = [
                'id' => $media->id,
                'url' => $media->getUrl(),
            ];
        }

        return response()->json([
            'uuid' => $uuid,
            'files' => $files,
        ]);
    }

    public function destroy(Media $media)
    {
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
