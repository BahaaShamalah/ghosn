<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Cms\StoreCategoryRequest;
use App\Http\Requests\Admin\Cms\UpdateCategoryRequest;
use App\Models\Category;
use App\Support\CmsSlug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $type = (string) $request->query('type', '');

        $categories = Category::query()
            ->withCount(['posts', 'campaigns'])
            ->when($type !== '', fn ($q) => $q->where('type', $type))
            ->when($search !== '', function ($q) use ($search): void {
                $term = '%'.$search.'%';
                $q->where(function ($inner) use ($term): void {
                    $inner->where('name_en', 'like', $term)
                        ->orWhere('name_ar', 'like', $term)
                        ->orWhere('slug', 'like', $term);
                });
            })
            ->orderBy('type')
            ->orderBy('name_en')
            ->paginate(20)
            ->withQueryString();

        return view('admin.cms.categories.index', [
            'categories' => $categories,
            'search' => $search,
            'type' => $type,
        ]);
    }

    public function create(): View
    {
        return view('admin.cms.categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = CmsSlug::uniqueFrom($data['name_en'], new Category);
        }

        $category = Category::query()->create($data);

        return redirect()
            ->route('admin.categories.edit', $category)
            ->with('status', __('admin.cms.category_created'));
    }

    public function edit(Category $category): View
    {
        return view('admin.cms.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return back()->with('status', __('admin.cms.category_updated'));
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('status', __('admin.cms.category_deleted'));
    }
}
