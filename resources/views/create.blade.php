@extends('feature-requests::layouts.app')

@section('title', 'Create Feature Request')
@section('header', 'Create New Request')
@section('subheader', 'Submit a new feature request for consideration')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('feature-requests.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Main Content Card -->
        <div class="rounded-lg border border-border bg-card">
            <div class="p-6 border-b border-border">
                <h3 class="text-lg font-semibold text-foreground">Request Details</h3>
                <p class="text-sm text-muted-foreground mt-1">Provide detailed information about your feature request</p>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Title -->
                <div class="space-y-2">
                    <label for="title" class="text-sm font-medium text-foreground">Title *</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title') }}"
                           placeholder="Brief, descriptive title for your feature request"
                           class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 @error('title') border-destructive @enderror">
                    @error('title')
                        <p class="text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <label for="description" class="text-sm font-medium text-foreground">Description *</label>
                    <textarea id="description" 
                              name="description" 
                              rows="6"
                              placeholder="Describe your feature request in detail. What problem does it solve? How should it work?"
                              class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 @error('description') border-destructive @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div class="space-y-2">
                    <label for="category_id" class="text-sm font-medium text-foreground">Category</label>
                    <select id="category_id" 
                            name="category_id"
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 @error('category_id') border-destructive @enderror">
                        <option value="">Select a category (optional)</option>
                        @foreach(\LaravelPlus\FeatureRequests\Models\Category::all() as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priority -->
                <div class="space-y-2">
                    <label for="priority" class="text-sm font-medium text-foreground">Priority</label>
                    <select id="priority" 
                            name="priority"
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 @error('priority') border-destructive @enderror">
                        <option value="low" {{ old('priority', 'low') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                    @error('priority')
                        <p class="text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tags -->
                <div class="space-y-2">
                    <label for="tags" class="text-sm font-medium text-foreground">Tags</label>
                    <input type="text" 
                           id="tags" 
                           name="tags" 
                           value="{{ old('tags') }}"
                           placeholder="Enter tags separated by commas (e.g., ui, mobile, api)"
                           class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 @error('tags') border-destructive @enderror">
                    <p class="text-xs text-muted-foreground">Add relevant tags to help categorize your request</p>
                    @error('tags')
                        <p class="text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Additional Options Card -->
        <div class="rounded-lg border border-border bg-card">
            <div class="p-6 border-b border-border">
                <h3 class="text-lg font-semibold text-foreground">Additional Options</h3>
                <p class="text-sm text-muted-foreground mt-1">Configure visibility and other settings</p>
            </div>
            
            <div class="p-6 space-y-4">
                <!-- Visibility -->
                <div class="flex items-center justify-between">
                    <div class="space-y-0.5">
                        <label for="is_public" class="text-sm font-medium text-foreground">Public Request</label>
                        <p class="text-xs text-muted-foreground">Allow other users to view and vote on this request</p>
                    </div>
                    <div class="relative inline-flex h-6 w-11 items-center rounded-full bg-input transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">
                        <input type="checkbox" 
                               id="is_public" 
                               name="is_public" 
                               value="1"
                               {{ old('is_public', true) ? 'checked' : '' }}
                               class="sr-only">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-background transition-transform translate-x-6"></span>
                    </div>
                </div>

                <!-- Featured -->
                <div class="flex items-center justify-between">
                    <div class="space-y-0.5">
                        <label for="is_featured" class="text-sm font-medium text-foreground">Featured Request</label>
                        <p class="text-xs text-muted-foreground">Highlight this request on the homepage</p>
                    </div>
                    <div class="relative inline-flex h-6 w-11 items-center rounded-full bg-input transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">
                        <input type="checkbox" 
                               id="is_featured" 
                               name="is_featured" 
                               value="1"
                               {{ old('is_featured') ? 'checked' : '' }}
                               class="sr-only">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-background transition-transform translate-x-6"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estimated Effort -->
        <div class="rounded-lg border border-border bg-card">
            <div class="p-6 border-b border-border">
                <h3 class="text-lg font-semibold text-foreground">Development Information</h3>
                <p class="text-sm text-muted-foreground mt-1">Help estimate the effort required for this feature</p>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="space-y-2">
                    <label for="estimated_effort" class="text-sm font-medium text-foreground">Estimated Effort</label>
                    <select id="estimated_effort" 
                            name="estimated_effort"
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 @error('estimated_effort') border-destructive @enderror">
                        <option value="">Select effort level (optional)</option>
                        <option value="small" {{ old('estimated_effort') == 'small' ? 'selected' : '' }}>Small (1-3 days)</option>
                        <option value="medium" {{ old('estimated_effort') == 'medium' ? 'selected' : '' }}>Medium (1-2 weeks)</option>
                        <option value="large" {{ old('estimated_effort') == 'large' ? 'selected' : '' }}>Large (2-4 weeks)</option>
                        <option value="xlarge" {{ old('estimated_effort') == 'xlarge' ? 'selected' : '' }}>Extra Large (1+ months)</option>
                    </select>
                    @error('estimated_effort')
                        <p class="text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="due_date" class="text-sm font-medium text-foreground">Target Date</label>
                    <input type="date" 
                           id="due_date" 
                           name="due_date" 
                           value="{{ old('due_date') }}"
                           class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 @error('due_date') border-destructive @enderror">
                    @error('due_date')
                        <p class="text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-between pt-6">
            <a href="{{ route('feature-requests.index') }}" 
               class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4">
                <i data-lucide="arrow-left" class="mr-2 h-4 w-4"></i>
                Cancel
            </a>
            
            <div class="flex items-center space-x-3">
                <button type="button" 
                        class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4">
                    <i data-lucide="eye" class="mr-2 h-4 w-4"></i>
                    Preview
                </button>
                
                <button type="submit" 
                        class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-6">
                    <i data-lucide="send" class="mr-2 h-4 w-4"></i>
                    Submit Request
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Auto-resize textarea
        const textarea = document.getElementById('description');
        if (textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        }

        // Character counter for title
        const titleInput = document.getElementById('title');
        if (titleInput) {
            const maxLength = 100;
            const counter = document.createElement('div');
            counter.className = 'text-xs text-muted-foreground mt-1';
            titleInput.parentNode.appendChild(counter);
            
            function updateCounter() {
                const remaining = maxLength - titleInput.value.length;
                counter.textContent = `${titleInput.value.length}/${maxLength} characters`;
                counter.className = `text-xs mt-1 ${remaining < 10 ? 'text-destructive' : 'text-muted-foreground'}`;
            }
            
            titleInput.addEventListener('input', updateCounter);
            updateCounter();
        }

        // Form validation
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const title = document.getElementById('title').value.trim();
                const description = document.getElementById('description').value.trim();
                
                if (!title || !description) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                    return false;
                }
            });
        }
    });
</script>
@endsection
