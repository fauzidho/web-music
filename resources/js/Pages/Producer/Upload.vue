<template>
  <div class="min-h-screen bg-[#0b0f19] text-gray-100 flex items-center justify-center p-6 relative overflow-hidden">
    <!-- Decorative Blurry Background Elements -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-orange-600/10 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-pink-500/10 rounded-full blur-3xl"></div>

    <div class="w-full max-w-2xl bg-gradient-to-b from-[#111827]/80 to-[#0f172a]/95 border border-gray-800 rounded-3xl p-8 shadow-2xl relative backdrop-blur-xl space-y-8">
      <!-- Title Section -->
      <div class="flex justify-between items-start border-b border-gray-800/80 pb-6">
        <div>
          <h2 class="text-3xl font-extrabold text-white tracking-tight">Upload Track</h2>
          <p class="text-sm text-gray-400 mt-1">Release a new beat or song to your fans</p>
        </div>
        <a href="/producer/dashboard" class="text-xs border border-gray-800 hover:border-gray-600 bg-gray-900/60 text-gray-400 hover:text-white px-4 py-2 rounded-xl transition-all font-semibold">
          Cancel & Return
        </a>
      </div>

      <!-- Error Alerts -->
      <div v-if="error" class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm font-semibold flex items-center gap-2">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span>{{ error }}</span>
      </div>

      <!-- Upload Form -->
      <form @submit.prevent="handleUpload" class="space-y-6">
        <div class="grid md:grid-cols-2 gap-6">
          <!-- Track Info Left -->
          <div class="space-y-5">
            <div>
              <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Track Title</label>
              <input 
                v-model="title" 
                type="text" 
                required 
                placeholder="e.g. Neon Shadows"
                class="w-full h-12 rounded-xl bg-gray-900/60 border border-gray-800 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none px-4 text-sm text-white placeholder-gray-500 transition-all"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Genre</label>
              <select 
                v-model="genre"
                required
                class="w-full h-12 rounded-xl bg-gray-900/60 border border-gray-800 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none px-4 text-sm text-white placeholder-gray-500 transition-all cursor-pointer"
              >
                <option value="" disabled>Select a Genre</option>
                <option value="Lo-Fi">🎧 Lo-Fi Beat</option>
                <option value="Synthwave">🌆 Synthwave</option>
                <option value="Hip-Hop">🔥 Hip-Hop / Rap</option>
                <option value="EDM">⚡ EDM / Electronic</option>
                <option value="Acoustic">🎸 Acoustic / Indie</option>
                <option value="Classical">🎹 Classical</option>
              </select>
            </div>

            <!-- Thumbnail Input -->
            <div>
              <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Track Cover Art (Thumbnail)</label>
              <div 
                @dragover.prevent="thumbnailDrag = true"
                @dragleave.prevent="thumbnailDrag = false"
                @drop.prevent="handleThumbnailDrop"
                class="border-2 border-dashed rounded-2xl p-6 text-center cursor-pointer select-none transition-all flex flex-col items-center justify-center min-h-[160px] relative overflow-hidden"
                :class="thumbnailDrag ? 'border-orange-500 bg-orange-500/5' : 'border-gray-800 bg-gray-900/40 hover:border-gray-700'"
                @click="$refs.thumbnailInput.click()"
              >
                <!-- Thumbnail Preview -->
                <div v-if="thumbnailPreview" class="absolute inset-0 bg-gray-950/90 flex items-center justify-center p-3">
                  <img :src="thumbnailPreview" class="w-full h-full object-cover rounded-lg" alt="Cover preview" />
                  <button @click.stop="removeThumbnail" class="absolute top-2 right-2 bg-red-600/90 text-white rounded-full p-1.5 hover:bg-red-500 transition-all shadow shadow-black">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                  </button>
                </div>

                <div v-else class="space-y-2">
                  <svg class="w-8 h-8 text-gray-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  <div class="text-sm font-semibold text-gray-300">Drop Cover image, or <span class="text-orange-400">browse</span></div>
                  <div class="text-xs text-gray-500">JPG, PNG up to 5MB</div>
                </div>
                <input ref="thumbnailInput" type="file" accept="image/*" class="sr-only" @change="handleThumbnailChange" />
              </div>
            </div>
          </div>

          <!-- Audio Input Right -->
          <div class="flex flex-col justify-between">
            <div class="flex-grow">
              <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Audio File (MP3 / WAV)</label>
              <div 
                @dragover.prevent="audioDrag = true"
                @dragleave.prevent="audioDrag = false"
                @drop.prevent="handleAudioDrop"
                class="border-2 border-dashed rounded-2xl p-10 text-center cursor-pointer select-none transition-all flex flex-col items-center justify-center h-[312px] relative overflow-hidden"
                :class="audioDrag ? 'border-orange-500 bg-orange-500/5' : 'border-gray-800 bg-gray-900/40 hover:border-gray-700'"
                @click="$refs.audioInput.click()"
              >
                <!-- Selected Audio display -->
                <div v-if="audioFile" class="space-y-4">
                  <div class="w-16 h-16 rounded-full bg-orange-500/10 border border-orange-500/20 flex items-center justify-center mx-auto text-orange-400 animate-pulse">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/></svg>
                  </div>
                  <div>
                    <div class="text-sm font-bold text-white max-w-[200px] truncate mx-auto">{{ audioFile.name }}</div>
                    <div class="text-xs text-gray-500 mt-1 font-semibold">{{ formatBytes(audioFile.size) }}</div>
                  </div>
                  <button @click.stop="removeAudio" class="text-xs bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white px-3 py-1.5 rounded-lg font-bold border border-red-500/20 transition-all">
                    Remove file
                  </button>
                </div>

                <div v-else class="space-y-3">
                  <div class="w-14 h-14 rounded-full bg-gray-950 border border-gray-800 flex items-center justify-center mx-auto text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                  </div>
                  <div class="text-sm font-semibold text-gray-300">Drop Audio, or <span class="text-orange-400">browse</span></div>
                  <div class="text-xs text-gray-500">MP3, WAV up to 20MB</div>
                </div>
                <input ref="audioInput" type="file" accept="audio/*" class="sr-only" @change="handleAudioChange" />
              </div>
            </div>
          </div>
        </div>

        <!-- Realtime Upload Progress Bar -->
        <div v-if="loading && progress !== null" class="space-y-2 pt-4">
          <div class="flex justify-between text-xs font-semibold text-gray-400 uppercase tracking-wider">
            <span>Uploading to Cloudinary...</span>
            <span>{{ progress }}%</span>
          </div>
          <div class="w-full h-2.5 bg-gray-900 border border-gray-800 rounded-full overflow-hidden">
            <div 
              class="h-full bg-gradient-to-r from-orange-500 via-amber-500 to-yellow-500 rounded-full transition-all duration-300"
              :style="{ width: progress + '%' }"
            ></div>
          </div>
        </div>

        <!-- Submit Button -->
        <button 
          type="submit" 
          :disabled="loading || !audioFile || !thumbnailFile"
          class="w-full h-14 rounded-2xl bg-gradient-to-r from-orange-600 to-amber-500 text-white font-bold text-base hover:opacity-90 transition-opacity flex items-center justify-center gap-2 shadow-xl shadow-orange-950/40 disabled:opacity-50 disabled:cursor-not-allowed pt-1"
        >
          <svg v-if="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>{{ loading ? 'Uploading assets...' : 'Publish Track to BeatGround' }}</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

