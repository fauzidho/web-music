<template>
  <div class="min-h-screen bg-[#0b0f19] text-gray-100 flex items-center justify-center p-6 relative overflow-hidden">
    <!-- Decorative Blurry Background Elements -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-orange-600/10 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-pink-500/10 rounded-full blur-3xl"></div>

    <div class="w-full max-w-md bg-gradient-to-b from-[#111827]/80 to-[#0f172a]/95 border border-gray-800 rounded-3xl p-8 shadow-2xl relative backdrop-blur-xl">
      <!-- Title & Branding -->
      <div class="text-center mb-8">
        <div class="inline-flex mb-4">
          <img src="/BeatGround.png" class="w-14 h-14 rounded-2xl object-cover shadow-lg shadow-orange-950/30" alt="BeatGround Logo" />
        </div>
        <h2 class="text-3xl font-extrabold text-white tracking-tight">Welcome Back</h2>
        <p class="text-sm text-gray-400 mt-2">Log in to stream and manage your music</p>
      </div>

      <!-- Error Alerts -->
      <div v-if="error" class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm font-semibold flex items-center gap-2">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span>{{ error }}</span>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="handleLogin" class="space-y-6">
        <div>
          <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Email Address</label>
          <input 
            v-model="email" 
            type="email" 
            required 
            placeholder="name@example.com"
            class="w-full h-12 rounded-xl bg-gray-900/60 border border-gray-800 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none px-4 text-sm text-white placeholder-gray-500 transition-all"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Password</label>
          <input 
            v-model="password" 
            type="password" 
            required 
            placeholder="••••••••"
            class="w-full h-12 rounded-xl bg-gray-900/60 border border-gray-800 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none px-4 text-sm text-white placeholder-gray-500 transition-all"
          />
        </div>

        <div class="flex items-center justify-between text-xs font-medium">
          <label class="flex items-center gap-2 text-gray-400 cursor-pointer">
            <input type="checkbox" class="rounded bg-gray-900 border-gray-800 text-orange-600 focus:ring-0" />
            <span>Remember me</span>
          </label>
          <a href="#" class="text-orange-400 hover:underline">Forgot password?</a>
        </div>

        <button 
          type="submit" 
          :disabled="loading"
          class="w-full h-12 rounded-xl bg-gradient-to-r from-orange-600 to-amber-500 text-white font-bold text-sm hover:opacity-90 transition-opacity flex items-center justify-center gap-2 shadow-lg shadow-orange-950/40 disabled:opacity-50"
        >
          <svg v-if="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>{{ loading ? 'Signing in...' : 'Sign In' }}</span>
        </button>
      </form>

      <!-- Toggle Link -->
      <div class="text-center mt-8 pt-6 border-t border-gray-800/80 text-sm text-gray-400">
        Don't have an account? 
        <a href="/register" class="text-orange-400 font-bold hover:underline">Register</a>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { auth } from '../../firebase';
import { signInWithEmailAndPassword } from 'firebase/auth';

export default {
  name: 'Login',
  setup() {
    const email = ref('');
    const password = ref('');
    const loading = ref(false);
    const error = ref(null);

    const handleLogin = async () => {
      loading.value = true;
      error.value = null;

      try {
        // 1. Sign in user in Firebase Auth Client-side
        const userCredential = await signInWithEmailAndPassword(auth, email.value, password.value);
        const user = userCredential.user;

        // 2. Fetch the secure Firebase ID Token (JWT)
        const idToken = await user.getIdToken();

        // 3. Send token to Laravel backend to verify and initialize Laravel session
        router.post('/login', { idToken }, {
          onError: (errors) => {
            error.value = errors.message || 'Login failed inside backend validation.';
            loading.value = false;
          }
        });
      } catch (err) {
        console.error(err);
        if (err.code === 'auth/user-not-found' || err.code === 'auth/wrong-password' || err.code === 'auth/invalid-credential') {
          error.value = 'Invalid email or password credentials.';
        } else {
          error.value = err.message || 'An error occurred during authentication.';
        }
        loading.value = false;
      }
    };

    return {
      email,
      password,
      loading,
      error,
      handleLogin
    };
  }
}
</script>
