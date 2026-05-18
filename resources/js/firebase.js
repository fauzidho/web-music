import { initializeApp } from 'firebase/app';
import { getAuth } from 'firebase/auth';
import { getFirestore } from 'firebase/firestore';

const firebaseConfig = {
  apiKey: import.meta.env.VITE_FIREBASE_API_KEY || "AIzaSyCNwMxAGIk1LXay7CcD_sac7Xol5VJLjN4",
  authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN || "music-web-e717c.firebaseapp.com",
  projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID || "music-web-e717c",
  storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET || "music-web-e717c.firebasestorage.app",
  messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID || "",
  appId: import.meta.env.VITE_FIREBASE_APP_ID || ""
};

const app = initializeApp(firebaseConfig);
export const auth = getAuth(app);
export const db = getFirestore(app);
export default app;
