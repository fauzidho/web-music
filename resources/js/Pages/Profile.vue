<template>
  <div class="space-y-10">
    <!-- Profile Card Banner -->
    <div class="relative bg-gradient-to-r from-orange-900/40 via-amber-950/20 to-[#0e1322]/80 border border-orange-500/10 rounded-3xl p-8 md:p-12 overflow-hidden shadow-xl">
      <div class="absolute -top-24 -right-24 w-80 h-80 bg-orange-600/10 rounded-full blur-3xl"></div>
      
      <!-- Edit Profile Button -->
      <button 
        v-if="isMyProfile"
        @click="openEditModal"
        class="absolute top-6 right-6 px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/10 rounded-xl text-xs font-bold text-white flex items-center gap-1.5 backdrop-blur-md transition-all cursor-pointer shadow-lg hover:scale-105 z-10"
      >
        <span>⚙️ Edit Profile</span>
      </button>

      <div class="relative flex flex-col md:flex-row items-center gap-6 md:gap-10">
        <!-- Initials Avatar -->
        <div class="w-24 h-24 md:w-32 md:h-32 rounded-3xl bg-gradient-to-tr from-orange-600 to-amber-500 border border-orange-500/30 flex items-center justify-center font-black text-white text-3xl md:text-5xl shadow-xl shadow-orange-950/50 select-none">
          {{ userProfile.name ? userProfile.name[0].toUpperCase() : 'U' }}
        </div>

        <!-- Details -->
        <div class="space-y-4 text-center md:text-left flex-grow">
          <div class="space-y-2">
            <div class="flex flex-col md:flex-row items-center justify-center md:justify-start gap-3">
              <h1 class="text-2xl md:text-4xl font-black text-white leading-tight tracking-tight">
                {{ userProfile.name || 'User Profile' }}
              </h1>
              <span class="px-3 py-1 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-400 text-xs font-bold uppercase tracking-wider">
                {{ userProfile.role }}
              </span>
            </div>
            <p v-if="userProfile.email && (isMyProfile || userProfile.role === 'producer')" class="text-xs md:text-sm text-gray-500 font-medium font-mono">
              {{ userProfile.email }}
            </p>
            
            <!-- Self Promote to Producer Option -->
            <div v-if="isMyProfile && userProfile.role === 'user'" class="pt-2">
              <button 
                @click="upgradeToProducer"
                class="px-4 py-2 bg-gradient-to-r from-orange-600 to-amber-500 hover:opacity-90 text-white font-bold text-xs rounded-xl shadow-lg transition-all hover:scale-105 cursor-pointer flex items-center gap-1.5 mx-auto md:mx-0"
              >
                <span>🎹 Upgrade to Producer Account</span>
              </button>
            </div>
          </div>

          <!-- Bio -->
          <p class="text-sm md:text-base text-gray-300 leading-relaxed max-w-xl mx-auto md:mx-0">
            {{ userProfile.bio || (isMyProfile ? 'Introduce yourself! Add a bio by clicking "Edit Profile" above.' : 'No biography provided.') }}
          </p>

          <!-- Social Links -->
          <div class="flex flex-wrap justify-center md:justify-start gap-3 pt-2">
            <a 
              v-if="userProfile.instagram"
              :href="formatUrl(userProfile.instagram)" 
              target="_blank" 
              class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-pink-600/10 to-purple-600/10 border border-pink-500/20 hover:border-pink-500/40 text-pink-400 hover:text-pink-300 text-xs font-bold rounded-xl transition-all hover:scale-105"
            >
              <span>📸 Instagram</span>
            </a>
            <a 
              v-if="userProfile.twitter"
              :href="formatUrl(userProfile.twitter)" 
              target="_blank" 
              class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600/10 to-sky-600/10 border border-blue-500/20 hover:border-blue-500/40 text-blue-400 hover:text-blue-300 text-xs font-bold rounded-xl transition-all hover:scale-105"
            >
              <span>🐦 Twitter / X</span>
            </a>
            <a 
              v-if="userProfile.youtube"
              :href="formatUrl(userProfile.youtube)" 
              target="_blank" 
              class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-red-600/10 to-orange-600/10 border border-red-500/20 hover:border-red-500/40 text-red-400 hover:text-red-300 text-xs font-bold rounded-xl transition-all hover:scale-105"
            >
              <span>🎥 YouTube</span>
            </a>
            <div 
              v-if="!userProfile.instagram && !userProfile.twitter && !userProfile.youtube"
              class="text-xs text-gray-600 font-semibold"
            >
              No social media linked.
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Creator Releases Section -->
    <div v-if="userProfile.role === 'producer' || userProfile.role === 'admin'" class="space-y-6">
      <h3 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
        <span>Released Tracks</span>
        <span class="text-xs bg-gray-900 border border-gray-800 text-gray-500 font-semibold px-2 py-0.5 rounded-full">
          {{ profileSongs.length }} Tracks
        </span>
      </h3>

      <!-- Loading State -->
      <div v-if="profileLoading" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 animate-pulse">
        <div v-for="i in 4" :key="'profile-skel-'+i" class="bg-gray-900/30 border border-gray-800/60 rounded-2xl p-4 flex flex-col gap-4">
          <div class="w-full aspect-square bg-gray-800/80 rounded-xl"></div>
          <div class="space-y-2">
            <div class="h-4 bg-gray-800/80 rounded w-3/4"></div>
            <div class="h-3 bg-gray-800/50 rounded w-1/2"></div>
          </div>
        </div>
      </div>

      <!-- Tracks Grid -->
      <div v-else-if="profileSongs.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div 
          v-for="song in profileSongs" 
          :key="song.id"
          class="group bg-gradient-to-b from-[#111827]/40 to-[#0f172a]/80 border rounded-2xl p-4 shadow-lg hover:shadow-xl hover:border-orange-500/30 transition-all duration-300 relative overflow-hidden"
          :class="{ 'border-orange-600 ring-1 ring-orange-600/30 bg-orange-950/5': isPlayingSong(song.id) , 'border-gray-800': !isPlayingSong(song.id) }"
        >
          <!-- Cover Image Playable -->
          <div @click="playSong(song)" class="relative aspect-square w-full rounded-xl overflow-hidden bg-gray-950 border border-gray-800/80 mb-4 shadow-inner cursor-pointer">
            <img :src="song.thumbnail_url" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="Cover" />
            
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

            <!-- Active Song Indicator -->
            <div v-if="isPlayingSong(song.id) && player.isPlaying" class="absolute bottom-3 left-3 px-2.5 py-1 rounded bg-orange-600/90 text-[10px] font-bold uppercase tracking-wider text-white flex items-center gap-1.5 shadow backdrop-blur-md">
              <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
              <span>Playing</span>
            </div>
          </div>

          <!-- Metadata -->
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

          <!-- Play & Like metrics -->
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

            <!-- Quick Actions -->
            <div class="flex gap-2">
              <button 
                @click="likeSong(song)" 
                class="w-7 h-7 rounded-lg bg-gray-900 border flex items-center justify-center transition-all duration-200 active:scale-75 cursor-pointer outline-none"
                :class="isLiked(song.id) 
                  ? 'border-pink-500 bg-pink-500/10 text-pink-500' 
                  : 'border-gray-800 text-gray-505 hover:bg-pink-500/10 hover:border-pink-500/30 hover:text-pink-400'"
              >
                <svg v-if="isLiked(song.id)" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty releases state -->
      <div v-else class="py-24 text-center border border-gray-800 border-dashed rounded-3xl space-y-4 bg-gray-900/20">
        <svg class="w-12 h-12 text-gray-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
        <h4 class="text-lg font-bold text-white">No tracks released yet</h4>
        <p class="text-sm text-gray-500 max-w-xs mx-auto">This creator hasn't published any productions yet.</p>
      </div>
    </div>

    <!-- Edit Profile Modal Overlay -->
    <div 
      v-if="showEditModal" 
      class="fixed inset-0 z-[70] flex items-center justify-center p-6 bg-black/80 backdrop-blur-sm"
      @click.self="closeEditModal"
    >
      <div class="w-full max-w-lg bg-gradient-to-b from-[#111827] to-[#0f172a] border border-gray-800 rounded-3xl p-6 shadow-2xl relative space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-start">
          <div>
            <h4 class="font-extrabold text-white text-lg tracking-tight">Edit Profile Settings</h4>
            <p class="text-xs text-gray-400 mt-0.5">Customize your bio and external social media links</p>
          </div>
          <button @click="closeEditModal" class="text-gray-500 hover:text-white cursor-pointer transition-colors outline-none text-xl font-bold">
            &times;
          </button>
        </div>

        <form @submit.prevent="saveProfile" class="space-y-4 text-xs font-semibold text-gray-400">
          <!-- Name Field -->
          <div class="space-y-1.5">
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Display Name</label>
            <input 
              v-model="editForm.name"
              type="text" 
              required
              class="w-full h-10 px-4 rounded-xl bg-gray-950 border border-gray-800 focus:border-orange-500 outline-none text-white transition-all font-semibold"
            />
          </div>

          <!-- Bio Field -->
          <div class="space-y-1.5">
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Biography / Description</label>
            <textarea 
              v-model="editForm.bio"
              rows="4"
              placeholder="Tell us about your musical style, background, or anything you like..."
              class="w-full p-4 rounded-xl bg-gray-950 border border-gray-800 focus:border-orange-500 outline-none text-white transition-all font-semibold resize-none leading-relaxed"
            ></textarea>
          </div>

          <!-- Social Links -->
          <div class="border-t border-gray-850 pt-4 space-y-3">
            <h5 class="text-[10px] font-extrabold uppercase text-gray-500 tracking-wider">Social Links</h5>
            
            <div class="space-y-3">
              <!-- Instagram -->
              <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-pink-500/10 border border-pink-500/20 text-pink-400 flex items-center justify-center text-xs flex-shrink-0">📸</span>
                <input 
                  v-model="editForm.instagram"
                  type="text"
                  placeholder="https://instagram.com/username"
                  class="flex-grow h-10 px-4 rounded-xl bg-gray-950 border border-gray-800 focus:border-orange-500 outline-none text-white font-semibold text-xs transition-all"
                />
              </div>

              <!-- Twitter -->
              <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center text-xs flex-shrink-0">🐦</span>
                <input 
                  v-model="editForm.twitter"
                  type="text"
                  placeholder="https://twitter.com/username"
                  class="flex-grow h-10 px-4 rounded-xl bg-gray-950 border border-gray-800 focus:border-orange-500 outline-none text-white font-semibold text-xs transition-all"
                />
              </div>

              <!-- YouTube -->
              <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 flex items-center justify-center text-xs flex-shrink-0">🎥</span>
                <input 
                  v-model="editForm.youtube"
                  type="text"
                  placeholder="https://youtube.com/@channel"
                  class="flex-grow h-10 px-4 rounded-xl bg-gray-950 border border-gray-800 focus:border-orange-500 outline-none text-white font-semibold text-xs transition-all"
                />
              </div>
            </div>
          </div>

          <!-- Buttons -->
          <div class="flex justify-end gap-3 pt-2">
            <button 
              type="button" 
              @click="closeEditModal"
              class="h-10 px-5 rounded-xl border border-gray-800 text-gray-400 hover:text-white font-bold text-xs hover:bg-gray-900 transition-all cursor-pointer"
            >
              Cancel
            </button>
            <button 
              type="submit"
              :disabled="saving"
              class="h-10 px-5 rounded-xl bg-gradient-to-r from-orange-600 to-amber-500 text-white font-bold text-xs hover:opacity-95 transition-opacity cursor-pointer flex items-center justify-center gap-2 disabled:opacity-50"
            >
              <svg v-if="saving" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ saving ? 'Saving...' : 'Save Changes' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch, onUnmounted } from 'vue';
