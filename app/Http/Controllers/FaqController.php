<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
    public function index()
    {
        try {
            $faqs = Faq::orderBy('order', 'asc')->orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'success' => true,
                'data' => $faqs,
                'message' => 'FAQs retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve FAQs: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $faq = Faq::create([
                'question' => $request->question,
                'answer' => $request->answer,
                'order' => $request->order ?? 0,
            ]);

            return response()->json([
                'success' => true,
                'data' => $faq,
                'message' => 'FAQ created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create FAQ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $faq = Faq::find($id);
            
            if (!$faq) {
                return response()->json([
                    'success' => false,
                    'message' => 'FAQ not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $faq,
                'message' => 'FAQ retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve FAQ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $faq = Faq::find($id);
        
        if (!$faq) {
            return response()->json([
                'success' => false,
                'message' => 'FAQ not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'question' => 'sometimes|required|string|max:500',
            'answer' => 'sometimes|required|string',
            'order' => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $faq->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $faq,
                'message' => 'FAQ updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update FAQ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $faq = Faq::find($id);
            
            if (!$faq) {
                return response()->json([
                    'success' => false,
                    'message' => 'FAQ not found'
                ], 404);
            }

            $faq->delete();

            return response()->json([
                'success' => true,
                'message' => 'FAQ deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete FAQ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'faqs' => 'required|array',
            'faqs.*.id' => 'required|exists:faqs,id',
            'faqs.*.order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            foreach ($request->faqs as $faqData) {
                Faq::where('id', $faqData['id'])->update(['order' => $faqData['order']]);
            }

            $faqs = Faq::orderBy('order', 'asc')->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $faqs,
                'message' => 'FAQs reordered successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder FAQs: ' . $e->getMessage()
            ], 500);
        }
    }
}