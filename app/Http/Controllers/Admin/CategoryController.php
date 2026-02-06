<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Check if user has permission to access store management.
     */
    protected function checkStoreAdminPermission()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('store_admin')) {
            abort(403, 'You do not have permission to access this resource.');
        }
    }

    public function index()
    {
        $this->checkStoreAdminPermission();
        
        $query = Category::withCount('products');

        if ($search = request('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $categories = $query->latest()->paginate(20)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $this->checkStoreAdminPermission();
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $this->checkStoreAdminPermission();
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $data['slug'] = Str::slug($data['name']);

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function show(Category $category)
    {
        $this->checkStoreAdminPermission();
        $category->load(['products' => function ($q) {
            $q->latest()->take(10);
        }]);

        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $this->checkStoreAdminPermission();
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $this->checkStoreAdminPermission();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $data['slug'] = Str::slug($data['name']);

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $this->checkStoreAdminPermission();
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }

    public function export()
    {
        $this->checkStoreAdminPermission();
        try {
            // Clear any output buffering
            if (ob_get_level()) {
                ob_end_clean();
            }

            $query = Category::withCount('products');

            if ($search = request('q')) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
                });
            }

            $categories = $query->latest()->get();

            $filename = 'categories_export_' . date('Y-m-d_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function() use ($categories) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                fputcsv($file, ['ID', 'Name', 'Slug', 'Products Count', 'Description', 'Created At']);
                
                foreach ($categories as $category) {
                    fputcsv($file, [
                        $category->id,
                        $category->name,
                        $category->slug,
                        $category->products_count ?? 0,
                        strip_tags($category->description ?? ''),
                        $category->created_at->format('Y-m-d H:i:s'),
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

            $query = Category::withCount('products');

            if ($search = request('q')) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
                });
            }

            $categories = $query->latest()->get();
            $totalCategories = $categories->count();
            $totalProducts = $categories->sum('products_count');

            $html = view('admin.reports.categories', compact('categories', 'totalCategories', 'totalProducts'))->render();
            
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->setOption('isRemoteEnabled', true);
            $dompdf->setOption('isHtml5ParserEnabled', true);
            $dompdf->render();
            
            return $dompdf->stream('categories_report_' . date('Y-m-d_His') . '.pdf', ['Attachment' => false]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }
}

