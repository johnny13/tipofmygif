<template>
  <q-page class="flex flex-center">
    <div class="column items-center q-gutter-md" style="max-width: 800px; width: 100%">
      <div class="text-h4 text-center q-mb-lg q-mt-xl">Search GIFs</div>
      
      <!-- Search Form -->
      <q-form @submit="searchGifs" class="q-gutter-md" style="width: 100%">
        <div class="row">
          <div class="col-9">
            <q-input
              square standout
              v-model="searchTerm"
              :rules="[val => !!val || 'Search term is required']"
              class="input-lg-text"
            />
          </div>
          <div class="col-3">
            <div class="form-label q-mt-none">
              <q-btn
                 color="positive"
                 class="text-dark text-black q-pt-md q-pb-sm"
                 size="lg"
                 icon-right="fas fa-search"
                 label="Search"
                 @click="searchGifs()"
              />
            </div>
          </div>
        </div>
        
      </q-form>

      <!-- Error Message -->
      <div v-if="error" class="text-negative text-center">
        {{ error }}
      </div>

      <!-- Results -->
      <div v-if="gifs.length > 0" class="q-mt-lg" style="width: 100%">
        <div class="text-h6 q-mb-md">Results ({{ gifs.length }})</div>
        <q-list bordered separator>
          <q-item v-for="gif in gifs" :key="gif.id" clickable @click="selectGif(gif)">
            <q-item-section avatar>
              <q-img
                :src="gif.images.fixed_height_small.url"
                :alt="gif.title"
                width="80px"
                height="80px"
                fit="cover"
              />
            </q-item-section>
            <q-item-section>
              <q-item-label>{{ gif.title || 'Untitled' }}</q-item-label>
              <q-item-label caption>
                Rating: {{ gif.rating || 'N/A' }} | 
                Size: {{ gif.images.original.width }}x{{ gif.images.original.height }}
              </q-item-label>
            </q-item-section>
            <q-item-section side>
                           <q-btn
               flat
               round
               color="primary"
               icon="fas fa-eye"
               @click.stop="previewGif(gif)"
             />
            </q-item-section>
          </q-item>
        </q-list>
      </div>

      <!-- No Results Message -->
      <div v-else-if="hasSearched && !isLoading" class="text-center q-mt-lg">
                 <q-icon name="fas fa-search" size="4rem" color="grey-5" />
        <div class="text-h6 q-mt-md text-grey-6">No GIFs found</div>
        <div class="text-body2 text-grey-5">Try a different search term</div>
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
          <q-img
            v-if="selectedGif"
            :src="selectedGif.images.original.url"
            :alt="selectedGif.title"
            style="max-width: 100%; max-height: 80vh;"
            fit="contain"
          />
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useQuasar } from 'quasar'
import { api } from 'src/boot/axios'

interface GifImage {
  url: string
  width: string
  height: string
}

interface GifImages {
  fixed_height_small: GifImage
  original: GifImage
}

interface Gif {
  id: string
  title: string
  rating: string
  images: GifImages
}

const $q = useQuasar()

const searchTerm = ref('')
const gifs = ref<Gif[]>([])
const isLoading = ref(false)
const error = ref('')
const hasSearched = ref(false)
const showPreview = ref(false)
const selectedGif = ref<Gif | null>(null)

const searchGifs = async () => {
  console.log("perfoming search")

  if (!searchTerm.value.trim()) return

  isLoading.value = true
  error.value = ''
  hasSearched.value = true

  try {
    const response = await api.get('/api/gifs/search', {
      params: {
        query: searchTerm.value.trim()
      }
    })
    
    gifs.value = response.data.data || []
    
    if (gifs.value.length === 0) {
      $q.notify({
        type: 'info',
        message: 'No GIFs found for your search term'
      })
    } else {
      $q.notify({
        type: 'positive',
        message: `Found ${gifs.value.length} GIFs`
      })
    }
  } catch (err: unknown) {
    const errorMessage = err && typeof err === 'object' && 'response' in err && 
      err.response && typeof err.response === 'object' && 'data' in err.response &&
      err.response.data && typeof err.response.data === 'object' && 'message' in err.response.data
      ? String(err.response.data.message)
      : 'Failed to search GIFs'
    error.value = errorMessage
    $q.notify({
      type: 'negative',
      message: errorMessage
    })
  } finally {
    isLoading.value = false
  }
}

const selectGif = (gif: Gif) => {
  selectedGif.value = gif
  showPreview.value = true
}

const previewGif = (gif: Gif) => {
  selectedGif.value = gif
  showPreview.value = true
}
</script>