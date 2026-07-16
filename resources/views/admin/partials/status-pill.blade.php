@php
    $map = [
        'pending' => 'bg-[rgba(191,160,74,0.16)] text-[#8a6d1f]',
        'approved' => 'bg-[rgba(64,97,57,0.14)] text-[#33502e]',
        'rejected' => 'bg-[rgba(162,74,55,0.13)] text-[#8a3d2d]',
        'paid' => 'bg-[rgba(64,97,57,0.14)] text-[#33502e]',
        'active' => 'bg-[rgba(129,149,98,0.2)] text-[#4a5f2f]',
        'draft' => 'bg-[rgba(150,167,145,0.28)] text-[#42563a]',
        'completed' => 'bg-[rgba(150,167,145,0.28)] text-[#42563a]',
    ];
    $pillClass = $map[$status ?? ''] ?? 'bg-[rgba(129,149,98,0.2)] text-[#4a5f2f]';
@endphp

<span class="inline-flex rounded-full px-2.5 py-1 text-[11.5px] font-bold {{ $pillClass }}">{{ $label ?? '' }}</span>
