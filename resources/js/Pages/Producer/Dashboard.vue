<template>
  <div class="min-h-screen bg-[#0b0f19] text-gray-100 selection:bg-orange-600 selection:text-white">
    <!-- Header/Nav -->
    <header class="border-b border-gray-800 bg-[#0e1322]/80 backdrop-blur-md sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        <div class="flex items-center gap-3 cursor-pointer" @click="navigateTo('Home')">
          <img src="/BeatGround.png" class="w-10 h-10 rounded-xl object-cover shadow-lg shadow-orange-950/30" alt="BeatGround Logo" />
          <span class="text-2xl font-black bg-clip-text text-transparent bg-gradient-to-r from-white via-gray-200 to-gray-400 tracking-tight">Beat<span class="text-orange-500">Ground</span></span>
          <span class="px-2 py-0.5 text-[10px] font-bold tracking-wider bg-orange-600/20 border border-orange-500/30 rounded text-orange-400 uppercase ml-2">Creator Hub</span>
        </div>

        <div class="flex items-center gap-6">
          <div class="flex items-center gap-3 text-right">
            <div class="hidden sm:block">
              <div class="text-sm font-bold text-white">{{ userName }}</div>
              <div class="text-xs text-orange-400 font-semibold uppercase tracking-wider">{{ userRole }}</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-orange-600/30 border border-orange-500/50 flex items-center justify-center font-bold text-white">
              {{ userName ? userName[0] : 'U' }}
            </div>
          </div>
          <button @click="navigateTo('Home')" class="text-sm font-semibold text-gray-400 hover:text-white transition-colors cursor-pointer outline-none">Return to Browse</button>
          <button @click="logout" class="text-gray-400 hover:text-red-400 transition-colors text-sm font-semibold cursor-pointer outline-none">Sign Out</button>
        </div>
      </div>
    </header>

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
                  <img :src="song.thumbnail_url" class="w-10 h-10 rounded-lg object-cover border border-gray-800 shadow" alt="cover" />
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
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { db } from '../../firebase';
import { collection, query, where, onSnapshot, doc, deleteDoc } from 'firebase/firestore';
import { state, navigateTo, logoutUser } from '../../store';

export default {
  name: 'ProducerDashboard',
  setup() {
    const songsList = ref([]);

    const userName = computed(() => state.currentUser?.name || 'Producer');
    const userRole = computed(() => state.currentUser?.role || 'producer');

    // Real-time Firestore sync restricted to the current logged-in producer's UID
    let unsubscribe = null;
    onMounted(() => {
      const uid = state.currentUser?.uid;
      if (uid) {
        const q = query(collection(db, 'songs'), where('producer_uid', '==', uid));
        unsubscribe = onSnapshot(q, (snapshot) => {
          songsList.value = snapshot.docs.map(doc => ({
            id: doc.id,
            ...doc.data()
          }));
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
      logout,
      navigateTo
    };
  }
}
</script>
