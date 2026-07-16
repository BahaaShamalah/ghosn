<div class="gh-admin-stat">
    <p class="gh-admin-stat-label">{{ $label }}</p>
    <p class="gh-admin-stat-value" @isset($dir) dir="{{ $dir }}" @endisset>{{ $value }}</p>
</div>
