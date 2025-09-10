@extends('feature-requests::layouts.public')

@section('title', 'Feature Requests Roadmap')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Feature Requests Roadmap</h1>
                        <p class="mt-2 text-gray-600">Track the progress of feature requests from idea to completion</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('feature-requests.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                            All Requests
                        </a>
                        <a href="{{ route('feature-requests.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Submit Request
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <form method="GET" action="{{ route('feature-requests.roadmap') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-64">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" 
                           name="search" 
                           id="search"
                           value="{{ request('search') }}"
                           placeholder="Search feature requests..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="min-w-48">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category_id" 
                            id="category_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }} ({{ $category->feature_requests_count }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'category_id']))
                        <a href="{{ route('feature-requests.roadmap') }}" 
                           class="ml-2 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Roadmap Columns -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @php
                $statusConfig = [
                    'pending' => ['title' => 'Pending', 'color' => 'gray', 'icon' => 'clock'],
                    'under_review' => ['title' => 'Under Review', 'color' => 'yellow', 'icon' => 'eye'],
                    'in_progress' => ['title' => 'In Progress', 'color' => 'blue', 'icon' => 'play']
                ];
            @endphp

            @foreach($statusConfig as $status => $config)
                <div class="bg-white rounded-lg shadow-sm border">
                    <!-- Column Header -->
                    <div class="p-4 border-b bg-{{ $config['color'] }}-50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-{{ $config['color'] }}-800">
                                {{ $config['title'] }}
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $config['color'] }}-100 text-{{ $config['color'] }}-800">
                                {{ $featureRequests[$status]->count() }}
                            </span>
                        </div>
                    </div>

                    <!-- Feature Requests -->
                    <div class="p-4 space-y-3 min-h-96">
                        @forelse($featureRequests[$status] as $request)
                            <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors cursor-pointer group"
                                 onclick="window.location.href='{{ route('feature-requests.show', $request->slug) }}'">
                                
                                <!-- Title -->
                                <h4 class="font-medium text-gray-900 group-hover:text-blue-600 transition-colors mb-2">
                                    {{ $request->title }}
                                </h4>

                                <!-- Description -->
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                    {{ Str::limit($request->description, 100) }}
                                </p>

                                <!-- Meta Information -->
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <div class="flex items-center space-x-3">
                                        <!-- Votes -->
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l3-3 3 3m0 0l3-3 3 3m-3-3v8"></path>
                                            </svg>
                                            <span>{{ ($request->up_votes ?? 0) - ($request->down_votes ?? 0) }}</span>
                                        </div>

                                        <!-- Comments -->
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                            <span>{{ $request->comment_count ?? 0 }}</span>
                                        </div>
                                    </div>

                                    <!-- Date -->
                                    <span>{{ $request->created_at->diffForHumans() }}</span>
                                </div>

                                <!-- Category -->
                                @if($request->category)
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $request->category->name }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-sm">No {{ strtolower($config['title']) }} requests</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Statistics -->
        <div class="mt-8 bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Roadmap Statistics</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $statistics['total'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Total Requests</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $statistics['in_progress'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">In Progress</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $statistics['completed'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Completed</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-600">{{ $statistics['total_votes'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Total Votes</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
