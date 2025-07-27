<template>
  <q-page class="flex flex-center">
    <div class="column items-center q-gutter-none" style="max-width: 800px; width: 100%">
      <div class="text-h4 text-center q-mb-lg q-mt-xl q-fw-bold">GIPHY SEARCH</div>

      <!-- Search Form -->
      <q-form @submit="searchGifs" style="width: 100%">
        <div class="row">
          <div class="col-9">
            <q-input square standout v-model="searchTerm" :rules="[val => !!val || 'Search term is required']"
              class="input-lg-text" />
          </div>
          <div class="col-3">
            <div class="form-label q-mt-none">
              <q-btn color="positive" class="text-dark text-black main-search-btn" size="lg"
                icon-right="fas fa-search fa-1x" label="Fetch" type="submit" />
            </div>
          </div>
        </div>

      </q-form>

      <!-- Error Message -->
      <div v-if="error" class="text-negative text-center">
        {{ error }}
      </div>

      <!-- Results -->
      <div v-if="gifs.length > 0" class="q-mt-lg q-pb-lg q-mb-lg" style="width: 100%">
        <div class="text-h6 q-mb-md">Results ({{ gifs.length }})</div>
        <q-list bordered separator>
          <q-item v-for="gif in gifs" :key="gif.id" clickable @click="selectGif(gif)">
            <q-item-section avatar>
              <q-img :src="gif.images.fixed_height_small.url" :alt="gif.title" width="80px" height="80px" fit="cover" />
            </q-item-section>
            <q-item-section>
              <q-item-label>{{ gif.title || 'Untitled' }}</q-item-label>
              <q-item-label caption>
                Rating: {{ gif.rating || 'N/A' }} |
                Size: {{ gif.images.original.width }}x{{ gif.images.original.height }}
              </q-item-label>
            </q-item-section>
            <q-item-section side>
              <q-btn flat round color="primary" icon="fas fa-eye" @click.stop="previewGif(gif)" />
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
          <div class="text-h6 text-white">{{ selectedGif?.title || '' }}</div>
          <q-space />
          <q-btn icon="fas fa-times" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section class="flex flex-center">
          <q-img v-if="selectedGif" :src="selectedGif.images.original.url" :alt="selectedGif.title"
            style="max-width: 100%; max-height: 80vh;" fit="contain" />
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Save Gif to User Account Dialog -->
    <q-dialog v-model="showSave">
      <q-card class="bg-black">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6 text-white">GIF SELECTED</div>
          <q-space />
          <q-btn icon="fas fa-times" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section class="flex flex-center">
          <q-img v-if="selectedGif" :src="selectedGif.images['480w_still'].url" :alt="selectedGif.title"
            style="max-width: 480px;" class="q-mx-auto" fit="contain" />
        </q-card-section>
        <q-card-section class="row items-center">
          <div class="selectedTitle q-my-lg">
            <div class="text-h4 text-white">{{ selectedGif?.title || ' ' }}</div>
          </div>
          <div class="lead q-mb-none q-mt-md q-text-center">
            Save this item to your account?
          </div>
          <q-btn color="positive" size="lg" class="full-width q-mt-md q-mb-sm" label="Save Gif"
            @click="saveGifClicked()" />
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useQuasar } from 'quasar'
import { api } from 'src/boot/axios'

// This is what we need to fill in with data from the selected object.
// This interface defines the structure of the Giphy data we expect to save.
interface GiphyData {
  id: string;
  title?: string;
  slug?: string;
  type?: string;
  rating?: string;
  url: string;
  bitly_url?: string;
  embed_url?: string;
  username?: string;
  import_datetime?: string;
  trending_datetime?: string;
  images: {
    original: {
      url: string;
      width: string;
      height: string;
      size?: number;
      webp?: string;
      frames?: number;
      hash?: string;
    };
    downsized: {
      url: string;
      width: string;
      height: string;
      size?: number;
    };
    '480w_still': {
      url: string;
    };
  };
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  [key: string]: any; // Allow additional fields for flexibility
}

