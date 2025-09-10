@extends('feature-requests::layouts.public')

@section('title', 'Feature Requests')
@section('header', 'Feature Requests')
@section('subheader', 'Share your ideas and vote on features you\'d like to see')

@section('content')
<div class="space-y-6">
    <!-- Header with Search and Submit Button -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Feature Requests</h1>
            <p class="text-gray-600 mt-1">Share your ideas and vote on features you'd like to see</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3">
            <!-- Search -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <form method="GET" action="{{ route('feature-requests.index') }}" class="flex">
                    <input type="text" 
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search requests..." 
                           class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 transition-colors">
                        Search
                    </button>
                </form>
            </div>
            
                <!-- Submit Button -->
                <a href="{{ route('feature-requests.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Submit Request
                </a>
        </div>
        </div>
    </div>

    <!-- Feature Requests Grid - In Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="space-y-6">
            @forelse($featureRequests as $request)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-blue-300 transition-all duration-200 overflow-hidden group cursor-pointer"
                     onclick="window.location.href='{{ route('feature-requests.show', $request->slug) }}'">
                    <div class="flex">
                        <!-- Left Side - Voting Section -->
                        <div class="flex flex-col items-center justify-center p-4 border-r border-gray-100 bg-gray-50">
                            @if(isset($request->user_has_voted) && $request->user_has_voted)
                                <!-- Already Voted - Show Current Vote Status -->
                                <div class="flex items-center space-x-2">
                                    <!-- Thumbs Up Button (Disabled) -->
                                    <div class="p-2 rounded-full 
                                        @if($request->user_vote_type === 'up') bg-orange-100 text-orange-500
                                        @else bg-gray-100 text-gray-400
                                        @endif">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M7.493 18.75c-.425 0-.82-.236-.975-.632A7.48 7.48 0 016 15.375c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75 2.25 2.25 0 012.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558-.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H14.23c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23h-.777zM2.331 10.977a11.969 11.969 0 00-.831 4.398 12 12 0 00.52 3.507c.26.85 1.084 1.368 1.973 1.368H4.9c.445 0 .72-.498.523-.898a8.963 8.963 0 01-.924-3.977c0-1.708.476-3.305 1.302-4.666.245-.403-.028-.959-.5-.959H4.25c-.833 0-1.612.453-1.918 1.227z"/>
                                        </svg>
                                    </div>

                                    <!-- Vote Count -->
                                    <div class="text-lg font-bold text-gray-700 min-w-[30px] text-center">
                                        {{ ($request->up_votes ?? 0) - ($request->down_votes ?? 0) }}
                                    </div>

                                    <!-- Thumbs Down Button (Disabled) -->
                                    <div class="p-2 rounded-full 
                                        @if($request->user_vote_type === 'down') bg-blue-100 text-blue-500
                                        @else bg-gray-100 text-gray-400
                                        @endif">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M7.493 18.75c-.425 0-.82-.236-.975-.632A7.48 7.48 0 016 15.375c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75 2.25 2.25 0 012.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558-.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H14.23c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23h-.777zM2.331 10.977a11.969 11.969 0 00-.831 4.398 12 12 0 00.52 3.507c.26.85 1.084 1.368 1.973 1.368H4.9c.445 0 .72-.498.523-.898a8.963 8.963 0 01-.924-3.977c0-1.708.476-3.305 1.302-4.666.245-.403-.028-.959-.5-.959H4.25c-.833 0-1.612.453-1.918 1.227z"/>
                                        </svg>
                                    </div>
                                </div>
                            @else
                                <!-- Inline Vote Buttons -->
                                <div class="flex items-center space-x-2">
                                    <!-- Thumbs Up Button -->
                                    <form action="{{ route('feature-requests.vote', $request->slug) }}" method="POST" onclick="event.stopPropagation()">
                                        @csrf
                                        <input type="hidden" name="vote_type" value="up">
                                        <button type="submit" 
                                                class="p-2 rounded-full hover:bg-orange-100 transition-colors group">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-gray-400 group-hover:text-orange-500">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M7 11v8a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1v-7a1 1 0 0 1 1 -1h3a4 4 0 0 0 4 -4v-1a2 2 0 0 1 4 0v5h3a2 2 0 0 1 2 2l-1 5a2 3 0 0 1 -2 2h-7a3 3 0 0 1 -3 -3" />
                                        </svg>
                                        </button>
                                    </form>

                                    <!-- Vote Count -->
                                    <div class="text-lg font-bold text-gray-700 min-w-[30px] text-center">
                                        {{ ($request->up_votes ?? 0) - ($request->down_votes ?? 0) }}
                                    </div>

                                    <!-- Thumbs Down Button -->
                                    <form action="{{ route('feature-requests.vote', $request->slug) }}" method="POST" onclick="event.stopPropagation()">
                                        @csrf
                                        <input type="hidden" name="vote_type" value="down">
                                        <button type="submit" 
                                                class="p-2 rounded-full hover:bg-blue-100 transition-colors group">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-gray-400 group-hover:text-blue-500">
                                                <path d="M15.73 5.25h1.035A7.465 7.465 0 0118 9.375a7.465 7.465 0 01-1.235 4.125h-.148c-.806 0-1.355.673-1.355 1.456 0 .31.12.616.33.835.806.875 1.309 1.76 1.309 2.889 0 .563-.115 1.109-.33 1.59-.215.48-.53.923-.93 1.309-.4.386-.885.69-1.44.923-.555.233-1.17.35-1.8.35H9.75a.75.75 0 01-.75-.75V3.75a.75.75 0 01.75-.75h.148c.806 0 1.355-.673 1.355-1.456 0-.31-.12-.616-.33-.835-.806-.875-1.309-1.76-1.309-2.889 0-.563.115-1.109.33-1.59.215-.48.53-.923.93-1.309.4-.386.885-.69 1.44-.923.555-.233 1.17-.35 1.8-.35H15.73zM21.669 13.023a11.969 11.969 0 00.831-4.398 12 12 0 00-.52-3.507c-.26-.85-1.084-1.368-1.973-1.368H19.1c-.445 0-.72.498-.523.898a8.963 8.963 0 01.924 3.977c0 1.708-.476 3.305-1.302 4.666-.245.403.028.959.5.959h.148c.833 0 1.612-.453 1.918-1.227z"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        <!-- Right Side - Content -->
                        <div class="flex-1 p-6">

                            <!-- Title and Description -->
                            <div class="mb-4">
                                <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                                    {{ $request->title }}
                                </h3>
                                
                                <p class="text-gray-600 line-clamp-2">{{ Str::limit($request->description, 150) }}</p>
                            </div>

                            <!-- Meta Info -->
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <div class="flex items-center space-x-6">
                                    <!-- Status Badge -->
                                    @if($request->status === 'pending')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <div class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5"></div>
                                            Pending
                                        </span>
                                    @elseif($request->status === 'in_progress')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-1.5"></div>
                                            In Progress
                                        </span>
                                    @elseif($request->status === 'completed')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></div>
                                            Completed
                                        </span>
                                    @elseif($request->status === 'rejected')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <div class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></div>
                                            Rejected
                                        </span>
                                    @endif

                                    @if($request->category)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                            {{ $request->category->name }}
                                        </div>
                                    @endif

                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                        {{ $request->comment_count ?? 0 }} comments
                                    </div>

                                    @if($request->user)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            {{ $request->user->name }}
                                        </div>
                                    @endif

                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $request->created_at->diffForHumans() }}
                    </div>

                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                        </svg>
                        {{ ($request->up_votes ?? 0) + ($request->down_votes ?? 0) }} votes
                    </div>
                                </div>

                                <div class="flex items-center text-blue-600 group-hover:text-blue-700">
                                    <span class="text-sm font-medium">View Details</span>
                                    <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 mx-auto mb-4">
                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No feature requests found</h3>
                    <p class="text-gray-500 mb-6">Be the first to submit a feature request!</p>
                    <a href="{{ route('feature-requests.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Submit First Request
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if($featureRequests->hasPages())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
            <div class="text-sm text-gray-500">
                Showing {{ $featureRequests->firstItem() }} to {{ $featureRequests->lastItem() }} of {{ $featureRequests->total() }} results
            </div>
            <div class="flex items-center space-x-2">
                @if($featureRequests->onFirstPage())
                    <button disabled class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-400 bg-gray-50">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Previous
                    </button>
                @else
                    <a href="{{ $featureRequests->previousPageUrl() }}" 
                       class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Previous
                    </a>
                @endif

                @if($featureRequests->hasMorePages())
                    <a href="{{ $featureRequests->nextPageUrl() }}" 
                       class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Next
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                @else
                    <button disabled class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-400 bg-gray-50">
                        Next
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                @endif
            </div>
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
