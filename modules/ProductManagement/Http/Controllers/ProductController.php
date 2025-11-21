<?php

namespace Modules\ProductManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\ProductManagement\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Authorization is handled by Policy and middleware
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Product::class);

        $products = Product::query()
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })
            ->when(isset($request->active), function ($query) use ($request) {
                return $query->where('is_active', $request->active);
            })
            ->orderBy('name')
            ->paginate(10);

        return view('product-management::products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Product::class);

        return view('product-management::products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Product::class);

        $tenantId = session('tenant_id');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                })
            ],
            'sku' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        // Upload gambar jika ada
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        $validated['tenant_id'] = $tenantId;
        $validated['is_active'] = $request->has('is_active');

        Product::create($validated);

        return redirect()->route('modules.product-management.products.index')
            ->with('success', 'Produk berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        Gate::authorize('view', $product);

        return view('product-management::products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        Gate::authorize('update', $product);

        return view('product-management::products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);
        Gate::authorize('update', $product);

        $tenantId = session('tenant_id');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                })->ignore($product->id)
            ],
            'sku' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        // Upload gambar baru jika ada
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        $validated['is_active'] = $request->has('is_active');

        $product->update($validated);

        return redirect()->route('modules.product-management.products.index')
            ->with('success', 'Produk berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        Gate::authorize('delete', $product);

        // Hapus gambar jika ada
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('modules.product-management.products.index')
            ->with('success', 'Produk berhasil dihapus');
    }
}
