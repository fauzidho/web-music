<template>
  <div class="min-h-screen bg-[#0b0f19] text-gray-100 selection:bg-purple-600 selection:text-white">
    <!-- Header -->
    <header class="border-b border-gray-800 bg-[#0e1322]/80 backdrop-blur-md sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <img src="/BeatGround.png" class="w-10 h-10 rounded-xl object-cover shadow-lg shadow-purple-900/30" alt="BeatGround Logo" />
          <span class="text-2xl font-black bg-clip-text text-transparent bg-gradient-to-r from-white via-gray-200 to-gray-400 tracking-tight">Beat<span class="text-purple-500">Ground</span></span>
          <span class="px-2 py-0.5 text-[10px] font-bold tracking-wider bg-red-600/20 border border-red-500/30 rounded text-red-400 uppercase ml-2">Admin Panel</span>
        </div>

        <div class="flex items-center gap-6">
          <a href="/" class="text-sm font-semibold text-gray-400 hover:text-white transition-colors">Return to Browse</a>
          <button @click="logout" class="text-xs font-semibold text-gray-400 hover:text-red-400 transition-colors">Sign Out</button>
        </div>
      </div>
    </header>

    <!-- Content Wrapper -->
    <main class="max-w-7xl mx-auto px-6 py-10 space-y-10">
      <!-- Title -->
      <div>
        <h1 class="text-4xl font-extrabold text-white tracking-tight">Platform Monitoring</h1>
        <p class="text-sm text-gray-400 mt-1">Review active users, moderate tracks, and track aggregate statistics</p>
      </div>

      <!-- Quick Metrics -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-b from-[#111827] to-[#0f172a] border border-gray-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
          <div class="absolute top-0 right-0 w-24 h-24 bg-orange-500/5 rounded-full blur-2xl"></div>
          <div class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Total Listeners</div>
          <div class="text-4xl font-black text-white mt-2">{{ totalListeners }}</div>
          <div class="text-xs text-orange-400 font-semibold mt-2">Active Profiles</div>
        </div>

        <div class="bg-gradient-to-b from-[#111827] to-[#0f172a] border border-gray-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
          <div class="absolute top-0 right-0 w-24 h-24 bg-pink-500/5 rounded-full blur-2xl"></div>
          <div class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Total Creators</div>
          <div class="text-4xl font-black text-white mt-2">{{ totalProducers }}</div>
          <div class="text-xs text-pink-400 font-semibold mt-2">Independent Beats creators</div>
        </div>

        <div class="bg-gradient-to-b from-[#111827] to-[#0f172a] border border-gray-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
          <div class="absolute top-0 right-0 w-24 h-24 bg-orange-500/5 rounded-full blur-2xl"></div>
          <div class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Total Uploaded Tracks</div>
          <div class="text-4xl font-black text-white mt-2">{{ songs.length }}</div>
          <div class="text-xs text-orange-400 font-semibold mt-2">Cloudinary & Firestore</div>
        </div>

        <div class="bg-gradient-to-b from-[#111827] to-[#0f172a] border border-gray-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
          <div class="absolute top-0 right-0 w-24 h-24 bg-green-500/5 rounded-full blur-2xl"></div>
          <div class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Total Listen Count</div>
          <div class="text-4xl font-black text-white mt-2">{{ totalPlays }}</div>
          <div class="text-xs text-green-400 font-semibold mt-2">Aggregated Plays</div>
        </div>
      </div>

      <!-- Tabbed Views -->
      <div class="space-y-6">
        <!-- Tabs Header -->
        <div class="flex gap-4 border-b border-gray-800 pb-3">
          <button 
            @click="activeTab = 'users'"
            class="h-10 px-6 rounded-xl text-xs font-bold uppercase tracking-wider transition-all"
            :class="activeTab === 'users' ? 'bg-orange-600 text-white' : 'text-gray-400 hover:text-white'"
          >
            User Accounts ({{ users.length }})
          </button>
          <button 
            @click="activeTab = 'songs'"
            class="h-10 px-6 rounded-xl text-xs font-bold uppercase tracking-wider transition-all"
            :class="activeTab === 'songs' ? 'bg-orange-600 text-white' : 'text-gray-400 hover:text-white'"
          >
            Content Moderation ({{ songs.length }})
          </button>
        </div>

        <!-- Tab 1: User Management -->
        <div v-if="activeTab === 'users'" class="bg-gradient-to-b from-[#111827] to-[#0f172a] border border-gray-800 rounded-2xl shadow-2xl overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="border-b border-gray-800/80 text-xs font-semibold text-gray-500 uppercase bg-[#0d121f]">
                  <th class="px-6 py-4">User UID</th>
                  <th class="px-6 py-4">Display Name</th>
                  <th class="px-6 py-4">Email</th>
                  <th class="px-6 py-4">Account Type</th>
                  <th class="px-6 py-4">Joined At</th>
                  <th class="px-6 py-4 text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-800/60 text-sm font-medium">
                <tr v-for="user in users" :key="user.uid" class="hover:bg-gray-900/30 transition-colors">
                  <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ user.uid }}</td>
                  <td class="px-6 py-4 font-bold text-white">{{ user.name }}</td>
                  <td class="px-6 py-4 text-gray-400">{{ user.email }}</td>
                  <td class="px-6 py-4">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase"
                      :class="user.role === 'admin' 
                        ? 'bg-red-500/10 border border-red-500/20 text-red-400' 
                        : (user.role === 'producer' ? 'bg-amber-500/10 border border-amber-500/20 text-amber-400' : 'bg-orange-500/10 border border-orange-500/20 text-orange-400')"
                    >
                      {{ user.role }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-gray-400">{{ formatDate(user.created_at) }}</td>
                  <td class="px-6 py-4 text-right">
                    <button 
                      v-if="user.role !== 'admin'"
                      @click="toggleRole(user.uid)" 
                      class="text-xs bg-orange-500/10 hover:bg-orange-600 border border-orange-500/20 text-orange-400 hover:text-white px-3 py-1.5 rounded-lg font-bold transition-all"
                    >
                      Switch Role
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Tab 2: Song Moderation -->
        <div v-if="activeTab === 'songs'" class="bg-gradient-to-b from-[#111827] to-[#0f172a] border border-gray-800 rounded-2xl shadow-2xl overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="border-b border-gray-800/80 text-xs font-semibold text-gray-500 uppercase bg-[#0d121f]">
                  <th class="px-6 py-4">Track Information</th>
                  <th class="px-6 py-4">Genre</th>
                  <th class="px-6 py-4">Uploaded By</th>
                  <th class="px-6 py-4 text-center">Plays</th>
                  <th class="px-6 py-4">Status</th>
                  <th class="px-6 py-4 text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-800/60 text-sm font-medium">
                <tr v-for="song in songs" :key="song.id" class="hover:bg-gray-900/30 transition-colors">
                  <td class="px-6 py-4 flex items-center gap-4">
                    <img :src="song.thumbnail_url" class="w-12 h-12 rounded-lg object-cover border border-gray-800 shadow" alt="" />
                    <div>
                      <div class="font-bold text-white text-base">{{ song.title }}</div>
                      <div class="text-xs text-gray-500 mt-0.5">ID: {{ song.id }}</div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <span class="px-2.5 py-1 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-400 text-xs font-bold uppercase">
                      {{ song.genre }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-gray-300">{{ song.producer_name }}</td>
                  <td class="px-6 py-4 text-center font-extrabold text-white">{{ song.plays_count || 0 }}</td>
                  <td class="px-6 py-4">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase"
                      :class="song.is_moderated 
                        ? 'bg-red-500/15 border border-red-500/30 text-red-400' 
                        : 'bg-green-500/10 border border-green-500/20 text-green-400'"
                    >
                      {{ song.is_moderated ? '🛑 Flagged' : '✅ Active' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <div class="inline-flex gap-3">
                      <button 
                        @click="toggleModeration(song.id)"
                        class="text-xs px-3 py-1.5 rounded-lg font-bold border transition-all"
                        :class="song.is_moderated 
                          ? 'bg-green-500/10 border-green-500/20 text-green-400 hover:bg-green-500 hover:text-white' 
                          : 'bg-yellow-500/10 border-yellow-500/20 text-yellow-400 hover:bg-yellow-500 hover:text-white'"
                      >
                        {{ song.is_moderated ? 'Approve' : 'Flag' }}
                      </button>
                      <button 
                        @click="deleteTrack(song.id)"
                        class="text-xs bg-red-500/10 hover:bg-red-500 border border-red-500/20 hover:border-red-500 text-red-400 hover:text-white px-3 py-1.5 rounded-lg font-bold transition-all"
                      >
                        Force Delete
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

export default {
  name: 'AdminDashboard',
  props: {
    users: {
      type: Array,
      required: true
    },
    songs: {
      type: Array,
      required: true
    }
  },
  setup(props) {
    const activeTab = ref('users');

    const totalListeners = computed(() => {
      return props.users.filter(u => u.role === 'user').length;
    });

    const totalProducers = computed(() => {
      return props.users.filter(u => u.role === 'producer').length;
    });

    const totalPlays = computed(() => {
      return props.songs.reduce((acc, song) => acc + (song.plays_count || 0), 0);
    });

    const toggleRole = (uid) => {
      if (confirm('Are you sure you want to change this user role?')) {
        router.post(`/admin/user/${uid}/role`);
      }
    };

    const toggleModeration = (songId) => {
      router.post(`/admin/track/${songId}/moderation`);
    };

    const deleteTrack = (songId) => {
      if (confirm('Are you absolutely sure you want to FORCE moderate and delete this track from the ecosystem? This is permanent.')) {
        router.delete(`/admin/track/${songId}`);
      }
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

    const logout = () => {
      router.post('/logout');
    };

    return {
      activeTab,
      totalListeners,
      totalProducers,
      totalPlays,
      toggleRole,
      toggleModeration,
      deleteTrack,
      formatDate,
      logout
    };
  }
}
</script>
