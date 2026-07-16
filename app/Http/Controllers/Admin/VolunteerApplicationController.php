<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VolunteerApplication;
use App\Services\Volunteers\VolunteerEmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VolunteerApplicationController extends Controller
{
    public function __construct(
        private readonly VolunteerEmailService $emails,
    ) {}
    public function index(Request $request): View
    {
        $query = VolunteerApplication::query()->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($inner) use ($search): void {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('area', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        return view('admin.volunteers.index', [
            'applications' => $query->paginate(15)->withQueryString(),
            'filters' => [
                'search' => $request->string('search')->toString(),
                'status' => $request->string('status')->toString(),
            ],
            'statuses' => [
                VolunteerApplication::STATUS_PENDING,
                VolunteerApplication::STATUS_APPROVED,
                VolunteerApplication::STATUS_REJECTED,
            ],
            'pendingCount' => VolunteerApplication::query()->pending()->count(),
        ]);
    }

    public function updateStatus(Request $request, VolunteerApplication $application): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:'.implode(',', [
                VolunteerApplication::STATUS_PENDING,
                VolunteerApplication::STATUS_APPROVED,
                VolunteerApplication::STATUS_REJECTED,
            ])],
        ]);

        $previousStatus = $application->status;

        $application->update(['status' => $validated['status']]);

        $this->emails->afterStatusChanged($application->fresh(), $previousStatus);

        return back()->with('status', __('admin.volunteers.status_updated'));
    }
}
