@extends('admin.layouts.app')

@section('title', __('admin.cms.new_page'))
@section('page-title', __('admin.cms.new_page'))

@section('content')
    <form method="POST" action="{{ route('admin.content-pages.store') }}" class="space-y-6">
        @csrf
        <div class="gh-admin-card rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6 md:p-7">
            @include('admin.cms.partials.page-fields', ['page' => new \App\Models\ContentPage()])
        </div>
        @include('admin.partials.form-actions', [
            'cancelUrl' => route('admin.content-pages.index'),
        ])
    </form>
@endsection

@push('scripts')
    @include('admin.partials.cms-scripts')
@endpush
