@if (session('status'))
    <div class="mb-6 rounded-[16px] border border-[#819562] bg-[rgba(129,149,98,0.16)] px-4 py-3 text-sm text-[#33502e]">{{ session('status') }}</div>
@endif
@if (session('error'))
    <div class="mb-6 rounded-[16px] border border-[#a24a37] bg-[rgba(162,74,55,0.12)] px-4 py-3 text-sm text-[#8a3d2d]">{{ session('error') }}</div>
@endif
