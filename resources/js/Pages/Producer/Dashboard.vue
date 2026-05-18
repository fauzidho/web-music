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
        <div class="px-6 py-5 border-b border-gray-800 bg-[#0e1322]/40 flex items-center justify-between">
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
                <th class="px-6 py-4 text-center">Lyrics</th>
                <th class="px-6 py-4">Release Date</th>
                <th class="px-6 py-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-800/60">
              <tr v-for="song in songsList" :key="song.id" class="hover:bg-white/[0.02] transition-colors group">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <img :src="song.thumbnail_url" class="w-10 h-10 rounded-lg object-cover border border-gray-800 shadow flex-shrink-0" alt="cover" />
                    <div class="truncate max-w-[180px]">
                      <div class="font-bold text-white truncate">{{ song.title }}</div>
                      <div class="text-[10px] text-gray-600 mt-0.5 font-mono truncate">{{ song.id }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2.5 py-1 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-400 text-xs font-bold uppercase">
                    {{ song.genre }}
                  </span>
                </td>
                <td class="px-6 py-4 text-gray-400 font-semibold text-sm">
                  {{ formatDuration(song.duration) }}
                </td>
                <td class="px-6 py-4 text-center font-extrabold text-white text-base">
                  {{ song.plays_count || 0 }}
                </td>
                <td class="px-6 py-4 text-center font-extrabold text-pink-400 text-base">
                  {{ song.likes_count || 0 }}
                </td>
                <td class="px-6 py-4 text-center">
                  <span v-if="song.lyrics" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-bold">
                    <span>✓</span> Ada
                  </span>
                  <span v-else class="inline-flex items-center px-2 py-0.5 rounded-full bg-gray-800/50 border border-gray-700/40 text-gray-500 text-[10px] font-bold">
                    — Kosong
                  </span>
                </td>
                <td class="px-6 py-4 text-gray-400 text-sm">
                  {{ formatDate(song.created_at) }}
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="inline-flex gap-2">
                    <!-- Edit Track Button -->
                    <button
                      @click="openEditModal(song)"
                      class="text-xs bg-blue-500/10 hover:bg-blue-500 border border-blue-500/20 hover:border-blue-500 text-blue-400 hover:text-white px-3 py-1.5 rounded-lg font-bold transition-all cursor-pointer"
                    >
                      ✏️ Edit
                    </button>
                    <!-- Delete Track Button -->
                    <button
                      @click="deleteTrack(song.id)"
                      class="text-xs bg-red-500/10 hover:bg-red-500 border border-red-500/20 hover:border-red-500 text-red-400 hover:text-white px-3 py-1.5 rounded-lg font-bold transition-all cursor-pointer"
                    >
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

    <!-- ══════════════════════════════════════ -->
    <!-- EDIT TRACK MODAL                       -->
    <!-- ══════════════════════════════════════ -->
    <Teleport to="body">
      <div
        v-if="showEditModal"
        class="fixed inset-0 bg-black/80 backdrop-blur-md z-[70] flex items-center justify-center p-4"
        @click.self="closeEditModal"
      >
        <div class="w-full max-w-lg bg-gradient-to-b from-[#131c2e] to-[#0d1320] border border-gray-700/80 rounded-3xl shadow-2xl overflow-hidden">

          <!-- Modal Header -->
          <div class="flex items-center justify-between px-8 py-6 border-b border-gray-800/80 bg-[#0f1828]/60">
            <div>
              <span class="text-[10px] font-bold text-blue-400 uppercase tracking-widest">Producer Tools</span>
              <h3 class="text-xl font-black text-white mt-0.5">Edit Track</h3>
            </div>
            <button
              @click="closeEditModal"
              class="w-8 h-8 rounded-full bg-gray-800/60 hover:bg-gray-700 text-gray-400 hover:text-white flex items-center justify-center text-lg transition-all cursor-pointer"
            >
              &times;
            </button>
          </div>

          <!-- Modal Body -->
          <div class="px-8 py-6 space-y-5 max-h-[70vh] overflow-y-auto">

            <!-- Track Title -->
            <div>
              <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                Track Title
              </label>
              <input
                v-model="editForm.title"
                type="text"
                placeholder="e.g. Neon Shadows"
                class="w-full h-12 rounded-xl bg-gray-900/70 border border-gray-700 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/50 outline-none px-4 text-sm text-white placeholder-gray-500 transition-all"
              />
            </div>

            <!-- Genre -->
            <div>
              <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                Genre
              </label>
              <select
                v-model="editForm.genre"
                class="w-full h-12 rounded-xl bg-gray-900/70 border border-gray-700 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/50 outline-none px-4 text-sm text-white transition-all cursor-pointer"
              >
                <option value="" disabled>Pilih Genre</option>
                <option value="Lo-Fi">🎧 Lo-Fi Beat</option>
                <option value="Synthwave">🌆 Synthwave</option>
                <option value="Hip-Hop">🔥 Hip-Hop / Rap</option>
                <option value="EDM">⚡ EDM / Electronic</option>
                <option value="Acoustic">🎸 Acoustic / Indie</option>
                <option value="Classical">🎹 Classical</option>
              </select>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-800/80 pt-2">
              <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                🎤 Lirik Lagu
                <span class="ml-2 text-[9px] text-gray-600 normal-case tracking-normal">(opsional)</span>
              </label>
              <textarea
                v-model="editForm.lyrics"
                rows="8"
                placeholder="Ketik atau paste lirik lagu di sini..."
                class="w-full p-4 rounded-2xl bg-gray-900/70 border border-gray-700 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/50 outline-none text-sm text-gray-200 placeholder-gray-600 transition-all resize-none leading-relaxed"
              ></textarea>
              <p class="text-[10px] text-gray-600 mt-1.5">Lirik ini akan muncul di panel kanan saat pendengar memutar lagu.</p>
            </div>

            <!-- Error display -->
            <div v-if="editError" class="p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-semibold flex items-center gap-2">
              <span>⚠️</span> {{ editError }}
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="px-8 py-5 border-t border-gray-800/80 bg-[#0f1828]/40 flex items-center gap-3">
            <button
              @click="closeEditModal"
              class="flex-1 h-11 rounded-xl border border-gray-700 hover:border-gray-500 text-gray-400 hover:text-white text-sm font-bold transition-all cursor-pointer"
            >
              Batal
            </button>
            <button
              @click="saveEdit"
              :disabled="saving"
              class="flex-[2] h-11 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-bold text-sm hover:opacity-90 transition-opacity flex items-center justify-center gap-2 shadow-lg disabled:opacity-50 cursor-pointer"
            >
              <svg v-if="saving" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
              </svg>
              <span>{{ saving ? 'Menyimpan...' : '💾 Simpan Perubahan' }}</span>
            </button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { db } from '../../firebase';
import { collection, onSnapshot, doc, deleteDoc, updateDoc } from 'firebase/firestore';
import { state, navigateTo, logoutUser } from '../../store';

export default {
  name: 'ProducerDashboard',
  setup() {
    const songsList = ref([]);

    // ── Edit Modal state ───────────────────────────────────────
    const showEditModal = ref(false);
    const activeSong    = ref(null);
    const saving        = ref(false);
    const editError     = ref('');
    const editForm      = ref({ title: '', genre: '', lyrics: '' });

    // ── Computed ───────────────────────────────────────────────
    const userName    = computed(() => state.currentUser?.name || 'Producer');
    const userRole    = computed(() => state.currentUser?.role || 'producer');
    const totalPlays  = computed(() =>
      songsList.value.reduce((acc, s) => acc + (s.plays_count || 0), 0)
    );
    const activeGenres = computed(() => {
      const g = songsList.value.map(s => s.genre?.toLowerCase()).filter(Boolean);
      return new Set(g).size;
    });

    // ── Firestore real-time listener ───────────────────────────
    let unsubscribe = null;
    onMounted(() => {
      const uid = state.currentUser?.uid;
      if (!uid) return;
      unsubscribe = onSnapshot(collection(db, 'songs'), (snapshot) => {
        const all = snapshot.docs.map(d => ({ id: d.id, ...d.data() }));
        songsList.value = all.filter(s => s.producer_uid === uid || s.producer_id === uid);
      }, (err) => console.error('Catalog load failed:', err));
    });
    onUnmounted(() => { if (unsubscribe) unsubscribe(); });

    // ── Helpers ────────────────────────────────────────────────
    const formatDuration = (secs) => {
      if (!secs) return '0:00';
      const m = Math.floor(secs / 60);
      const s = Math.floor(secs % 60);
      return `${m}:${s < 10 ? '0' : ''}${s}`;
    };
    const formatDate = (dateStr) => {
      if (!dateStr) return '-';
      try { return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }); }
      catch { return dateStr; }
    };

    // ── Delete ─────────────────────────────────────────────────
    const deleteTrack = async (songId) => {
      if (confirm('Yakin ingin menghapus track ini secara permanen dari BeatGround?')) {
        try { await deleteDoc(doc(db, 'songs', songId)); }
        catch (err) { console.error('Delete failed:', err); }
      }
    };

    // ── Edit Modal ─────────────────────────────────────────────
    const openEditModal = (song) => {
      activeSong.value  = song;
      editForm.value    = { title: song.title || '', genre: song.genre || '', lyrics: song.lyrics || '' };
      editError.value   = '';
      showEditModal.value = true;
    };
    const closeEditModal = () => {
      showEditModal.value = false;
      activeSong.value    = null;
    };
    const saveEdit = async () => {
      if (!editForm.value.title.trim()) {
        editError.value = 'Judul track tidak boleh kosong.';
        return;
      }
      if (!editForm.value.genre) {
        editError.value = 'Silakan pilih genre.';
        return;
      }
      saving.value     = true;
      editError.value  = '';
      try {
        await updateDoc(doc(db, 'songs', activeSong.value.id), {
          title:  editForm.value.title.trim(),
          genre:  editForm.value.genre,
          lyrics: editForm.value.lyrics.trim()
        });
        closeEditModal();
      } catch (err) {
        editError.value = 'Gagal menyimpan: ' + err.message;
      } finally {
        saving.value = false;
      }
    };

    const logout = async () => { await logoutUser(); };

    return {
      songsList, userName, userRole,
      totalPlays, activeGenres,
      formatDuration, formatDate,
      deleteTrack,
      showEditModal, activeSong, editForm, editError, saving,
      openEditModal, closeEditModal, saveEdit,
      logout, navigateTo
    };
  }
}
</script>
