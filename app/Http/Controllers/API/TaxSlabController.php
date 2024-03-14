<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\TaxSlabResource;
use App\Models\TaxSlab;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

class TaxSlabController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:vat-rate-management', ['except' => ['allVatRates']]);
    }

    public function index()
    {
        $slabs = TaxSlab::get();

        return TaxSlabResource::collection($slabs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'range' => 'required',
            'taxes' => 'required | array'
        ]);

        $slab = TaxSlab::create(['range' => $request->range]);
        $slab->taxes()->attach($request->taxes);

        return $this->responseWithSuccess('Tax slab created successfully!');
    }

    public function update(Request $request, TaxSlab $slab)
    {
        $request->validate([
            'range' => 'required',
            'taxes' => 'required | array'
        ]);

        $slab->update(['range' => $request->range]);
        $slab->taxes()->sync($request->taxes);

        return $this->responseWithSuccess('Tax slab updated successfully!');
    }

    public function destroy(Request $request, TaxSlab $slab)
    {
        $slab->delete();

        return $this->responseWithSuccess('Tax slab deleted successfully!');
    }
}
