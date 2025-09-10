@extends('feature-requests::layouts.public')

@section('title', $featureRequest->title)
@section('header', $featureRequest->title)
@section('subheader', 'Feature Request Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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

                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $featureRequest->title }}</h1>
                    
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
                    <h3 class="text-lg font-semibold text-gray-900">Comments ({{ $featureRequest->comment_count ?? 0 }})</h3>
                </div>
                
                <div class="p-6">
                    <!-- Add Comment Form -->
                    <form action="{{ route('feature-requests.comments.store', $featureRequest->slug) }}" method="POST" class="mb-6">
                        @csrf
                        <div class="space-y-3">
                            <textarea name="content" 
                                      rows="3"
                                      placeholder="Share your thoughts on this feature request..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                            <div class="flex justify-end">
                                <button type="submit" 
                                        class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                    Post Comment
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Comments List -->
                    <div class="space-y-4">
                        @forelse($featureRequest->comments as $comment)
                            <div class="flex space-x-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-white text-sm font-medium">
                                    {{ substr($comment->user->name, 0, 1) }}
                                </div>
                                <div class="flex-1 space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        {!! nl2br(e($comment->content)) !!}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="h-8 w-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <p class="text-sm text-gray-500">No comments yet. Be the first to share your thoughts!</p>
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
                                <form action="{{ route('feature-requests.vote', $featureRequest->slug) }}" method="POST">
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
                                <form action="{{ route('feature-requests.vote', $featureRequest->slug) }}" method="POST">
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
            navigator.clipboard.writeText(window.location.href).then(function() {
                alert('Link copied to clipboard!');
            });
        }
    }

    // Auto-resize comment textarea
    document.addEventListener('DOMContentLoaded', function() {
        const commentTextarea = document.querySelector('textarea[name="content"]');
        if (commentTextarea) {
            commentTextarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        }
    });
</script>
@endsection
