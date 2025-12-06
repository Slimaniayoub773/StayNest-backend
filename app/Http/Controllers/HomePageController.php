<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\HomePage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class HomePageController extends Controller
{
    public function index()
    {
        try {
            $homePage = HomePage::first();
            
            if (!$homePage) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'No home page data found'
                ], 200);
            }

            return response()->json([
                'success' => true,
                'data' => $homePage
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch home page data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'description' => 'nullable|string',
                'years_experience' => 'nullable|integer|min:0|max:100',
                'map' => 'nullable|string',
                'facebook' => 'nullable|url|max:255',
                'instagram' => 'nullable|url|max:255',
                'whatsapp' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->except('logo');
            
            // Handle logo upload to S3
            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                $path = $request->file('logo')->store('home-page-logos', 's3');
                
                // Make the file publicly accessible
                Storage::disk('s3')->setVisibility($path, 'public');
                
                $url = Storage::disk('s3')->url($path);
                $data['logo'] = $url;

                Log::info('Logo uploaded to S3:', [
                    'path' => $path,
                    'url' => $url,
                    'size' => $request->file('logo')->getSize()
                ]);
            }

            // Check if home page data already exists
            $homePage = HomePage::first();
            
            if ($homePage) {
                // Update existing - delete old logo from S3 if exists
                if ($request->hasFile('logo') && $homePage->logo) {
                    $this->deleteLogoFromS3($homePage->logo);
                }
                
                $homePage->update($data);
                $message = 'Home page updated successfully';
            } else {
                // Create new
                $homePage = HomePage::create($data);
                $message = 'Home page created successfully';
            }

            return response()->json([
                'success' => true,
                'data' => $homePage,
                'message' => $message
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to save home page data:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save home page data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $homePage = HomePage::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'description' => 'nullable|string',
                'years_experience' => 'nullable|integer|min:0|max:100',
                'map' => 'nullable|string',
                'facebook' => 'nullable|url|max:255',
                'instagram' => 'nullable|url|max:255',
                'whatsapp' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->except('logo');
            
            // Handle logo upload to S3
            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                // Delete old logo from S3 if exists
                if ($homePage->logo) {
                    $this->deleteLogoFromS3($homePage->logo);
                }
                
                // Upload new logo to S3
                $path = $request->file('logo')->store('home-page-logos', 's3');
                
                // Make the file publicly accessible
                Storage::disk('s3')->setVisibility($path, 'public');
                
                $url = Storage::disk('s3')->url($path);
                $data['logo'] = $url;

                Log::info('Logo updated on S3:', [
                    'old_logo' => $homePage->logo,
                    'new_path' => $path,
                    'new_url' => $url
                ]);
            }
            // If no logo is sent at all, we keep the existing logo

            $homePage->update($data);

            return response()->json([
                'success' => true,
                'data' => $homePage,
                'message' => 'Home page updated successfully'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to update home page data:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update home page data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $homePage = HomePage::findOrFail($id);

            // Delete logo file from S3 if exists
            if ($homePage->logo) {
                $this->deleteLogoFromS3($homePage->logo);
            }

            $homePage->delete();

            return response()->json([
                'success' => true,
                'message' => 'Home page data deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to delete home page data:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete home page data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadLogo(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                $path = $request->file('logo')->store('home-page-logos', 's3');
                
                // Make the file publicly accessible
                Storage::disk('s3')->setVisibility($path, 'public');
                
                $url = Storage::disk('s3')->url($path);

                return response()->json([
                    'success' => true,
                    'logo_url' => $url,
                    'logo_path' => $path,
                    'message' => 'Logo uploaded successfully'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'No valid logo file provided'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Failed to upload logo:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload logo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method to delete logo from S3
     */
    private function deleteLogoFromS3($logoUrl)
    {
        try {
            if ($logoUrl && str_contains($logoUrl, 'staynest-images.s3.eu-central-2.idrivee2.com')) {
                $path = str_replace(Storage::disk('s3')->url(''), '', $logoUrl);
                Storage::disk('s3')->delete($path);
                Log::info('Logo deleted from S3:', ['path' => $path]);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to delete logo from S3:', ['error' => $e->getMessage(), 'url' => $logoUrl]);
            return false;
        }
    }

    /**
     * Proxy method for serving logo images (to avoid CORS issues)
     */
    public function proxyLogo($filename)
    {
        try {
            $filePath = 'home-page-logos/' . urldecode($filename);
            
            if (!Storage::disk('s3')->exists($filePath)) {
                return response()->json(['error' => 'Logo not found'], 404);
            }
            
            $file = Storage::disk('s3')->get($filePath);
            $mimeType = Storage::disk('s3')->mimeType($filePath);
            
            return response($file, 200)
                ->header('Content-Type', $mimeType)
                ->header('Cache-Control', 'public, max-age=86400')
                ->header('Access-Control-Allow-Origin', '*');
                
        } catch (\Exception $e) {
            Log::error('Logo proxy error:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}