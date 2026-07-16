<?php

namespace App\Services\Pages;

use App\Models\Page;
use Illuminate\Support\Collection;

class LandingContentRepository
{
    public function __construct(
        private readonly PageBuilderService $builder,
    ) {}

    public function homePage(): ?Page
    {
        return $this->builder->findHomePage();
    }

    /**
     * Fresh DB query on every call — no caching so admin edits appear immediately.
     *
     * @return Collection<int, array{key: string, type: string, title_en: string, title_ar: string, sort_order: int, blocks: array<int, array<string, mixed>>}>
     */
    public function homeSections(): Collection
    {
        return $this->builder->homeSectionsForRender();
    }
}
