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
    public function index()
    {
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

        $products = $query->latest()->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
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
        $product->load('category', 'orderItems.order');

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
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
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    public function export()
    {
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
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
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
                    $product->stock,
                    $product->status ?? '-',
                    $product->featured ? 'Yes' : 'No',
                    strip_tags($product->description ?? ''),
                    $product->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function report()
    {
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
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        return $dompdf->stream('products_report_' . date('Y-m-d_His') . '.pdf');
    }
}

