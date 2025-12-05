<?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Controller;
    use App\Models\HomePage;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Validator;

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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
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

        $data = $request->all();

        // Handle logo upload - store full URL instead of just path
        if ($request->hasFile('logo')) {
    // Handle new logo upload
    $logoPath = $request->file('logo')->store('home-page', 'public');
    $data['logo'] = Storage::disk('public')->url($logoPath);
} elseif ($request->has('logo') && empty($request->input('logo'))) {
    // Handle logo removal (when logo is empty string)
    if ($homePage->logo) {
        $oldPath = str_replace(Storage::disk('public')->url(''), '', $homePage->logo);
        Storage::disk('public')->delete($oldPath);
        $data['logo'] = null; // Set to null in database
    }
} else {
    // Keep existing logo
    unset($data['logo']);
}

        // Check if home page data already exists
        $homePage = HomePage::first();
        
        if ($homePage) {
            // Update existing
            if ($request->hasFile('logo') && $homePage->logo) {
                // Extract path from URL to delete old file
                $oldPath = str_replace(Storage::disk('public')->url(''), '', $homePage->logo);
                Storage::disk('public')->delete($oldPath);
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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
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

        $data = $request->all();

        // Handle logo upload
       if ($request->hasFile('logo')) {
    // Handle new logo upload
    $logoPath = $request->file('logo')->store('home-page', 'public');
    $data['logo'] = Storage::disk('public')->url($logoPath);
} elseif ($request->has('logo') && empty($request->input('logo'))) {
    // Handle logo removal (when logo is empty string)
    if ($homePage->logo) {
        $oldPath = str_replace(Storage::disk('public')->url(''), '', $homePage->logo);
        Storage::disk('public')->delete($oldPath);
        $data['logo'] = null; // Set to null in database
    }
} else {
    // Keep existing logo
    unset($data['logo']);
}
        $homePage->update($data);

        return response()->json([
            'success' => true,
            'data' => $homePage,
            'message' => 'Home page updated successfully'
        ], 200);

    } catch (\Exception $e) {
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

                // Delete logo file if exists
                if ($homePage->logo) {
                    Storage::disk('public')->delete($homePage->logo);
                }

                $homePage->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Home page data deleted successfully'
                ], 200);

            } catch (\Exception $e) {
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
                    'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation error',
                        'errors' => $validator->errors()
                    ], 422);
                }

                $logoPath = $request->file('logo')->store('home-page', 'public');

                return response()->json([
                    'success' => true,
                    'logo_url' => Storage::disk('public')->url($logoPath),
                    'logo_path' => $logoPath,
                    'message' => 'Logo uploaded successfully'
                ], 200);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload logo',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
        public function getLogo($filename)
{
    try {
        $filePath = 'home-page/' . urldecode($filename);
        
        if (!Storage::disk('public')->exists($filePath)) {
            return response()->json(['error' => 'Logo not found'], 404);
        }
        
        $file = Storage::disk('public')->get($filePath);
        $mimeType = Storage::disk('public')->mimeType($filePath);
        
        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Cache-Control', 'public, max-age=86400')
            ->header('Access-Control-Allow-Origin', '*');
            
    } catch (\Exception $e) {
        return response()->json(['error' => 'Server error'], 500);
    }
}
    }