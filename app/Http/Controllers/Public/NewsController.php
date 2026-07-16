<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $categorySlug = (string) $request->query('category', '');

        $postsQuery = Post::query()
            ->published()
            ->with(['category', 'featuredImage'])
            ->when($categorySlug !== '', function ($q) use ($categorySlug): void {
                $q->whereHas('category', fn ($cat) => $cat->where('slug', $categorySlug));
            })
            ->when($search !== '', function ($q) use ($search): void {
                $term = '%'.$search.'%';
                $q->where(function ($inner) use ($term): void {
                    $inner->where('title_en', 'like', $term)
                        ->orWhere('title_ar', 'like', $term)
                        ->orWhere('excerpt_en', 'like', $term)
                        ->orWhere('excerpt_ar', 'like', $term);
                });
            })
            ->orderByDesc('published_at')
            ->orderByDesc('created_at');

        $featured = (clone $postsQuery)->first();
        $posts = (clone $postsQuery)
            ->when($featured, fn ($q) => $q->whereKeyNot($featured->id))
            ->paginate(9)
            ->withQueryString();

        return view('public.news.index', [
            'featured' => $featured,
            'posts' => $posts,
            'categories' => Category::query()->where('type', Category::TYPE_POST)->orderBy('name_en')->get(),
            'search' => $search,
            'activeCategory' => $categorySlug,
        ]);
    }

    public function show(string $slug): View
    {
        $post = Post::query()
            ->published()
            ->with(['category', 'featuredImage'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedPosts = Post::query()
            ->published()
            ->with('featuredImage')
            ->whereKeyNot($post->id)
            ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('public.news.show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'preview' => false,
        ]);
    }
}
