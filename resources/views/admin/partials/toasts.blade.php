@php
    $flashPayload = null;

    if (session('status')) {
        $flashPayload = ['type' => 'success', 'message' => session('status')];
    } elseif (session('error')) {
        $flashPayload = ['type' => 'error', 'message' => session('error')];
    }
@endphp

<div
    id="admin-toast-root"
    class="pointer-events-none fixed top-4 end-4 z-[100] flex w-full max-w-sm flex-col items-end gap-2 px-4"
    @if ($flashPayload)
        data-flash="@json($flashPayload)"
    @endif
></div>
