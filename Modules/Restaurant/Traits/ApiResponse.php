<?php

namespace Modules\Shops\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

trait ApiResponse
{
    protected function responseWithSuccess($message = '', $data = [], $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function responseWithError($message = '', $data = [], $code = 400)
    {
        return response()->json([
            'error' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public  function uploadImage(UploadedFile $file, string $directory)
    {
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs($directory, $fileName);
        return $directory . '/' . $fileName;
    }

    public function deleteImage($imageName)
    {
        if ($imageName && Storage::exists($imageName)) {
            Storage::delete($imageName);
            return true;
        }
        return false;
    }
}
