@extends('layouts.app')

@section('title')
    <h1 class="page-title">Create Festival</h1>
    <p class="page-subtitle">Fill in the details to create a new festival</p>
@endsection

@section('header-actions')
    <a href="{{ route('events.index') }}" class="btn btn-ghost">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Back
    </a>
@endsection

@section('content')
    <div class="max-w-3xl mx-auto animate-in">
        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-secondary-900">Festival Information</h2>
            </div>
            <div class="card-body">
                @include('events._form')
            </div>
        </div>
    </div>
@endsection