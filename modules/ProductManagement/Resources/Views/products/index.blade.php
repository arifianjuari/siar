@extends('layouts.app')
@section('content')
@php $hideDefaultHeader = true; @endphp
<div class="container-fluid py-4">
    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Produk</h1>
        @if(\App\Helpers\PermissionHelper::hasPermission('product-management', 'can_create'))
        <a href="{{ route('modules.product-management.products.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="bi bi-plus-lg"></i> Tambah Produk
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">Daftar Produk</h6>
            <form action="{{ route('modules.product-management.products.index') }}" method="GET" class="d-flex">
                <div class="input-group">
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Cari produk..." value="{{ request('search') }}">
                    <button class="btn btn-sm btn-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Kode</th>
                            <th>Nama Produk</th>
                            <th>Stok</th>
                            <th>Harga</th>
                            <th width="10%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $index => $product)
                        <tr>
                            <td>{{ $products->firstItem() + $index }}</td>
                            <td>{{ $product->code ?? '-' }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ number_format($product->stock) }}</td>
                            <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <a href="{{ route('modules.product-management.products.show', $product->id) }}" class="btn btn-sm btn-info me-1">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    @if(\App\Helpers\PermissionHelper::hasPermission('product-management', 'can_edit'))
                                    <a href="{{ route('modules.product-management.products.edit', $product->id) }}" class="btn btn-sm btn-warning me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endif
                                    
                                    @if(\App\Helpers\PermissionHelper::hasPermission('product-management', 'can_delete'))
                                    <form action="{{ route('modules.product-management.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data produk</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 