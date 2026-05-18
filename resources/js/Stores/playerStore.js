import { reactive } from 'vue';

export const playerStore = reactive({
  currentTrack: null,
  isPlaying: false,
  audio: null,
  duration: 0,
  currentTime: 0,
  volume: 0.8,
  queue: [],
  currentIndex: -1,

  init() {
    if (this.audio) return;
    
    if (typeof window !== 'undefined') {
      this.audio = new Audio();
      this.audio.volume = this.volume;

      // Realtime playback updates
      this.audio.addEventListener('timeupdate', () => {
        this.currentTime = Math.round(this.audio.currentTime);
      });

      this.audio.addEventListener('durationchange', () => {
        if (this.audio.duration) {
          this.duration = Math.round(this.audio.duration);
        }
      });

      // Auto-advance to next song in the queue on completion
      this.audio.addEventListener('ended', () => {
        this.next();
      });
    }
  },

  play(track, trackQueue = []) {
    this.init();
    if (!track) return;

    // Track active queue
    if (trackQueue.length > 0) {
      this.queue = trackQueue;
      this.currentIndex = trackQueue.findIndex(t => t.id === track.id);
    } else if (this.queue.length === 0) {
      this.queue = [track];
      this.currentIndex = 0;
    }

    // Toggle if same track is clicked
    if (this.currentTrack?.id === track.id) {
      this.togglePlay();
      return;
    }

    try {
      this.currentTrack = track;
      this.audio.src = track.audio_url;
      this.audio.play();
      this.isPlaying = true;

      // Track play count in Firestore in the background
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      fetch(`/track/${track.id}/play`, { 
        method: 'POST', 
        headers: { 
          'X-CSRF-TOKEN': csrfToken,
          'Content-Type': 'application/json'
        } 
      }).catch(err => console.error("Firestore counter failed:", err));

    } catch (err) {
      console.error("Audio playback error:", err);
    }
  },

  togglePlay() {
    this.init();
    if (!this.currentTrack) return;

    if (this.isPlaying) {
      this.audio.pause();
      this.isPlaying = false;
    } else {
      this.audio.play();
      this.isPlaying = true;
    }
  },

  seek(secs) {
    if (!this.audio) return;
    this.audio.currentTime = secs;
    this.currentTime = secs;
  },

  setVolume(vol) {
    this.volume = vol;
    if (this.audio) {
      this.audio.volume = vol;
    }
  },

  next() {
    if (this.queue.length === 0 || this.currentIndex === -1) return;
    const nextIndex = (this.currentIndex + 1) % this.queue.length;
    this.play(this.queue[nextIndex], this.queue);
  },

  prev() {
    if (this.queue.length === 0 || this.currentIndex === -1) return;
    const prevIndex = this.currentIndex === 0 ? this.queue.length - 1 : this.currentIndex - 1;
    this.play(this.queue[prevIndex], this.queue);
  }
});
