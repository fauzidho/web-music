<template>
  <div class="min-h-screen bg-[#0b0f19] text-gray-100 selection:bg-orange-600 selection:text-white">

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-6 py-10 space-y-10">
      <!-- Welcome Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-gray-800 pb-8">
        <div>
          <h2 class="text-3xl font-extrabold text-white tracking-tight">Creator Dashboard</h2>
          <p class="text-sm text-gray-400 mt-1">Manage and track the performance of your released music</p>
        </div>
        <button @click="navigateTo('ProducerUpload')" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-orange-600 to-amber-500 text-white font-bold px-6 h-12 rounded-xl hover:opacity-90 transition-opacity shadow-lg shadow-orange-950/40 cursor-pointer outline-none">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          <span>Release New Track</span>
        </button>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-b from-[#111827] to-[#0f172a] border border-gray-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
          <div class="absolute top-0 right-0 w-24 h-24 bg-orange-500/5 rounded-full blur-2xl"></div>
          <div class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Total Plays</div>
          <div class="text-4xl font-black text-white mt-2">{{ totalPlays }}</div>
          <div class="text-xs text-orange-400 font-semibold mt-2">Realtime from Firestore</div>
        </div>

        <div class="bg-gradient-to-b from-[#111827] to-[#0f172a] border border-gray-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
          <div class="absolute top-0 right-0 w-24 h-24 bg-orange-500/5 rounded-full blur-2xl"></div>
          <div class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Uploaded Tracks</div>
          <div class="text-4xl font-black text-white mt-2">{{ songsList.length }}</div>
          <div class="text-xs text-orange-400 font-semibold mt-2">Fully Hosted on Cloudinary</div>
        </div>

        <div class="bg-gradient-to-b from-[#111827] to-[#0f172a] border border-gray-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
          <div class="absolute top-0 right-0 w-24 h-24 bg-orange-500/5 rounded-full blur-2xl"></div>
          <div class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Genres Active</div>
          <div class="text-4xl font-black text-white mt-2">{{ activeGenres }}</div>
          <div class="text-xs text-orange-400 font-semibold mt-2">Niche diversity metric</div>
        </div>
      </div>

      <!-- Tracks Table Section -->
      <div class="bg-gradient-to-b from-[#0e1322] to-[#0b0f19] border border-gray-800/80 rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-850 bg-[#0e1322]/40 flex items-center justify-between">
          <h3 class="font-extrabold text-white text-lg tracking-tight">Your Catalog</h3>
        </div>

        <div v-if="songsList.length === 0" class="p-16 text-center text-gray-500">
          <svg class="w-12 h-12 mx-auto text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
          <p class="font-bold text-gray-400">No releases found</p>
          <p class="text-xs text-gray-600 mt-1">Get started by sharing your first production track!</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-gray-800 text-gray-500 text-xs font-bold uppercase tracking-wider bg-gray-950/20">
                <th class="px-6 py-4">Release Title</th>
                <th class="px-6 py-4">Genre</th>
                <th class="px-6 py-4">Duration</th>
                <th class="px-6 py-4 text-center">Plays</th>
                <th class="px-6 py-4 text-center">Likes</th>
                <th class="px-6 py-4">Release Date</th>
                <th class="px-6 py-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-850">
              <tr v-for="song in songsList" :key="song.id" class="hover:bg-gray-850/20 transition-colors group">
                <td class="px-6 py-4 flex items-center gap-3">
                  <img :src="song.thumbnail_url" class="w-10 h-10 rounded-lg object-cover border border-gray-800 shadow animate-fade-in" alt="cover" />
                  <div class="truncate max-w-[200px]">
                    <div class="font-bold text-white truncate">{{ song.title }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">ID: {{ song.id }}</div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2.5 py-1 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-400 text-xs font-bold uppercase">
                    {{ song.genre }}
                  </span>
                </td>
                <td class="px-6 py-4 text-gray-400 font-semibold">
                  {{ formatDuration(song.duration) }}
                </td>
                <td class="px-6 py-4 text-center font-extrabold text-white text-base">
                  {{ song.plays_count || 0 }}
                </td>
                <td class="px-6 py-4 text-center font-extrabold text-pink-400 text-base">
                  {{ song.likes_count || 0 }}
                </td>
                <td class="px-6 py-4 text-gray-400">
                  {{ formatDate(song.created_at) }}
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="inline-flex gap-3">
                    <button @click="openLyricsModal(song)" class="text-xs bg-orange-500/10 hover:bg-orange-500 border border-orange-500/20 hover:border-orange-500 text-orange-400 hover:text-white px-3 py-1.5 rounded-lg font-bold transition-all cursor-pointer">
                      ✍️ Edit Lyrics
                    </button>
                    <button @click="deleteTrack(song.id)" class="text-xs bg-red-500/10 hover:bg-red-500 border border-red-500/20 hover:border-red-500 text-red-400 hover:text-white px-3 py-1.5 rounded-lg font-bold transition-all cursor-pointer">
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <!-- Lyrics Editor Modal Overlay -->
    <div v-if="showLyricsModal" class="fixed inset-0 bg-black/85 backdrop-blur-md z-50 flex items-center justify-center p-6 transition-all duration-300">
      <div class="w-full max-w-xl bg-gradient-to-b from-[#111827] to-[#0f172a] border border-gray-800 rounded-3xl p-8 shadow-2xl space-y-6 relative animate-fade-in">
        <button @click="closeLyricsModal" class="absolute top-6 right-6 text-gray-400 hover:text-white text-2xl transition-colors cursor-pointer outline-none">&times;</button>
        
        <div>
          <span class="text-xs font-bold text-orange-500 uppercase tracking-wider">Songwriter Desk</span>
          <h3 class="text-2xl font-black text-white mt-1">Edit Track Lyrics</h3>
          <p class="text-xs text-gray-400 mt-1">Update the lyrics for <span class="text-white font-bold">"{{ activeSongForLyrics?.title }}"</span></p>
        </div>

        <div class="space-y-2">
          <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Lyrics Text</label>
          <textarea 
            v-model="lyricsText" 
            rows="10"
            placeholder="Type or paste the song lyrics here..."
            class="w-full p-4 rounded-2xl bg-gray-950/60 border border-gray-850 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none text-sm text-gray-200 placeholder-gray-600 transition-all resize-none leading-relaxed font-semibold"
          ></textarea>
        </div>

        <div class="flex gap-4">
          <button @click="closeLyricsModal" class="flex-1 h-12 rounded-xl border border-gray-800 hover:border-gray-600 text-gray-400 hover:text-white text-sm font-bold transition-colors cursor-pointer">
            Cancel
          </button>
          <button @click="saveLyrics" :disabled="savingLyrics" class="flex-grow h-12 rounded-xl bg-gradient-to-r from-orange-600 to-amber-500 text-white font-bold text-sm hover:opacity-90 transition-opacity flex items-center justify-center gap-2 shadow-lg shadow-orange-950/40 disabled:opacity-50 cursor-pointer">
            <svg v-if="savingLyrics" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ savingLyrics ? 'Saving...' : 'Save Lyrics' }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { db } from '../../firebase';
import { collection, query, where, onSnapshot, doc, deleteDoc, updateDoc } from 'firebase/firestore';
import { state, navigateTo, logoutUser } from '../../store';

export default {
  name: 'ProducerDashboard',
  setup() {
    const songsList = ref([]);
    
    // Lyrics Editor state
    const showLyricsModal = ref(false);
    const activeSongForLyrics = ref(null);
    const lyricsText = ref('');
    const savingLyrics = ref(false);

    const userName = computed(() => state.currentUser?.name || 'Producer');
    const userRole = computed(() => state.currentUser?.role || 'producer');

    // Real-time Firestore sync supporting both producer_uid and producer_id schemas
    let unsubscribe = null;
    onMounted(() => {
      const uid = state.currentUser?.uid;
      if (uid) {
        unsubscribe = onSnapshot(collection(db, 'songs'), (snapshot) => {
          const allSongs = snapshot.docs.map(doc => ({
            id: doc.id,
            ...doc.data()
          }));
          songsList.value = allSongs.filter(song => song.producer_uid === uid || song.producer_id === uid);
        }, (err) => {
          console.error("Failed to load catalog:", err);
        });
      }
    });

    onUnmounted(() => {
      if (unsubscribe) {
        unsubscribe();
      }
    });

    const totalPlays = computed(() => {
      return songsList.value.reduce((acc, song) => acc + (song.plays_count || 0), 0);
    });

    const activeGenres = computed(() => {
      const genres = songsList.value.map(song => song.genre?.toLowerCase()).filter(Boolean);
      return new Set(genres).size;
    });

    const formatDuration = (secs) => {
      if (!secs) return '0:00';
      const m = Math.floor(secs / 60);
      const s = Math.floor(secs % 60);
      return `${m}:${s < 10 ? '0' : ''}${s}`;
    };

    const formatDate = (dateStr) => {
      if (!dateStr) return '-';
      try {
        const d = new Date(dateStr);
        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
      } catch (e) {
        return dateStr;
      }
    };

    const deleteTrack = async (songId) => {
      if (confirm('Are you absolutely sure you want to delete this track? This will permanently remove it from BeatGround.')) {
        try {
          await deleteDoc(doc(db, 'songs', songId));
        } catch (err) {
          console.error("Delete track failed:", err);
        }
      }
    };

    const openLyricsModal = (song) => {
      activeSongForLyrics.value = song;
      lyricsText.value = song.lyrics || '';
      showLyricsModal.value = true;
    };

    const closeLyricsModal = () => {
      showLyricsModal.value = false;
      activeSongForLyrics.value = null;
      lyricsText.value = '';
    };

    const saveLyrics = async () => {
      if (!activeSongForLyrics.value) return;
      savingLyrics.value = true;
      try {
        await updateDoc(doc(db, 'songs', activeSongForLyrics.value.id), {
          lyrics: lyricsText.value.trim()
        });
        showLyricsModal.value = false;
      } catch (err) {
        alert("Failed to save lyrics: " + err.message);
      } finally {
        savingLyrics.value = false;
      }
    };

    const logout = async () => {
      await logoutUser();
    };

    return {
      songsList,
      userName,
      userRole,
      totalPlays,
      activeGenres,
      formatDuration,
      formatDate,
      deleteTrack,
      showLyricsModal,
      activeSongForLyrics,
      lyricsText,
      savingLyrics,
      openLyricsModal,
      closeLyricsModal,
      saveLyrics,
      logout,
      navigateTo
    };
  }
}
</script>
