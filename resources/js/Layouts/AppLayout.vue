<template>
  <div class="min-h-screen bg-[#0b0f19] text-gray-100 flex flex-col justify-between selection:bg-orange-600 selection:text-white pb-28">
    <!-- Header -->
    <header class="border-b border-gray-800 bg-[#0e1322]/80 backdrop-blur-md sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between gap-4">
        <!-- Logo -->
        <div class="flex items-center gap-3 cursor-pointer flex-shrink-0" @click="navigateTo('Home')">
          <img src="/BeatGround.png" class="w-10 h-10 rounded-xl object-cover shadow-lg shadow-orange-950/30" alt="BeatGround Logo" />
          <span class="text-xl md:text-2xl font-black bg-clip-text text-transparent bg-gradient-to-r from-white via-gray-200 to-gray-400 tracking-tight">Beat<span class="text-orange-500">Ground</span></span>
        </div>
        
        <!-- Navigation Links -->
        <nav class="flex items-center gap-4 md:gap-8 text-xs md:text-sm font-semibold text-gray-400">
          <button @click="navigateTo('Home')" class="hover:text-orange-400 transition-colors cursor-pointer outline-none" :class="{ 'text-white': state.currentRoute === 'Home' }">Browse</button>
          
          <!-- Producer Dashboard Link -->
          <button 
            v-if="userRole === 'producer' || userRole === 'admin'"
            @click="navigateTo('ProducerDashboard')" 
            class="hover:text-orange-400 transition-colors cursor-pointer outline-none" 
            :class="{ 'text-white': state.currentRoute.startsWith('Producer') }"
          >
            Producer Panel
          </button>

          <!-- Admin Dashboard Link -->
          <button 
            v-if="userRole === 'admin'"
            @click="navigateTo('AdminDashboard')" 
            class="hover:text-orange-400 transition-colors cursor-pointer outline-none" 
            :class="{ 'text-white': state.currentRoute.startsWith('Admin') }"
          >
            Admin Panel
          </button>
        </nav>

        <!-- User Options -->
        <div class="flex items-center gap-4 flex-shrink-0">
          <div class="flex items-center gap-3 text-right">
            <div class="hidden sm:block">
              <div class="text-sm font-bold text-white leading-none">{{ userName }}</div>
              <div class="flex items-center justify-end gap-1.5 mt-1">
                <span class="text-[9px] text-orange-400 font-bold uppercase tracking-wider bg-orange-600/10 px-1.5 py-0.5 rounded border border-orange-500/10">{{ userRole }}</span>
              </div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-orange-600/20 border border-orange-500/30 flex items-center justify-center font-bold text-white shadow-md select-none">
              {{ userName ? userName[0] : 'U' }}
            </div>
          </div>
          <button @click="logout" class="text-xs font-semibold text-gray-400 hover:text-red-400 transition-colors cursor-pointer">Sign Out</button>
        </div>
      </div>
    </header>

    <!-- Main Content Slot -->
    <main class="max-w-7xl mx-auto px-6 py-8 flex-grow w-full">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-800/50 bg-gradient-to-b from-[#0a0e1a]/80 to-[#060810] pt-12 pb-16 px-6 relative overflow-hidden mt-auto">
      <div class="absolute top-0 left-1/2 -translate-x-1/2 w-1/2 h-px bg-gradient-to-r from-transparent via-orange-500/20 to-transparent"></div>
      <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-8 relative z-10">
        <!-- Brand -->
        <div class="flex items-center gap-3 opacity-80 hover:opacity-100 transition-opacity cursor-pointer">
          <img src="/BeatGround.png" class="w-8 h-8 rounded-lg shadow-lg" alt="Logo" />
          <div class="text-left">
            <h5 class="text-sm font-black text-gray-200 tracking-tight">BeatGround</h5>
            <p class="text-[9px] text-orange-500/80 font-bold uppercase tracking-widest mt-0.5">Next-Gen Audio Platform</p>
          </div>
        </div>
        
        <!-- Links -->
        <div class="flex flex-wrap justify-center gap-x-8 gap-y-3 text-xs font-bold text-gray-500 uppercase tracking-wider">
          <a href="#" class="hover:text-orange-400 transition-colors">Discover</a>
          <a href="#" class="hover:text-orange-400 transition-colors">For Creators</a>
          <a href="#" class="hover:text-orange-400 transition-colors">Terms of Service</a>
          <a href="#" class="hover:text-orange-400 transition-colors">Privacy Policy</a>
        </div>

        <!-- Copyright -->
        <div class="text-xs text-gray-600 font-medium text-center md:text-right">
          &copy; 2026 Crafted by <span class="text-gray-400 font-bold hover:text-white transition-colors cursor-pointer">Fauzi Ridho Anshori</span>
        </div>
      </div>
    </footer>

    <!-- Persistent Bottom Audio Player Bar -->
    <div 
      v-if="player.currentTrack" 
      class="fixed bottom-0 left-0 right-0 h-24 bg-[#0d1222]/95 border-t border-gray-800 backdrop-blur-xl z-50 flex items-center justify-between px-6 shadow-2xl transition-all duration-300"
    >
      <!-- Track Details Left -->
      <div class="flex items-center gap-4 w-1/4 min-w-[200px]">
        <img :src="player.currentTrack.thumbnail_url" class="w-14 h-14 rounded-lg object-cover border border-gray-800 shadow-md flex-shrink-0 animate-fade-in" alt="Cover" />
        <div class="truncate">
          <h4 class="font-bold text-white text-sm truncate">{{ player.currentTrack.title }}</h4>
          <p class="text-xs text-orange-400 font-medium truncate mt-0.5">{{ player.currentTrack.producer_name }}</p>
        </div>
      </div>

      <!-- Player Controls & Timeline Center -->
      <div class="flex flex-col items-center gap-2 flex-grow max-w-2xl px-6">
        <!-- Buttons -->
        <div class="flex items-center gap-5">
          <button @click="player.prev" class="text-gray-400 hover:text-white transition-colors cursor-pointer" title="Previous Track">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 6h2v12H6zm3.5 6L18 6v12z"/></svg>
          </button>
          
          <button @click="player.togglePlay" class="w-10 h-10 rounded-full bg-white hover:scale-105 transition-transform flex items-center justify-center text-black shadow-lg shadow-white/10 cursor-pointer">
            <svg v-if="!player.isPlaying" class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
            <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
          </button>

          <button @click="player.next" class="text-gray-400 hover:text-white transition-colors cursor-pointer" title="Next Track">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M16 6h2v12h-2zm-10.5 12V6l8.5 6z"/></svg>
          </button>
        </div>

        <!-- Timeline Slider -->
        <div class="flex items-center gap-3 w-full text-xs font-semibold text-gray-500">
          <span>{{ formatTime(player.currentTime) }}</span>
          <div class="flex-grow relative group py-2">
            <input 
              type="range" 
              min="0" 
              :max="player.duration || 100" 
              :value="player.currentTime"
              @input="onSeek"
              class="w-full h-1 rounded-full appearance-none outline-none cursor-pointer accent-white group-hover:h-1.5 transition-all"
              :style="{ background: `linear-gradient(to right, #ffffff ${(player.currentTime / (player.duration || 1)) * 100}%, #1f2937 ${(player.currentTime / (player.duration || 1)) * 100}%)` }"
            />
          </div>
          <span>{{ formatTime(player.duration) }}</span>
        </div>
      </div>

      <!-- Volume & Options Right -->
      <div class="flex items-center justify-end gap-4 w-1/4 min-w-[180px] text-gray-400">
        <!-- Volume Icon -->
        <button @click="toggleMute" class="hover:text-white transition-colors cursor-pointer">
          <svg v-if="player.volume === 0" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77zM4.5 9H9l5-5v16l-5-5H4.5V9z"/></svg>
          <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg>
        </button>
        <!-- Volume Input Slider -->
        <input 
          type="range" 
          min="0" 
          max="1" 
          step="0.05"
          :value="player.volume"
          @input="onVolumeChange"
          class="w-20 h-1 rounded-full appearance-none outline-none cursor-pointer accent-white"
          :style="{ background: `linear-gradient(to right, #ffffff ${player.volume * 100}%, #1f2937 ${player.volume * 100}%)` }"
        />

        <!-- Lyrics Toggle Button -->
        <button 
          @click="toggleLyricsPane"
          class="w-9 h-9 rounded-lg flex items-center justify-center transition-all cursor-pointer border text-xs"
          :class="showLyricsPane ? 'border-orange-500 text-orange-500 bg-orange-600/10 font-bold shadow-lg shadow-orange-950/30' : 'border-gray-800 text-gray-400 hover:text-white hover:border-gray-600 bg-gray-900/40'"
          title="Toggle Lyrics"
        >
          🎤
        </button>
      </div>
    </div>

    <!-- Sliding Lyrics Side Pane -->
    <div 
      class="fixed top-20 right-0 bottom-24 w-full sm:w-96 bg-[#0a0e1a]/98 border-l border-gray-800 shadow-2xl backdrop-blur-2xl z-[45] flex flex-col transition-transform duration-300"
      :class="showLyricsPane && player.currentTrack ? 'translate-x-0' : 'translate-x-full'"
    >
      <!-- Header -->
      <div class="p-6 border-b border-gray-800 flex items-center justify-between bg-gray-950/40">
        <div class="flex items-center gap-3">
          <span class="text-xl">🎤</span>
          <h4 class="font-extrabold text-white text-base tracking-tight">Song Lyrics</h4>
        </div>
        <button @click="showLyricsPane = false" class="text-gray-500 hover:text-white cursor-pointer transition-colors outline-none text-2xl font-bold">
          &times;
        </button>
      </div>

      <!-- Track details -->
      <div v-if="player.currentTrack" class="p-6 border-b border-gray-850 flex items-center gap-4 bg-gray-950/20">
        <img :src="player.currentTrack.thumbnail_url" class="w-12 h-12 rounded-lg object-cover border border-gray-800 shadow-md" alt="Cover" />
        <div class="truncate">
          <h5 class="font-bold text-white text-sm truncate">{{ player.currentTrack.title }}</h5>
          <p class="text-xs text-orange-400 font-medium truncate mt-0.5">{{ player.currentTrack.producer_name }}</p>
        </div>
      </div>

      <!-- Lyrics Body scroll container -->
      <div class="flex-grow overflow-y-auto p-6 space-y-4 select-text">
        <div v-if="player.currentTrack?.lyrics" class="text-sm md:text-base text-gray-200 leading-relaxed whitespace-pre-line text-center font-bold tracking-wide py-4">
          {{ player.currentTrack.lyrics }}
        </div>
        <div v-else class="h-full flex flex-col items-center justify-center text-center text-gray-500 py-24 space-y-4">
          <span class="text-3xl">🤫</span>
          <h5 class="font-bold text-gray-400 text-sm">No lyrics found</h5>
          <p class="text-xs text-gray-600 max-w-[200px] mx-auto">The producer hasn't added lyrics for this track yet.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed, onMounted, ref } from 'vue';
import { playerStore } from '../Stores/playerStore';
import { db } from '../firebase';
import { doc, updateDoc } from 'firebase/firestore';
import { state, navigateTo, logoutUser } from '../store';

export default {
  name: 'AppLayout',
  setup() {
    const userName = computed(() => state.currentUser?.name || '');
    const userRole = computed(() => state.currentUser?.role || 'user');

    const player = computed(() => playerStore);
    const showLyricsPane = ref(false);

    // Initialize player store on mounting
    onMounted(() => {
      playerStore.init();
    });

    const formatTime = (secs) => {
      if (!secs) return '0:00';
      const m = Math.floor(secs / 60);
      const s = Math.floor(secs % 60);
      return `${m}:${s < 10 ? '0' : ''}${s}`;
    };

    const onSeek = (e) => {
      playerStore.seek(parseFloat(e.target.value));
    };

    const onVolumeChange = (e) => {
      playerStore.setVolume(parseFloat(e.target.value));
    };

    const previousVolume = ref(0.8);
    const toggleMute = () => {
      if (playerStore.volume > 0) {
        previousVolume.value = playerStore.volume;
        playerStore.setVolume(0);
      } else {
        playerStore.setVolume(previousVolume.value);
      }
    };

    const toggleLyricsPane = () => {
      showLyricsPane.value = !showLyricsPane.value;
    };

    const logout = async () => {
      await logoutUser();
    };

    return {
      state,
      userName,
      userRole,
      player,
      showLyricsPane,
      formatTime,
      onSeek,
      onVolumeChange,
      toggleMute,
      toggleLyricsPane,
      logout,
      navigateTo
    };
  }
}
</script>

<style scoped>
/* Range Slider styling */
input[type="range"]::-webkit-slider-thumb {
  appearance: none;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: white;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
  cursor: pointer;
}
input[type="range"]::-moz-range-thumb {
  width: 12px;
  height: 12px;
  border: 0;
  border-radius: 50%;
  background: white;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
  cursor: pointer;
}
</style>
