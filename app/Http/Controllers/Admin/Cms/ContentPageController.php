<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Cms\BulkContentPageRequest;
use App\Http\Requests\Admin\Cms\StoreContentPageRequest;
use App\Http\Requests\Admin\Cms\UpdateContentPageRequest;
use App\Models\ContentPage;
use App\Models\Media;
use App\Services\Cms\ContentPageService;
use App\Support\CmsSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentPageController extends Controller
{
    public function __construct(
        private readonly ContentPageService $pages,
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'status' => (string) $request->query('status', ''),
            'search' => trim((string) $request->query('search', '')),
            'language' => (string) $request->query('language', ''),
            'date_from' => (string) $request->query('date_from', ''),
            'date_to' => (string) $request->query('date_to', ''),
            'sort' => (string) $request->query('sort', 'updated_desc'),
        ];

        $pages = $this->filteredQuery($filters)
            ->with(['featuredImage', 'author'])
            ->paginate(15)
            ->withQueryString();

        return view('admin.cms.pages.index', [
            'pages' => $pages,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('admin.cms.pages.create', $this->formData());
    }

    public function store(StoreContentPageRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['author_id'] = auth()->id();

        if (empty($data['slug'])) {
            $data['slug'] = CmsSlug::uniqueFrom($data['title_en'], new ContentPage);
        }

        $page = ContentPage::query()->create($data);

        return redirect()
            ->route('admin.content-pages.edit', $page)
            ->with('status', __('admin.cms.page_created'));
    }

    public function edit(ContentPage $contentPage): View
    {
        return view('admin.cms.pages.edit', array_merge($this->formData(), ['page' => $contentPage]));
    }

    public function update(UpdateContentPageRequest $request, ContentPage $contentPage): RedirectResponse
    {
        $contentPage->update($request->validated());

        return back()->with('status', __('admin.cms.page_updated'));
    }

    public function destroy(ContentPage $contentPage): RedirectResponse
    {
        if (! $contentPage->canDelete()) {
            return back()->with('error', __('admin.cms.page_delete_protected'));
        }

        $contentPage->delete();

        return redirect()
            ->route('admin.content-pages.index')
            ->with('status', __('admin.cms.page_deleted'));
    }

    public function duplicate(ContentPage $contentPage): RedirectResponse
    {
        $copy = $this->pages->duplicate($contentPage);

        return redirect()
            ->route('admin.content-pages.edit', $copy)
            ->with('status', __('admin.cms.page_duplicated'));
    }

    public function bulk(BulkContentPageRequest $request): RedirectResponse
    {
        $ids = $request->validated('ids');
        $action = $request->validated('action');

        $result = match ($action) {
            'publish' => $this->pages->bulkPublish($ids),
            'unpublish' => $this->pages->bulkUnpublish($ids),
            'delete' => $this->pages->bulkDelete($ids),
            'duplicate' => $this->pages->bulkDuplicate($ids),
            default => ['processed' => 0, 'skipped' => 0],
        };

        $message = match ($action) {
            'publish' => __('admin.cms.bulk_published', ['count' => $result['processed']]),
            'unpublish' => __('admin.cms.bulk_unpublished', ['count' => $result['processed']]),
            'delete' => __('admin.cms.bulk_deleted', ['count' => $result['processed'], 'skipped' => $result['skipped'] ?? 0]),
            'duplicate' => __('admin.cms.bulk_duplicated', ['count' => $result['duplicated'] ?? $result['processed']]),
            default => __('admin.cms.bulk_done'),
        };

        if (($result['skipped'] ?? 0) > 0 && $action === 'delete') {
            return back()->with('error', $message);
        }

        return back()->with('status', $message);
    }

    public function preview(ContentPage $contentPage): View
    {
        return view('public.pages.show', [
            'page' => $contentPage,
            'preview' => true,
        ]);
    }

    /**
     * @param  array<string, string>  $filters
     */
    private function filteredQuery(array $filters): Builder
    {
        $query = ContentPage::query()
            ->when($filters['status'] !== '', fn ($q) => $q->where('status', $filters['status']))
            ->when($filters['language'] !== '', fn ($q) => $q->withLanguage($filters['language']))
            ->when($filters['date_from'] !== '', fn ($q) => $q->whereDate('updated_at', '>=', $filters['date_from']))
            ->when($filters['date_to'] !== '', fn ($q) => $q->whereDate('updated_at', '<=', $filters['date_to']))
            ->when($filters['search'] !== '', function ($q) use ($filters): void {
                $term = '%'.$filters['search'].'%';
                $q->where(function ($inner) use ($term): void {
                    $inner->where('title_en', 'like', $term)
                        ->orWhere('title_ar', 'like', $term)
                        ->orWhere('slug', 'like', $term);
                });
            });

        return match ($filters['sort']) {
            'title_asc' => $query->orderBy('title_en'),
            'created_desc' => $query->latest('created_at'),
            default => $query->latest('updated_at'),
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'mediaLibrary' => Media::query()->latest()->limit(200)->get(),
        ];
    }
}
