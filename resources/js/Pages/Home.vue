<template>
  <div class="space-y-10">
    <!-- Welcome Spotlight Banner -->
    <div class="relative bg-gradient-to-r from-orange-900/40 via-amber-950/20 to-[#0e1322]/80 border border-orange-500/10 rounded-3xl p-8 md:p-12 overflow-hidden shadow-xl">
      <div class="absolute -top-24 -right-24 w-80 h-80 bg-orange-600/10 rounded-full blur-3xl"></div>
      
      <div class="max-w-2xl relative space-y-4">
        <span class="px-3 py-1 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-400 text-xs font-semibold uppercase tracking-wider">
          🎧 Live Streaming
        </span>
        <h1 class="text-3xl md:text-5xl font-black text-white leading-tight tracking-tight">
          Explore Trending <span class="bg-clip-text text-transparent bg-gradient-to-r from-orange-400 via-amber-400 to-yellow-400">Independent Beats</span>
        </h1>
        <p class="text-sm md:text-base text-gray-400 leading-relaxed">
          Welcome to BeatGround, your personal gateway to real-time audio. Click play on any track to start streaming without interruptions. Explore by genre or search for your favorite producers.
        </p>
      </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <!-- Genre Chips -->
      <div class="flex flex-wrap gap-2.5">
        <button 
          v-for="g in genresList" 
          :key="g"
          @click="selectedGenre = g"
          class="h-10 px-5 rounded-full text-xs font-bold uppercase tracking-wider border select-none transition-all flex items-center gap-1.5"
          :class="selectedGenre === g 
            ? 'bg-orange-600 border-orange-500 text-white font-extrabold shadow-lg shadow-orange-950/40' 
            : 'bg-gray-900/60 border-gray-800 text-gray-400 hover:border-gray-700 hover:text-white'"
        >
          <span>{{ g }}</span>
        </button>
      </div>

      <!-- Search Input Box -->
      <div class="relative w-full md:w-80">
        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </span>
        <input 
          v-model="searchQuery"
          type="text" 
          placeholder="Search track title or producer..." 
          class="w-full h-11 pl-12 pr-4 rounded-xl bg-gray-900/60 border border-gray-800 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none text-xs text-white placeholder-gray-500 transition-all font-semibold"
        />
      </div>
    </div>

    <!-- Tracks Grid -->
    <div>
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
          <span>Featured Music Selection</span>
          <span class="text-xs bg-gray-900 border border-gray-800 text-gray-500 font-semibold px-2 py-0.5 rounded-full">
            {{ filteredSongs.length }} Tracks
          </span>
        </h3>
      </div>

      <!-- Empty State -->
      <div v-if="filteredSongs.length === 0" class="py-24 text-center border border-gray-800 border-dashed rounded-3xl space-y-4 bg-gray-900/20">
        <svg class="w-12 h-12 text-gray-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
        <h4 class="text-lg font-bold text-white">No tracks match your query</h4>
        <p class="text-sm text-gray-500 max-w-xs mx-auto">Try clearing search query or selecting a different genre category.</p>
      </div>

      <!-- Grid list -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div 
          v-for="song in filteredSongs" 
          :key="song.id"
          class="group bg-gradient-to-b from-[#111827]/40 to-[#0f172a]/80 border rounded-2xl p-4 shadow-lg hover:shadow-xl hover:border-orange-500/30 transition-all duration-300 relative overflow-hidden"
          :class="{ 'border-orange-600 ring-1 ring-orange-600/30 bg-orange-950/5': isPlayingSong(song.id) , 'border-gray-800': !isPlayingSong(song.id) }"
        >
          <!-- Thumbnail Container -->
          <div class="relative aspect-square w-full rounded-xl overflow-hidden bg-gray-950 border border-gray-800/80 mb-4 shadow-inner">
            <img :src="song.thumbnail_url" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="Cover" />
            
            <!-- Hover Action Overlay -->
            <div class="absolute inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
              <button 
                @click="playSong(song)" 
                class="w-16 h-16 rounded-full bg-orange-600 hover:bg-orange-500 text-white flex items-center justify-center shadow-lg shadow-orange-950/50 hover:scale-105 transition-transform"
              >
                <!-- Play/Pause Icon -->
                <svg v-if="!isPlayingSong(song.id) || !player.isPlaying" class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                <svg v-else class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
              </button>
            </div>

            <!-- Realtime active indicator pulse -->
            <div v-if="isPlayingSong(song.id) && player.isPlaying" class="absolute bottom-3 left-3 px-2.5 py-1 rounded bg-orange-600/90 text-[10px] font-bold uppercase tracking-wider text-white flex items-center gap-1.5 shadow backdrop-blur-md">
              <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
              <span>Playing</span>
            </div>
          </div>

          <!-- Title & Artist -->
          <div class="space-y-1">
            <div class="flex items-start justify-between gap-2">
              <h4 class="font-extrabold text-white text-base truncate flex-grow tracking-tight group-hover:text-orange-400 transition-colors" :title="song.title">
                {{ song.title }}
              </h4>
              <span class="text-[10px] bg-orange-500/10 border border-orange-500/20 text-orange-400 font-bold px-2 py-0.5 rounded uppercase flex-shrink-0">
                {{ song.genre }}
              </span>
            </div>
            <p class="text-xs text-gray-400 font-medium truncate">{{ song.producer_name }}</p>
          </div>

          <!-- Play statistics & Interaction -->
          <div class="mt-4 pt-3 border-t border-gray-800/80 flex items-center justify-between text-xs font-semibold text-gray-500">
            <div class="flex items-center gap-3">
              <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ song.plays_count || 0 }}</span>
              </span>
              <span class="flex items-center gap-1">
                <svg class="w-4 h-4 text-pink-500/80" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                <span class="text-pink-400">{{ song.likes_count || 0 }}</span>
              </span>
            </div>

            <!-- Interactivity buttons -->
            <div class="flex gap-2">
              <button 
                @click="likeSong(song)" 
                class="w-7 h-7 rounded-lg bg-gray-900 border hover:bg-pink-500/10 hover:border-pink-500/30 flex items-center justify-center transition-colors"
                :class="isLiked(song.id) ? 'border-pink-500 text-pink-500' : 'border-gray-800 text-gray-500'"
                title="Like Track"
              >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { playerStore } from '../Stores/playerStore';
