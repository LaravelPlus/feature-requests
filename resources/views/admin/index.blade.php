@extends('feature-requests::layouts.app')

@section('title', 'Feature Requests Dashboard')
@section('header', 'Feature Requests')
@section('subheader', 'Manage and track feature requests from your users')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="rounded-lg border border-border bg-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Requests</p>
                    <p class="text-2xl font-bold text-foreground">{{ $featureRequests->total() }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                    <i data-lucide="lightbulb" class="h-6 w-6 text-blue-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 font-medium">+12%</span>
                <span class="text-muted-foreground ml-2">from last month</span>
            </div>
        </div>

        <div class="rounded-lg border border-border bg-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Active Votes</p>
                    <p class="text-2xl font-bold text-foreground">{{ $featureRequests->sum('vote_count') }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                    <i data-lucide="thumbs-up" class="h-6 w-6 text-green-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 font-medium">+8%</span>
                <span class="text-muted-foreground ml-2">from last month</span>
            </div>
        </div>

        <div class="rounded-lg border border-border bg-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">In Progress</p>
                    <p class="text-2xl font-bold text-foreground">{{ $featureRequests->where('status', 'in_progress')->count() }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-100">
                    <i data-lucide="clock" class="h-6 w-6 text-yellow-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-yellow-600 font-medium">3 new</span>
                <span class="text-muted-foreground ml-2">this week</span>
            </div>
        </div>

        <div class="rounded-lg border border-border bg-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Completed</p>
                    <p class="text-2xl font-bold text-foreground">{{ $featureRequests->where('status', 'completed')->count() }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                    <i data-lucide="check-circle" class="h-6 w-6 text-green-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 font-medium">+5</span>
                <span class="text-muted-foreground ml-2">this month</span>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex flex-wrap items-center gap-2">
            <!-- Status Filter -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">
                    <i data-lucide="filter" class="mr-2 h-4 w-4"></i>
                    Status
                    <i data-lucide="chevron-down" class="ml-2 h-4 w-4"></i>
                </button>
                <div x-show="open" 
                     @click.away="open = false"
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute top-full left-0 mt-1 w-48 bg-popover border border-border rounded-md shadow-lg py-1 z-50">
                    <a href="{{ request()->fullUrlWithQuery(['status' => '']) }}" 
                       class="flex items-center px-3 py-2 text-sm text-foreground hover:bg-accent">
                        All Statuses
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}" 
                       class="flex items-center px-3 py-2 text-sm text-foreground hover:bg-accent">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full mr-3"></div>
                        Pending
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'in_progress']) }}" 
                       class="flex items-center px-3 py-2 text-sm text-foreground hover:bg-accent">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                        In Progress
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'completed']) }}" 
                       class="flex items-center px-3 py-2 text-sm text-foreground hover:bg-accent">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                        Completed
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}" 
                       class="flex items-center px-3 py-2 text-sm text-foreground hover:bg-accent">
                        <div class="w-2 h-2 bg-red-500 rounded-full mr-3"></div>
                        Rejected
                    </a>
                </div>
            </div>

            <!-- Sort Filter -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">
                    <i data-lucide="arrow-up-down" class="mr-2 h-4 w-4"></i>
                    Sort
                    <i data-lucide="chevron-down" class="ml-2 h-4 w-4"></i>
                </button>
                <div x-show="open" 
                     @click.away="open = false"
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute top-full left-0 mt-1 w-48 bg-popover border border-border rounded-md shadow-lg py-1 z-50">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" 
                       class="flex items-center px-3 py-2 text-sm text-foreground hover:bg-accent">
                        <i data-lucide="calendar" class="mr-2 h-4 w-4"></i>
                        Newest First
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'oldest']) }}" 
                       class="flex items-center px-3 py-2 text-sm text-foreground hover:bg-accent">
                        <i data-lucide="calendar" class="mr-2 h-4 w-4"></i>
                        Oldest First
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'votes']) }}" 
                       class="flex items-center px-3 py-2 text-sm text-foreground hover:bg-accent">
                        <i data-lucide="thumbs-up" class="mr-2 h-4 w-4"></i>
                        Most Voted
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'comments']) }}" 
                       class="flex items-center px-3 py-2 text-sm text-foreground hover:bg-accent">
                        <i data-lucide="message-circle" class="mr-2 h-4 w-4"></i>
                        Most Comments
                    </a>
                </div>
            </div>

            <!-- View Toggle -->
            <div class="flex items-center border border-input rounded-md">
                <button class="p-2 hover:bg-accent rounded-l-md">
                    <i data-lucide="grid-3x3" class="h-4 w-4"></i>
                </button>
                <button class="p-2 hover:bg-accent rounded-r-md border-l border-input">
                    <i data-lucide="list" class="h-4 w-4"></i>
                </button>
            </div>
        </div>

        <a href="{{ route('feature-requests.create') }}" 
           class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4">
            <i data-lucide="plus" class="mr-2 h-4 w-4"></i>
            New Request
        </a>
    </div>

    <!-- Feature Requests Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($featureRequests as $request)
            <div class="group rounded-lg border border-border bg-card hover:shadow-md transition-all duration-200 overflow-hidden">
                <!-- Card Header -->
                <div class="p-6 pb-4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            @if($request->status === 'pending')
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">
                                    <div class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5"></div>
                                    Pending
                                </span>
                            @elseif($request->status === 'in_progress')
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                                    <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-1.5"></div>
                                    In Progress
                                </span>
                            @elseif($request->status === 'completed')
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                    <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></div>
                                    Completed
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                    <div class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></div>
                                    Rejected
                                </span>
                            @endif
                            
                            @if($request->priority === 'high')
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                    <i data-lucide="alert-triangle" class="w-3 h-3 mr-1"></i>
                                    High
                                </span>
                            @elseif($request->priority === 'medium')
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">
                                    <i data-lucide="minus" class="w-3 h-3 mr-1"></i>
                                    Medium
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                    <i data-lucide="chevron-down" class="w-3 h-3 mr-1"></i>
                                    Low
                                </span>
                            @endif
                        </div>
                        
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="p-1 rounded-md hover:bg-accent opacity-0 group-hover:opacity-100 transition-opacity">
                                <i data-lucide="more-vertical" class="h-4 w-4 text-muted-foreground"></i>
                            </button>
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 top-full mt-1 w-48 bg-popover border border-border rounded-md shadow-lg py-1 z-50">
                                <a href="{{ route('feature-requests.show', $request->slug ?? $request->uuid) }}" 
                                   class="flex items-center px-3 py-2 text-sm text-foreground hover:bg-accent">
                                    <i data-lucide="eye" class="mr-2 h-4 w-4"></i>
                                    View Details
                                </a>
                                <a href="{{ route('feature-requests.edit', $request->slug ?? $request->uuid) }}" 
                                   class="flex items-center px-3 py-2 text-sm text-foreground hover:bg-accent">
                                    <i data-lucide="edit" class="mr-2 h-4 w-4"></i>
                                    Edit
                                </a>
                                <div class="border-t border-border my-1"></div>
                                <button class="flex items-center w-full px-3 py-2 text-sm text-destructive hover:bg-accent">
                                    <i data-lucide="trash-2" class="mr-2 h-4 w-4"></i>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-foreground mb-2 line-clamp-2 group-hover:text-primary transition-colors">
                        <a href="{{ route('feature-requests.show', $request->slug ?? $request->uuid) }}">{{ $request->title }}</a>
                    </h3>
                    
                    <p class="text-sm text-muted-foreground line-clamp-3 mb-4">{{ Str::limit($request->description, 120) }}</p>

                    @if($request->category)
                        <div class="flex items-center text-xs text-muted-foreground mb-3">
                            <i data-lucide="tag" class="h-3 w-3 mr-1"></i>
                            {{ $request->category->name }}
                        </div>
                    @endif
                </div>

                <!-- Card Footer -->
                <div class="px-6 py-4 bg-muted/50 border-t border-border">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 text-sm text-muted-foreground">
                            <div class="flex items-center">
                                <i data-lucide="thumbs-up" class="h-4 w-4 mr-1"></i>
                                {{ $request->vote_count }}
                            </div>
                            <div class="flex items-center">
                                <i data-lucide="message-circle" class="h-4 w-4 mr-1"></i>
                                {{ $request->comment_count ?? 0 }}
                            </div>
                            <div class="flex items-center">
                                <i data-lucide="eye" class="h-4 w-4 mr-1"></i>
                                {{ $request->view_count ?? 0 }}
                            </div>
                        </div>
                        
                        <div class="text-xs text-muted-foreground">
                            {{ $request->created_at->diffForHumans() }}
                        </div>
                    </div>
                    
                    @if($request->user)
                        <div class="flex items-center mt-3">
                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-primary text-primary-foreground text-xs font-medium mr-2">
                                {{ substr($request->user->name, 0, 1) }}
                            </div>
                            <span class="text-sm text-muted-foreground">{{ $request->user->name }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-muted mx-auto mb-4">
                        <i data-lucide="lightbulb" class="h-8 w-8 text-muted-foreground"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-foreground mb-2">No feature requests yet</h3>
                    <p class="text-muted-foreground mb-6">Get started by creating your first feature request.</p>
                    <a href="{{ route('feature-requests.create') }}" 
                       class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4">
                        <i data-lucide="plus" class="mr-2 h-4 w-4"></i>
                        Create First Request
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($featureRequests->hasPages())
        <div class="flex items-center justify-between">
            <div class="text-sm text-muted-foreground">
                Showing {{ $featureRequests->firstItem() }} to {{ $featureRequests->lastItem() }} of {{ $featureRequests->total() }} results
            </div>
            <div class="flex items-center space-x-2">
                @if($featureRequests->onFirstPage())
                    <button disabled class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">
                        <i data-lucide="chevron-left" class="h-4 w-4"></i>
                        Previous
                    </button>
                @else
                    <a href="{{ $featureRequests->previousPageUrl() }}" 
                       class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">
                        <i data-lucide="chevron-left" class="h-4 w-4"></i>
                        Previous
                    </a>
                @endif

                @if($featureRequests->hasMorePages())
                    <a href="{{ $featureRequests->nextPageUrl() }}" 
                       class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">
                        Next
                        <i data-lucide="chevron-right" class="h-4 w-4 ml-1"></i>
                    </a>
                @else
                    <button disabled class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">
                        Next
                        <i data-lucide="chevron-right" class="h-4 w-4 ml-1"></i>
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection