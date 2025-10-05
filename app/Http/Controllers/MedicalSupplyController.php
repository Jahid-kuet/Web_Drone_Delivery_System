<?php

namespace App\Http\Controllers;

use App\Models\MedicalSupply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MedicalSupplyController extends Controller
{
    /**
     * Display a listing of medical supplies
     */
    public function index(Request $request)
    {
        $query = MedicalSupply::query();
        
        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        
        // Category filter
        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }
        
        // Type filter
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }
        
        // Stock level filter
        if ($stockLevel = $request->input('stock_level')) {
            switch ($stockLevel) {
                case 'low':
                    $query->lowStock();
                    break;
                case 'out':
                    $query->where('quantity_in_stock', 0);
                    break;
                case 'adequate':
                    $query->where('quantity_in_stock', '>', DB::raw('minimum_stock_level'));
                    break;
            }
        }
        
        // Expiry filter
        if ($expiryFilter = $request->input('expiry')) {
            switch ($expiryFilter) {
                case 'expired':
                    $query->expired();
                    break;
                case 'expiring_soon':
                    $query->expiringSoon();
                    break;
            }
        }
        
        // Sorting
        $sortBy = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);
        
        $supplies = $query->paginate(20);
        
        // Get statistics
        $stats = [
            'total' => MedicalSupply::count(),
            'low_stock' => MedicalSupply::lowStock()->count(),
            'out_of_stock' => MedicalSupply::where('quantity_in_stock', 0)->count(),
            'expiring_soon' => MedicalSupply::expiringSoon()->count(),
            'expired' => MedicalSupply::expired()->count(),
        ];
        
        return view('admin.supplies.index', compact('supplies', 'stats'));
    }

    /**
     * Show the form for creating a new medical supply
     */
    public function create()
    {
        return view('admin.supplies.create');
    }

    /**
     * Store a newly created medical supply
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:medical_supplies,sku|max:100',
            'category' => 'required|string|in:medication,equipment,vaccine,blood_product,diagnostic,other',
            'type' => 'required|string|in:urgent,standard,scheduled,emergency',
            'description' => 'nullable|string',
            'manufacturer' => 'nullable|string|max:255',
            'quantity_in_stock' => 'required|integer|min:0',
            'unit_of_measurement' => 'required|string|max:50',
            'minimum_stock_level' => 'required|integer|min:0',
            'reorder_quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'weight_grams' => 'required|numeric|min:0',
            'volume_ml' => 'nullable|numeric|min:0',
            'storage_temperature_min' => 'nullable|numeric',
            'storage_temperature_max' => 'nullable|numeric',
            'requires_refrigeration' => 'required|boolean',
            'is_fragile' => 'required|boolean',
            'expiry_date' => 'nullable|date|after:today',
            'batch_number' => 'nullable|string|max:100',
            'status' => 'required|string|in:active,inactive,discontinued',
            'image' => 'nullable|image|max:2048',
        ]);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image_url'] = $request->file('image')->store('medical-supplies', 'public');
        }
        
        $supply = MedicalSupply::create($validated);
        
        return redirect()->route('admin.supplies.show', $supply)
            ->with('success', 'Medical supply created successfully!');
    }

    /**
     * Display the specified medical supply
     */
    public function show(MedicalSupply $supply)
    {
        $supply->load(['deliveryRequests' => function ($query) {
            $query->latest()->limit(10);
        }]);
        
        // Get stock history (from audit logs)
        $stockHistory = $supply->auditLogs()
            ->where(function ($query) {
                $query->whereRaw("JSON_EXTRACT(new_values, '$.quantity_in_stock') IS NOT NULL")
                    ->orWhereRaw("JSON_EXTRACT(old_values, '$.quantity_in_stock') IS NOT NULL");
            })
            ->latest()
            ->limit(20)
            ->get();
        
        return view('admin.supplies.show', compact('supply', 'stockHistory'));
    }

    /**
     * Show the form for editing the specified medical supply
     */
    public function edit(MedicalSupply $supply)
    {
        return view('admin.supplies.edit', compact('supply'));
    }

    /**
     * Update the specified medical supply
     */
    public function update(Request $request, MedicalSupply $supply)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:medical_supplies,sku,' . $supply->id,
            'category' => 'required|string|in:medication,equipment,vaccine,blood_product,diagnostic,other',
            'type' => 'required|string|in:urgent,standard,scheduled,emergency',
            'description' => 'nullable|string',
            'manufacturer' => 'nullable|string|max:255',
            'quantity_in_stock' => 'required|integer|min:0',
            'unit_of_measurement' => 'required|string|max:50',
            'minimum_stock_level' => 'required|integer|min:0',
            'reorder_quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'weight_grams' => 'required|numeric|min:0',
            'volume_ml' => 'nullable|numeric|min:0',
            'storage_temperature_min' => 'nullable|numeric',
            'storage_temperature_max' => 'nullable|numeric',
            'requires_refrigeration' => 'required|boolean',
            'is_fragile' => 'required|boolean',
            'expiry_date' => 'nullable|date',
            'batch_number' => 'nullable|string|max:100',
            'status' => 'required|string|in:active,inactive,discontinued',
            'image' => 'nullable|image|max:2048',
        ]);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($supply->image_url) {
                Storage::disk('public')->delete($supply->image_url);
            }
            $validated['image_url'] = $request->file('image')->store('medical-supplies', 'public');
        }
        
        $supply->update($validated);
        
        return redirect()->route('admin.supplies.show', $supply)
            ->with('success', 'Medical supply updated successfully!');
    }

    /**
     * Remove the specified medical supply
     */
    public function destroy(MedicalSupply $supply)
    {
        // Check if supply is used in any pending/active deliveries
        $activeDeliveries = $supply->deliveryRequests()
            ->whereIn('status', ['pending', 'approved'])
            ->count();
        
        if ($activeDeliveries > 0) {
            return redirect()->route('admin.supplies.index')
                ->with('error', 'Cannot delete supply with active delivery requests!');
        }
        
        // Delete image
        if ($supply->image_url) {
            Storage::disk('public')->delete($supply->image_url);
        }
        
        $supply->delete();
        
        return redirect()->route('admin.supplies.index')
            ->with('success', 'Medical supply deleted successfully!');
    }

    /**
     * Adjust stock level
     */
    public function adjustStock(Request $request, MedicalSupply $supply)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|not_in:0',
            'reason' => 'required|string|max:500',
            'type' => 'required|string|in:add,reduce,set',
        ]);
        
        DB::beginTransaction();
        
        try {
            $oldQuantity = $supply->quantity_in_stock;
            
            switch ($validated['type']) {
                case 'add':
                    $supply->increaseStock($validated['quantity']);
                    break;
                case 'reduce':
                    $supply->reduceStock(abs($validated['quantity']));
                    break;
                case 'set':
                    $supply->quantity_in_stock = abs($validated['quantity']);
                    $supply->save();
                    break;
            }
            
            // Log the adjustment
            DB::table('stock_adjustments')->insert([
                'medical_supply_id' => $supply->id,
                'user_id' => auth()->id(),
                'old_quantity' => $oldQuantity,
                'new_quantity' => $supply->quantity_in_stock,
                'adjustment_type' => $validated['type'],
                'reason' => $validated['reason'],
                'created_at' => now(),
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.supplies.show', $supply)
                ->with('success', 'Stock adjusted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to adjust stock: ' . $e->getMessage());
        }
    }

    /**
     * Export supplies list
     */
    public function export(Request $request)
    {
        $format = $request->input('format', 'csv');
        
        $supplies = MedicalSupply::all();
        
        // Implementation depends on export library
        // For now, return CSV headers
        $filename = 'medical-supplies-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($supplies) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'SKU', 'Name', 'Category', 'Type', 'Quantity', 'Unit', 
                'Min Level', 'Unit Price', 'Status', 'Expiry Date'
            ]);
            
            // Data
            foreach ($supplies as $supply) {
                fputcsv($file, [
                    $supply->sku,
                    $supply->name,
                    $supply->category,
                    $supply->type,
                    $supply->quantity_in_stock,
                    $supply->unit_of_measurement,
                    $supply->minimum_stock_level,
                    $supply->unit_price,
                    $supply->status,
                    $supply->expiry_date ? $supply->expiry_date->format('Y-m-d') : 'N/A',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get low stock supplies (AJAX)
     */
    public function lowStockAlert()
    {
        $supplies = MedicalSupply::lowStock()
            ->active()
            ->get();
        
        return response()->json([
            'count' => $supplies->count(),
            'supplies' => $supplies->map(function ($supply) {
                return [
                    'id' => $supply->id,
                    'name' => $supply->name,
                    'sku' => $supply->sku,
                    'quantity' => $supply->quantity_in_stock,
                    'minimum' => $supply->minimum_stock_level,
                    'reorder' => $supply->reorder_quantity,
                    'status' => $supply->stock_status,
                ];
            }),
        ]);
    }

    /**
     * Get expiring supplies (AJAX)
     */
    public function expiringAlert()
    {
        $supplies = MedicalSupply::expiringSoon()
            ->active()
            ->get();
        
        return response()->json([
            'count' => $supplies->count(),
            'supplies' => $supplies->map(function ($supply) {
                return [
                    'id' => $supply->id,
                    'name' => $supply->name,
                    'sku' => $supply->sku,
                    'expiry_date' => $supply->expiry_date->format('Y-m-d'),
                    'days_until_expiry' => $supply->expiry_date->diffInDays(now()),
                    'batch_number' => $supply->batch_number,
                ];
            }),
        ]);
    }
}