import { db } from '../firebase';
import { doc, onSnapshot, updateDoc, collection, query, where, getDoc, setDoc, increment } from 'firebase/firestore';
import { state, navigateTo } from '../store';
import { playerStore } from '../Stores/playerStore';

export default {
  name: 'Profile',
  props: {
    params: {
      type: Object,
      default: () => ({})
    }
  },
  setup(props) {
    const userProfile = ref({
      name: '',
      role: 'user',
      email: '',
      bio: '',
      instagram: '',
      twitter: '',
      youtube: ''
    });
    const profileSongs = ref([]);
    const profileLoading = ref(true);
    const showEditModal = ref(false);
    const saving = ref(false);
    const likedTracks = ref(new Set());

    const editForm = ref({
      name: '',
      bio: '',
      instagram: '',
      twitter: '',
      youtube: ''
    });

    const targetUid = computed(() => props.params?.uid || state.currentUser?.uid);
    const isMyProfile = computed(() => state.currentUser && targetUid.value === state.currentUser.uid);

    let unsubUser = null;
    let unsubSongs = null;

    const startListener = (uid) => {
      // Clean up previous listeners
      if (unsubUser) {
        unsubUser();
        unsubUser = null;
      }
      if (unsubSongs) {
        unsubSongs();
        unsubSongs = null;
      }

      if (!uid) {
        profileLoading.value = false;
        return;
      }

      profileLoading.value = true;

      // 1. Listen for user details
      unsubUser = onSnapshot(doc(db, 'users', uid), async (snap) => {
        if (snap.exists()) {
          userProfile.value = { ...userProfile.value, ...snap.data() };
        } else {
          // Fallback context
          userProfile.value = {
            name: 'Unknown User',
            role: 'user',
            email: '',
            bio: '',
            instagram: '',
            twitter: '',
            youtube: ''
          };
        }
        profileLoading.value = false;

        // 2. Fetch and listen to songs if role is producer/admin
        const userRole = userProfile.value.role;
        if (userRole === 'producer' || userRole === 'admin') {
          const songsQuery = query(collection(db, 'songs'), where('producer_uid', '==', uid));
          unsubSongs = onSnapshot(songsQuery, (songsSnap) => {
            const list = songsSnap.docs.map(d => ({ id: d.id, ...d.data() }));
            list.sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0));
            profileSongs.value = list;
          }, (err) => {
            console.error("Failed to fetch producer songs in real-time:", err);
          });
        } else {
          profileSongs.value = [];
        }
      }, (err) => {
        console.error("Failed to fetch user profile in real-time:", err);
        profileLoading.value = false;
      });

      // 3. Load user liked tracks if viewing logged-in session
      const currentSessionUid = state.currentUser?.uid;
      if (currentSessionUid) {
        getDoc(doc(db, 'likes', currentSessionUid)).then((likesDoc) => {
          if (likesDoc.exists()) {
            likedTracks.value = new Set(likesDoc.data().song_ids || []);
          }
        }).catch((err) => console.warn("Failed to load likes in Profile:", err));
      }
    };

    // Watch targetUid changes (due to routeParams update)
    watch(targetUid, (newUid) => {
      startListener(newUid);
    }, { immediate: true });

    onUnmounted(() => {
      if (unsubUser) unsubUser();
      if (unsubSongs) unsubSongs();
    });

    const formatUrl = (url) => {
      if (!url) return '';
      if (url.startsWith('http://') || url.startsWith('https://')) {
        return url;
      }
      return `https://${url}`;
    };

    const openEditModal = () => {
      editForm.value = {
        name: userProfile.value.name || '',
        bio: userProfile.value.bio || '',
        instagram: userProfile.value.instagram || '',
        twitter: userProfile.value.twitter || '',
        youtube: userProfile.value.youtube || ''
      };
      showEditModal.value = true;
    };

    const closeEditModal = () => {
      showEditModal.value = false;
    };

    const saveProfile = async () => {
      if (!editForm.value.name.trim()) return;
      saving.value = true;
      try {
        const uid = targetUid.value;
        const userRef = doc(db, 'users', uid);
        const updatedFields = {
          name: editForm.value.name.trim(),
          bio: editForm.value.bio.trim(),
          instagram: editForm.value.instagram.trim(),
          twitter: editForm.value.twitter.trim(),
          youtube: editForm.value.youtube.trim()
        };

        await updateDoc(userRef, updatedFields);

        // Sync local current user session if it is their own profile
        if (state.currentUser && state.currentUser.uid === uid) {
          state.currentUser.name = updatedFields.name;
        }

        // Also update producer name on songs uploaded by this producer
        if (userProfile.value.role === 'producer' || userProfile.value.role === 'admin') {
          // Note: Standard Firestore does not support update queries across multiple documents easily without batched writes/multiple requests.
          // Let's do updates on all songs loaded in profileSongs since they belong to this producer.
          const songUpdates = profileSongs.value.map(s => {
            return updateDoc(doc(db, 'songs', s.id), {
              producer_name: updatedFields.name
            });
          });
          await Promise.all(songUpdates);
        }

        closeEditModal();
      } catch (err) {
        console.error("Failed to update profile:", err);
        alert("Failed to update profile: " + err.message);
      } finally {
        saving.value = false;
      }
    };

    const upgradeToProducer = async () => {
      if (!confirm("Are you sure you want to upgrade your account to a Producer? This will allow you to release and manage tracks on BeatGround.")) return;
      try {
        const uid = targetUid.value;
        const userRef = doc(db, 'users', uid);
        await updateDoc(userRef, {
          role: 'producer'
        });
        
        // Sync local auth state
        if (state.currentUser && state.currentUser.uid === uid) {
          state.currentUser.role = 'producer';
        }
        
        alert("Congratulations! You are now a Producer. Refreshing your panel access...");
      } catch (err) {
        console.error("Failed to upgrade to producer:", err);
        alert("Failed to upgrade role: " + err.message);
      }
    };

    // --- Audio Player ---
    const player = computed(() => playerStore);

    const isPlayingSong = (songId) => {
      return playerStore.currentTrack?.id === songId;
    };

    const playSong = (song) => {
      playerStore.play(song, profileSongs.value);
    };

    // --- Likes ---
    const isLiked = (songId) => {
      return likedTracks.value.has(songId);
    };

    const likeSong = async (song) => {
      const currentSessionUid = state.currentUser?.uid;
      if (!currentSessionUid) return;

      const liked = isLiked(song.id);
      const songRef = doc(db, 'songs', song.id);
      const likesRef = doc(db, 'likes', currentSessionUid);

      try {
        if (liked) {
          await updateDoc(songRef, { likes_count: increment(-1) });
          likedTracks.value.delete(song.id);
          const likesSnap = await getDoc(likesRef);
          const current = likesSnap.exists() ? (likesSnap.data().song_ids || []) : [];
          await setDoc(likesRef, { song_ids: current.filter(id => id !== song.id) });
        } else {
          await updateDoc(songRef, { likes_count: increment(1) });
          likedTracks.value.add(song.id);
          const likesSnap = await getDoc(likesRef);
          const current = likesSnap.exists() ? (likesSnap.data().song_ids || []) : [];
          if (!current.includes(song.id)) {
            await setDoc(likesRef, { song_ids: [...current, song.id] });
          }
        }
      } catch (err) {
        console.error('Like/unlike action failed in profile page:', err);
      }
    };

    return {
      state,
      userProfile,
      profileSongs,
      profileLoading,
      showEditModal,
      saving,
      editForm,
      isMyProfile,
      formatUrl,
      openEditModal,
      closeEditModal,
      saveProfile,
      upgradeToProducer,
      
      // Player
      player,
      isPlayingSong,
      playSong,

      // Likes
      isLiked,
      likeSong
    };
  }
}
</script>
