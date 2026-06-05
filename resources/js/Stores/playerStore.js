import { reactive } from 'vue';
import { db } from '../firebase';
import { doc, updateDoc, increment } from 'firebase/firestore';

export const playerStore = reactive({
  currentTrack: null,
  isPlaying: false,
  audio: null,
  duration: 0,
  currentTime: 0,
  volume: 0.8,
  queue: [],
  currentIndex: -1,
  loopMode: 'off', // 'off', 'all', 'one'
  // Track if play count was already incremented for the current track session
  _countedTrackId: null,

  init() {
    if (this.audio) return;

    if (typeof window !== 'undefined') {
      this.audio = new Audio();
      this.audio.volume = this.volume;

      // Realtime playback time updates
      this.audio.addEventListener('timeupdate', () => {
        this.currentTime = Math.round(this.audio.currentTime);

        // Increment play count only after 15 seconds of actual listening (not just clicking)
        // and only once per track session to prevent spam
        if (
          this.currentTrack &&
          this.currentTime >= 15 &&
          this._countedTrackId !== this.currentTrack.id
        ) {
          this._countedTrackId = this.currentTrack.id;
          this._incrementPlayCount(this.currentTrack.id);
        }
      });

      this.audio.addEventListener('durationchange', () => {
        if (this.audio.duration && isFinite(this.audio.duration)) {
          this.duration = Math.round(this.audio.duration);
        }
      });

      // Auto-advance to next song in the queue on completion
      this.audio.addEventListener('ended', () => {
        if (this.loopMode === 'one') {
          this.currentTime = 0;
          this.audio.currentTime = 0;
          this.audio.play();
        } else {
          this.next();
        }
      });
    }
  },

  // Direct Firestore plays_count increment — replaces old Laravel fetch call
  async _incrementPlayCount(trackId) {
    try {
      await updateDoc(doc(db, 'songs', trackId), {
        plays_count: increment(1)
      });
    } catch (err) {
      console.error('Failed to increment play count:', err);
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

    // Toggle play/pause if same track is clicked
    if (this.currentTrack?.id === track.id) {
      this.togglePlay();
      return;
    }

    try {
      // Reset counted flag for new track so 15s timer starts fresh
      this._countedTrackId = null;
      this.currentTrack = track;
      this.currentTime = 0;
      this.duration = 0;
      this.audio.src = track.audio_url;
      this.audio.play();
      this.isPlaying = true;
    } catch (err) {
      console.error('Audio playback error:', err);
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

  toggleLoop() {
    if (this.loopMode === 'off') {
      this.loopMode = 'all';
    } else if (this.loopMode === 'all') {
      this.loopMode = 'one';
    } else {
      this.loopMode = 'off';
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
    
    if (this.currentIndex === this.queue.length - 1 && this.loopMode !== 'all') {
      this.isPlaying = false;
      if (this.audio) this.audio.pause();
      return;
    }

    const nextIndex = (this.currentIndex + 1) % this.queue.length;
    this.play(this.queue[nextIndex], this.queue);
  },

  prev() {
    if (this.queue.length === 0 || this.currentIndex === -1) return;
    const prevIndex = this.currentIndex === 0 ? this.queue.length - 1 : this.currentIndex - 1;
    this.play(this.queue[prevIndex], this.queue);
  }
});
