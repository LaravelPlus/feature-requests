@extends('feature-requests::layouts.public')

@section('title', $featureRequest->title)
@section('header', $featureRequest->title)
@section('subheader', 'Feature Request Details')

@section('content')
<!-- Toast Notifications -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

<!-- Copy Modal -->
<div id="copy-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Copy to Clipboard</h3>
                <button onclick="closeCopyModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2">Select and copy the text below:</p>
                <textarea id="copy-text" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm font-mono bg-gray-50 resize-none" rows="4"></textarea>
            </div>
            <div class="flex justify-end space-x-2">
                <button onclick="closeCopyModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                    Close
                </button>
                <button onclick="selectAndCopy()" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    Select All & Copy
                </button>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header with FR ID -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $featureRequest->title }}</h1>
                <div class="flex items-center space-x-4 mt-2">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-500">FR ID:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 font-mono">
                            {{ $featureRequest->uuid }}
                        </span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-500">Slug:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 font-mono">
                            {{ $featureRequest->slug }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="copyToClipboard('{{ $featureRequest->uuid }}')" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Copy UUID
                </button>
                <button onclick="copyToClipboard('{{ url()->current() }}')" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                    Copy Link
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Request Header -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-8">
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            @if($featureRequest->status === 'pending')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                                    Pending Review
                                </span>
                            @elseif($featureRequest->status === 'in_progress')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                    In Progress
                                </span>
                            @elseif($featureRequest->status === 'completed')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                    Completed
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                    Rejected
                                </span>
                            @endif
                            
                            @if($featureRequest->is_featured)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    Featured
                                </span>
                            @endif
                        </div>
                    </div>

                    
                    <div class="prose prose-lg max-w-none text-gray-600">
                        {!! nl2br(e($featureRequest->description)) !!}
                        
                        @if($featureRequest->additional_info)
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h4 class="text-lg font-semibold text-gray-900 mb-3">Additional Information</h4>
                                <div class="text-gray-600">
                                    {!! nl2br(e($featureRequest->additional_info)) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Comments ({{ $featureRequest->comment_count ?? 0 }})</h3>
                        <div class="flex items-center space-x-2 text-sm text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <span>Join the discussion</span>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <!-- Add Comment Form -->
                    <div class="mb-8">
                        <form action="{{ route('feature-requests.comments.store', $featureRequest->slug ?? $featureRequest->uuid) }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div class="flex items-start space-x-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-blue-500 to-purple-600 text-white text-sm font-medium flex-shrink-0">
                                        @auth
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        @endauth
                                    </div>
                                    <div class="flex-1">
                                        <textarea name="content" 
                                                  rows="4"
                                                  placeholder="Share your thoughts on this feature request..."
                                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none transition-all duration-200 placeholder-gray-400"></textarea>
                                        <div class="flex items-center justify-between mt-3">
                                            <p class="text-xs text-gray-500">Press Ctrl+Enter to submit</p>
                                            <button type="submit" 
                                                    class="px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                                Post Comment
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Comments List -->
                    <div class="space-y-6">
                        @forelse($featureRequest->comments as $comment)
                            <div class="group">
                                <div class="flex space-x-4">
                                    <!-- Avatar -->
                                    <div class="flex-shrink-0">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-r from-blue-500 to-purple-600 text-white text-sm font-medium shadow-sm">
                                            {{ substr($comment->user->name, 0, 1) }}
                                        </div>
                                    </div>
                                    
                                    <!-- Comment Content -->
                                    <div class="flex-1 min-w-0">
                                        <!-- Comment Header -->
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center space-x-3">
                                                <span class="text-sm font-semibold text-gray-900">{{ $comment->user->name }}</span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Member
                                                </span>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs text-gray-500">{{ $comment->created_at->format('M j, Y') }}</span>
                                                <span class="text-xs text-gray-400">•</span>
                                                <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Comment Body -->
                                        <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                                            {!! nl2br(e($comment->content)) !!}
                                        </div>
                                        
                                        <!-- Comment Actions -->
                                        <div class="flex items-center space-x-4 mt-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                            <button class="flex items-center space-x-1 text-xs text-gray-500 hover:text-gray-700 transition-colors">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                </svg>
                                                <span>Like</span>
                                            </button>
                                            <button class="flex items-center space-x-1 text-xs text-gray-500 hover:text-gray-700 transition-colors">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                </svg>
                                                <span>Reply</span>
                                            </button>
                                            <button class="flex items-center space-x-1 text-xs text-gray-500 hover:text-gray-700 transition-colors">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                                </svg>
                                                <span>Share</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-medium text-gray-900 mb-2">No comments yet</h4>
                                <p class="text-sm text-gray-500 mb-4">Be the first to share your thoughts on this feature request!</p>
                                <div class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Start the conversation
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Vote Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Vote for this feature</h3>
                    
                    <div class="text-center">
                        <div class="mb-4">
                            <div class="text-3xl font-bold text-gray-900 mb-1">{{ ($featureRequest->up_votes ?? 0) - ($featureRequest->down_votes ?? 0) }}</div>
                            <div class="text-sm text-gray-500">net votes</div>
                            <div class="text-xs text-gray-400 mt-1">
                                {{ $featureRequest->up_votes ?? 0 }} up • {{ $featureRequest->down_votes ?? 0 }} down
                            </div>
                        </div>
                        
                        @if(isset($featureRequest->user_has_voted) && $featureRequest->user_has_voted)
                            <!-- Already Voted - Show Current Vote Status -->
                            <div class="flex items-center justify-center space-x-4">
                                <!-- Thumbs Up Button (Disabled) -->
                                <div class="p-3 rounded-full 
                                    @if($featureRequest->user_vote_type === 'up') bg-orange-100 text-orange-500
                                    @else bg-gray-100 text-gray-400
                                    @endif">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M7.493 18.75c-.425 0-.82-.236-.975-.632A7.48 7.48 0 016 15.375c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75 2.25 2.25 0 012.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558-.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H14.23c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23h-.777zM2.331 10.977a11.969 11.969 0 00-.831 4.398 12 12 0 00.52 3.507c.26.85 1.084 1.368 1.973 1.368H4.9c.445 0 .72-.498.523-.898a8.963 8.963 0 01-.924-3.977c0-1.708.476-3.305 1.302-4.666.245-.403-.028-.959-.5-.959H4.25c-.833 0-1.612.453-1.918 1.227z"/>
                                    </svg>
                                </div>

                                <!-- Vote Count -->
                                <div class="text-2xl font-bold text-gray-700 min-w-[40px] text-center">
                                    {{ ($featureRequest->up_votes ?? 0) - ($featureRequest->down_votes ?? 0) }}
                                </div>

                                <!-- Thumbs Down Button (Disabled) -->
                                <div class="p-3 rounded-full 
                                    @if($featureRequest->user_vote_type === 'down') bg-blue-100 text-blue-500
                                    @else bg-gray-100 text-gray-400
                                    @endif">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M15.73 5.25h1.035A7.465 7.465 0 0118 9.375a7.465 7.465 0 01-1.235 4.125h-.148c-.806 0-1.355.673-1.355 1.456 0 .31.12.616.33.835.806.875 1.309 1.76 1.309 2.889 0 .563-.115 1.109-.33 1.59-.215.48-.53.923-.93 1.309-.4.386-.885.69-1.44.923-.555.233-1.17.35-1.8.35H9.75a.75.75 0 01-.75-.75V3.75a.75.75 0 01.75-.75h.148c.806 0 1.355-.673 1.355-1.456 0-.31-.12-.616-.33-.835-.806-.875-1.309-1.76-1.309-2.889 0-.563.115-1.109.33-1.59-.215-.48-.53-.923-.93-1.309-.4-.386-.885-.69-1.44-.923-.555-.233-1.17-.35-1.8-.35H15.73zM21.669 13.023a11.969 11.969 0 00.831-4.398 12 12 0 00-.52-3.507c-.26-.85-1.084-1.368-1.973-1.368H19.1c-.445 0-.72.498-.523.898a8.963 8.963 0 01.924 3.977c0 1.708-.476 3.305-1.302 4.666-.245.403.028.959.5.959h.148c.833 0 1.612-.453 1.918-1.227z"/>
                                    </svg>
                                </div>
                            </div>
                        @else
                            <!-- Inline Vote Buttons -->
                            <div class="flex items-center justify-center space-x-4">
                                <!-- Thumbs Up Button -->
                                <form action="{{ route('feature-requests.vote', $featureRequest->slug ?? $featureRequest->uuid) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="vote_type" value="up">
                                    <button type="submit" 
                                            class="p-3 rounded-full hover:bg-orange-100 transition-colors group">
                                        <svg class="w-6 h-6 text-gray-400 group-hover:text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M7.493 18.75c-.425 0-.82-.236-.975-.632A7.48 7.48 0 016 15.375c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75 2.25 2.25 0 012.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558-.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H14.23c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23h-.777zM2.331 10.977a11.969 11.969 0 00-.831 4.398 12 12 0 00.52 3.507c.26.85 1.084 1.368 1.973 1.368H4.9c.445 0 .72-.498.523-.898a8.963 8.963 0 01-.924-3.977c0-1.708.476-3.305 1.302-4.666.245-.403-.028-.959-.5-.959H4.25c-.833 0-1.612.453-1.918 1.227z"/>
                                        </svg>
                                    </button>
                                </form>
                                
                                <!-- Vote Count -->
                                <div class="text-2xl font-bold text-gray-700 min-w-[40px] text-center">
                                    {{ ($featureRequest->up_votes ?? 0) - ($featureRequest->down_votes ?? 0) }}
                                </div>
                                
                                <!-- Thumbs Down Button -->
                                <form action="{{ route('feature-requests.vote', $featureRequest->slug ?? $featureRequest->uuid) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="vote_type" value="down">
                                    <button type="submit" 
                                            class="p-3 rounded-full hover:bg-blue-100 transition-colors group">
                                        <svg class="w-6 h-6 text-gray-400 group-hover:text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M15.73 5.25h1.035A7.465 7.465 0 0118 9.375a7.465 7.465 0 01-1.235 4.125h-.148c-.806 0-1.355.673-1.355 1.456 0 .31.12.616.33.835.806.875 1.309 1.76 1.309 2.889 0 .563-.115 1.109-.33 1.59-.215.48-.53.923-.93 1.309-.4.386-.885.69-1.44.923-.555.233-1.17.35-1.8.35H9.75a.75.75 0 01-.75-.75V3.75a.75.75 0 01.75-.75h.148c.806 0 1.355-.673 1.355-1.456 0-.31-.12-.616-.33-.835-.806-.875-1.309-1.76-1.309-2.889 0-.563.115-1.109.33-1.59-.215-.48-.53-.923-.93-1.309-.4-.386-.885-.69-1.44-.923-.555-.233-1.17-.35-1.8-.35H15.73zM21.669 13.023a11.969 11.969 0 00.831-4.398 12 12 0 00-.52-3.507c-.26-.85-1.084-1.368-1.973-1.368H19.1c-.445 0-.72.498-.523.898a8.963 8.963 0 01.924 3.977c0 1.708-.476 3.305-1.302 4.666-.245.403.028.959.5.959h.148c.833 0 1.612-.453 1.918-1.227z"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Request Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Information</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Status</span>
                            <span class="text-sm font-medium text-gray-900 capitalize">{{ $featureRequest->status }}</span>
                        </div>
                        
                        @if($featureRequest->category)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Category</span>
                                <span class="text-sm font-medium text-gray-900">{{ $featureRequest->category->name }}</span>
                            </div>
                        @endif
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Created</span>
                            <span class="text-sm font-medium text-gray-900">{{ $featureRequest->created_at->format('M j, Y') }}</span>
                        </div>
                        
                        @if($featureRequest->due_date)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Target Date</span>
                                <span class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($featureRequest->due_date)->format('M j, Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Author Info -->
            @if($featureRequest->user)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Requested by</h3>
                        
                        <div class="flex items-center space-x-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-white text-sm font-medium">
                                {{ substr($featureRequest->user->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $featureRequest->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $featureRequest->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tags -->
            @if($featureRequest->tags)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Tags</h3>
                        
                        <div class="flex flex-wrap gap-2">
                            @foreach(explode(',', $featureRequest->tags) as $tag)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ trim($tag) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                    
                    <div class="space-y-2">
                        <button onclick="shareRequest()" 
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                            Share
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function shareRequest() {
        if (navigator.share) {
            navigator.share({
                title: '{{ $featureRequest->title }}',
                text: '{{ Str::limit($featureRequest->description, 100) }}',
                url: window.location.href
            });
        } else {
            // Fallback: copy to clipboard
            copyToClipboard(window.location.href, 'Link copied!');
        }
    }

    function copyToClipboard(text, successMessage = 'Copied!') {
        // Check if clipboard API is available and we're in a secure context
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(function() {
                showToast(successMessage, 'success');
            }).catch(function(err) {
                console.error('Clipboard API failed: ', err);
                fallbackCopyToClipboard(text, successMessage);
            });
        } else {
            // Use fallback method
            fallbackCopyToClipboard(text, successMessage);
        }
    }

    function fallbackCopyToClipboard(text, successMessage) {
        // Create a temporary textarea element
        const textArea = document.createElement('textarea');
        textArea.value = text;
        
        // Make it invisible
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        textArea.style.opacity = '0';
        textArea.style.pointerEvents = 'none';
        
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            // Try to copy using the old execCommand method
            const successful = document.execCommand('copy');
            if (successful) {
                showToast(successMessage, 'success');
            } else {
                // Show modal for manual copy
                showCopyModal(text);
            }
        } catch (err) {
            console.error('Fallback copy failed: ', err);
            // Show modal for manual copy
            showCopyModal(text);
        } finally {
            // Clean up
            document.body.removeChild(textArea);
        }
    }

    function showCopyModal(text) {
        const modal = document.getElementById('copy-modal');
        const textarea = document.getElementById('copy-text');
        textarea.value = text;
        modal.classList.remove('hidden');
        // Focus on the textarea after a short delay
        setTimeout(() => {
            textarea.focus();
            textarea.select();
        }, 100);
    }

    function closeCopyModal() {
        const modal = document.getElementById('copy-modal');
        modal.classList.add('hidden');
    }

    function selectAndCopy() {
        const textarea = document.getElementById('copy-text');
        textarea.focus();
        textarea.select();
        
        try {
            const successful = document.execCommand('copy');
            if (successful) {
                showToast('Copied!', 'success');
                closeCopyModal();
            } else {
                showToast('Please use Ctrl+C to copy', 'error');
            }
        } catch (err) {
            showToast('Please use Ctrl+C to copy', 'error');
        }
    }

    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        
        const bgColor = type === 'success' ? 'bg-green-50' : 'bg-red-50';
        const textColor = type === 'success' ? 'text-green-800' : 'text-red-800';
        const borderColor = type === 'success' ? 'border-green-200' : 'border-red-200';
        const iconColor = type === 'success' ? 'text-green-400' : 'text-red-400';
        const icon = type === 'success' 
            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
        
        toast.className = `${bgColor} ${borderColor} ${textColor} border rounded-lg p-4 shadow-lg max-w-sm transform transition-all duration-300 ease-in-out translate-x-full opacity-0`;
        toast.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${icon}
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" class="inline-flex ${textColor} hover:opacity-75">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        container.appendChild(toast);
        
        // Trigger animation
        setTimeout(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        }, 100);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }

    // Auto-resize comment textarea and keyboard shortcuts
    document.addEventListener('DOMContentLoaded', function() {
        const commentTextarea = document.querySelector('textarea[name="content"]');
        const commentForm = document.querySelector('form[action*="comments.store"]');
        
        if (commentTextarea) {
            // Auto-resize textarea
            commentTextarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
            
            // Ctrl+Enter to submit
            commentTextarea.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'Enter') {
                    e.preventDefault();
                    if (commentForm) {
                        commentForm.submit();
                    }
                }
            });
            
            // Focus on textarea when clicking "Start the conversation"
            const startConversationBtn = document.querySelector('[class*="bg-blue-50"]');
            if (startConversationBtn) {
                startConversationBtn.addEventListener('click', function() {
                    commentTextarea.focus();
                    commentTextarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
                });
            }
        }
        
        // Add smooth scrolling to comments section
        const commentsSection = document.querySelector('[class*="Comments"]');
        if (commentsSection) {
            // Add a subtle animation when comments load
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === 1) { // Element node
                                node.style.opacity = '0';
                                node.style.transform = 'translateY(10px)';
                                node.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                                
                                setTimeout(() => {
                                    node.style.opacity = '1';
                                    node.style.transform = 'translateY(0)';
                                }, 100);
                            }
                        });
                    }
                });
            });
            
            observer.observe(commentsSection, { childList: true, subtree: true });
        }
    });
</script>
@endsection
