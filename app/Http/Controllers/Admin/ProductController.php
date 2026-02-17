<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Check if user has permission to access store management (view only for partners).
     */
    protected function checkStoreAdminPermission($allowPartners = false)
    {
        $user = auth()->user();
        if ($allowPartners && $user->is_partner) {
            return; // Partners can view
        }
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('store_admin')) {
            abort(403, 'You do not have permission to access this resource.');
        }
    }

    public function index()
    {
        $this->checkStoreAdminPermission(true); // Allow partners to view
        
        $query = Product::with('category');

        if ($search = request('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($categoryId = request('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Quick date filters
        if ($dateFilter = request('created_at')) {
            switch ($dateFilter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->where('created_at', '>=', now()->startOfWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->startOfMonth());
                    break;
            }
        }

        // Sorting
        $sortColumn = request('sort', 'created_at');
        $sortDirection = request('direction', 'desc');
        
        // Validate sort column
        $allowedSorts = ['name', 'sku', 'price', 'stock', 'status', 'created_at'];
        if (in_array($sortColumn, $allowedSorts)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->latest();
        }

        $perPage = request('per_page', 20);
        $products = $query->paginate($perPage)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $this->checkStoreAdminPermission();
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $this->checkStoreAdminPermission();
        
        $data = $request->validated();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = Str::slug($data['name']) . '-' . time() . '.' . $image->getClientOriginalExtension();
            
            // Create directory if it doesn't exist
            $imagePath = public_path('images/toys');
            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }
            
            // Move uploaded file
            $image->move($imagePath, $imageName);
            
            $data['image_url'] = $imageName;
        }
        
        // Auto-generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
            // Ensure uniqueness
            $originalSlug = $data['slug'];
            $counter = 1;
            while (Product::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }
        
        // Auto-generate SKU if not provided
        if (empty($data['sku'])) {
            $data['sku'] = 'TOY-' . strtoupper(Str::random(6));
            // Ensure uniqueness
            while (Product::where('sku', $data['sku'])->exists()) {
                $data['sku'] = 'TOY-' . strtoupper(Str::random(6));
            }
        }
        
        // Set default status if not provided
        if (empty($data['status'])) {
            $data['status'] = 'active';
        }
        
        Product::create($data);
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $this->checkStoreAdminPermission(true); // Allow partners to view
        $product->load('category', 'orderItems.order');

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->checkStoreAdminPermission();
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->checkStoreAdminPermission();
        $data = $request->validated();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = Str::slug($data['name']) . '-' . time() . '.' . $image->getClientOriginalExtension();
            
            // Create directory if it doesn't exist
            $imagePath = public_path('images/toys');
            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }
            
            // Move uploaded file
            $image->move($imagePath, $imageName);
            
            // Delete old image if exists
            if ($product->image_url && file_exists(public_path('images/toys/' . $product->image_url))) {
                @unlink(public_path('images/toys/' . $product->image_url));
            }
            
            $data['image_url'] = $imageName;
        }
        
        // Auto-generate slug if not provided and name changed
        if (empty($data['slug']) && $data['name'] !== $product->name) {
            $data['slug'] = Str::slug($data['name']);
            // Ensure uniqueness (excluding current product)
            $originalSlug = $data['slug'];
            $counter = 1;
            while (Product::where('slug', $data['slug'])->where('id', '!=', $product->id)->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        } elseif (empty($data['slug'])) {
            // Keep existing slug if name didn't change
            $data['slug'] = $product->slug;
        }
        
        // Auto-generate SKU if not provided
        if (empty($data['sku'])) {
            $data['sku'] = 'TOY-' . strtoupper(Str::random(6));
            // Ensure uniqueness (excluding current product)
            while (Product::where('sku', $data['sku'])->where('id', '!=', $product->id)->exists()) {
                $data['sku'] = 'TOY-' . strtoupper(Str::random(6));
            }
        }
        
        $product->update($data);
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->checkStoreAdminPermission();
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    public function export()
    {
        $this->checkStoreAdminPermission();
        try {
            // Clear any output buffering
            if (ob_get_level()) {
                ob_end_clean();
            }

            $query = Product::with('category');

            if ($search = request('q')) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            }

            if ($categoryId = request('category_id')) {
                $query->where('category_id', $categoryId);
            }

            $products = $query->latest()->get();

            $filename = 'products_export_' . date('Y-m-d_His') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function() use ($products) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Add CSV headers
                fputcsv($file, ['ID', 'Name', 'SKU', 'Category', 'Price', 'Stock', 'Status', 'Featured', 'Description', 'Created At']);
                
                // Add data rows
                foreach ($products as $product) {
                    fputcsv($file, [
                        $product->id,
                        $product->name,
                        $product->sku ?? '-',
                        $product->category->name ?? '-',
                        $product->price,
                        $product->stock ?? 0,
                        $product->status ?? 'active',
                        $product->featured ? 'Yes' : 'No',
                        strip_tags($product->description ?? ''),
                        $product->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    public function report()
    {
        $this->checkStoreAdminPermission();
        try {
            // Clear any output buffering
            if (ob_get_level()) {
                ob_end_clean();
            }

            $query = Product::with('category');

            if ($search = request('q')) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            }

            if ($categoryId = request('category_id')) {
                $query->where('category_id', $categoryId);
            }

            $products = $query->latest()->get();
            $totalProducts = $products->count();
            $totalValue = $products->sum('price');
            $lowStock = $products->where('stock', '<', 10)->count();

            $html = view('admin.reports.products', compact('products', 'totalProducts', 'totalValue', 'lowStock'))->render();
            
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->getOptions()->set('isRemoteEnabled', true);
            $dompdf->getOptions()->set('isHtml5ParserEnabled', true);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            
            return $dompdf->stream('products_report_' . date('Y-m-d_His') . '.pdf', ['Attachment' => false]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }

    /**
     * Handle bulk actions on products (delete, update status, etc.)
     */
    public function bulk(\Illuminate\Http\Request $request)
    {
        $this->checkStoreAdminPermission();

        $request->validate([
            'action' => 'required|string|in:delete,activate,deactivate,feature,unfeature',
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:products,id',
        ]);

        $ids = $request->ids;
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No items selected.');
        }

        $products = Product::whereIn('id', $ids);
        $count = $products->count();

        switch ($request->action) {
            case 'delete':
                $products->delete();
                $message = "{$count} product(s) deleted successfully.";
                break;
            case 'activate':
                $products->update(['status' => 'active']);
                $message = "{$count} product(s) activated.";
                break;
            case 'deactivate':
                $products->update(['status' => 'inactive']);
                $message = "{$count} product(s) deactivated.";
                break;
            case 'feature':
                $products->update(['featured' => true]);
                $message = "{$count} product(s) marked as featured.";
                break;
            case 'unfeature':
                $products->update(['featured' => false]);
                $message = "{$count} product(s) unfeatured.";
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action.');
        }

        return redirect()->route('admin.products.index')->with('success', $message);
    }
}

