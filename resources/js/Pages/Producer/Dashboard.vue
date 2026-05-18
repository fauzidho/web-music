<template>
  <div class="min-h-screen bg-[#0b0f19] text-gray-100 selection:bg-purple-600 selection:text-white">
    <!-- Header/Nav -->
    <header class="border-b border-gray-800 bg-[#0e1322]/80 backdrop-blur-md sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <img src="/BeatGround.png" class="w-10 h-10 rounded-xl object-cover shadow-lg shadow-purple-900/30" alt="BeatGround Logo" />
          <span class="text-2xl font-black bg-clip-text text-transparent bg-gradient-to-r from-white via-gray-200 to-gray-400 tracking-tight">Beat<span class="text-purple-500">Ground</span></span>
          <span class="px-2 py-0.5 text-[10px] font-bold tracking-wider bg-purple-600/20 border border-purple-500/30 rounded text-purple-400 uppercase ml-2">Creator Hub</span>
        </div>

        <div class="flex items-center gap-6">
          <div class="flex items-center gap-3 text-right">
            <div class="hidden sm:block">
              <div class="text-sm font-bold text-white">{{ $page.props.auth.user.name }}</div>
              <div class="text-xs text-purple-400 font-semibold uppercase tracking-wider">{{ $page.props.auth.user.role }}</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-purple-600/30 border border-purple-500/50 flex items-center justify-center font-bold text-white">
              {{ $page.props.auth.user.name[0] }}
            </div>
          </div>
          <a href="/" class="text-sm font-semibold text-gray-400 hover:text-white transition-colors">Return to Browse</a>
          <button @click="logout" class="text-gray-400 hover:text-red-400 transition-colors text-sm font-semibold">Sign Out</button>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-6 py-10 space-y-10">
      <!-- Welcome Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-4xl font-extrabold text-white tracking-tight">Creator Dashboard</h1>
          <p class="text-sm text-gray-400 mt-1">Manage your tracks, monitor stats, and upload new beats</p>
        </div>
        <a href="/producer/upload" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-purple-600 to-pink-500 text-white font-bold px-6 h-12 rounded-xl hover:opacity-90 transition-opacity shadow-lg shadow-purple-950/40">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
          </svg>
          <span>Upload New Track</span>
        </a>
      </div>

      <!-- Quick Metrics -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-b from-[#111827] to-[#0f172a] border border-gray-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
          <div class="absolute top-0 right-0 w-24 h-24 bg-purple-500/5 rounded-full blur-2xl"></div>
          <div class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Total Plays</div>
          <div class="text-4xl font-black text-white mt-2">{{ totalPlays }}</div>
          <div class="text-xs text-purple-400 font-semibold mt-2">Realtime from Firestore</div>
        </div>

        <div class="bg-gradient-to-b from-[#111827] to-[#0f172a] border border-gray-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
          <div class="absolute top-0 right-0 w-24 h-24 bg-pink-500/5 rounded-full blur-2xl"></div>
          <div class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Uploaded Tracks</div>
          <div class="text-4xl font-black text-white mt-2">{{ songs.length }}</div>
          <div class="text-xs text-pink-400 font-semibold mt-2">Fully Hosted on Cloudinary</div>
        </div>

        <div class="bg-gradient-to-b from-[#111827] to-[#0f172a] border border-gray-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
          <div class="absolute top-0 right-0 w-24 h-24 bg-orange-500/5 rounded-full blur-2xl"></div>
          <div class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Genres Active</div>
          <div class="text-4xl font-black text-white mt-2">{{ activeGenres }}</div>
          <div class="text-xs text-orange-400 font-semibold mt-2">Audience reach</div>
        </div>
      </div>

      <!-- Song List Table -->
      <div class="bg-gradient-to-b from-[#111827] to-[#0f172a] border border-gray-800 rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-800 flex justify-between items-center">
          <h3 class="text-lg font-bold text-white">Your Released Beats</h3>
          <span class="px-3 py-1 rounded-full bg-gray-900 border border-gray-800 text-xs text-gray-400 font-medium">
            {{ songs.length }} Tracks
          </span>
        </div>

        <!-- Empty State -->
        <div v-if="songs.length === 0" class="p-20 text-center space-y-4">
          <div class="w-16 h-16 rounded-full bg-gray-900 border border-gray-800 flex items-center justify-center mx-auto text-gray-500">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
            </svg>
          </div>
          <h4 class="text-lg font-bold text-white">No tracks released yet</h4>
          <p class="text-sm text-gray-500 max-w-sm mx-auto">Upload your first audio track to make it public for BeatGround listeners.</p>
          <a href="/producer/upload" class="inline-flex bg-purple-600 hover:bg-purple-500 text-white font-bold px-6 py-3 rounded-xl transition-colors text-sm shadow-lg shadow-purple-950/40">
            Release Your First Beat
          </a>
        </div>

        <!-- Tracks List -->
        <div v-else class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-gray-800/80 text-xs font-semibold text-gray-500 uppercase bg-[#0d121f]">
                <th class="px-6 py-4">Track Details</th>
                <th class="px-6 py-4">Genre</th>
                <th class="px-6 py-4">Duration</th>
                <th class="px-6 py-4 text-center">Plays</th>
                <th class="px-6 py-4 text-center">Likes</th>
                <th class="px-6 py-4">Uploaded At</th>
                <th class="px-6 py-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-800/60 text-sm font-medium">
              <tr v-for="song in songs" :key="song.id" class="hover:bg-gray-900/30 transition-colors">
                <td class="px-6 py-4 flex items-center gap-4">
                  <!-- Thumbnail -->
                  <img :src="song.thumbnail_url" class="w-12 h-12 rounded-lg object-cover border border-gray-800 shadow-md flex-shrink-0" alt="Track cover" />
                  <div>
                    <div class="font-bold text-white text-base">{{ song.title }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">ID: {{ song.id }}</div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2.5 py-1 rounded-full bg-purple-500/10 border border-purple-500/20 text-purple-400 text-xs font-bold uppercase">
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
                    <button @click="deleteTrack(song.id)" class="text-xs bg-red-500/10 hover:bg-red-500 border border-red-500/20 hover:border-red-500 text-red-400 hover:text-white px-3 py-1.5 rounded-lg font-bold transition-all">
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
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';

export default {
  name: 'ProducerDashboard',
  props: {
    songs: {
      type: Array,
      required: true
    }
  },
  setup(props) {
    const totalPlays = computed(() => {
      return props.songs.reduce((acc, song) => acc + (song.plays_count || 0), 0);
    });

    const activeGenres = computed(() => {
      const genres = props.songs.map(song => song.genre?.toLowerCase()).filter(Boolean);
      return new Set(genres).size;
    });

    const formatDuration = (secs) => {
      if (!secs) return '0:00';
      const m = Math.floor(secs / 60);
      const s = secs % 60;
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

    const deleteTrack = (songId) => {
      if (confirm('Are you absolutely sure you want to delete this track? This will permanently delete both the audio file and thumbnail from Cloudinary.')) {
        router.delete(`/producer/track/${songId}`);
      }
    };

    const logout = () => {
      router.post('/logout');
    };

    return {
      totalPlays,
      activeGenres,
      formatDuration,
      formatDate,
      deleteTrack,
      logout
    };
  }
}
</script>
