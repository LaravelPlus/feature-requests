<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Feature Requests
        </h2>
        <Link
          v-if="canCreate"
          :href="route('feature-requests.create')"
          class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
        >
          Create Feature Request
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div 
            v-for="(count, status) in statistics" 
            :key="status"
            class="bg-white overflow-hidden shadow rounded-lg"
          >
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div 
                    :class="getStatusIconClass(status)"
                    class="w-8 h-8 rounded-full flex items-center justify-center"
                  >
                    <component :is="getStatusIcon(status)" class="w-5 h-5" />
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      {{ getStatusLabel(status) }}
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ count }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Search
              </label>
              <input
                v-model="filters.search"
                type="text"
                placeholder="Search feature requests..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <!-- Status Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Status
              </label>
              <select
                v-model="filters.status"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="under_review">Under Review</option>
                <option value="planned">Planned</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>

            <!-- Category Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Category
              </label>
              <select
                v-model="filters.category_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">All Categories</option>
                <option 
                  v-for="category in categories" 
                  :key="category.id" 
                  :value="category.id"
                >
                  {{ category.name }}
                </option>
              </select>
            </div>

            <!-- Sort -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Sort By
              </label>
              <select
                v-model="filters.sort_by"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="created_at">Newest</option>
                <option value="votes">Most Voted</option>
                <option value="title">Title</option>
                <option value="status">Status</option>
              </select>
            </div>
          </div>

          <!-- Filter Actions -->
          <div class="mt-4 flex justify-end space-x-2">
            <button
              @click="clearFilters"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
            >
              Clear Filters
            </button>
            <button
              @click="applyFilters"
              class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700"
            >
              Apply Filters
            </button>
          </div>
        </div>

        <!-- Feature Requests Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
          <FeatureRequestCard
            v-for="featureRequest in featureRequests.data"
            :key="featureRequest.id"
            :feature-request="featureRequest"
          />
        </div>

        <!-- Pagination -->
        <div v-if="featureRequests.links" class="mt-8">
          <nav class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
              <Link
                v-if="featureRequests.prev_page_url"
                :href="featureRequests.prev_page_url"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Previous
              </Link>
              <Link
                v-if="featureRequests.next_page_url"
                :href="featureRequests.next_page_url"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Next
              </Link>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Showing
                  <span class="font-medium">{{ featureRequests.from }}</span>
                  to
                  <span class="font-medium">{{ featureRequests.to }}</span>
                  of
                  <span class="font-medium">{{ featureRequests.total }}</span>
                  results
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                  <Link
                    v-for="link in featureRequests.links"
                    :key="link.label"
                    :href="link.url"
                    v-html="link.label"
                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                    :class="getPaginationLinkClass(link)"
                  />
                </nav>
              </div>
            </div>
          </nav>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import FeatureRequestCard from '@/Components/FeatureRequestCard.vue'
import { 
  ClockIcon, 
  EyeIcon, 
  CheckCircleIcon, 
  XCircleIcon,
  ExclamationTriangleIcon,
  PlayIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  featureRequests: {
    type: Object,
    required: true
  },
  categories: {
    type: Array,
    default: () => []
  },
  statistics: {
    type: Object,
    default: () => ({})
  },
  canCreate: {
    type: Boolean,
    default: false
  }
})

const filters = reactive({
  search: '',
  status: '',
  category_id: '',
  sort_by: 'created_at'
})

const getStatusLabel = (status) => {
  const labels = {
    pending: 'Pending',
    under_review: 'Under Review',
    planned: 'Planned',
    in_progress: 'In Progress',
    completed: 'Completed',
    rejected: 'Rejected'
  }
  return labels[status] || status
}

const getStatusIcon = (status) => {
  const icons = {
    pending: ClockIcon,
    under_review: EyeIcon,
    planned: ExclamationTriangleIcon,
    in_progress: PlayIcon,
    completed: CheckCircleIcon,
    rejected: XCircleIcon
  }
  return icons[status] || ClockIcon
}

const getStatusIconClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-600',
    under_review: 'bg-blue-100 text-blue-600',
    planned: 'bg-purple-100 text-purple-600',
    in_progress: 'bg-orange-100 text-orange-600',
    completed: 'bg-green-100 text-green-600',
    rejected: 'bg-red-100 text-red-600'
  }
  return classes[status] || 'bg-gray-100 text-gray-600'
}

const getPaginationLinkClass = (link) => {
  if (link.active) {
    return 'z-10 bg-blue-50 border-blue-500 text-blue-600'
  }
  if (!link.url) {
    return 'bg-white border-gray-300 text-gray-500 cursor-not-allowed'
  }
  return 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
}

const applyFilters = () => {
  router.get(route('feature-requests.index'), filters, {
    preserveState: true,
    replace: true
  })
}

const clearFilters = () => {
  Object.keys(filters).forEach(key => {
    filters[key] = ''
  })
  filters.sort_by = 'created_at'
  applyFilters()
}
</script>
