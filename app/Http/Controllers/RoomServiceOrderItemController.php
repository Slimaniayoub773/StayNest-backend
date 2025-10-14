<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RoomServiceOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomServiceOrderItemController extends Controller
{
    public function index(Request $request)
    {
        $query = RoomServiceOrderItem::with(['order', 'item'])
            ->orderBy('created_at', 'desc');

        if ($request->has('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        $items = $query->paginate($request->per_page ?? 15);

        return response()->json([
    'success' => true,
    'data' => $items  // This contains the paginated data
]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:room_service_orders,id',
            'item_id' => 'required|exists:room_service_items,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $item = RoomServiceOrderItem::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $item
        ], 201);
    }

    public function show($id)
    {
        $item = RoomServiceOrderItem::with(['order', 'item'])->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Order item not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = RoomServiceOrderItem::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Order item not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $item->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    public function destroy($id)
    {
        $item = RoomServiceOrderItem::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Order item not found'
            ], 404);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order item deleted successfully'
        ]);
    }
}