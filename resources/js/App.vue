<template>
  <!-- Full Screen Loader for Auth state resolution -->
  <div v-if="state.loadingAuth" class="min-h-screen bg-[#0b0f19] flex flex-col items-center justify-center gap-4">
    <div class="relative w-16 h-16">
      <div class="absolute inset-0 rounded-full border-4 border-orange-500/10"></div>
      <div class="absolute inset-0 rounded-full border-4 border-t-orange-500 animate-spin"></div>
    </div>
    <span class="text-xs uppercase tracking-widest font-black text-gray-500">Initializing BeatGround...</span>
  </div>

  <div v-else class="min-h-screen bg-[#0b0f19] text-gray-100">
    <!-- Standalone full-screen auth routes -->
    <template v-if="isAuthRoute">
      <component :is="activeComponent" :params="state.routeParams" />
    </template>

    <!-- Wrapped authenticated routes (Includes Sidebar Header & Persistent Bottom Music Player) -->
    <template v-else>
      <AppLayout>
        <component :is="activeComponent" :params="state.routeParams" />
      </AppLayout>
    </template>
  </div>
</template>

<script>
import { computed, onMounted } from 'vue';
import { state, initAuthListener } from './store';
import AppLayout from './Layouts/AppLayout.vue';

// Eager Page Imports
import Home from './Pages/Home.vue';
import Login from './Pages/Auth/Login.vue';
import Register from './Pages/Auth/Register.vue';
import ProducerDashboard from './Pages/Producer/Dashboard.vue';
import ProducerUpload from './Pages/Producer/Upload.vue';
import AdminDashboard from './Pages/Admin/Dashboard.vue';
import Profile from './Pages/Profile.vue';

export default {
  name: 'App',
  components: {
    AppLayout
  },
  setup() {
    // Map string identifiers to Page Component Classes
    const componentsMap = {
      Home,
      Login,
      Register,
      ProducerDashboard,
      ProducerUpload,
      AdminDashboard,
      Profile
    };

    // Initialize Auth Listener on Mount
    onMounted(() => {
      initAuthListener();
    });

    const isAuthRoute = computed(() => {
      return state.currentRoute === 'Login' || state.currentRoute === 'Register';
    });

    const activeComponent = computed(() => {
      return componentsMap[state.currentRoute] || Home;
    });

    return {
      state,
      isAuthRoute,
      activeComponent
    };
  }
}
</script>

<style>
/* Global Custom Styles */
::-webkit-scrollbar {
  width: 8px;
}
::-webkit-scrollbar-track {
  background: #0b0f19;
}
::-webkit-scrollbar-thumb {
  background: #1f2937;
  border-radius: 4px;
}
::-webkit-scrollbar-thumb:hover {
  background: #374151;
}
</style>
