/**
 * SINEMA TEGAL - Persistent Audio Streaming Engine (Spotify / YT Music Style)
 */

(function () {
  const STORAGE_KEY = 'sinema_tegal_audio_state';
  const audio = new Audio();
  window.SinemaAudio = audio;

  let playlistQueue = [];
  let currentTrackIndex = -1;
  let currentTrackData = null;
  let isDraggingSeek = false;

  // DOM Elements
  const bottomPlayer = document.getElementById('bottomPlayer');
  const playerInner = bottomPlayer?.querySelector('.player-inner');
  const youtubePlayerContainer = document.getElementById('youtubePlayerContainer');
  const youtubePlayerFrame = document.getElementById('youtubePlayerFrame');
  const playBtn = document.getElementById('playerPlayBtn');
  const playIcon = document.getElementById('playerPlayIcon');
  const prevBtn = document.getElementById('playerPrevBtn');
  const nextBtn = document.getElementById('playerNextBtn');
  const thumbImg = document.getElementById('playerThumb');
  const titleEl = document.getElementById('playerTitle');
  const artistEl = document.getElementById('playerArtist');
  const currentTimeEl = document.getElementById('playerCurrentTime');
  const totalTimeEl = document.getElementById('playerTotalTime');
  const seekBar = document.getElementById('playerSeekBar');
  const seekFill = document.getElementById('playerSeekFill');
  const volumeSlider = document.getElementById('playerVolume');
  const lyricsDrawer = document.getElementById('lyricsDrawer');
  const lyricsContent = document.getElementById('lyricsContent');
  const lyricsTitle = document.getElementById('lyricsTitle');
  const lyricsToggleBtn = document.getElementById('lyricsToggleBtn');
  const playlistToggleBtn = document.getElementById('playlistToggleBtn');
  const paywallModal = document.getElementById('paywallModal');

  // Format MM:SS
  function formatTime(seconds) {
    if (isNaN(seconds)) return '00:00';
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins < 10 ? '0' : ''}${mins}:${secs < 10 ? '0' : ''}${secs}`;
  }

  function playExternalMediaEmbed(url) {
    if (!youtubePlayerContainer || !youtubePlayerFrame) return false;
    let embedUrl;
    try {
      embedUrl = new URL(url);
    } catch (error) {
      return false;
    }
    const isYoutube = embedUrl.hostname === 'www.youtube.com' && /^\/embed\/[\w-]+$/.test(embedUrl.pathname);
    const isSpotify = embedUrl.hostname === 'open.spotify.com' && /^\/embed\/track\/[\w]+$/.test(embedUrl.pathname);
    const isAppleMusic = embedUrl.hostname === 'embed.music.apple.com' && /^\/[^/]+\/song\/[\w-]+\/\d+$/.test(embedUrl.pathname);
    if (!isYoutube && !isSpotify && !isAppleMusic) return false;
    if (isYoutube) {
      embedUrl.searchParams.set('autoplay', '1');
      embedUrl.searchParams.set('playsinline', '1');
    }
    youtubePlayerFrame.src = embedUrl.toString();
    youtubePlayerContainer.hidden = false;
    if (playerInner) playerInner.classList.add('is-hidden');
    return true;
  }

  function stopYoutubeEmbed() {
    if (!youtubePlayerContainer || !youtubePlayerFrame) return;
    youtubePlayerFrame.src = '';
    youtubePlayerContainer.hidden = true;
    if (playerInner) playerInner.classList.remove('is-hidden');
  }

  function syncQueueForTrack(trackId) {
    const targetId = Number(trackId);
    let index = playlistQueue.findIndex(track => Number(track.id) === targetId);

    if (index === -1) {
      const dolananTracks = Array.from(document.querySelectorAll('.tembang-dolanan-track[data-track-id]'))
        .map(button => ({ id: Number(button.dataset.trackId) }))
        .filter(track => Number.isFinite(track.id));
      const dolananIndex = dolananTracks.findIndex(track => track.id === targetId);
      if (dolananIndex !== -1) {
        playlistQueue = dolananTracks;
        index = dolananIndex;
      }
    }

    if (index === -1) {
      const jawaTracks = Array.from(document.querySelectorAll('.musik-jawa-track[data-track-id]'))
        .map(button => ({ id: Number(button.dataset.trackId) }))
        .filter(track => Number.isFinite(track.id));
      const jawaIndex = jawaTracks.findIndex(track => track.id === targetId);
      if (jawaIndex !== -1) {
        playlistQueue = jawaTracks;
        index = jawaIndex;
      }
    }

    if (index === -1) {
      const visibleTracks = Array.from(document.querySelectorAll('.track-card[data-track-id]'))
        .map(card => ({ id: Number(card.dataset.trackId) }))
        .filter(track => Number.isFinite(track.id));
      if (visibleTracks.length) {
        playlistQueue = visibleTracks;
        index = playlistQueue.findIndex(track => track.id === targetId);
      }
    }

    if (index === -1) {
      playlistQueue = [{ id: targetId }];
      index = 0;
    }

    currentTrackIndex = index;
  }

  // Restore state from LocalStorage on page load
  function restoreState() {
    try {
      const saved = localStorage.getItem(STORAGE_KEY);
      if (saved) {
        const state = JSON.parse(saved);
        if (state.track) {
          syncQueueForTrack(state.track.id);
          setTrackUI(state.track);
          if (!playExternalMediaEmbed(state.track.audio_url)) {
            audio.src = state.track.audio_url;
            audio.currentTime = state.currentTime || 0;
          }
          if (state.volume !== undefined) {
            audio.volume = state.volume;
            if (volumeSlider) volumeSlider.value = state.volume * 100;
          }
          if (state.isPlaying) {
            // Autoplay may require user gesture on first page load
            audio.play().then(() => updatePlayIcon(true)).catch(() => updatePlayIcon(false));
          }
        }
      }
    } catch (e) {
      console.warn('Audio state restore failed:', e);
    }
  }

  function saveState(isPlaying) {
    if (!currentTrackData) return;
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify({
        track: currentTrackData,
        currentTime: audio.currentTime,
        volume: audio.volume,
        isPlaying: isPlaying ?? !audio.paused
      }));
    } catch (e) {}
  }

  function setTrackUI(track) {
    currentTrackData = track;
    if (bottomPlayer) bottomPlayer.classList.remove('is-hidden');
    if (playerInner) playerInner.classList.remove('is-hidden');
    if (thumbImg) thumbImg.src = track.cover_url || '/images/default-track.jpg';
    if (titleEl) titleEl.textContent = track.title;
    if (artistEl) artistEl.textContent = track.artist + (track.album ? ` • ${track.album}` : '');
    if (lyricsTitle) lyricsTitle.textContent = `${track.title} - ${track.artist}`;
    if (lyricsContent) {
      lyricsContent.textContent = track.lyrics || 'Lirik tembang kiye durung kasedia.';
    }

    // Highlight track card in current DOM if any
    document.querySelectorAll('.track-card, .tembang-dolanan-track, .musik-jawa-track').forEach(el => {
      if (el.dataset.trackId == track.id) {
        el.classList.add('is-playing');
      } else {
        el.classList.remove('is-playing');
      }
    });

    // Update MediaSession API for OS player
    if ('mediaSession' in navigator) {
      navigator.mediaSession.metadata = new MediaMetadata({
        title: track.title,
        artist: track.artist,
        album: track.album || 'Sinema Tegal Music',
        artwork: [{ src: track.cover_url, sizes: '512x512', type: 'image/jpeg' }]
      });
    }
  }

  function updatePlayIcon(isPlaying) {
    if (!playIcon || !bottomPlayer) return;
    if (isPlaying) {
      playIcon.innerHTML = '&#10074;&#10074;'; // Pause icon
      bottomPlayer.classList.add('is-playing');
    } else {
      playIcon.innerHTML = '&#9654;'; // Play icon
      bottomPlayer.classList.remove('is-playing');
    }
  }

  // Play a specific track via ID
  window.playTrackById = async function (trackId) {
    try {
      syncQueueForTrack(trackId);
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const response = await fetch(`/api/tracks/${trackId}/play`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken || '',
          'Accept': 'application/json'
        }
      });

      const data = await response.json();

      if (!response.ok || !data.allowed) {
        // Show Paywall / VIP Lock modal
        if (paywallModal) {
          const msgEl = document.getElementById('paywallMessage');
          if (msgEl) msgEl.textContent = data.message || 'Tembang kiye butuh Akses VIP.';
          paywallModal.classList.add('is-open');
        } else {
          alert(data.message || 'Tembang kiye khusus Anggota VIP Dulur Wasgitel.');
        }
        return;
      }

      setTrackUI(data.track);
      if (playExternalMediaEmbed(data.track.audio_url)) {
        audio.pause();
        audio.removeAttribute('src');
        audio.load();
        updatePlayIcon(false);
        saveState(false);
        return;
      }
      stopYoutubeEmbed();
      document.getElementById('dearGodUnlockBtn')?.remove();
      audio.loop = false;
      audio.src = data.track.audio_url;
      audio.load();
      await audio.play();
      updatePlayIcon(true);
      saveState(true);

    } catch (err) {
      console.error('Error playing track:', err);
    }
  };

  // Play entire playlist
  window.playPlaylist = async function (playlistId) {
    try {
      const res = await fetch(`/api/playlists/${playlistId}`);
      const data = await res.json();
      if (data.tracks && data.tracks.length > 0) {
        playlistQueue = data.tracks;
        currentTrackIndex = 0;
        if (bottomPlayer) bottomPlayer.classList.remove('playlist-is-open');
        if (playlistToggleBtn) playlistToggleBtn.setAttribute('aria-expanded', 'false');
        window.playTrackById(playlistQueue[0].id);
      }
    } catch (e) {
      console.error('Failed to load playlist:', e);
    }
  };

  // Audio Event Listeners
  audio.addEventListener('play', () => {
    updatePlayIcon(true);
    saveState(true);
  });

  audio.addEventListener('pause', () => {
    updatePlayIcon(false);
    saveState(false);
  });

  audio.addEventListener('timeupdate', () => {
    if (!isDraggingSeek) {
      const current = audio.currentTime;
      const duration = audio.duration || 1;
      const pct = (current / duration) * 100;
      if (seekFill) seekFill.style.width = `${pct}%`;
      if (currentTimeEl) currentTimeEl.textContent = formatTime(current);
      if (totalTimeEl && !isNaN(audio.duration)) totalTimeEl.textContent = formatTime(audio.duration);
    }
  });

  audio.addEventListener('ended', () => {
    if (playlistQueue.length > 0 && currentTrackIndex < playlistQueue.length - 1) {
      currentTrackIndex++;
      window.playTrackById(playlistQueue[currentTrackIndex].id);
    } else {
      updatePlayIcon(false);
      saveState(false);
    }
  });

  // UI Controls Bindings
  if (playBtn) {
    playBtn.addEventListener('click', () => {
      if (!audio.src) return;
      if (audio.paused) {
        audio.play();
      } else {
        audio.pause();
      }
    });
  }

  if (prevBtn) {
    prevBtn.addEventListener('click', () => {
      if (audio.currentTime > 3) {
        audio.currentTime = 0;
      } else if (playlistQueue.length > 0) {
        currentTrackIndex = (currentTrackIndex - 1 + playlistQueue.length) % playlistQueue.length;
        window.playTrackById(playlistQueue[currentTrackIndex].id);
      }
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', () => {
      if (playlistQueue.length > 0) {
        currentTrackIndex = (currentTrackIndex + 1) % playlistQueue.length;
        window.playTrackById(playlistQueue[currentTrackIndex].id);
      }
    });
  }

  // Seekbar click & drag
  if (seekBar) {
    seekBar.addEventListener('click', (e) => {
      if (!audio.duration) return;
      const rect = seekBar.getBoundingClientRect();
      const clickPos = (e.clientX - rect.left) / rect.width;
      audio.currentTime = clickPos * audio.duration;
    });
  }

  // Volume slider
  if (volumeSlider) {
    volumeSlider.addEventListener('input', (e) => {
      const vol = e.target.value / 100;
      audio.volume = vol;
      saveState();
    });
  }

  // Lyrics drawer toggle
  if (lyricsToggleBtn && lyricsDrawer) {
    lyricsToggleBtn.addEventListener('click', () => {
      lyricsDrawer.classList.toggle('is-open');
    });
  }

  if (playlistToggleBtn && bottomPlayer) {
    playlistToggleBtn.addEventListener('click', () => {
      const isOpen = bottomPlayer.classList.toggle('playlist-is-open');
      playlistToggleBtn.setAttribute('aria-expanded', String(isOpen));
    });
  }

  const closeLyricsBtn = document.getElementById('closeLyricsBtn');
  if (closeLyricsBtn && lyricsDrawer) {
    closeLyricsBtn.addEventListener('click', () => {
      lyricsDrawer.classList.remove('is-open');
    });
  }

  // Close modals
  document.querySelectorAll('.modal-close').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.modal-backdrop').forEach(m => m.classList.remove('is-open'));
    });
  });

  // Init
  document.addEventListener('DOMContentLoaded', () => {
    restoreState();
  });
})();
