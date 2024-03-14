<?php

namespace Modules\Hotel\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\GeneralSetting;

trait ApiResponse
{
    /*** fpr success message with data ****/
    protected function responseWithSuccess($message = '', $data = [], $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**** show error  /*****/
    protected function responseWithError($message = '', $data = [], $code = 400)
    {
        return response()->json([
            'error' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**** uplode image ** */
    public  function uploadImage(UploadedFile $file, string $directory)
    {
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs($directory, $fileName);
        return $directory . '/' . $fileName;
    }

    /*** delete image ****/
    public function deleteImage($imageName)
    {
        if ($imageName && Storage::exists($imageName)) {
            Storage::delete($imageName);
            return true;
        }
        return false;
    }
    /*** fucntion for get invoice prefix */
    public function invoicePrefix()
    {
        $genrateLeads = GeneralSetting::where('key', 'invoice_prefix')->first()->value;
        return !empty($genrateLeads) ? $genrateLeads : "TH";
    }

    /*** function for get booking prefix */
    public function bookingPrefix()
    {
        $bookingPrefix = GeneralSetting::where('key', 'booking_prefix')->first()->value;
        return !empty($bookingPrefix) ? $bookingPrefix : "TH";
    }
}
