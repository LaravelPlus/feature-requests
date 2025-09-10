<template>
  <div class="flex items-center space-x-2">
    <!-- Up Vote Button -->
    <button
      @click="vote('up')"
      :disabled="voting || !canVote"
      class="flex items-center space-x-1 px-3 py-2 rounded-lg border transition-colors duration-200"
      :class="getUpVoteButtonClass()"
    >
      <ThumbUpIcon class="w-4 h-4" />
      <span>{{ upVoteCount }}</span>
    </button>

    <!-- Down Vote Button -->
    <button
      @click="vote('down')"
      :disabled="voting || !canVote"
      class="flex items-center space-x-1 px-3 py-2 rounded-lg border transition-colors duration-200"
      :class="getDownVoteButtonClass()"
    >
      <ThumbDownIcon class="w-4 h-4" />
      <span>{{ downVoteCount }}</span>
    </button>

    <!-- Vote Statistics -->
    <div v-if="showStatistics" class="text-sm text-gray-500 ml-2">
      <span class="font-medium">{{ netVotes }}</span>
      <span class="text-xs">({{ approvalRate }}% approval)</span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { ThumbUpIcon, ThumbDownIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  featureRequestId: {
    type: Number,
    required: true
  },
  initialVoteCount: {
    type: Number,
    default: 0
  },
  initialUpVotes: {
    type: Number,
    default: 0
  },
  initialDownVotes: {
    type: Number,
    default: 0
  },
  userVote: {
    type: String,
    default: null // 'up', 'down', or null
  },
  canVote: {
    type: Boolean,
    default: true
  },
  showStatistics: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['vote-changed'])

const voting = ref(false)
const upVoteCount = ref(props.initialUpVotes)
const downVoteCount = ref(props.initialDownVotes)
const currentUserVote = ref(props.userVote)

const netVotes = computed(() => upVoteCount.value - downVoteCount.value)
const approvalRate = computed(() => {
  const total = upVoteCount.value + downVoteCount.value
  return total > 0 ? Math.round((upVoteCount.value / total) * 100) : 0
})

const getUpVoteButtonClass = () => {
  if (currentUserVote.value === 'up') {
    return 'border-blue-500 bg-blue-50 text-blue-600'
  }
  return 'border-gray-300 hover:border-blue-500 hover:text-blue-600'
}

const getDownVoteButtonClass = () => {
  if (currentUserVote.value === 'down') {
    return 'border-red-500 bg-red-50 text-red-600'
  }
  return 'border-gray-300 hover:border-red-500 hover:text-red-600'
}

const vote = async (voteType) => {
  if (voting.value || !props.canVote) return
  
  voting.value = true
  
  try {
    const response = await fetch(`/api/feature-requests/votes`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        feature_request_id: props.featureRequestId,
        vote_type: voteType
      })
    })

    if (response.ok) {
      const data = await response.json()
      
      // Update vote counts
      upVoteCount.value = data.data.statistics.up_votes
      downVoteCount.value = data.data.statistics.down_votes
      currentUserVote.value = voteType
      
      emit('vote-changed', {
        voteType,
        statistics: data.data.statistics
      })
    } else {
      const error = await response.json()
      console.error('Vote error:', error.message)
    }
  } catch (error) {
    console.error('Error voting:', error)
  } finally {
    voting.value = false
  }
}

const removeVote = async () => {
  if (voting.value || !props.canVote) return
  
  voting.value = true
  
  try {
    const response = await fetch(`/api/feature-requests/votes`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        feature_request_id: props.featureRequestId
      })
    })

    if (response.ok) {
      const data = await response.json()
      
      // Update vote counts
      upVoteCount.value = data.data.statistics.up_votes
      downVoteCount.value = data.data.statistics.down_votes
      currentUserVote.value = null
      
      emit('vote-changed', {
        voteType: null,
        statistics: data.data.statistics
      })
    } else {
      const error = await response.json()
      console.error('Remove vote error:', error.message)
    }
  } catch (error) {
    console.error('Error removing vote:', error)
  } finally {
    voting.value = false
  }
}
</script>
