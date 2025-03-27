<?php

namespace App\Http\Controllers\Modules\ProductManagement;

use App\Helpers\PermissionHelper;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Constructor dengan middleware untuk check izin
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!PermissionHelper::hasPermission('product-management', 'can_view')) {
                abort(403, 'Akses tidak diizinkan');
            }

            return $next($request);
        });

        $this->middleware(function ($request, $next) {
            if (!PermissionHelper::hasPermission('product-management', 'can_create')) {
                abort(403, 'Akses tidak diizinkan untuk menambah produk');
            }

            return $next($request);
        })->only(['create', 'store']);

        $this->middleware(function ($request, $next) {
            if (!PermissionHelper::hasPermission('product-management', 'can_edit')) {
                abort(403, 'Akses tidak diizinkan untuk mengedit produk');
            }

            return $next($request);
        })->only(['edit', 'update']);

        $this->middleware(function ($request, $next) {
            if (!PermissionHelper::hasPermission('product-management', 'can_delete')) {
                abort(403, 'Akses tidak diizinkan untuk menghapus produk');
            }

            return $next($request);
        })->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::tenantScope()
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

        return view('modules.ProductManagement.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('modules.ProductManagement.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
        $product = Product::tenantScope()->findOrFail($id);

        return view('modules.ProductManagement.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::tenantScope()->findOrFail($id);

        return view('modules.ProductManagement.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::tenantScope()->findOrFail($id);
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
        $product = Product::tenantScope()->findOrFail($id);

        // Hapus gambar jika ada
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('modules.product-management.products.index')
            ->with('success', 'Produk berhasil dihapus');
    }
}
