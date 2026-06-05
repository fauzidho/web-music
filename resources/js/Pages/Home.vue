<template>
  <div class="space-y-10">
    <!-- Welcome Spotlight Banner -->
    <div class="relative bg-gradient-to-r from-orange-900/40 via-amber-950/20 to-[#0e1322]/80 border border-orange-500/10 rounded-3xl p-8 md:p-12 overflow-hidden shadow-xl">
      <div class="absolute -top-24 -right-24 w-80 h-80 bg-orange-600/10 rounded-full blur-3xl"></div>
      
      <!-- Admin Edit Button -->
      <button 
        v-if="state.currentUser?.role === 'admin'"
        @click="openEditBannerModal"
        class="absolute top-6 right-6 px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/10 rounded-xl text-xs font-bold text-white flex items-center gap-1.5 backdrop-blur-md transition-all cursor-pointer shadow-lg hover:scale-105 z-10"
      >
        <span>⚙️ Edit Banner</span>
      </button>

      <div class="max-w-2xl relative space-y-4">
        <span class="px-3 py-1 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-400 text-xs font-semibold uppercase tracking-wider">
          {{ bannerData.tag }}
        </span>
        <h1 class="text-3xl md:text-5xl font-black text-white leading-tight tracking-tight">
          {{ bannerData.title_normal }} <span class="bg-clip-text text-transparent bg-gradient-to-r from-orange-400 via-amber-400 to-yellow-400">{{ bannerData.title_highlight }}</span>
        </h1>
        <p class="text-sm md:text-base text-gray-400 leading-relaxed">
          {{ bannerData.description }}
        </p>
      </div>
    </div>

    <!-- Tabs Nav -->
    <div class="flex border-b border-gray-800/80 gap-6">
      <button 
        @click="activeViewTab = 'browse'"
        class="pb-4 font-bold text-sm tracking-wide uppercase transition-all relative cursor-pointer outline-none"
        :class="activeViewTab === 'browse' ? 'text-orange-500' : 'text-gray-500 hover:text-gray-300'"
      >
        Browse Beats
        <span v-if="activeViewTab === 'browse'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-orange-500 rounded-full"></span>
      </button>
      
      <button 
        v-if="state.currentUser"
        @click="activeViewTab = 'playlists'"
        class="pb-4 font-bold text-sm tracking-wide uppercase transition-all relative cursor-pointer outline-none flex items-center gap-2"
        :class="activeViewTab === 'playlists' ? 'text-orange-500' : 'text-gray-500 hover:text-gray-300'"
      >
        <span>📂 My Playlists</span>
        <span class="px-1.5 py-0.5 rounded-full bg-gray-900 border border-gray-800 text-[10px] text-gray-400">{{ playlists.length }}</span>
        <span v-if="activeViewTab === 'playlists'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-orange-500 rounded-full"></span>
      </button>
    </div>

    <!-- VIEW 1: BROWSE BEATS -->
    <div v-if="activeViewTab === 'browse'" class="space-y-10">
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
        <div v-if="!isLoading && filteredSongs.length === 0" class="py-24 text-center border border-gray-800 border-dashed rounded-3xl space-y-4 bg-gray-900/20">
          <svg class="w-12 h-12 text-gray-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
          <h4 class="text-lg font-bold text-white">No tracks match your query</h4>
          <p class="text-sm text-gray-500 max-w-xs mx-auto">Try clearing search query or selecting a different genre category.</p>
        </div>

        <!-- Skeleton Loaders -->
        <div v-else-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div v-for="i in 8" :key="'skel-'+i" class="bg-gray-900/30 border border-gray-800/60 rounded-2xl p-4 flex flex-col gap-4 animate-pulse">
            <div class="w-full aspect-square bg-gray-800/80 rounded-xl"></div>
            <div class="space-y-2">
              <div class="h-4 bg-gray-800/80 rounded w-3/4"></div>
              <div class="h-3 bg-gray-800/50 rounded w-1/2"></div>
            </div>
            <div class="mt-2 pt-3 border-t border-gray-800/50 flex justify-between">
              <div class="h-3 bg-gray-800/50 rounded w-1/4"></div>
              <div class="flex gap-2">
                <div class="w-6 h-6 bg-gray-800/80 rounded-md"></div>
                <div class="w-6 h-6 bg-gray-800/80 rounded-md"></div>
              </div>
            </div>
          </div>
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
            <div @click="playSong(song)" class="relative aspect-square w-full rounded-xl overflow-hidden bg-gray-950 border border-gray-800/80 mb-4 shadow-inner cursor-pointer">
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
              <p 
                @click.stop="navigateTo('Profile', { uid: song.producer_uid })" 
                class="text-xs text-gray-400 hover:text-orange-400 font-medium truncate cursor-pointer transition-colors inline-block"
              >
                {{ song.producer_name }}
              </p>
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
                <!-- Comment Button -->
                <button 
                  @click="openCommentsModal(song)" 
                  class="w-auto px-2.5 h-7 rounded-lg bg-gray-900 border border-gray-800 hover:bg-orange-500/10 hover:border-orange-500/30 flex items-center justify-center gap-1.5 transition-colors text-gray-400 hover:text-orange-400"
                  title="View Comments"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                  <span class="text-[10px] font-bold">{{ song.comments_count || 0 }}</span>
                </button>

                <!-- Add to Playlist Button -->
                <button 
                  v-if="state.currentUser"
                  @click="openAddToPlaylistModal(song)" 
                  class="w-7 h-7 rounded-lg bg-gray-900 border border-gray-800 hover:bg-orange-500/10 hover:border-orange-500/30 flex items-center justify-center transition-colors text-gray-500 hover:text-orange-400"
                  title="Add to Playlist"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </button>

                <button 
                  @click="likeSong(song)" 
                  class="w-7 h-7 rounded-lg bg-gray-900 border flex items-center justify-center transition-all duration-200 active:scale-75"
                  :class="isLiked(song.id) 
                    ? 'border-pink-500 bg-pink-500/10 text-pink-500 shadow-sm shadow-pink-500/20' 
                    : 'border-gray-800 text-gray-500 hover:bg-pink-500/10 hover:border-pink-500/30 hover:text-pink-400'"
                  :title="isLiked(song.id) ? 'Unlike Track' : 'Like Track'"
                >
                  <!-- Filled heart = liked -->
                  <svg v-if="isLiked(song.id)" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                  </svg>
                  <!-- Outline heart = not liked -->
                  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- VIEW 2: MY PLAYLISTS -->
    <div v-if="activeViewTab === 'playlists'" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Left side: Playlist Management -->
      <div class="lg:col-span-1 space-y-6">
        <!-- New Playlist Creator Card -->
        <div class="bg-gradient-to-b from-[#111827]/60 to-[#0f172a]/90 border border-gray-800 rounded-2xl p-6 shadow-xl space-y-4">
          <h4 class="font-extrabold text-white text-base tracking-tight">Create Playlist</h4>
          <form @submit.prevent="createPlaylist" class="flex gap-2">
            <input 
              v-model="newPlaylistName"
              type="text" 
              placeholder="e.g. Midnight Lo-Fi Beats" 
              required
              class="flex-grow h-10 px-4 rounded-xl bg-gray-950 border border-gray-800 focus:border-orange-500 outline-none text-xs text-white placeholder-gray-500 transition-all font-semibold"
            />
            <button 
              type="submit"
              class="h-10 px-4 rounded-xl bg-gradient-to-r from-orange-600 to-amber-500 text-white font-bold text-xs hover:opacity-90 transition-opacity cursor-pointer flex-shrink-0"
            >
              Create
            </button>
          </form>
        </div>

        <!-- Playlists List -->
        <div class="space-y-3">
          <h4 class="font-extrabold text-white text-base tracking-tight mb-4">Your Collections</h4>
          
          <div v-if="!isLoading && playlists.length === 0" class="text-center p-8 border border-gray-800 border-dashed rounded-2xl text-gray-500">
            <svg class="w-8 h-8 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <p class="font-bold text-gray-400 text-xs">No playlists created</p>
            <p class="text-[10px] text-gray-600 mt-0.5">Use the form above to get started!</p>
          </div>

          <div v-else-if="isLoading" class="space-y-3 animate-pulse">
            <div v-for="i in 3" :key="'skel-pl-'+i" class="h-16 rounded-2xl bg-gray-900/40 border border-gray-800/50"></div>
          </div>

          <div 
            v-else
            v-for="pl in playlists" 
            :key="pl.id"
            @click="selectedPlaylist = pl"
            class="group flex items-center justify-between p-4 rounded-2xl border cursor-pointer transition-all hover:bg-gray-900/40"
            :class="selectedPlaylist?.id === pl.id ? 'border-orange-500/50 bg-orange-500/5' : 'border-gray-850 bg-gray-900/10'"
          >
            <div class="flex items-center gap-3 truncate">
              <div class="w-10 h-10 rounded-xl bg-orange-600/10 border border-orange-500/20 flex items-center justify-center text-orange-400 text-sm flex-shrink-0">
                📂
              </div>
              <div class="truncate">
                <h5 class="font-extrabold text-white text-sm group-hover:text-orange-400 transition-colors truncate">{{ pl.name }}</h5>
                <p class="text-[10px] text-gray-500 font-semibold uppercase mt-0.5">{{ pl.songs ? pl.songs.length : 0 }} Tracks</p>
              </div>
            </div>
            
            <button 
              @click.stop="deletePlaylist(pl.id)"
              class="w-8 h-8 rounded-lg bg-gray-950 border border-gray-900 hover:border-red-500/40 hover:bg-red-500/10 text-gray-600 hover:text-red-400 flex items-center justify-center transition-colors cursor-pointer"
              title="Delete Playlist"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Right side: Detailed Tracks of Selected Playlist -->
      <div class="lg:col-span-2">
        <div v-if="!selectedPlaylist" class="h-full border border-gray-800 border-dashed rounded-3xl p-16 flex flex-col items-center justify-center text-center text-gray-500 min-h-[300px]">
          <svg class="w-12 h-12 text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
          <h4 class="font-extrabold text-gray-400 text-sm">Select a Collection</h4>
          <p class="text-xs text-gray-600 mt-1 max-w-xs mx-auto">Click any playlist on the left to view tracks, remove items, or play the entire playlist queue.</p>
        </div>

        <div v-else class="bg-[#0e1322]/40 border border-gray-800/80 rounded-3xl overflow-hidden shadow-2xl p-6 space-y-6">
          <!-- Playlist Info Header -->
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-gray-800 pb-6">
            <div>
              <span class="text-[10px] font-bold tracking-wider bg-orange-600/10 border border-orange-500/20 text-orange-400 uppercase px-2 py-0.5 rounded">
                Playlist Catalog
              </span>
              <h3 class="text-2xl font-black text-white mt-2">{{ selectedPlaylist.name }}</h3>
              <p class="text-xs text-gray-500 font-semibold mt-1">ORGANIZED FOR SINK WITH BOTTOM PLAYER</p>
            </div>
            
            <button 
              v-if="selectedPlaylist.songs && selectedPlaylist.songs.length > 0"
              @click="playPlaylist(selectedPlaylist)"
              class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-orange-600 to-amber-500 hover:opacity-95 text-white font-bold text-sm px-6 h-11 rounded-xl shadow-lg shadow-orange-950/40 cursor-pointer"
            >
              <span>▶ Play Playlist Queue</span>
            </button>
          </div>

          <!-- Songs List Table -->
          <div v-if="!selectedPlaylist.songs || selectedPlaylist.songs.length === 0" class="py-16 text-center text-gray-500">
            <svg class="w-10 h-10 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="font-bold text-gray-400 text-xs">Playlist is empty</p>
            <p class="text-[10px] text-gray-600 mt-0.5">Browse beats and click the "+" button to add tracks!</p>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="border-b border-gray-800/80 text-gray-500 text-xs font-bold uppercase tracking-wider bg-gray-950/10">
                  <th class="px-4 py-3">Title</th>
                  <th class="px-4 py-3">Genre</th>
                  <th class="px-4 py-3">Producer</th>
                  <th class="px-4 py-3 text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-850/40">
                <tr 
                  v-for="s in selectedPlaylist.songs" 
                  :key="s.id"
                  class="hover:bg-gray-850/10 transition-colors group"
                >
                  <td class="px-4 py-3 font-bold text-white flex items-center gap-3">
                    <img @click="playPlaylistSong(s, selectedPlaylist)" :src="s.thumbnail_url" class="w-9 h-9 rounded-lg object-cover border border-gray-800 cursor-pointer" alt="cover" />
                    <span class="truncate max-w-[150px]">{{ s.title }}</span>
                  </td>
                  <td class="px-4 py-3 text-xs text-gray-400 uppercase font-semibold">{{ s.genre }}</td>
                  <td class="px-4 py-3 text-xs">
                    <span 
                      @click="navigateTo('Profile', { uid: s.producer_uid })" 
                      class="text-gray-400 hover:text-orange-400 cursor-pointer transition-colors font-medium"
                    >
                      {{ s.producer_name }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-right">
                    <div class="inline-flex gap-2">
                      <button 
                        @click="playPlaylistSong(s, selectedPlaylist)"
                        class="w-7 h-7 rounded-lg bg-orange-600/10 hover:bg-orange-600 border border-orange-500/20 hover:border-orange-500 text-orange-400 hover:text-white flex items-center justify-center transition-all cursor-pointer"
                        title="Play Track"
                      >
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                      </button>
                      
                      <button 
                        @click="removeFromPlaylist(selectedPlaylist, s.id)"
                        class="w-7 h-7 rounded-lg bg-gray-950 border border-gray-900 hover:border-red-500/40 hover:bg-red-500/10 text-gray-600 hover:text-red-400 flex items-center justify-center transition-colors cursor-pointer"
                        title="Remove from Playlist"
                      >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7"/></svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL OVERLAY: ADD TO PLAYLIST -->
    <div 
      v-if="showAddToPlaylistModal && activeSongForPlaylist" 
      class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-black/70 backdrop-blur-sm"
      @click.self="closeAddToPlaylistModal"
    >
      <div class="w-full max-w-md bg-gradient-to-b from-[#111827] to-[#0f172a] border border-gray-800 rounded-3xl p-6 shadow-2xl relative space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-start">
          <div>
            <h4 class="font-extrabold text-white text-lg tracking-tight">Add to Playlist</h4>
            <p class="text-xs text-gray-400 mt-0.5">Collect your favorite tracks in custom lists</p>
          </div>
          <button @click="closeAddToPlaylistModal" class="text-gray-500 hover:text-white cursor-pointer transition-colors outline-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <!-- Selected Song Info -->
        <div class="flex items-center gap-4 bg-gray-950/40 p-3 rounded-2xl border border-gray-850">
          <img :src="activeSongForPlaylist.thumbnail_url" class="w-12 h-12 rounded-lg object-cover border border-gray-800" alt="Cover" />
          <div class="truncate">
            <h5 class="font-bold text-white text-sm truncate">{{ activeSongForPlaylist.title }}</h5>
            <p class="text-xs text-orange-400 font-medium truncate mt-0.5">{{ activeSongForPlaylist.producer_name }}</p>
          </div>
        </div>

        <!-- Playlist Selector -->
        <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
          <div 
            v-for="pl in playlists" 
            :key="pl.id"
            @click="addToPlaylist(pl, activeSongForPlaylist)"
            class="flex items-center justify-between p-3 rounded-xl border border-gray-850 bg-gray-900/10 hover:bg-orange-500/5 hover:border-orange-500/30 cursor-pointer transition-all"
          >
            <div class="flex items-center gap-3">
              <span class="text-xs">📂</span>
              <span class="font-bold text-white text-xs">{{ pl.name }}</span>
            </div>
            <span class="text-[10px] text-gray-500 font-semibold">{{ pl.songs ? pl.songs.length : 0 }} Tracks</span>
          </div>
          
          <div v-if="playlists.length === 0" class="text-center py-6 text-xs text-gray-500 font-semibold">
            No playlists found. Create one below to add this track!
          </div>
        </div>

        <!-- Create & Add Quick Form -->
        <div class="border-t border-gray-850 pt-4 space-y-3">
          <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Quick Create Playlist</label>
          <div class="flex gap-2">
            <input 
              v-model="quickPlaylistName"
              type="text" 
              placeholder="New playlist name..."
              class="flex-grow h-10 px-4 rounded-xl bg-gray-950 border border-gray-800 focus:border-orange-500 outline-none text-xs text-white placeholder-gray-500 transition-all font-semibold"
            />
            <button 
              @click="createAndAddToPlaylist(activeSongForPlaylist)"
              class="h-10 px-4 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-bold text-xs transition-colors cursor-pointer flex-shrink-0"
            >
              Create & Add
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL OVERLAY: EDIT HOMEPAGE BANNER -->
    <div 
      v-if="showEditBannerModal" 
      class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-black/70 backdrop-blur-sm"
      @click.self="showEditBannerModal = false"
    >
      <div class="w-full max-w-lg bg-gradient-to-b from-[#111827] to-[#0f172a] border border-gray-800 rounded-3xl p-6 shadow-2xl relative space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-start">
          <div>
            <h4 class="font-extrabold text-white text-lg tracking-tight">Edit Banner Settings</h4>
            <p class="text-xs text-gray-400 mt-0.5">Customize the homepage welcome spotlight banner content</p>
          </div>
          <button @click="showEditBannerModal = false" class="text-gray-500 hover:text-white cursor-pointer transition-colors outline-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <!-- Form fields -->
        <form @submit.prevent="saveBannerSettings" class="space-y-4 text-xs font-semibold text-gray-405">
          <div class="space-y-1.5">
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tag / Badge Text</label>
            <input 
              v-model="editBannerForm.tag"
              type="text" 
              required
              class="w-full h-10 px-4 rounded-xl bg-gray-950 border border-gray-800 focus:border-orange-500 outline-none text-white transition-all font-semibold"
            />
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
              <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Title (Normal Part)</label>
              <input 
                v-model="editBannerForm.title_normal"
                type="text" 
                required
                class="w-full h-10 px-4 rounded-xl bg-gray-950 border border-gray-800 focus:border-orange-500 outline-none text-white transition-all font-semibold"
              />
            </div>
            <div class="space-y-1.5">
              <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Title (Highlighted Orange Part)</label>
              <input 
                v-model="editBannerForm.title_highlight"
                type="text" 
                required
                class="w-full h-10 px-4 rounded-xl bg-gray-950 border border-gray-800 focus:border-orange-500 outline-none text-white transition-all font-semibold"
              />
            </div>
          </div>

          <div class="space-y-1.5">
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Description Text</label>
            <textarea 
              v-model="editBannerForm.description"
              required
              rows="4"
              class="w-full p-4 rounded-xl bg-gray-950 border border-gray-800 focus:border-orange-500 outline-none text-white transition-all font-semibold resize-none"
            ></textarea>
          </div>

          <!-- Buttons -->
          <div class="flex justify-end gap-3 pt-2">
            <button 
              type="button" 
              @click="showEditBannerModal = false"
              class="h-10 px-5 rounded-xl border border-gray-800 text-gray-400 hover:text-white font-bold text-xs hover:bg-gray-900 transition-all cursor-pointer"
            >
              Cancel
            </button>
            <button 
              type="submit"
              class="h-10 px-5 rounded-xl bg-gradient-to-r from-orange-600 to-amber-500 text-white font-bold text-xs hover:opacity-95 transition-opacity cursor-pointer"
            >
              Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>
    <!-- MODAL OVERLAY: COMMENTS -->
    <div 
      v-if="showCommentsModal" 
      class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6 bg-black/80 backdrop-blur-md"
      @click.self="closeCommentsModal"
    >
      <div class="w-full max-w-lg bg-[#0f1423] border border-gray-800 rounded-2xl shadow-2xl flex flex-col max-h-[85vh] overflow-hidden">
        <!-- Header -->
        <div class="p-5 border-b border-gray-800 flex items-center justify-between bg-gray-950/40">
          <div class="flex items-center gap-3">
            <span class="text-xl">💬</span>
            <div>
              <h4 class="font-extrabold text-white text-base tracking-tight">Comments</h4>
              <p class="text-xs text-orange-400 font-medium truncate max-w-[200px]">{{ activeSongForComments?.title }}</p>
            </div>
          </div>
          <button @click="closeCommentsModal" class="text-gray-500 hover:text-white cursor-pointer transition-colors outline-none text-xl font-bold">
            &times;
          </button>
        </div>

        <!-- Comments List -->
        <div class="flex-grow overflow-y-auto p-5 space-y-4">
          <div v-if="commentsList.length === 0" class="text-center py-10 text-gray-500 space-y-2">
            <svg class="w-10 h-10 mx-auto text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <p class="text-sm font-semibold">Be the first to comment!</p>
          </div>
          
          <div v-for="comment in commentsList" :key="comment.id" class="flex gap-3">
            <div class="w-8 h-8 rounded-full bg-orange-600/20 border border-orange-500/30 flex items-center justify-center font-bold text-white text-xs flex-shrink-0">
              {{ comment.user_name ? comment.user_name[0].toUpperCase() : 'U' }}
            </div>
            <div class="bg-gray-900/50 border border-gray-800 rounded-2xl rounded-tl-none p-3 flex-grow">
              <div class="flex justify-between items-start mb-1">
                <span class="text-xs font-bold text-gray-300">{{ comment.user_name }}</span>
                <span class="text-[9px] text-gray-500">{{ new Date(comment.created_at).toLocaleDateString() }}</span>
              </div>
              <p class="text-sm text-gray-400 whitespace-pre-wrap leading-relaxed">{{ comment.text }}</p>
            </div>
          </div>
        </div>

        <!-- Comment Input -->
        <div class="p-4 border-t border-gray-800 bg-gray-950/60" v-if="state.currentUser">
          <form @submit.prevent="submitComment" class="flex gap-3">
            <input 
              v-model="newCommentText"
              type="text" 
              placeholder="Write a comment..."
              required
              class="flex-grow h-10 px-4 rounded-xl bg-gray-900 border border-gray-800 focus:border-orange-500 outline-none text-sm text-white placeholder-gray-500 transition-all"
            />
            <button 
              type="submit"
              class="h-10 px-4 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-bold text-sm transition-colors cursor-pointer flex-shrink-0"
              :disabled="!newCommentText.trim()"
            >
              Post
            </button>
          </form>
        </div>
        <div v-else class="p-4 border-t border-gray-800 bg-gray-950/60 text-center">
          <p class="text-xs text-gray-500">Please <a href="/login" class="text-orange-400 hover:underline">log in</a> to comment.</p>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { playerStore } from '../Stores/playerStore';
import { db } from '../firebase';
import { collection, onSnapshot, doc, updateDoc, increment, addDoc, deleteDoc, query, where, setDoc, getDoc } from 'firebase/firestore';
import { state, navigateTo } from '../store';

export default {
  name: 'Home',
  setup() {
    const activeViewTab = ref('browse');
    const searchQuery = ref('');
    const selectedGenre = ref('All');
    const songsList = ref([]);
    const isLoading = ref(true);
    
    // Playlists reactive state
    const playlists = ref([]);
    const newPlaylistName = ref('');
    const selectedPlaylist = ref(null);
    const showAddToPlaylistModal = ref(false);
    const activeSongForPlaylist = ref(null);
    const quickPlaylistName = ref('');

    // Banner modal state
    const bannerData = ref({
      tag: 'NEW RELEASE',
      title_normal: 'Discover',
      title_highlight: 'Independent Beats',
      description: 'Welcome to BeatGround, your personal gateway to real-time audio. Click play on any track to start streaming without interruptions. Explore by genre, search for your favorite producers, or organize your favorite tracks into playlists!'
    });
    const showEditBannerModal = ref(false);
    const editBannerForm = ref({
      tag: '',
      title_normal: '',
      title_highlight: '',
      description: ''
    });

    // Comments Modal State
    const showCommentsModal = ref(false);
    const activeSongForComments = ref(null);
    const commentsList = ref([]);
    const newCommentText = ref('');
    let unsubComments = null;

    const openCommentsModal = (song) => {
      activeSongForComments.value = song;
      showCommentsModal.value = true;
      commentsList.value = [];
      
      const q = query(
        collection(db, 'comments'), 
        where('song_id', '==', song.id)
      );
      unsubComments = onSnapshot(q, (snapshot) => {
        const list = snapshot.docs.map(d => ({
          id: d.id,
          ...d.data()
        }));
        // Sort by newest first
        list.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        commentsList.value = list;
      }, (err) => {
        console.error('Failed to load comments:', err);
      });
    };

    const closeCommentsModal = () => {
      showCommentsModal.value = false;
      activeSongForComments.value = null;
      if (unsubComments) {
        unsubComments();
        unsubComments = null;
      }
      newCommentText.value = '';
    };

    const submitComment = async () => {
      if (!newCommentText.value.trim() || !state.currentUser || !activeSongForComments.value) return;
      try {
        const text = newCommentText.value.trim();
        newCommentText.value = ''; // clear immediately for UX
        
        await addDoc(collection(db, 'comments'), {
          song_id: activeSongForComments.value.id,
          user_uid: state.currentUser.uid,
          user_name: state.currentUser.name,
          text: text,
          created_at: new Date().toISOString()
        });
        
        await updateDoc(doc(db, 'songs', activeSongForComments.value.id), {
          comments_count: increment(1)
        });
      } catch (err) {
        console.error("Failed to post comment:", err);
      }
    };

    // Liked track IDs for current user — loaded from Firestore on mount
    const likedTracks = ref(new Set());

    const genresList = ['All', 'Lo-Fi', 'Synthwave', 'Hip-Hop', 'EDM', 'Acoustic'];

    const player = computed(() => playerStore);

    // Sync all songs from Firestore collection in real-time
    let unsubscribe = null;
    let unsubPlaylists = null;
    let unsubBanner = null;

    onMounted(async () => {
      // 1. Sync Songs
      unsubscribe = onSnapshot(collection(db, 'songs'), (snapshot) => {
        songsList.value = snapshot.docs.map(d => ({
          id: d.id,
          ...d.data()
        }));
        isLoading.value = false;
      }, (err) => {
        console.error('Firestore songs listener failed:', err);
        isLoading.value = false;
      });

      const uid = state.currentUser?.uid;

      if (uid) {
        // 2. Sync User's Playlists
        const q = query(collection(db, 'playlists'), where('user_uid', '==', uid));
        unsubPlaylists = onSnapshot(q, (snapshot) => {
          playlists.value = snapshot.docs.map(d => ({
            id: d.id,
            ...d.data()
          }));
          // Re-sync active selected playlist in details view
          if (selectedPlaylist.value) {
            const updated = playlists.value.find(p => p.id === selectedPlaylist.value.id);
            selectedPlaylist.value = updated || null;
          }
        }, (err) => {
          console.error('Firestore playlists listener failed:', err);
        });

        // 3. Load liked tracks from Firestore (likes/{uid})
        try {
          const likesDoc = await getDoc(doc(db, 'likes', uid));
          if (likesDoc.exists()) {
            const data = likesDoc.data();
            likedTracks.value = new Set(data.song_ids || []);
          }
        } catch (err) {
          console.warn('Could not load liked tracks:', err);
        }
      }

      // 4. Sync Homepage Banner Settings
      unsubBanner = onSnapshot(doc(db, 'settings', 'homepage_banner'), (docSnap) => {
        if (docSnap.exists()) {
          bannerData.value = { ...bannerData.value, ...docSnap.data() };
        }
      }, (err) => {
        console.error('Firestore banner listener failed:', err);
      });
    });

    onUnmounted(() => {
      if (unsubscribe) unsubscribe();
      if (unsubPlaylists) unsubPlaylists();
      if (unsubBanner) unsubBanner();
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
      return likedTracks.value.has(songId);
    };

    const likeSong = async (song) => {
      const uid = state.currentUser?.uid;
      if (!uid) return;

      const liked = isLiked(song.id);
      const songRef  = doc(db, 'songs', song.id);
      const likesRef = doc(db, 'likes', uid);

      try {
        if (liked) {
          // --- UNLIKE ---
          await updateDoc(songRef, { likes_count: increment(-1) });
          likedTracks.value.delete(song.id);
          // Persist removal to Firestore likes doc
          const likesSnap = await getDoc(likesRef);
          const current = likesSnap.exists() ? (likesSnap.data().song_ids || []) : [];
          await setDoc(likesRef, { song_ids: current.filter(id => id !== song.id) });
        } else {
          // --- LIKE ---
          await updateDoc(songRef, { likes_count: increment(1) });
          likedTracks.value.add(song.id);
          // Persist addition to Firestore likes doc
          const likesSnap = await getDoc(likesRef);
          const current = likesSnap.exists() ? (likesSnap.data().song_ids || []) : [];
          if (!current.includes(song.id)) {
            await setDoc(likesRef, { song_ids: [...current, song.id] });
          }
        }
      } catch (err) {
        console.error('Like/unlike action failed:', err);
      }
    };

    // --- PLAYLIST CORE ACTIONS ---
    const openAddToPlaylistModal = (song) => {
      activeSongForPlaylist.value = song;
      showAddToPlaylistModal.value = true;
    };

    const closeAddToPlaylistModal = () => {
      activeSongForPlaylist.value = null;
      showAddToPlaylistModal.value = false;
      quickPlaylistName.value = '';
    };

    const createPlaylist = async () => {
      if (!newPlaylistName.value.trim() || !state.currentUser) return;
      try {
        await addDoc(collection(db, 'playlists'), {
          name: newPlaylistName.value.trim(),
          user_uid: state.currentUser.uid,
          songs: [],
          created_at: new Date().toISOString()
        });
        newPlaylistName.value = '';
      } catch (err) {
        console.error("Failed to create playlist:", err);
        alert("Failed to create playlist: " + err.message + "\n\nTip: Please verify that your Firebase Firestore Security Rules permit read/write operations for the 'playlists' collection.");
      }
    };

    const deletePlaylist = async (playlistId) => {
      if (confirm('Are you sure you want to permanently delete this playlist collection?')) {
        try {
          await deleteDoc(doc(db, 'playlists', playlistId));
        } catch (err) {
          console.error("Failed to delete playlist:", err);
          alert("Failed to delete playlist: " + err.message);
        }
      }
    };

    const addToPlaylist = async (playlist, song) => {
      const alreadyExists = playlist.songs?.some(s => s.id === song.id);
      if (alreadyExists) {
        alert("This track is already present in your playlist!");
        return;
      }

      try {
        const playlistRef = doc(db, 'playlists', playlist.id);
        const updatedSongs = playlist.songs ? [...playlist.songs] : [];
        updatedSongs.push({
          id: song.id,
          title: song.title,
          genre: song.genre,
          audio_url: song.audio_url,
          thumbnail_url: song.thumbnail_url,
          producer_name: song.producer_name,
          duration: song.duration
        });

        await updateDoc(playlistRef, {
          songs: updatedSongs
        });
        closeAddToPlaylistModal();
      } catch (err) {
        console.error("Failed to add song to playlist:", err);
        alert("Failed to add song to playlist: " + err.message);
      }
    };

    const createAndAddToPlaylist = async (song) => {
      if (!quickPlaylistName.value.trim() || !state.currentUser) return;
      try {
        const newDoc = await addDoc(collection(db, 'playlists'), {
          name: quickPlaylistName.value.trim(),
          user_uid: state.currentUser.uid,
          songs: [],
          created_at: new Date().toISOString()
        });
        
        // Add to newly created playlist
        const newPlaylist = {
          id: newDoc.id,
          songs: []
        };
        await addToPlaylist(newPlaylist, song);
      } catch (err) {
        console.error("Quick create and add failed:", err);
        alert("Failed to create and add: " + err.message);
      }
    };

    const removeFromPlaylist = async (playlist, songId) => {
      try {
        const playlistRef = doc(db, 'playlists', playlist.id);
        const updatedSongs = playlist.songs.filter(s => s.id !== songId);
        
        await updateDoc(playlistRef, {
          songs: updatedSongs
        });
      } catch (err) {
        console.error("Failed to remove song from playlist:", err);
        alert("Failed to remove track: " + err.message);
      }
    };

    // Playing whole playlist
    const playPlaylist = (playlist) => {
      if (!playlist.songs || playlist.songs.length === 0) return;
      playerStore.play(playlist.songs[0], playlist.songs);
    };

    const playPlaylistSong = (song, playlist) => {
      playerStore.play(song, playlist.songs);
    };

    // --- BANNER ACTIONS ---
    const openEditBannerModal = () => {
      editBannerForm.value = { ...bannerData.value };
      showEditBannerModal.value = true;
    };

    const saveBannerSettings = async () => {
      try {
        await setDoc(doc(db, 'settings', 'homepage_banner'), {
          tag: editBannerForm.value.tag.trim(),
          title_normal: editBannerForm.value.title_normal.trim(),
          title_highlight: editBannerForm.value.title_highlight.trim(),
          description: editBannerForm.value.description.trim()
        });
        showEditBannerModal.value = false;
      } catch (err) {
        console.error("Failed to save banner settings:", err);
        alert("Failed to save banner settings: " + err.message + "\n\nTip: Please verify that your Firebase Firestore Security Rules permit read/write operations for the 'settings' collection.");
      }
    };

    return {
      state,
      activeViewTab,
      searchQuery,
      navigateTo,
      selectedGenre,
      genresList,
      filteredSongs,
      isLoading,
      player,
      isPlayingSong,
      playSong,
      isLiked,
      likeSong,
      
      // Playlist returns
      playlists,
      newPlaylistName,
      selectedPlaylist,
      showAddToPlaylistModal,
      activeSongForPlaylist,
      quickPlaylistName,
      openAddToPlaylistModal,
      closeAddToPlaylistModal,
      createPlaylist,
      deletePlaylist,
      addToPlaylist,
      createAndAddToPlaylist,
      removeFromPlaylist,
      playPlaylist,
      playPlaylistSong,

      // Banner returns
      bannerData,
      showEditBannerModal,
      editBannerForm,
      openEditBannerModal,
      saveBannerSettings,

      // Comments returns
      showCommentsModal,
      activeSongForComments,
      commentsList,
      newCommentText,
      openCommentsModal,
      closeCommentsModal,
      submitComment
    };
  }
}
</script>
