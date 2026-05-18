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
        <button type="button" @click="navigateTo('ProducerDashboard')" class="text-xs border border-gray-800 hover:border-gray-600 bg-gray-900/60 text-gray-400 hover:text-white px-4 py-2 rounded-xl transition-all font-semibold cursor-pointer outline-none">
          Cancel & Return
        </button>
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

            <!-- Upload Preset Configuration (Premium Direct Upload Helper) -->
            <div>
              <div class="flex items-center justify-between mb-2">
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Unsigned Upload Preset</label>
                <span class="text-[10px] text-orange-400 font-semibold bg-orange-600/10 px-2 py-0.5 rounded">Required</span>
              </div>
              <input 
                v-model="uploadPreset" 
                type="text" 
                required 
                placeholder="e.g. ml_default"
                class="w-full h-12 rounded-xl bg-gray-900/60 border border-gray-800 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none px-4 text-sm text-white placeholder-gray-500 transition-all"
              />
              <span class="text-[9px] text-gray-500 block mt-1 leading-normal">Cloudinary dashboard -> Settings -> Upload -> Upload presets (Unsigned). Default is usually BeatGround.</span>
            </div>

            <!-- Optional Lyrics Field -->
            <div>
              <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Track Lyrics (Optional)</label>
              <textarea 
                v-model="lyrics" 
                rows="4"
                placeholder="Enter track lyrics here..."
                class="w-full p-4 rounded-xl bg-gray-900/60 border border-gray-800 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none text-xs text-white placeholder-gray-500 transition-all resize-none font-semibold"
              ></textarea>
            </div>
          </div>

          <!-- File Uploads Right -->
          <div class="flex flex-col justify-between space-y-6">
            <!-- Thumbnail Input -->
            <div class="space-y-2">
              <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Cover Art (Square)</label>
              
              <div 
                v-if="!thumbnailPreview"
                @dragover.prevent="thumbnailDrag = true"
                @dragleave.prevent="thumbnailDrag = false"
                @drop.prevent="handleThumbnailDrop"
                class="relative border border-dashed rounded-2xl flex flex-col items-center justify-center p-6 transition-all"
                :class="thumbnailDrag ? 'border-orange-500 bg-orange-500/5' : 'border-gray-855 hover:border-gray-700 bg-gray-900/20'"
              >
                <input 
                  type="file" 
                  accept="image/*"
                  @change="handleThumbnailChange"
                  class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                />
                <svg class="w-8 h-8 text-gray-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="text-xs font-bold text-gray-300">Drag thumbnail or click</span>
                <span class="text-[10px] text-gray-500 mt-1">PNG, JPG up to 5MB</span>
              </div>

              <!-- Thumbnail Preview Active -->
              <div v-else class="relative rounded-2xl overflow-hidden border border-gray-855 aspect-square group shadow-lg max-w-[140px]">
                <img :src="thumbnailPreview" class="w-full h-full object-cover" alt="Preview cover" />
                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                  <button type="button" @click="removeThumbnail" class="text-xs bg-red-600 hover:bg-red-500 text-white font-bold px-3 py-1.5 rounded-lg transition-colors cursor-pointer">
                    Remove
                  </button>
                </div>
              </div>
            </div>

            <!-- Audio Dropzone -->
            <div class="flex-grow flex flex-col justify-end space-y-2">
              <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Audio File</label>
              
              <div 
                v-if="!audioFile"
                @dragover.prevent="audioDrag = true"
                @dragleave.prevent="audioDrag = false"
                @drop.prevent="handleAudioDrop"
                class="border border-dashed rounded-2xl flex flex-col items-center justify-center p-8 flex-grow transition-all relative"
                :class="audioDrag ? 'border-orange-500 bg-orange-500/5' : 'border-gray-855 hover:border-gray-700 bg-gray-900/20'"
              >
                <input 
                  type="file" 
                  accept="audio/*"
                  @change="handleAudioChange"
                  class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                />
                <svg class="w-10 h-10 text-gray-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                <span class="text-xs font-bold text-gray-300">Drag beat file or browse</span>
                <span class="text-[10px] text-gray-500 mt-1">MP3, WAV up to 20MB</span>
              </div>

              <!-- Audio Uploaded Active -->
              <div v-else class="border border-gray-800 rounded-2xl bg-gray-955/30 p-4 flex flex-col items-center justify-center text-center gap-2 relative overflow-hidden shadow-inner flex-grow">
                <div class="w-10 h-10 rounded-full bg-orange-600/10 border border-orange-500/20 flex items-center justify-center text-orange-400">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>
                </div>
                <div class="max-w-[200px] truncate">
                  <h4 class="font-extrabold text-white text-xs truncate">{{ audioFile.name }}</h4>
                  <p class="text-[10px] text-gray-500 mt-0.5">{{ formatBytes(audioFile.size) }}</p>
                </div>
                <button type="button" @click="removeAudio" class="text-[10px] font-bold text-red-400 hover:text-red-300 transition-colors mt-1 cursor-pointer">
                  Remove Track File
                </button>
              </div>
            </div>

            <!-- Upload Progress Slider Bar -->
            <div v-if="progress !== null" class="space-y-2">
              <div class="flex items-center justify-between text-xs font-bold text-gray-400">
                <span>Uploading...</span>
                <span>{{ progress }}%</span>
              </div>
              <div class="w-full h-2 bg-gray-800 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-orange-600 to-amber-500 rounded-full transition-all duration-300" :style="{ width: progress + '%' }"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <button 
          type="submit"
          :disabled="loading"
          class="w-full h-12 rounded-xl bg-gradient-to-r from-orange-600 to-amber-500 text-white font-bold text-sm hover:opacity-90 transition-opacity flex items-center justify-center gap-2 shadow-lg shadow-orange-950/40 disabled:opacity-50 cursor-pointer outline-none"
        >
          <svg v-if="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>{{ loading ? 'Uploading assets to Cloudinary...' : 'Publish Track to BeatGround' }}</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import { db } from '../../firebase';
