<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
{
    public function index()
    {
        try {
            $blogs = Blog::orderBy('created_at', 'desc')->get();
            return response()->json([
                'success' => true,
                'data' => $blogs,
                'message' => 'Blogs retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve blogs: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'category' => 'nullable|string|max:100',
            'author' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $blogData = [
                'title' => $request->title,
                'content' => $request->content,
                'category' => $request->category ?? '',
                'author' => $request->author ?? ''
            ];

            // Handle image upload to S3
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $image = $request->file('image');
                
                // Generate unique filename
                $filename = 'blog-images/' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                // Store in S3
                Storage::disk('s3')->put($filename, file_get_contents($image), 'public');
                
                // Get S3 URL
                $s3Url = Storage::disk('s3')->url($filename);
                
                // Store S3 URL in database
                $blogData['image'] = $s3Url;
                
                Log::info('Blog image uploaded:', [
                    's3_url' => $s3Url,
                    'filename' => $filename
                ]);
            }

            $blog = Blog::create($blogData);

            return response()->json([
                'success' => true,
                'data' => $blog,
                'message' => 'Blog created successfully'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Blog creation failed:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create blog: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $blog = Blog::find($id);
            
            if (!$blog) {
                return response()->json([
                    'success' => false,
                    'message' => 'Blog not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $blog,
                'message' => 'Blog retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve blog: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);
        
        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'category' => 'nullable|string|max:100',
            'author' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $blogData = [
                'title' => $request->title,
                'content' => $request->content,
                'category' => $request->category ?? '',
                'author' => $request->author ?? ''
            ];

            // Handle image update
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // Delete old image from S3 if exists
                if ($blog->image && $this->isS3Url($blog->image)) {
                    $oldFilename = $this->extractS3Filename($blog->image);
                    if ($oldFilename) {
                        Storage::disk('s3')->delete($oldFilename);
                        Log::info('Old blog image deleted from S3:', ['filename' => $oldFilename]);
                    }
                }

                // Upload new image to S3
                $image = $request->file('image');
                $filename = 'blog-images/' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                Storage::disk('s3')->put($filename, file_get_contents($image), 'public');
                $s3Url = Storage::disk('s3')->url($filename);
                
                $blogData['image'] = $s3Url;
                
                Log::info('New blog image uploaded to S3:', [
                    'filename' => $filename,
                    's3_url' => $s3Url
                ]);
            }

            $blog->update($blogData);

            return response()->json([
                'success' => true,
                'data' => $blog,
                'message' => 'Blog updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Blog update failed:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update blog: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $blog = Blog::find($id);
        
        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found'
            ], 404);
        }

        try {
            // Delete associated image from S3 if exists
            if ($blog->image && $this->isS3Url($blog->image)) {
                $filename = $this->extractS3Filename($blog->image);
                if ($filename) {
                    Storage::disk('s3')->delete($filename);
                    Log::info('Blog image deleted from S3:', ['filename' => $filename]);
                }
            }

            $blog->delete();

            return response()->json([
                'success' => true,
                'message' => 'Blog deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Blog deletion failed:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete blog: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extract filename from S3 URL
     */
    private function extractS3Filename($url)
    {
        // Extract filename from S3 URL
        // URL format: https://staynest-images.s3.eu-central-2.idrivee2.com/blog-images/filename.jpg
        if (strpos($url, 'staynest-images.s3.eu-central-2.idrivee2.com/') !== false) {
            return str_replace('https://staynest-images.s3.eu-central-2.idrivee2.com/', '', $url);
        }
        
        return null;
    }

    /**
     * Check if URL is from S3
     */
    private function isS3Url($url)
    {
        return strpos($url ?? '', 'staynest-images.s3.eu-central-2.idrivee2.com') !== false;
    }

    /**
     * Proxy for blog images (to handle CORS)
     */
    public function proxyBlogImage($filename)
    {
        try {
            $filePath = 'blog-images/' . urldecode($filename);
            
            if (!Storage::disk('s3')->exists($filePath)) {
                return response()->json(['error' => 'Blog image not found'], 404);
            }
            
            $file = Storage::disk('s3')->get($filePath);
            $mimeType = Storage::disk('s3')->mimeType($filePath);
            
            return response($file, 200)
                ->header('Content-Type', $mimeType)
                ->header('Cache-Control', 'public, max-age=86400')
                ->header('Access-Control-Allow-Origin', '*');
                
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}