<div id="admin-delete-modal" data-admin-delete-modal class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-md rounded-[20px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8] p-6 shadow-2xl">
        <h2 class="text-lg font-bold text-[#2f4327]">{{ __('admin.confirm_delete_title') }}</h2>
        <p class="mt-2 text-sm text-[#5f6857]" data-admin-delete-message>{{ __('admin.cms.confirm_delete') }}</p>
        <div class="mt-6 flex justify-end gap-2">
            <button type="button" data-admin-delete-cancel class="rounded-[11px] border border-[rgba(64,97,57,0.18)] px-4 py-2 text-sm font-semibold text-[#406139] hover:bg-[rgba(64,97,57,0.06)]">{{ __('admin.cms.cancel') }}</button>
            <form method="POST" data-admin-bulk-delete-form action="{{ route('admin.content-pages.bulk') }}" class="hidden">
                @csrf
                <input type="hidden" name="action" value="delete">
                <div data-admin-bulk-delete-ids></div>
                <button type="submit" class="hidden rounded-[11px] bg-[#a24a37] px-4 py-2 text-sm font-semibold text-white hover:bg-[#8a3d2d]">{{ __('admin.cms.delete') }}</button>
            </form>
            <form method="POST" data-admin-delete-form>
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-[11px] bg-[#a24a37] px-4 py-2 text-sm font-semibold text-white hover:bg-[#8a3d2d]">{{ __('admin.cms.delete') }}</button>
            </form>
        </div>
    </div>
</div>
