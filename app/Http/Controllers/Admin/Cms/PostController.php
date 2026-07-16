<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Cms\StorePostRequest;
use App\Http\Requests\Admin\Cms\UpdatePostRequest;
use App\Models\Category;
use App\Models\Media;
use App\Models\Post;
use App\Support\CmsSlug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'status' => (string) $request->query('status', ''),
            'category_id' => (string) $request->query('category_id', ''),
            'search' => trim((string) $request->query('search', '')),
        ];

        $posts = Post::query()
            ->with(['category', 'featuredImage'])
            ->when($filters['status'] !== '', fn ($q) => $q->where('status', $filters['status']))
            ->when($filters['category_id'] !== '', fn ($q) => $q->where('category_id', $filters['category_id']))
            ->when($filters['search'] !== '', function ($q) use ($filters): void {
                $term = '%'.$filters['search'].'%';
                $q->where(function ($inner) use ($term): void {
                    $inner->where('title_en', 'like', $term)
                        ->orWhere('title_ar', 'like', $term)
                        ->orWhere('slug', 'like', $term);
                });
            })
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.cms.posts.index', [
            'posts' => $posts,
            'filters' => $filters,
            'categories' => Category::query()->where('type', Category::TYPE_POST)->orderBy('name_en')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.cms.posts.create', $this->formData());
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = CmsSlug::uniqueFrom($data['title_en'], new Post);
        }

        if ($data['status'] === Post::STATUS_PUBLISHED && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $post = Post::query()->create($data);

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('status', __('admin.cms.post_created'));
    }

    public function edit(Post $post): View
    {
        return view('admin.cms.posts.edit', array_merge($this->formData(), ['post' => $post]));
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $data = $request->validated();

        if ($data['status'] === Post::STATUS_PUBLISHED && empty($data['published_at'])) {
            $data['published_at'] = $post->published_at ?? now();
        }

        $post->update($data);

        return back()->with('status', __('admin.cms.post_updated'));
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('status', __('admin.cms.post_deleted'));
    }

    public function preview(Post $post): View
    {
        $related = Post::query()
            ->published()
            ->whereKeyNot($post->id)
            ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('public.news.show', [
            'post' => $post,
            'relatedPosts' => $related,
            'preview' => true,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'categories' => Category::query()->where('type', Category::TYPE_POST)->orderBy('name_en')->get(),
            'mediaLibrary' => Media::query()->latest()->limit(200)->get(),
        ];
    }
}