import { db } from '../firebase';
import { collection, onSnapshot, doc, updateDoc, increment } from 'firebase/firestore';

export default {
  name: 'Home',
  setup() {
    const searchQuery = ref('');
    const selectedGenre = ref('All');
    const songsList = ref([]);
    
    // Track localized likes dynamically in UI for speed
    const likedTracks = ref([]);

    const genresList = ['All', 'Lo-Fi', 'Synthwave', 'Hip-Hop', 'EDM', 'Acoustic'];

    const player = computed(() => playerStore);

    // Sync all songs from Firestore collection in real-time
    let unsubscribe = null;
    onMounted(() => {
      unsubscribe = onSnapshot(collection(db, 'songs'), (snapshot) => {
        songsList.value = snapshot.docs.map(doc => ({
          id: doc.id,
          ...doc.data()
        }));
      }, (err) => {
        console.error("Firestore songs listener failed:", err);
      });
    });

    onUnmounted(() => {
      if (unsubscribe) {
        unsubscribe();
      }
    });

    // Dynamic Filter logic
    const filteredSongs = computed(() => {
      let list = songsList.value;

      // Genre filter
      if (selectedGenre.value !== 'All') {
        list = list.filter(song => song.genre?.toLowerCase() === selectedGenre.value.toLowerCase());
      }

      // Search query filter
      if (searchQuery.value.trim() !== '') {
        const query = searchQuery.value.toLowerCase();
        list = list.filter(song => 
          song.title?.toLowerCase().includes(query) || 
          song.producer_name?.toLowerCase().includes(query)
        );
      }

      return list;
    });

    const isPlayingSong = (songId) => {
      return playerStore.currentTrack?.id === songId;
    };

    const playSong = (song) => {
      // Pass the track and the active filtered list as the queue!
      playerStore.play(song, filteredSongs.value);
    };

    const isLiked = (songId) => {
      return likedTracks.value.includes(songId);
    };

    const likeSong = async (song) => {
      if (isLiked(song.id)) return; // Simple debounce

      try {
        const songRef = doc(db, 'songs', song.id);
        await updateDoc(songRef, {
          likes_count: increment(1)
        });
        
        likedTracks.value.push(song.id);
      } catch (err) {
        console.error("Like action failed:", err);
      }
    };

    return {
      searchQuery,
      selectedGenre,
      genresList,
      filteredSongs,
      player,
      isPlayingSong,
      playSong,
      isLiked,
      likeSong
    };
  }
}
</script>
