<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Facility;
use App\Factories\AssetFactory; // Pattern 1
use App\Services\AssetHistoryService; // Pattern 3
use Barryvdh\DomPDF\Facade\Pdf;

class AssetController extends Controller
{
    // ==========================================
    // 1. READ (Manage / List)
    // ==========================================
    public function manage()
    {
        $assets = Asset::with('facility')->paginate(10);
        return view('admin.assets.index', compact('assets'));
    }

    // ==========================================
    // 2. CREATE
    // ==========================================
    public function create()
    {
        $facilities = Facility::select('id', 'name')->get();
        return view('admin.assets.create', compact('facilities'));
    }

    // ==========================================
    // 3. STORE (Uses Factory + Observer)
    // ==========================================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_id'   => 'required|integer|exists:facilities,id',
            'name'          => 'required|string|max:255',
            'type'          => 'required|string|max:100',
            'serial_number' => 'required|string|max:100|unique:assets,serial_number',
            'condition'     => 'required|string|in:Good,Fair,Damaged,Maintenance,Retired',
            'maintenance_note' => 'nullable|string|max:500',
        ]);

        // PATTERN: FACTORY
        // The Factory creates the asset.
        // The OBSERVER (running in background) detects this creation and generates the XML.
        $asset = AssetFactory::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $asset], 201);
        }

        return redirect()->route('admin.assets.manage')->with('success', 'Asset added successfully!');
    }

    // ==========================================
    // 4. EDIT
    // ==========================================
    public function edit($id)
    {
        $asset = Asset::findOrFail($id);
        $facilities = Facility::select('id', 'name')->get();
        return view('admin.assets.edit', compact('asset', 'facilities'));
    }

    // ==========================================
    // 5. UPDATE (Uses Factory + Observer)
    // ==========================================
    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $validated = $request->validate([
            'facility_id'   => 'required|integer|exists:facilities,id',
            'name'          => 'required|string|max:255',
            'type'          => 'required|string|max:100',
            'serial_number' => 'required|string|max:100|unique:assets,serial_number,' . $id,
            'condition'     => 'required|string|in:Good,Fair,Damaged,Maintenance,Retired',
            'maintenance_note' => 'nullable|string|max:500'
        ]);
    
        // PATTERN: FACTORY
        // Factory updates logic. Observer detects update and appends to XML.
        AssetFactory::update($asset, $validated);

        return redirect()->route('admin.assets.manage')->with('success', 'Asset updated successfully!');
    }

    // ==========================================
    // 6. DESTROY (Observer handles XML deletion)
    // ==========================================
    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete(); // Observer deletes the XML file automatically.

        return back()->with('success', 'Asset deleted successfully!');
    }

    // ==========================================
    // 7. SHOW (Uses Service to read XML)
    // ==========================================
    public function show($id, AssetHistoryService $historyService)
    {
        $asset = Asset::with('facility')->findOrFail($id);

        $xmlRecords = $historyService->getHistory($id);
        
        return view('admin.assets.show', compact('asset', 'xmlRecords'));
    }

    // ==========================================
    // 8. PDF REPORT
    // ==========================================
    public function generateReport()
    {
        $assets = Asset::with('facility')->whereIn('condition', ['Maintenance', 'Damaged'])->get();
        $pdf = Pdf::loadView('admin.pdf.assets_report', ['assets' => $assets, 'filter' => 'Damaged/Maintenance']);
        return $pdf->stream('tarumt-asset-report.pdf');
    }
}