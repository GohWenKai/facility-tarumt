<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use App\Models\Building;
use App\Models\Booking;
use App\Models\User;
use App\Adapters\SearchQueryAdapter; // Import Pattern
use App\Http\Requests\StoreFacilityRequest; // Import Pattern
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class FacilityController extends Controller
{
    protected $searchAdapter;

    // 1. Dependency Injection (Adapter Pattern)
    public function __construct(SearchQueryAdapter $adapter)
    {
        $this->searchAdapter = $adapter;
    }

    // ============================================================
    // FRONTEND VIEWS (Restored Original Logic)
    // ============================================================

    public function index(Request $request)
    {
        $query = Facility::with('building')
            ->where('status', 'Available')
            ->withCount(['assets as broken_assets_count' => function ($q) {
                $q->whereIn('condition', ['Damaged', 'Maintenance']);
            }]);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $facilities = $query->paginate(9);
        
        return view('users.facilities.index', compact('facilities'));
    }
    
    public function show($id, Request $request)
    {
        $users = User::whereDate('created_at', today())->get();

        $facility = Facility::with(['building', 'assets'])->findOrFail($id);

        // 1. Get the requested date, or default to Today
        $dateParam = $request->query('date'); 
        $currentDate = $dateParam ? Carbon::parse($dateParam) : Carbon::now();

        $start_hour = (int) Carbon::parse($facility->start_time)->format('H');
        $end_hour   = (int) Carbon::parse($facility->end_time)->format('H');

        // Filter for Broken Assets
        $brokenAssets = $facility->assets->filter(function ($asset) {
            return in_array($asset->condition, ['Damaged', 'Maintenance']);
        });

        // 2. Update Schedule Query
        $schedule = Booking::where('facility_id', $id)
            ->where('start_time', '>=', $currentDate->copy()->startOfDay())
            ->where('start_time', '<=', $currentDate->copy()->addDays(7)->endOfDay())
            ->where('status', '!=', 'rejected') 
            ->orderBy('start_time', 'asc')
            ->get()
            ->groupBy(function($val) {
                return Carbon::parse($val->start_time)->format('Y-m-d');
            });

        // 3. Pass ALL variables to the view
        return view('users.facilities.show', compact('facility', 'brokenAssets', 'schedule', 'start_hour', 'end_hour', 'currentDate', 'users'));
    }

    // ============================================================
    // XML SEARCH API (Using Adapter Pattern)
    // ============================================================
    public function search(Request $request)
    {
        // 1. Use Adapter to normalize input (XML or Query)
        // This replaces the complex try-catch XML logic in your controller
        $criteria = $this->searchAdapter->parseCriteria($request);

        // 2. Build Query
        $query = Facility::with('building')
            ->withCount(['assets as broken_assets_count' => function ($q) {
                $q->whereIn('condition', ['Damaged', 'Maintenance']);
            }]);

        if (!empty($criteria['keyword'])) {
            $k = $criteria['keyword'];
            $query->where(function($q) use ($k) {
                $q->where('name', 'like', "%{$k}%")
                  ->orWhere('type', 'like', "%{$k}%");
            });
        }

        if (!empty($criteria['min_capacity'])) {
            $query->where('capacity', '>=', $criteria['min_capacity']);
        }

        if (!empty($criteria['building_id'])) {
            $query->where('building_id', $criteria['building_id']);
        }

        if ($criteria['requires_projector']) {
            $query->whereHas('assets', function($q) {
                $q->where('name', 'Projector')->where('status', 'working');
            });
        }

        $results = $query->take(10)->get();

        return response()->json($results);
    }

    // ============================================================
    // ADMIN MANAGEMENT (Restored Original Logic + Form Request)
    // ============================================================

    public function manage()
    {
        $facilities = Facility::with('building')->paginate(10);
        return view('admin.facilities.index', compact('facilities'));
    }

    public function create()
    {
        $buildings = Building::select('id', 'name')->get();
        return view('admin.facilities.create', compact('buildings'));
    }

    // STORE uses the Form Request Pattern
    public function store(StoreFacilityRequest $request)
    {
        // Validation is handled automatically by StoreFacilityRequest
        $data = $request->validated();

        // Handle Image
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('facilities', 'public');
        }
        unset($data['image']);

        $facility = Facility::create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $facility], 201);
        }

        return redirect()->route('admin.facilities.manage')
            ->with('success', 'Facility created successfully!');
    }

    public function edit($id)
    {
        $facility = Facility::findOrFail($id);
        return view('admin.facilities.edit', compact('facility'));
    }

    // UPDATE uses Standard Validation (simpler since building_id is missing)
    public function update(Request $request, $id)
    {
        $facility = Facility::findOrFail($id);

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'type'       => 'required|string', 
            'capacity'   => 'required|integer|min:1',
            'status'     => 'required|string|in:Available,Maintenance,Closed',
            'start_time' => 'required|date_format:H:i|after_or_equal:08:00|before:22:00',
            'end_time'   => 'required|date_format:H:i|after:start_time|before_or_equal:22:00',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($facility->image_path && Storage::disk('public')->exists($facility->image_path)) {
                Storage::disk('public')->delete($facility->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('facilities', 'public');
        }
        
        unset($validated['image']);

        $facility->update($validated);

        return redirect()->route('admin.facilities.manage')
            ->with('success', 'Facility updated successfully!');
    }

    public function destroy($id)
    {
        $facility = Facility::findOrFail($id);
        $facility->delete();

        return back()->with('success', 'Facility deleted successfully!');
    }
}