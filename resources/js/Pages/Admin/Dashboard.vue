<template>
  <div class="min-h-screen bg-[#0b0f19] text-gray-100 selection:bg-purple-600 selection:text-white">

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
          <div class="text-4xl font-black text-white mt-2">{{ songsList.length }}</div>
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
        <div class="flex border-b border-gray-800/80 gap-6">
          <button 
            @click="activeTab = 'users'"
            class="pb-4 font-bold text-sm tracking-wide uppercase transition-all relative cursor-pointer outline-none"
            :class="activeTab === 'users' ? 'text-purple-400' : 'text-gray-500 hover:text-gray-300'"
          >
            Users Monitoring
            <span v-if="activeTab === 'users'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-purple-500 rounded-full"></span>
          </button>
          
          <button 
            @click="activeTab = 'tracks'"
            class="pb-4 font-bold text-sm tracking-wide uppercase transition-all relative cursor-pointer outline-none"
            :class="activeTab === 'tracks' ? 'text-purple-400' : 'text-gray-500 hover:text-gray-300'"
          >
            Tracks Moderation
            <span v-if="activeTab === 'tracks'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-purple-500 rounded-full"></span>
          </button>
        </div>

        <!-- Users Table Tab -->
        <div v-if="activeTab === 'users'" class="bg-[#0e1322]/60 border border-gray-800/80 rounded-2xl overflow-hidden shadow-2xl">
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="border-b border-gray-800 text-gray-500 text-xs font-bold uppercase tracking-wider bg-gray-950/20">
                  <th class="px-6 py-4">User Name</th>
                  <th class="px-6 py-4">Email</th>
                  <th class="px-6 py-4">Account Role</th>
                  <th class="px-6 py-4">Joined At</th>
                  <th class="px-6 py-4 text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-850">
                <tr v-for="user in usersList" :key="user.id" class="hover:bg-gray-850/10 transition-colors group">
                  <td class="px-6 py-4 font-bold text-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded bg-purple-600/10 border border-purple-500/20 flex items-center justify-center font-black text-purple-400 text-xs">
                      {{ user.name ? user.name[0] : 'U' }}
                    </div>
                    <span>{{ user.name }}</span>
                  </td>
                  <td class="px-6 py-4 text-gray-400">{{ user.email }}</td>
                  <td class="px-6 py-4">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide"
                      :class="user.role === 'admin' 
                        ? 'bg-red-500/10 border border-red-500/20 text-red-400' 
                        : user.role === 'producer' 
                          ? 'bg-orange-500/10 border border-orange-500/20 text-orange-400' 
                          : 'bg-blue-500/10 border border-blue-500/20 text-blue-400'"
                    >
                      {{ user.role }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-gray-500 text-xs">{{ formatDate(user.created_at) }}</td>
                  <td class="px-6 py-4 text-right">
                    <button 
                      v-if="user.role !== 'admin'"
                      @click="toggleRole(user)"
                      class="text-xs bg-purple-500/10 hover:bg-purple-500 border border-purple-500/20 hover:border-purple-500 text-purple-400 hover:text-white px-3 py-1.5 rounded-lg font-bold transition-all cursor-pointer"
                    >
                      Promote to {{ user.role === 'producer' ? 'Listener' : 'Producer' }}
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Tracks Moderation Tab -->
        <div v-if="activeTab === 'tracks'" class="bg-[#0e1322]/60 border border-gray-800/80 rounded-2xl overflow-hidden shadow-2xl">
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="border-b border-gray-800 text-gray-500 text-xs font-bold uppercase tracking-wider bg-gray-950/20">
                  <th class="px-6 py-4">Track Title</th>
                  <th class="px-6 py-4">Producer</th>
                  <th class="px-6 py-4 text-center">Plays</th>
                  <th class="px-6 py-4">Status</th>
                  <th class="px-6 py-4 text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-850">
                <tr v-for="song in songsList" :key="song.id" class="hover:bg-gray-850/10 transition-colors">
                  <td class="px-6 py-4 flex items-center gap-3">
                    <img :src="song.thumbnail_url" class="w-10 h-10 rounded-lg object-cover border border-gray-800" alt="cover" />
                    <div>
                      <div class="font-bold text-white">{{ song.title }}</div>
                      <div class="text-[10px] text-gray-500 mt-0.5">ID: {{ song.id }}</div>
                    </div>
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
                        @click="toggleModeration(song)"
                        class="text-xs px-3 py-1.5 rounded-lg font-bold border transition-all cursor-pointer"
                        :class="song.is_moderated 
                          ? 'bg-green-500/10 border-green-500/20 text-green-400 hover:bg-green-500 hover:text-white' 
                          : 'bg-yellow-500/10 border-yellow-500/20 text-yellow-400 hover:bg-yellow-500 hover:text-white'"
                      >
                        {{ song.is_moderated ? 'Approve' : 'Flag' }}
                      </button>
                      <button 
                        @click="deleteTrack(song.id)"
                        class="text-xs bg-red-500/10 hover:bg-red-500 border border-red-500/20 hover:border-red-500 text-red-400 hover:text-white px-3 py-1.5 rounded-lg font-bold transition-all cursor-pointer"
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
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { db } from '../../firebase';
import { collection, onSnapshot, doc, updateDoc, deleteDoc } from 'firebase/firestore';
import { state, navigateTo, logoutUser } from '../../store';

export default {
  name: 'AdminDashboard',
  setup() {
    const activeTab = ref('users');
    const usersList = ref([]);
    const songsList = ref([]);

    // Live subscriptions to all users and all songs
    let unsubUsers = null;
    let unsubSongs = null;

    onMounted(() => {
      unsubUsers = onSnapshot(collection(db, 'users'), (snapshot) => {
        usersList.value = snapshot.docs.map(doc => ({
          id: doc.id,
          ...doc.data()
        }));
      }, (err) => {
        console.error("Users live subscription failed:", err);
      });

      unsubSongs = onSnapshot(collection(db, 'songs'), (snapshot) => {
        songsList.value = snapshot.docs.map(doc => ({
          id: doc.id,
          ...doc.data()
        }));
      }, (err) => {
        console.error("Songs live subscription failed:", err);
      });
    });

    onUnmounted(() => {
      if (unsubUsers) unsubUsers();
      if (unsubSongs) unsubSongs();
    });

    const totalListeners = computed(() => {
      return usersList.value.filter(u => u.role === 'user').length;
    });

    const totalProducers = computed(() => {
      return usersList.value.filter(u => u.role === 'producer').length;
    });

    const totalPlays = computed(() => {
      return songsList.value.reduce((acc, song) => acc + (song.plays_count || 0), 0);
    });

    const toggleRole = async (user) => {
      const nextRole = user.role === 'producer' ? 'user' : 'producer';
      if (confirm(`Are you sure you want to change user "${user.name}" role to ${nextRole}?`)) {
        try {
          await updateDoc(doc(db, 'users', user.id), {
            role: nextRole
          });
          alert(`Successfully updated user "${user.name}" role to ${nextRole}!`);
        } catch (err) {
          console.error("Failed to update user role:", err);
          if (err.code === 'permission-denied') {
            alert(`Gagal mengubah role: Firebase Security Rules memblokir penulisan ini.\n\nTip: Karena Firestore dijalankan langsung dari client, Anda harus memperbarui Firebase Security Rules di Firebase Console agar mengizinkan Admin mengubah role pengguna lain.\n\nGunakan aturan berikut di Firebase Console:\n\nmatch /users/{userId} {\n  allow read: if true;\n  allow create, update: if request.auth != null && (request.auth.uid == userId || get(/databases/$(database)/documents/users/$(request.auth.uid)).data.role == "admin");\n}`);
          } else {
            alert("Failed to update user role: " + err.message);
          }
        }
      }
    };

    const toggleModeration = async (song) => {
      const nextMod = !song.is_moderated;
      try {
        await updateDoc(doc(db, 'songs', song.id), {
          is_moderated: nextMod
        });
      } catch (err) {
        console.error("Failed to toggle moderation status:", err);
      }
    };

    const deleteTrack = async (songId) => {
      if (confirm('Are you absolutely sure you want to FORCE moderate and delete this track from the ecosystem? This is permanent.')) {
        try {
          await deleteDoc(doc(db, 'songs', songId));
        } catch (err) {
          console.error("Force delete failed:", err);
        }
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

    const logout = async () => {
      await logoutUser();
    };

    return {
      activeTab,
      usersList,
      songsList,
      totalListeners,
      totalProducers,
      totalPlays,
      toggleRole,
      toggleModeration,
      deleteTrack,
      formatDate,
      logout,
      navigateTo
    };
  }
}
</script>
