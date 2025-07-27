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
              <q-img :src="gif.still_480w_url" :alt="gif.title" width="80px" height="80px" fit="cover" />
            </q-item-section>
            <q-item-section>
              <q-item-label>{{ gif.title || 'Untitled' }}</q-item-label>
              <q-item-label caption>
                Saved: {{ formatDate(gif.created_at) }} |
                Size: {{ gif.original_width }}x{{ gif.original_height }}
              </q-item-label>
            </q-item-section>
            <q-item-section side>
              <q-btn flat round color="primary" icon="fas fa-comment" @click.stop="commentGif(gif)" />
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
    <q-dialog v-model="showPreview">
      <q-card class="bg-black">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6 text-white">GIF SELECTED</div>
          <q-space />
          <q-btn icon="fas fa-times" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section class="flex flex-center">
          <q-img v-if="selectedGif" :src="selectedGif.still_480w_url" :alt="selectedGif.title" style="max-width: 200px;"
            class="q-mx-auto" fit="contain" />
        </q-card-section>
        <q-card-section class="row items-center">
          <div class="selectedTitle q-my-lg">
            <div class="text-h5 text-white">{{ selectedGif?.title || ' ' }}</div>
          </div>
          <div class="row full-width">
            <div class="col">
              <q-separator class="q-my-md" />
            </div>
          </div>
          <div class="lead q-mb-md q-mt-sm">
            <div class="row">
              <div class="col-2">RATING</div>
              <div class="col">
                <q-icon v-for="star in 5" :key="star"
                  :name="star <= previewRatingsSelectModel ? 'fas fa-star fa-lg' : 'far fa-star fa-lg'" color="yellow"
                  class="q-mr-xs" />
              </div>
              <div class="col">
                <q-select filled v-model="previewRatingsSelectModel" :options="previewRatingsSelectOptions"
                  label="Rating" dense style="min-width: 100px" @update:model-value="onRatingChange" />
              </div>
            </div>
          </div>
          <div class="row full-width">
            <div class="col">
              <q-separator class="q-my-md" />
            </div>
          </div>
          <div class="lead q-mb-md q-mt-sm">
            Comments
          </div>
          <q-list bordered class="rounded-borders full-width" v-if="previewCommentsArray.length > 0">
            <q-item v-for="comment in previewCommentsArray" :key="comment.id">
              <q-item-section>
                <q-item-label>{{ comment.comment }}</q-item-label>
              </q-item-section>
              <q-item-section top side>
                <div class="text-grey-8 q-gutter-xs">
                  <q-btn class="gt-xs" size="12px" flat dense round icon="fas fa-edit" @click="editComment(comment)" />
                  <q-btn class="gt-xs" size="12px" flat dense round icon="fas fa-trash"
                    @click="deleteComment(comment)" />
                </div>
              </q-item-section>
            </q-item>
          </q-list>
          <q-btn :disabled="saveChangeLocked" :color="(saveChangeLocked) ? 'gray' : 'positive'" size="lg"
            class="full-width q-mt-xl q-mb-sm" label="Save Changes" @click="saveChanges" />
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Show Comment Dialog -->
    <q-dialog v-model="showCommentDialog">
      <q-card style="width: 100%;max-width: 600px;">
        <q-card-section class="row items-center">
          <div class="text-h6">Comment on GIF</div>
          <q -space />
          <q-btn icon="fas fa-times" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          <q-input v-model="commentInput" label="Comment" class="q-mb-md" />
        </q-card-section>
        <q-card-actions align="right">
          <q-btn solid label="Confirm" color="positive" @click="confirmComment" />
        </q-card-actions>
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

interface userComment {
  id: number
  user_id: number
  gif_id: number
  comment: string
}

interface userRating {
  id: number
  user_id: number
  gif_id: number
  rating: number
}

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
  user_rating: number
  has_user_rating: boolean
  comments: userComment[]
  ratings: userRating[]
}

const $q = useQuasar()

