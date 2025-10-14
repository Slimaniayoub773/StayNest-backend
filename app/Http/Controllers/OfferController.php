<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OfferController extends Controller
{
    public function index()
    {
        $offers = Offer::orderBy('start_date', 'desc')->get();
        return response()->json(['data' => $offers], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_percentage' => 'required|numeric|between:0,100',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'promo_code' => 'nullable|string|max:50|unique:offers,promo_code',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $offer = Offer::create($request->all());

        return response()->json(['data' => $offer, 'message' => 'Offer created successfully'], 201);
    }

    public function update(Request $request, Offer $offer)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_percentage' => 'required|numeric|between:0,100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'promo_code' => 'nullable|string|max:50|unique:offers,promo_code,'.$offer->id,
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $offer->update($request->all());

        return response()->json(['data' => $offer, 'message' => 'Offer updated successfully'], 200);
    }

    public function destroy(Offer $offer)
    {
        $offer->delete();
        return response()->json(['message' => 'Offer deleted successfully'], 200);
    }

    public function toggleStatus(Offer $offer)
    {
        $offer->update(['is_active' => !$offer->is_active]);
        return response()->json(['message' => 'Offer status updated', 'is_active' => $offer->is_active], 200);
    }
}