<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LegalMention;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class LegalMentionController extends Controller
{
    /**
     * Display a listing of legal mentions.
     */
    public function index(): JsonResponse
    {
        try {
            $legalMentions = LegalMention::orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'success' => true,
                'data' => $legalMentions,
                'message' => 'Legal mentions retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve legal mentions.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created legal mention.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $legalMention = LegalMention::create($request->all());

            return response()->json([
                'success' => true,
                'data' => $legalMention,
                'message' => 'Legal mention created successfully.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create legal mention.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified legal mention.
     */
    public function show($id): JsonResponse
    {
        try {
            $legalMention = LegalMention::find($id);

            if (!$legalMention) {
                return response()->json([
                    'success' => false,
                    'message' => 'Legal mention not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $legalMention,
                'message' => 'Legal mention retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve legal mention.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified legal mention.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $legalMention = LegalMention::find($id);

            if (!$legalMention) {
                return response()->json([
                    'success' => false,
                    'message' => 'Legal mention not found.'
                ], 404);
            }

            $legalMention->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $legalMention,
                'message' => 'Legal mention updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update legal mention.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified legal mention.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $legalMention = LegalMention::find($id);

            if (!$legalMention) {
                return response()->json([
                    'success' => false,
                    'message' => 'Legal mention not found.'
                ], 404);
            }

            $legalMention->delete();

            return response()->json([
                'success' => true,
                'message' => 'Legal mention deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete legal mention.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}