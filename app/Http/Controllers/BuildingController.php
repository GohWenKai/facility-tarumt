<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Services\ImageService; // Import Service
use App\Http\Requests\SaveBuildingRequest; // Import Request
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    protected $imageService;

    // 1. Dependency Injection (Service Pattern)
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    // ==========================================
    // 1. READ
    // ==========================================
    public function manage()
    {
        $buildings = Building::paginate(10);
        return view('admin.buildings.index', compact('buildings'));
    }

    // ==========================================
    // 2. CREATE
    // ==========================================
    public function create()
    {
        return view('admin.buildings.create');
    }

    // ==========================================
    // 3. STORE
    // ==========================================
    // Use the Custom Request for Validation
    public function store(SaveBuildingRequest $request) 
    {
        // 1. Get Validated Data
        $data = $request->validated();

        // 2. Delegate Image Logic to Service
        if ($request->hasFile('image')) {
            $data['image_path'] = $this->imageService->upload(
                $request->file('image'), 
                'buildings'
            );
        }
        unset($data['image']);

        // 3. Create
        $building = Building::create($data);

        // Response
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $building], 201);
        }

        return redirect()->route('admin.buildings.manage')
            ->with('success', 'Building created successfully!');
    }

    // ==========================================
    // 4. EDIT
    // ==========================================
    public function edit($id)
    {
        $building = Building::findOrFail($id);
        return view('admin.buildings.edit', compact('building'));
    }

    // ==========================================
    // 5. UPDATE
    // ==========================================
    public function update(SaveBuildingRequest $request, $id)
    {
        $building = Building::findOrFail($id);
        $data = $request->validated();

        // 1. Delegate Image Update to Service
        if ($request->hasFile('image')) {
            $data['image_path'] = $this->imageService->replace(
                $building->image_path,      // Old path
                $request->file('image'),    // New file
                'buildings'                 // Folder
            );
        }
        unset($data['image']);

        $building->update($data);

        return redirect()->route('admin.buildings.manage')
            ->with('success', 'Building updated successfully!');
    }

    // ==========================================
    // 6. DESTROY
    // ==========================================
    public function destroy($id)
    {
        $building = Building::findOrFail($id);
        
        // Prevent deleting if facilities exist
        if($building->facilities()->exists()) {
             return back()->with('error', 'Cannot delete building. Delete associated facilities first.');
        }

        // 1. Delegate Image Deletion to Service
        $this->imageService->delete($building->image_path);

        $building->delete();

        return back()->with('success', 'Building deleted successfully!');
    }

    public function show($id)
    {
        // In your original code, this was a redirect. Keeping it as is.
        return redirect()->route('admin.buildings.manage');
    }
}