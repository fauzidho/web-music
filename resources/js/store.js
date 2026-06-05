import { reactive, computed } from 'vue';
import { auth, db } from './firebase';
import { onAuthStateChanged, signOut } from 'firebase/auth';
import { doc, getDoc } from 'firebase/firestore';

// Core reactive global state
export const state = reactive({
    currentUser: null,
    currentRoute: 'Home',
    routeParams: {},
    loadingAuth: true,
    isRegistering: false
});

// Helper checking function for route transition guard
export function navigateTo(route, params = {}) {
    if (state.loadingAuth) {
        state.currentRoute = route;
        state.routeParams = params;
        return;
    }

    const isLoggedIn = !!state.currentUser;
    const userRole = state.currentUser?.role || 'user';

    // Auth validation
    if (!isLoggedIn) {
        if (route !== 'Login' && route !== 'Register') {
            state.currentRoute = 'Login';
            return;
        }
    } else {
        if (route === 'Login' || route === 'Register') {
            state.currentRoute = 'Home';
            return;
        }
    }

    // Role-based authorization
    if (route.startsWith('Producer') && userRole !== 'producer' && userRole !== 'admin') {
        state.currentRoute = 'Home';
        return;
    }
    if (route.startsWith('Admin') && userRole !== 'admin') {
        state.currentRoute = 'Home';
        return;
    }

    state.currentRoute = route;
    state.routeParams = params;
    
    // Auto-scroll to top on view transition
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Initialize Auth State Listener
export function initAuthListener() {
    onAuthStateChanged(auth, async (firebaseUser) => {
        if (state.isRegistering) {
            // Bypass during active registration to let Register.vue handle the write and redirect cleanly
            return;
        }
        state.loadingAuth = true;
        if (firebaseUser) {
            try {
                // Fetch the user role and profile from Firestore collection
                const userRef = doc(db, 'users', firebaseUser.uid);
                const userSnap = await getDoc(userRef);

                if (userSnap.exists()) {
                    const profile = userSnap.data();
                    state.currentUser = {
                        uid: firebaseUser.uid,
                        name: profile.name || firebaseUser.displayName || 'User',
                        email: profile.email || firebaseUser.email,
                        role: profile.role || 'user',
                        created_at: profile.created_at
                    };
                } else {
                    // Profile fallback if missing
                    state.currentUser = {
                        uid: firebaseUser.uid,
                        name: firebaseUser.displayName || 'User',
                        email: firebaseUser.email,
                        role: 'user'
                    };
                }
            } catch (err) {
                console.error("Failed to fetch user profile:", err);
                state.currentUser = {
                    uid: firebaseUser.uid,
                    name: firebaseUser.displayName || 'User',
                    email: firebaseUser.email,
                    role: 'user'
                };
            }

            // Redirect away from Auth pages if logged in
            if (state.currentRoute === 'Login' || state.currentRoute === 'Register') {
                state.currentRoute = 'Home';
            }
        } else {
            state.currentUser = null;
            if (state.currentRoute !== 'Register') {
                state.currentRoute = 'Login';
            }
        }
        state.loadingAuth = false;
    });
}

// Global Logout Action
export async function logoutUser() {
    state.loadingAuth = true;
    try {
        await signOut(auth);
        state.currentUser = null;
        navigateTo('Login');
    } catch (err) {
        console.error("Sign-out failed:", err);
    } finally {
        state.loadingAuth = false;
    }
}
