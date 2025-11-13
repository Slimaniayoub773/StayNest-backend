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
    'years_experience' => 'nullable|integer|min:0|max:100', // Add this line
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
                    $logoPath = $request->file('logo')->store('home-page', 'public');
                    $data['logo'] = $logoPath;
                }

                // Check if home page data already exists
                $homePage = HomePage::first();
                
                if ($homePage) {
                    // Update existing
                    if ($request->hasFile('logo') && $homePage->logo) {
                        Storage::disk('public')->delete($homePage->logo);
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
    'years_experience' => 'nullable|integer|min:0|max:100', // Add this line
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
                    // Delete old logo
                    if ($homePage->logo) {
                        Storage::disk('public')->delete($homePage->logo);
                    }
                    
                    $logoPath = $request->file('logo')->store('home-page', 'public');
                    $data['logo'] = $logoPath;
                } else {
                    // Keep existing logo if not updating
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
    }