<?php

namespace Modules\Restaurant\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use App\Http\Controllers\Controller as Controller;
use Modules\Hotel\Transformers\CommonResource;
use Illuminate\Http\Request;
use Modules\Hotel\Traits\ApiResponse;
use Exception;
use Modules\Restaurant\Entities\FoodCategory;

class FoodCategoryController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $search = $request->term ?? '';

        $cats = FoodCategory::latest()->withcount('items')->when($search, function ($q) use ($search) {
            $q->where('category_name', 'like', "%$search%");
        })->paginate($request->perPage);

        return CommonResource::collection($cats);
    }
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $imagePath = '';
        if (request()->hasFile('image') && request('image') != '') {
            $file = $request->file('image');
            $directory = 'public/modules/roomcatimg'; // Specify the directory to store the images
            $imagePath = $this->uploadImage($file, $directory);
        }
        if (!empty($request->cat_id)) {
            $this->validate($request, [
                'category_name' =>  'required|string|max:100|unique:foodcategorys,category_name,' . $request->cat_id,
                 'image' => 'max:2048'
            ]);
            try {
                $hotelcategory = FoodCategory::where('id', $request->cat_id)->first();
                $hotelcategory->update([
                    'category_name' => $request->category_name,
                    'image' => $imagePath,
                ]);
                return $this->responseWithSuccess('Food Category Edit successfully!');
            } catch (Exception $e) {
                return $this->responseWithError($e->getMessage());
            }
        } else {
            $this->validate($request, [
                'category_name' =>  'required|string|max:100|unique:foodcategorys,category_name',
                 'image' => 'max:2048'
            ]);
            try {
                FoodCategory::create([
                    'category_name' => $request->category_name,
                    'image' => $imagePath,
                ]);
                return $this->responseWithSuccess('Food Category added successfully!');
            } catch (Exception $e) {
                return $this->responseWithError($e->getMessage());
            }
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        try {
            $hotelcategory = FoodCategory::where('id', $id)->first();
            if (@$hotelcategory) {
                return new CommonResource($hotelcategory);
            } else {
                return $this->responseWithError('Sorry you request can\'t Process!');
            }
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            $hotelcategory = FoodCategory::where('id', $id)->first();
            $hotelcategory->delete();
            return $this->responseWithSuccess('Food Category deleted successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function list()
    {
        return CommonResource::collection(FoodCategory::latest()->get());
    }

    public function statusUpdate(){

    }
}