// This value is used to lock the save changes button until the user has made changes to the rating or comments.
const saveChangeLocked = ref(true)
const previewRatingsOriginalValue = ref(0)

const savedGifs = ref<SavedGif[]>([])
const isLoading = ref(false)
const error = ref('')
const showPreview = ref(false)
const showDeleteConfirm = ref(false)
const selectedGif = ref<SavedGif | null>(null)
const previewRatingsSelectModel = ref(1)
const previewRatingsSelectOptions = ref([1, 2, 3, 4, 5])
const previewCommentsArray = ref<userComment[]>([])
const showCommentDialog = ref(false)
const commentInput = ref('')

// Load Saved GIFs. Runs on page load. Grabs ALL gifs from the database, for the current user.
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

const onRatingChange = (value: number) => {
  console.log(`Rating changed to: ${value}`);
  if (value !== previewRatingsOriginalValue.value) {
    saveChangeLocked.value = false
  } else {
    saveChangeLocked.value = true
  }
};

// Select a GIF to preview. This is the main function that is called when the user clicks on a GIF.
const selectGif = (gif: SavedGif) => {
  // Set the selected GIF.
  selectedGif.value = gif

  // Check For Rating
  // Set the original rating value. This is used to determine if the user has made changes to the rating.
  previewRatingsOriginalValue.value = selectedGif.value.user_rating || 0

  // Set the rating value. This is used to display the rating in the preview.
  previewRatingsSelectModel.value = selectedGif.value.user_rating || 0

  // Set the comments array. This is used to display the comments in the preview. 
  previewCommentsArray.value = selectedGif.value.comments

  // Show the preview dialog.
  showPreview.value = true
}

const saveChanges = async () => {
  if (!selectedGif.value) return

  try {
    // Save the rating
    await api.post('/api/ratings', {
      gif_id: selectedGif.value.giphy_id,
      rating: previewRatingsSelectModel.value
    })

    // Update the local data
    selectedGif.value.user_rating = previewRatingsSelectModel.value
    selectedGif.value.has_user_rating = true

    // Reset the save button state
    saveChangeLocked.value = true
    previewRatingsOriginalValue.value = previewRatingsSelectModel.value

    $q.notify({
      type: 'positive',
      message: 'Rating saved successfully'
    })

    // Close the dialog
    showPreview.value = false
  } catch (err: unknown) {
    const errorMessage = err && typeof err === 'object' && 'response' in err &&
      err.response && typeof err.response === 'object' && 'data' in err.response &&
      err.response.data && typeof err.response.data === 'object' && 'message' in err.response.data
      ? String(err.response.data.message)
      : 'Failed to save rating'

    $q.notify({
      type: 'negative',
      message: errorMessage
    })
  }
}

const deleteGif = (gif: SavedGif) => {
  console.log(`Deleting GIF: ${gif.id}`);
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

const editComment = (comment: userComment) => {
  console.log(`Editing comment: ${comment.id}`);
}

const deleteComment = (comment: userComment) => {
  console.log(`Deleting comment: ${comment.id}`);
}

const commentGif = (gif: SavedGif) => {
  selectedGif.value = gif
  commentInput.value = ''
  showCommentDialog.value = true
}

const confirmComment = async () => {
  if (!selectedGif.value || !commentInput.value.trim()) return

  try {
    await api.post('/api/comments', {
      gif_id: selectedGif.value.giphy_id,
      comment: commentInput.value.trim()
    })

    $q.notify({
      type: 'positive',
      message: 'Comment added successfully'
    })

    showCommentDialog.value = false
    commentInput.value = ''

    // Reload the GIFs to get updated comments
    await loadSavedGifs()
  } catch (err: unknown) {
    const errorMessage = err && typeof err === 'object' && 'response' in err &&
      err.response && typeof err.response === 'object' && 'data' in err.response &&
      err.response.data && typeof err.response.data === 'object' && 'message' in err.response.data
      ? String(err.response.data.message)
      : 'Failed to add comment'

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