<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['guest', 'room'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'data' => $reviews
        ]);
    }

    public function destroy(Review $review)
    {
        $review->delete();
        
        return response()->json([
            'message' => 'Review deleted successfully'
        ]);
    }
}