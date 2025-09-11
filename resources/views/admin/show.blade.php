@extends('feature-requests::layouts.app')

@section('title', $featureRequest->title)
@section('header', $featureRequest->title)
@section('subheader', 'Feature Request Details')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Request Header -->
            <div class="rounded-lg border border-border bg-card">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            @if($featureRequest->status === 'pending')
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800">
                                    <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                                    Pending Review
                                </span>
                            @elseif($featureRequest->status === 'in_progress')
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                    In Progress
                                </span>
                            @elseif($featureRequest->status === 'completed')
                                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                    Completed
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800">
                                    <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                    Rejected
                                </span>
                            @endif
                            
                            @if($featureRequest->priority === 'high')
                                <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800">
                                    <i data-lucide="alert-triangle" class="w-4 h-4 mr-1"></i>
                                    High Priority
                                </span>
                            @elseif($featureRequest->priority === 'medium')
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800">
                                    <i data-lucide="minus" class="w-4 h-4 mr-1"></i>
                                    Medium Priority
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800">
                                    <i data-lucide="chevron-down" class="w-4 h-4 mr-1"></i>
                                    Low Priority
                                </span>
                            @endif
                        </div>
                        
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="p-2 rounded-md hover:bg-accent">
                                <i data-lucide="more-vertical" class="h-5 w-5 text-muted-foreground"></i>
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
                                <a href="{{ route('feature-requests.edit', $featureRequest->slug ?? $featureRequest->uuid) }}" 
                                   class="flex items-center px-3 py-2 text-sm text-foreground hover:bg-accent">
                                    <i data-lucide="edit" class="mr-2 h-4 w-4"></i>
                                    Edit Request
                                </a>
                                <button class="flex items-center w-full px-3 py-2 text-sm text-destructive hover:bg-accent">
                                    <i data-lucide="trash-2" class="mr-2 h-4 w-4"></i>
                                    Delete Request
                                </button>
                            </div>
                        </div>
                    </div>

                    <h1 class="text-2xl font-bold text-foreground mb-4">{{ $featureRequest->title }}</h1>
                    
                    <div class="prose prose-sm max-w-none text-muted-foreground">
                        {!! nl2br(e($featureRequest->description)) !!}
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="rounded-lg border border-border bg-card">
                <div class="p-6 border-b border-border">
                    <h3 class="text-lg font-semibold text-foreground">Comments ({{ $featureRequest->comment_count ?? 0 }})</h3>
                </div>
                
                <div class="p-6">
                    <!-- Add Comment Form -->
                    @auth
                        <form action="{{ route('feature-requests.comments.store', $featureRequest->slug) }}" method="POST" class="mb-6">
                            @csrf
                            <div class="space-y-3">
                                <textarea name="content" 
                                          rows="3"
                                          placeholder="Share your thoughts on this feature request..."
                                          class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"></textarea>
                                <div class="flex justify-end">
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4">
                                        <i data-lucide="send" class="mr-2 h-4 w-4"></i>
                                        Post Comment
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-6 border border-dashed border-border rounded-lg mb-6">
                            <i data-lucide="log-in" class="h-8 w-8 text-muted-foreground mx-auto mb-2"></i>
                            <p class="text-sm text-muted-foreground mb-3">Please sign in to leave a comment</p>
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4">
                                Sign In
                            </a>
                        </div>
                    @endauth

                    <!-- Comments List -->
                    <div class="space-y-4">
                        @forelse($featureRequest->comments as $comment)
                            <div class="flex space-x-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-primary-foreground text-sm font-medium">
                                    {{ substr($comment->user->name, 0, 1) }}
                                </div>
                                <div class="flex-1 space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-foreground">{{ $comment->user->name }}</span>
                                        <span class="text-xs text-muted-foreground">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-sm text-foreground">
                                        {!! nl2br(e($comment->content)) !!}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i data-lucide="message-circle" class="h-8 w-8 text-muted-foreground mx-auto mb-2"></i>
                                <p class="text-sm text-muted-foreground">No comments yet. Be the first to share your thoughts!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Vote Section -->
            <div class="rounded-lg border border-border bg-card">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Vote for this feature</h3>
                    
                    @auth
                        <div class="text-center">
                            <div class="mb-4">
                                <div class="text-3xl font-bold text-foreground mb-1">{{ $featureRequest->vote_count }}</div>
                                <div class="text-sm text-muted-foreground">votes</div>
                            </div>
                            
                            <form action="{{ route('feature-requests.vote', $featureRequest->slug) }}" method="POST" class="space-y-3">
                                @csrf
                                <button type="submit" 
                                        class="w-full inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background bg-primary text-primary-foreground hover:bg-primary/90 h-10">
                                    <i data-lucide="thumbs-up" class="mr-2 h-4 w-4"></i>
                                    Vote for this feature
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="text-center">
                            <div class="mb-4">
                                <div class="text-3xl font-bold text-foreground mb-1">{{ $featureRequest->vote_count }}</div>
                                <div class="text-sm text-muted-foreground">votes</div>
                            </div>
                            <a href="{{ route('login') }}" 
                               class="w-full inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background bg-primary text-primary-foreground hover:bg-primary/90 h-10">
                                <i data-lucide="log-in" class="mr-2 h-4 w-4"></i>
                                Sign in to vote
                            </a>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Request Info -->
            <div class="rounded-lg border border-border bg-card">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Request Information</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-muted-foreground">Status</span>
                            <span class="text-sm font-medium text-foreground capitalize">{{ $featureRequest->status }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-muted-foreground">Priority</span>
                            <span class="text-sm font-medium text-foreground capitalize">{{ $featureRequest->priority }}</span>
                        </div>
                        
                        @if($featureRequest->category)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-muted-foreground">Category</span>
                                <span class="text-sm font-medium text-foreground">{{ $featureRequest->category->name }}</span>
                            </div>
                        @endif
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-muted-foreground">Created</span>
                            <span class="text-sm font-medium text-foreground">{{ $featureRequest->created_at->format('M j, Y') }}</span>
                        </div>
                        
                        @if($featureRequest->due_date)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-muted-foreground">Target Date</span>
                                <span class="text-sm font-medium text-foreground">{{ \Carbon\Carbon::parse($featureRequest->due_date)->format('M j, Y') }}</span>
                            </div>
                        @endif
                        
                        @if($featureRequest->estimated_effort)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-muted-foreground">Effort</span>
                                <span class="text-sm font-medium text-foreground capitalize">{{ $featureRequest->estimated_effort }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Author Info -->
            @if($featureRequest->user)
                <div class="rounded-lg border border-border bg-card">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-foreground mb-4">Requested by</h3>
                        
                        <div class="flex items-center space-x-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-primary-foreground text-sm font-medium">
                                {{ substr($featureRequest->user->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="text-sm font-medium text-foreground">{{ $featureRequest->user->name }}</div>
                                <div class="text-xs text-muted-foreground">{{ $featureRequest->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tags -->
            @if($featureRequest->tags)
                <div class="rounded-lg border border-border bg-card">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-foreground mb-4">Tags</h3>
                        
                        <div class="flex flex-wrap gap-2">
                            @foreach(explode(',', $featureRequest->tags) as $tag)
                                <span class="inline-flex items-center rounded-md bg-secondary px-2.5 py-0.5 text-xs font-medium text-secondary-foreground">
                                    {{ trim($tag) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="rounded-lg border border-border bg-card">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Actions</h3>
                    
                    <div class="space-y-2">
                        <a href="{{ route('feature-requests.edit', $featureRequest->slug) }}" 
                           class="w-full inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">
                            <i data-lucide="edit" class="mr-2 h-4 w-4"></i>
                            Edit Request
                        </a>
                        
                        <button class="w-full inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">
                            <i data-lucide="share" class="mr-2 h-4 w-4"></i>
                            Share
                        </button>
                        
                        <button class="w-full inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">
                            <i data-lucide="flag" class="mr-2 h-4 w-4"></i>
                            Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Auto-resize comment textarea
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