// Define the expected response structure (adjust based on your API)
// interface SaveGifResponse {
//   status: string;
//   data: {
//     id: string;
//     giphy_id: string;
//     giphy_data: GiphyData;
//   };
// }

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
  slug: string
  bitly_url: string
  username: string
  type: string

}

const $q = useQuasar()

const searchTerm = ref('')
const gifs = ref<Gif[]>([])
const isLoading = ref(false)
const error = ref('')
const hasSearched = ref(false)
const showPreview = ref(false)
const showSave = ref(false)
const selectedGif = ref<Gif | null>(null)

const saveGifClicked = async () => {
  if (!selectedGif.value) {
    error.value = 'No GIF selected';
    $q.notify({
      type: 'negative',
      message: 'No GIF selected',
    });
    return;
  }

  const giphy_id = selectedGif.value.id;
  const giphy_data: GiphyData = {
    id: selectedGif.value.id,
    title: selectedGif.value.title || '',
    slug: selectedGif.value.slug || '',
    type: selectedGif.value.type || '',
    rating: selectedGif.value.rating || '',
    url: selectedGif.value.images.original.url,
    bitly_url: selectedGif.value.bitly_url || '',
    embed_url: selectedGif.value.images.original.url,
    username: selectedGif.value.username || '',
    import_datetime: new Date().toISOString(),
    trending_datetime: new Date().toISOString(),
    images: {
      original: {
        url: selectedGif.value.images.original.url,
        width: selectedGif.value.images.original.width,
        height: selectedGif.value.images.original.height,
        size: 0, // This would need to come from the API response
        webp: selectedGif.value.images.original.url,
        frames: 0, // This would need to come from the API response
        hash: ''
      },
      downsized: {
        url: selectedGif.value.images.fixed_height_small.url,
        width: selectedGif.value.images.fixed_height_small.width,
        height: selectedGif.value.images.fixed_height_small.height,
        size: 0
      },
      '480w_still': {
        url: selectedGif.value.images.fixed_height_small.url // Fallback to small image
      }
    }
  };

  console.log('Data we are going to save: ', giphy_data)

  try {
    const response = await saveGifToDB(giphy_id, giphy_data);
    showSave.value = false; // Close dialog on success
    console.log(response)

    error.value = '';
  } catch (err) {
    const errorMessage = err instanceof Error ? err.message : 'Failed to save GIF';
    error.value = errorMessage;
    $q.notify({
      type: 'negative',
      message: errorMessage,
    });
  }
};

// eslint-disable-next-line @typescript-eslint/no-explicit-any
const saveGifToDB = async (id: string, gdata: any) => {
  try {
    console.log(id, gdata)
    const response = await api.post('/api/gifs/save', {
      giphy_id: id,
      giphy_data: gdata,
    })
    console.log(response)

    $q.notify({
      type: 'positive',
      message: `GIF saved successfully!`,
    });
  }
  catch (err: unknown) {
    console.log(err)
    $q.notify({
      type: 'negative',
      message: 'error msg'
    })
  }
  return true
}

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
        type: 'warning',
        message: 'No GIFs found for your search term',
        position: 'top',
        icon: 'fas fa-times'
      })
    } else {
      $q.notify({
        type: 'positive',
        message: `Found ${gifs.value.length} GIFs`,
        position: 'top',
        icon: 'fas fa-star'
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

  if (!selectedGif.value) {
    $q.notify({
      type: 'negative',
      message: 'please try that again',
      position: 'top',
      icon: 'fas fa-redo'
    })
    return
  }

  console.log("select gif fired", selectedGif.value)
  selectedGif.value = gif
  showSave.value = true
}

const previewGif = (gif: Gif) => {
  selectedGif.value = gif
  showPreview.value = true
}
</script>
