<template>
  <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200">
    <!-- Header -->
    <div class="flex items-start justify-between mb-4">
      <div class="flex-1">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">
          <Link :href="route('feature-requests.show', featureRequest.slug)" class="hover:text-blue-600">
            {{ featureRequest.title }}
          </Link>
        </h3>
        <div class="flex items-center space-x-4 text-sm text-gray-500">
          <span class="flex items-center">
            <UserIcon class="w-4 h-4 mr-1" />
            {{ featureRequest.user.name }}
          </span>
          <span class="flex items-center">
            <CalendarIcon class="w-4 h-4 mr-1" />
            {{ formatDate(featureRequest.created_at) }}
          </span>
          <span v-if="featureRequest.category" class="flex items-center">
            <TagIcon class="w-4 h-4 mr-1" />
            {{ featureRequest.category.name }}
          </span>
        </div>
      </div>
      
      <!-- Status Badge -->
      <span 
        :class="getStatusBadgeClass(featureRequest.status)"
        class="px-3 py-1 rounded-full text-xs font-medium"
      >
        {{ featureRequest.status_label }}
      </span>
    </div>

    <!-- Description -->
    <p class="text-gray-700 mb-4 line-clamp-3">
      {{ featureRequest.description }}
    </p>

    <!-- Tags -->
    <div v-if="featureRequest.tags && featureRequest.tags.length" class="mb-4">
      <div class="flex flex-wrap gap-2">
        <span 
          v-for="tag in featureRequest.tags" 
          :key="tag"
          class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full"
        >
          {{ tag }}
        </span>
      </div>
    </div>

    <!-- Footer -->
    <div class="flex items-center justify-between">
      <div class="flex items-center space-x-4">
        <!-- Vote Button -->
        <button
          v-if="featureRequest.can_be_voted_on"
          @click="toggleVote"
          :disabled="voting"
          class="flex items-center space-x-2 px-3 py-2 rounded-lg border transition-colors duration-200"
          :class="getVoteButtonClass()"
        >
          <ThumbUpIcon class="w-4 h-4" />
          <span>{{ featureRequest.vote_count }}</span>
        </button>

        <!-- Comment Count -->
        <div class="flex items-center space-x-2 text-gray-500">
          <ChatBubbleLeftIcon class="w-4 h-4" />
          <span>{{ featureRequest.comment_count }}</span>
        </div>

        <!-- View Count -->
        <div class="flex items-center space-x-2 text-gray-500">
          <EyeIcon class="w-4 h-4" />
          <span>{{ featureRequest.view_count }}</span>
        </div>
      </div>

      <!-- Priority Badge -->
      <span 
        v-if="featureRequest.priority !== 'medium'"
        :class="getPriorityBadgeClass(featureRequest.priority)"
        class="px-2 py-1 rounded text-xs font-medium"
      >
        {{ featureRequest.priority }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import { 
  UserIcon, 
  CalendarIcon, 
  TagIcon, 
  ThumbUpIcon, 
  ChatBubbleLeftIcon, 
  EyeIcon 
} from '@heroicons/vue/24/outline'

const props = defineProps({
  featureRequest: {
    type: Object,
    required: true
  }
})

const voting = ref(false)

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const getStatusBadgeClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    under_review: 'bg-blue-100 text-blue-800',
    planned: 'bg-purple-100 text-purple-800',
    in_progress: 'bg-orange-100 text-orange-800',
    completed: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getPriorityBadgeClass = (priority) => {
  const classes = {
    low: 'bg-gray-100 text-gray-800',
    medium: 'bg-blue-100 text-blue-800',
    high: 'bg-orange-100 text-orange-800',
    critical: 'bg-red-100 text-red-800'
  }
  return classes[priority] || 'bg-gray-100 text-gray-800'
}

const getVoteButtonClass = () => {
  // This would need to be determined based on user's vote status
  return 'border-gray-300 hover:border-blue-500 hover:text-blue-600'
}

const toggleVote = async () => {
  if (voting.value) return
  
  voting.value = true
  
  try {
    // Implement vote toggle logic here
    // This would make an API call to vote/unvote
    console.log('Voting on feature request:', props.featureRequest.id)
  } catch (error) {
    console.error('Error voting:', error)
  } finally {
    voting.value = false
  }
}
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
