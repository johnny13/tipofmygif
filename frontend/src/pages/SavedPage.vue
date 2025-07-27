<template>
  <q-page class="flex flex-center">
    <div class="column items-center q-gutter-md" style="max-width: 800px; width: 100%">
      <div class="text-h4 text-center q-mb-lg q-mt-xl">My Saved GIFs</div>

      <!-- Loading State -->
      <div v-if="isLoading" class="text-center q-mt-lg">
        <q-spinner-dots size="3rem" color="primary" />
        <div class="text-h6 q-mt-md">Loading your saved GIFs...</div>
      </div>

      <!-- Error Message -->
      <div v-else-if="error" class="text-negative text-center">
        {{ error }}
      </div>

      <!-- Results -->
      <div v-else-if="savedGifs.length > 0" class="q-mt-lg" style="width: 100%">
        <div class="text-h6 q-mb-md">Saved GIFs ({{ savedGifs.length }})</div>
        <q-list bordered separator>
          <q-item v-for="gif in savedGifs" :key="gif.id" clickable @click="selectGif(gif)">
            <q-item-section avatar>

            </q-item-section>
            <q-item-section>
              <q-item-label>{{ gif.title || 'Untitled' }}</q-item-label>
              <q-item-label caption>
                Saved: {{ formatDate(gif.created_at) }} |
                Size: {{ gif.original_width }}x{{ gif.original_height }}
              </q-item-label>
            </q-item-section>
            <q-item-section side>
              <q-btn flat round color="primary" icon="fas fa-eye" @click.stop="previewGif(gif)" />
              <q-btn flat round color="negative" icon="fas fa-trash" @click.stop="deleteGif(gif)" />
            </q-item-section>
          </q-item>
        </q-list>
      </div>

      <!-- No Results Message -->
      <div v-else class="text-center q-mt-lg">
        <q-icon name="fas fa-star" size="4rem" color="grey-5" />
        <div class="text-h6 q-mt-md text-grey-6">No saved GIFs yet</div>
        <div class="text-body2 text-grey-5">Search for GIFs and save them to see them here</div>
        <q-btn color="primary" label="Search GIFs" class="q-mt-md" @click="$router.push('/search')" />
      </div>
    </div>

    <!-- GIF Preview Dialog -->
    <q-dialog v-model="showPreview" maximized>
      <q-card class="bg-black">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6 text-white">{{ selectedGif?.title || 'GIF Preview' }}</div>
          <q-space />
          <q-btn icon="fas fa-times" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section class="flex flex-center">
          <q-img v-if="selectedGif" :src="selectedGif.url" :alt="selectedGif.title"
            style="max-width: 100%; max-height: 80vh;" fit="contain" />
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Delete Confirmation Dialog -->
    <q-dialog v-model="showDeleteConfirm">
      <q-card>
        <q-card-section class="row items-center">
          <div class="text-h6">Delete Saved GIF</div>
          <q-space />
          <q-btn icon="fas fa-times" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          Are you sure you want to delete "{{ selectedGif?.title || 'this GIF' }}" from your saved items?
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="primary" v-close-popup />
          <q-btn flat label="Delete" color="negative" @click="confirmDelete" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { api } from 'src/boot/axios'

// interface GiphyData {
//   title?: string
//   url: string
//   width?: number
//   height?: number
//   giphy_id: string
//   author_username?: string
// }

interface SavedGif {
  id: number
  giphy_id: string
  created_by: number
  title: string
  slug: string
  type: string
  rating: string
  url: string
  bitly_url: string
  embed_url: string
  original_width: number
  original_height: number
  original_size: number
  original_url: string
  original_webp: string
  original_frames: number
  original_hash: string
  downsized_url: string
  downsized_width: number
  downsized_height: number
  downsized_size: number
  still_480w_url: string
  source_post_url: string
  source_tld: string
  author_username: string
  author_avatar_url: string
  author_profile_url: string
  author_display_name: string
  author_is_verified: boolean
  created_at: string
  updated_at: string
}

const $q = useQuasar()

const savedGifs = ref<SavedGif[]>([])
const isLoading = ref(false)
const error = ref('')
const showPreview = ref(false)
const showDeleteConfirm = ref(false)
const selectedGif = ref<SavedGif | null>(null)

const loadSavedGifs = async () => {
  isLoading.value = true
  error.value = ''

  try {
    const response = await api.get('/api/gifs/my?page=1&per_page=20')
    savedGifs.value = response.data.data || []

    console.log(response)
    if (savedGifs.value.length === 0) {
      $q.notify({
        type: 'info',
        message: 'No saved GIFs found'
      })
    } else {
      $q.notify({
        type: 'positive',
        message: `Loaded ${savedGifs.value.length} saved GIFs`
      })
    }

    console.log(savedGifs.value)
  } catch (err: unknown) {
    const errorMessage = err && typeof err === 'object' && 'response' in err &&
      err.response && typeof err.response === 'object' && 'data' in err.response &&
      err.response.data && typeof err.response.data === 'object' && 'message' in err.response.data
      ? String(err.response.data.message)
      : 'Failed to load saved GIFs'
    error.value = errorMessage
    $q.notify({
      type: 'negative',
      message: errorMessage
    })
  } finally {
    isLoading.value = false
  }
}

const selectGif = (gif: SavedGif) => {
  selectedGif.value = gif
  showPreview.value = true
}

const previewGif = (gif: SavedGif) => {
  selectedGif.value = gif
  showPreview.value = true
}

const deleteGif = (gif: SavedGif) => {
  selectedGif.value = gif
  showDeleteConfirm.value = true
}

const confirmDelete = async () => {
  if (!selectedGif.value) return

  try {
    await api.delete(`/api/gifs/${selectedGif.value.id}`)

    // Remove from local list
    const index = savedGifs.value.findIndex(gif => gif.id === selectedGif.value?.id)
    if (index > -1) {
      savedGifs.value.splice(index, 1)
    }

    showDeleteConfirm.value = false
    selectedGif.value = null

    $q.notify({
      type: 'positive',
      message: 'GIF deleted successfully'
    })
  } catch (err: unknown) {
    const errorMessage = err && typeof err === 'object' && 'response' in err &&
      err.response && typeof err.response === 'object' && 'data' in err.response &&
      err.response.data && typeof err.response.data === 'object' && 'message' in err.response.data
      ? String(err.response.data.message)
      : 'Failed to delete GIF'

    $q.notify({
      type: 'negative',
      message: errorMessage
    })
  }
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Load saved GIFs when component mounts
onMounted(async () => {
  await loadSavedGifs()
})
</script>