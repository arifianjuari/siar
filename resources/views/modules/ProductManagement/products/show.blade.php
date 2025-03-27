@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Produk</h1>
        <a href="{{ route('modules.product-management.products.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow">
                <div class="card-body text-center">
                    @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded mb-3" style="max-height: 250px;">
                    @else
                    <div class="p-5 bg-light rounded mb-3 d-flex align-items-center justify-content-center">
                        <i class="bi bi-image-fill fs-1 text-muted"></i>
                    </div>
                    @endif
                    
                    <h4>{{ $product->name }}</h4>
                    <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }} mb-3">
                        {{ $product->is_active ? 'Aktif' : 'Non-aktif' }}
                    </span>
                    <h5 class="text-primary mt-2">Rp {{ number_format($product->price, 0, ',', '.') }}</h5>
                    
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        @if(\App\Helpers\PermissionHelper::hasPermission('product-management', 'can_edit'))
                        <a href="{{ route('modules.product-management.products.edit', $product->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        @endif
                        
                        @if(\App\Helpers\PermissionHelper::hasPermission('product-management', 'can_delete'))
                        <form action="{{ route('modules.product-management.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Informasi Produk</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th width="30%">Kode Produk</th>
                                <td>{{ $product->code ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>SKU</th>
                                <td>{{ $product->sku ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Stok</th>
                                <td>{{ number_format($product->stock) }}</td>
                            </tr>
                            <tr>
                                <th>Dibuat</th>
                                <td>{{ $product->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Diperbarui</th>
                                <td>{{ $product->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Deskripsi</h6>
                </div>
                <div class="card-body">
                    @if($product->description)
                    <p class="mb-0">{{ $product->description }}</p>
                    @else
                    <p class="text-muted mb-0">Tidak ada deskripsi</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 