export default {
  name: 'Upload',
  setup() {
    const title = ref('');
    const genre = ref('');
    const thumbnailFile = ref(null);
    const thumbnailPreview = ref(null);
    const thumbnailDrag = ref(false);

    const audioFile = ref(null);
    const audioDrag = ref(false);

    const loading = ref(false);
    const progress = ref(null);
    const error = ref(null);

    // Helpers
    const formatBytes = (bytes, decimals = 2) => {
      if (!bytes) return '0 Bytes';
      const k = 1024;
      const dm = decimals < 0 ? 0 : decimals;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    };

    // Thumbnail handlers
    const handleThumbnailChange = (e) => {
      const file = e.target.files[0];
      setThumbnail(file);
    };

    const handleThumbnailDrop = (e) => {
      thumbnailDrag.value = false;
      const file = e.dataTransfer.files[0];
      setThumbnail(file);
    };

    const setThumbnail = (file) => {
      if (!file) return;
      if (!file.type.startsWith('image/')) {
        error.value = 'Cover art must be an image file (JPG, PNG).';
        return;
      }
      if (file.size > 5242880) { // 5MB
        error.value = 'Cover art size cannot exceed 5MB.';
        return;
      }
      thumbnailFile.value = file;
      thumbnailPreview.value = URL.createObjectURL(file);
      error.value = null;
    };

    const removeThumbnail = () => {
      thumbnailFile.value = null;
      if (thumbnailPreview.value) {
        URL.revokeObjectURL(thumbnailPreview.value);
        thumbnailPreview.value = null;
      }
    };

    // Audio handlers
    const handleAudioChange = (e) => {
      const file = e.target.files[0];
      setAudio(file);
    };

    const handleAudioDrop = (e) => {
      audioDrag.value = false;
      const file = e.dataTransfer.files[0];
      setAudio(file);
    };

    const setAudio = (file) => {
      if (!file) return;
      if (!file.type.startsWith('audio/')) {
        error.value = 'Track must be an audio file (MP3, WAV, OGG, M4A).';
        return;
      }
      if (file.size > 20971520) { // 20MB
        error.value = 'Audio file size cannot exceed 20MB.';
        return;
      }
      audioFile.value = file;
      error.value = null;
    };

    const removeAudio = () => {
      audioFile.value = null;
    };

    // Form Submission
    const handleUpload = () => {
      if (!title.value.trim() || !genre.value || !audioFile.value || !thumbnailFile.value) {
        error.value = 'Please complete all form fields and upload files.';
        return;
      }

      loading.value = true;
      error.value = null;

      // Construct Multipart FormData
      const formData = new FormData();
      formData.append('title', title.value);
      formData.append('genre', genre.value);
      formData.append('audio', audioFile.value);
      formData.append('thumbnail', thumbnailFile.value);

      router.post('/producer/upload', formData, {
        forceFormData: true,
        onProgress: (val) => {
          if (val) {
            progress.value = val.percentage;
          }
        },
        onError: (errs) => {
          console.error(errs);
          error.value = errs.message || 'An error occurred uploading your media to Cloudinary.';
          loading.value = false;
          progress.value = null;
        },
        onFinish: () => {
          loading.value = false;
          progress.value = null;
        }
      });
    };

    return {
      title,
      genre,
      thumbnailFile,
      thumbnailPreview,
      thumbnailDrag,
      audioFile,
      audioDrag,
      loading,
      progress,
      error,
      formatBytes,
      handleThumbnailChange,
      handleThumbnailDrop,
      removeThumbnail,
      handleAudioChange,
      handleAudioDrop,
      removeAudio,
      handleUpload
    };
  }
}
</script>