import { collection, addDoc } from 'firebase/firestore';
import { state, navigateTo } from '../../store';

export default {
  name: 'Upload',
  setup() {
    const title = ref('');
    const genre = ref('');
    const lyrics = ref('');
    
    // Customizable upload preset (defaulted to BeatGround)
    const uploadPreset = ref('BeatGround');

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

    // Form Submission (Direct Cloudinary Unsigned Upload + Firestore AddDoc)
    const handleUpload = async () => {
      if (!title.value.trim() || !genre.value || !audioFile.value || !thumbnailFile.value) {
        error.value = 'Please complete all form fields and upload files.';
        return;
      }

      loading.value = true;
      error.value = null;
      progress.value = 10;

      try {
        // 1. Upload Thumbnail Image (resource_type: image)
        progress.value = 25;
        const thumbData = new FormData();
        thumbData.append('file', thumbnailFile.value);
        thumbData.append('upload_preset', uploadPreset.value);

        const thumbRes = await fetch(`https://api.cloudinary.com/v1_1/dscfj5jwa/image/upload`, {
          method: 'POST',
          body: thumbData
        });

        if (!thumbRes.ok) {
          const errData = await thumbRes.json().catch(() => ({}));
          throw new Error(`Thumbnail upload failed: ${errData.error?.message || thumbRes.statusText}. Please verify unsigned uploads are active on your Cloudinary account.`);
        }
        const thumbJson = await thumbRes.json();

        // 2. Upload Audio File (resource_type: video)
        progress.value = 65;
        const audioData = new FormData();
        audioData.append('file', audioFile.value);
        audioData.append('upload_preset', uploadPreset.value);

        const audioRes = await fetch(`https://api.cloudinary.com/v1_1/dscfj5jwa/video/upload`, {
          method: 'POST',
          body: audioData
        });

        if (!audioRes.ok) {
          const errData = await audioRes.json().catch(() => ({}));
          throw new Error(`Audio upload failed: ${errData.error?.message || audioRes.statusText}. Please verify that audio formats (video resource classification) are allowed.`);
        }
        const audioJson = await audioRes.json();

        progress.value = 90;

        // 3. Document insertion into Firestore songs collection
        await addDoc(collection(db, 'songs'), {
          title: title.value.trim(),
          genre: genre.value,
          audio_url: audioJson.secure_url,
          audio_public_id: audioJson.public_id,
          thumbnail_url: thumbJson.secure_url,
          thumbnail_public_id: thumbJson.public_id,
          duration: Math.round(audioJson.duration || 0),
          plays_count: 0,
          likes_count: 0,
          producer_uid: state.currentUser?.uid || '',
          producer_id: state.currentUser?.uid || '', // Save both to prevent any field mismatch!
          producer_name: state.currentUser?.name || 'Producer',
          lyrics: lyrics.value.trim(),
          created_at: new Date().toISOString()
        });

        progress.value = 100;
        loading.value = false;

        // Redirect back to Creator Dashboard
        navigateTo('ProducerDashboard');
      } catch (err) {
        console.error("Direct upload flow failed:", err);
        error.value = err.message || 'An error occurred during file upload.';
        loading.value = false;
        progress.value = null;
      }
    };

    return {
      title,
      genre,
      lyrics,
      uploadPreset,
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
      handleUpload,
      navigateTo
    };
  }
}
</script>
