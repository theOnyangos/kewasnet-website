<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= esc($title ?? 'Ticket Scanner') ?></title>
  <link rel="shortcut icon" href="<?= base_url("mission.jpg") ?>">
  <link rel="stylesheet" href="<?= base_url("assets/css/styles.css") ?>">
  <link rel="stylesheet" href="<?= base_url("assets/css/new.css") ?>">
  <!-- Select2 (same as website) -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
  <style>
    /* Match events page scanning usability */
    #reader video { width: 100% !important; height: auto !important; }
  </style>
</head>
<body>
  <section class="py-12 md:py-16 relative text-gray-900" style="background-color: #fafafa; min-height: 100vh; position: relative;">
    <div class="events-page-pattern-bg"></div>

    <div class="container mx-auto px-4 relative z-10">
      <div class="max-w-5xl mx-auto">
        <div class="bg-white rounded-xl p-6 sm:p-8 shadow-md">
          <div class="flex items-center justify-between gap-3 mb-6">
            <div class="flex items-center">
              <img src="<?= base_url('assets/new/site-logo.png') ?>" alt="KEWASNET Logo" class="h-10">
            </div>

            <form method="post" action="<?= base_url('scanner/logout') ?>">
              <?= csrf_field() ?>
              <button type="submit" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition">
                Logout
              </button>
            </form>
          </div>

          <div class="mb-6">
            <div class="flex flex-col items-start mb-5">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Ticket Scanner</h1>
                <p class="text-slate-600 text-sm">Signed in as <?= esc($scannerUserName ?? 'Admin') ?></p>
              </div>
            <label class="block text-sm font-medium text-slate-700 mb-2" for="eventSelect">Select Event</label>
            <select id="eventSelect" class="select2 w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" data-placeholder="Select an event…">
              <option value="">Loading events…</option>
            </select>
            <p class="text-xs text-slate-500 mt-2">Select the event you are verifying tickets for. Tickets from other events will be rejected.</p>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white border border-slate-200 rounded-xl p-5">
              <div class="flex items-center justify-between mb-4">
                <div class="font-semibold text-slate-800 flex items-center gap-2">
                  <i data-lucide="scan-line" class="w-5 h-5 text-primary"></i>
                  Camera Scanner
                </div>
                <div class="flex items-center gap-2">
                  <input id="qrImageInput" type="file" accept="image/*" class="hidden" />
                  <button id="scanImageBtn" type="button" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition">
                    Scan from Image
                  </button>
                  <button id="startBtn" class="gradient-btn text-white font-medium px-4 py-2 rounded-lg">
                    Start
                  </button>
                </div>
              </div>
              <div id="reader" class="rounded-xl overflow-hidden bg-white border border-slate-200"></div>
              <p class="text-xs text-slate-500 mt-3">
                Allow camera permission. If camera is unavailable (common on older iOS), use “Scan from Image” or manual entry.
              </p>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-5">
              <div class="font-semibold text-slate-800 flex items-center gap-2 mb-3">
                <i data-lucide="clipboard-paste" class="w-5 h-5 text-primary"></i>
                Manual Entry
              </div>
              <textarea id="qrInput" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" rows="6" placeholder="Paste QR code data here"></textarea>
              <div class="grid grid-cols-2 gap-3 mt-3">
                <button id="checkBtn" class="px-4 py-3 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition">
                  Check
                </button>
                <button id="confirmBtn" class="gradient-btn text-white font-medium px-4 py-3 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                  Confirm Check-In
                </button>
              </div>

              <div id="statusWrap" class="hidden mt-4"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- jQuery + Select2 (same as website) -->
  <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <!-- QR scanner package (CDN) -->
  <script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.10/minified/html5-qrcode.min.js"></script>
  <script>
    lucide.createIcons();

    // Select2 init for event dropdown
    function initEventSelect2() {
      if (window.$ && $.fn && typeof $.fn.select2 === 'function') {
        $('#eventSelect').select2({
          theme: 'bootstrap-5',
          width: '100%',
          placeholder: $('#eventSelect').data('placeholder') || 'Select an event…',
          allowClear: true
        });
      }
    }

    if (window.$) {
      $(document).ready(function() {
        initEventSelect2();
      });
    }
    const eventSelect = document.getElementById('eventSelect');
    const qrInput = document.getElementById('qrInput');
    const checkBtn = document.getElementById('checkBtn');
    const confirmBtn = document.getElementById('confirmBtn');
    const statusWrap = document.getElementById('statusWrap');
    const startBtn = document.getElementById('startBtn');
    const scanImageBtn = document.getElementById('scanImageBtn');
    const qrImageInput = document.getElementById('qrImageInput');

    let lastCheckedPayload = null;
    let html5Qr = null;
    let nativeStream = null;
    let nativeVideoEl = null;
    let nativeStopRequested = false;
    let audioCtx = null;

    function ensureAudioUnlocked() {
      try {
        if (!audioCtx) {
          audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (audioCtx && audioCtx.state === 'suspended') {
          audioCtx.resume().catch(() => {});
        }
      } catch (e) {
        // Some environments block audio entirely; ignore.
      }
    }

    function tone({ freq = 880, durationMs = 120, type = 'sine', gain = 0.08 } = {}) {
      ensureAudioUnlocked();
      if (!audioCtx) return;
      const t0 = audioCtx.currentTime;

      const osc = audioCtx.createOscillator();
      const g = audioCtx.createGain();
      osc.type = type;
      osc.frequency.setValueAtTime(freq, t0);

      // quick attack + exponential-ish decay to avoid clicks
      g.gain.setValueAtTime(0.0001, t0);
      g.gain.linearRampToValueAtTime(gain, t0 + 0.01);
      g.gain.linearRampToValueAtTime(0.0001, t0 + durationMs / 1000);

      osc.connect(g);
      g.connect(audioCtx.destination);

      osc.start(t0);
      osc.stop(t0 + durationMs / 1000 + 0.02);
    }

    function beepSuccess() {
      // two short pleasant beeps
      tone({ freq: 880, durationMs: 110, type: 'sine', gain: 0.07 });
      setTimeout(() => tone({ freq: 1175, durationMs: 120, type: 'sine', gain: 0.07 }), 140);
    }

    function beepError() {
      // one longer lower beep
      tone({ freq: 220, durationMs: 260, type: 'square', gain: 0.06 });
    }

    function showStatus(kind, title, lines = []) {
      statusWrap.classList.remove('hidden');

      let cls = 'alert alert-success';
      let icon = 'check-circle';
      if (kind === 'warn') { cls = 'alert alert-warning'; icon = 'alert-triangle'; }
      if (kind === 'bad') { cls = 'alert alert-danger'; icon = 'alert-circle'; }

      const listHtml = (lines || []).map(t => `<li>${String(t).replaceAll('<','&lt;').replaceAll('>','&gt;')}</li>`).join('');
      statusWrap.innerHTML = `
        <div class="${cls}">
          <i data-lucide="${icon}" class="w-8 h-8 mr-2"></i>
          <div>
            <div class="font-semibold">${title}</div>
            ${lines && lines.length ? `<ul class="mt-2 text-sm space-y-1">${listHtml}</ul>` : ``}
          </div>
        </div>
      `;
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
    }

    function stopNativeScanner() {
      nativeStopRequested = true;
      try {
        if (nativeStream) {
          nativeStream.getTracks().forEach(t => t.stop());
        }
      } catch (e) {}
      nativeStream = null;
      if (nativeVideoEl && nativeVideoEl.parentNode) {
        nativeVideoEl.parentNode.removeChild(nativeVideoEl);
      }
      nativeVideoEl = null;
    }

    async function ensureHtml5QrcodeLoaded() {
      if (window.Html5Qrcode) return true;

      const sources = [
        // primary
        'https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.10/minified/html5-qrcode.min.js',
        // fallback (in case jsdelivr is blocked on some networks/devices)
        'https://unpkg.com/html5-qrcode@2.3.10/minified/html5-qrcode.min.js',
      ];

      function loadScript(src) {
        return new Promise((resolve, reject) => {
          const s = document.createElement('script');
          s.src = src;
          s.async = true;
          s.onload = () => resolve(true);
          s.onerror = () => reject(new Error(`Failed to load ${src}`));
          document.head.appendChild(s);
        });
      }

      for (const src of sources) {
        try {
          await loadScript(src);
          if (window.Html5Qrcode) return true;
        } catch (e) {
          // keep trying other sources
        }
      }

      return false;
    }

    async function startNativeQrScanner() {
      ensureAudioUnlocked();
      if (!('mediaDevices' in navigator) || !navigator.mediaDevices.getUserMedia) {
        showStatus('bad', 'Scanner unavailable', ['This browser cannot access the camera. Use manual entry.']);
        beepError();
        return;
      }
      if (!('BarcodeDetector' in window)) {
        showStatus('bad', 'Scanner unavailable', [
          'QR scanner library failed to load.',
          'This device/browser does not support the native QR fallback.',
          'Use manual entry, or check your site CSP/network blocking external scripts.'
        ]);
        beepError();
        return;
      }

      stopNativeScanner();
      nativeStopRequested = false;

      const reader = document.getElementById('reader');
      reader.innerHTML = '';
      nativeVideoEl = document.createElement('video');
      nativeVideoEl.setAttribute('playsinline', 'true');
      nativeVideoEl.setAttribute('autoplay', 'true');
      nativeVideoEl.muted = true;
      nativeVideoEl.style.width = '100%';
      nativeVideoEl.style.height = 'auto';
      nativeVideoEl.style.display = 'block';
      reader.appendChild(nativeVideoEl);

      try {
        showStatus('warn', 'Starting camera…', ['Using native QR scanner (fallback).']);
        nativeStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' } }, audio: false });
        nativeVideoEl.srcObject = nativeStream;

        const detector = new BarcodeDetector({ formats: ['qr_code'] });
        startBtn.textContent = 'Running';
        startBtn.disabled = true;

        const tick = async () => {
          if (nativeStopRequested || !nativeVideoEl) return;
          try {
            const barcodes = await detector.detect(nativeVideoEl);
            if (barcodes && barcodes.length) {
              const raw = barcodes[0].rawValue || '';
              if (raw) {
                qrInput.value = raw;
                stopNativeScanner();
                startBtn.disabled = false;
                startBtn.textContent = 'Start';
                verify('check');
                return;
              }
            }
          } catch (err) {
            // ignore per-frame errors
          }
          requestAnimationFrame(tick);
        };

        requestAnimationFrame(tick);
      } catch (err) {
        console.error(err);
        stopNativeScanner();
        startBtn.disabled = false;
        startBtn.textContent = 'Start';
        showStatus('bad', 'Camera error', ['Unable to start camera. Check permissions, or use manual entry.']);
        beepError();
      }
    }

    async function fetchEvents() {
      const resp = await fetch('<?= base_url('scanner/events') ?>', { credentials: 'same-origin' });
      const data = await resp.json();
      eventSelect.innerHTML = '';
      const opt0 = document.createElement('option');
      opt0.value = '';
      opt0.textContent = 'Select an event…';
      eventSelect.appendChild(opt0);

      (data.events || []).forEach(e => {
        const opt = document.createElement('option');
        opt.value = e.id;
        const when = e.start_date ? new Date(e.start_date).toLocaleDateString() : '';
        opt.textContent = `${e.title}${when ? ' — ' + when : ''}${e.venue ? ' @ ' + e.venue : ''}`;
        eventSelect.appendChild(opt);
      });

      // Refresh select2 UI after options update
      if (window.$ && $.fn && typeof $.fn.select2 === 'function') {
        $('#eventSelect').trigger('change.select2');
      }
    }

    async function verify(mode) {
      ensureAudioUnlocked();
      const eventId = eventSelect.value;
      const qr = (qrInput.value || '').trim();

      if (!eventId) {
        showStatus('warn', 'Select event', ['Please select an event first.']);
        return;
      }
      if (!qr) {
        showStatus('warn', 'Missing QR', ['Paste/scan a QR code first.']);
        return;
      }

      confirmBtn.disabled = true;
      showStatus('warn', mode === 'checkin' ? 'Checking in…' : 'Checking…', []);

      const form = new FormData();
      form.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
      form.append('event_id', eventId);
      form.append('qr_code_data', qr);
      form.append('mode', mode);

      const resp = await fetch('<?= base_url('scanner/verify') ?>', {
        method: 'POST',
        credentials: 'same-origin',
        body: form
      });
      const data = await resp.json();

      if (!data.success) {
        showStatus('bad', 'Invalid', [data.message || 'Invalid ticket']);
        beepError();
        lastCheckedPayload = null;
        return;
      }

      if (data.checked_in === true) {
        showStatus('ok', 'Checked in', [
          `Attendee: ${data.ticket?.attendee_name || ''}`,
          `Ticket: ${data.ticket?.ticket_number || ''}`,
          `Event: ${data.event?.title || ''}`,
        ]);
        beepSuccess();
        lastCheckedPayload = null;
        qrInput.value = '';
        return;
      }

      // Check-only valid ticket
      lastCheckedPayload = { eventId, qr };
      showStatus('ok', 'Valid ticket', [
        `Attendee: ${data.ticket?.attendee_name || ''}`,
        `Ticket: ${data.ticket?.ticket_number || ''}`,
        `Event: ${data.event?.title || ''}`,
        'Tap “Confirm Check-In” to mark as used.'
      ]);
      beepSuccess();
      confirmBtn.disabled = false;
    }

    async function scanFromImageFile(file) {
      ensureAudioUnlocked();
      const eventId = eventSelect.value;
      if (!eventId) {
        showStatus('warn', 'Select event', ['Please select an event first.']);
        return;
      }
      if (!file) return;

      const ok = await ensureHtml5QrcodeLoaded();
      if (!ok || !window.Html5Qrcode) {
        showStatus('bad', 'Scanner unavailable', [
          'QR scanner library failed to load.',
          'Please check your network/CSP or try manual entry.'
        ]);
        beepError();
        return;
      }

      try {
        showStatus('warn', 'Scanning image…', ['Reading QR code from selected image.']);

        // Use a dedicated instance for file scans (does not require camera permissions).
        const fileScanner = new Html5Qrcode('reader');
        const decodedText = await fileScanner.scanFile(file, /* showImage= */ true);

        if (decodedText) {
          qrInput.value = decodedText;
          beepSuccess();
          await verify('check');
        } else {
          showStatus('bad', 'No QR found', ['No QR code detected in the selected image.']);
          beepError();
        }
      } catch (err) {
        console.error(err);
        showStatus('bad', 'Scan failed', ['Unable to read a QR code from that image. Try a clearer photo, or use manual entry.']);
        beepError();
      } finally {
        // allow re-selecting the same file again
        if (qrImageInput) qrImageInput.value = '';
      }
    }

    checkBtn.addEventListener('click', (e) => {
      e.preventDefault();
      ensureAudioUnlocked();
      verify('check');
    });

    confirmBtn.addEventListener('click', (e) => {
      e.preventDefault();
      ensureAudioUnlocked();
      verify('checkin');
    });

    startBtn.addEventListener('click', async (e) => {
      e.preventDefault();
      ensureAudioUnlocked();
      const eventId = eventSelect.value;
      if (!eventId) {
        showStatus('warn', 'Select event', ['Please select an event first.']);
        return;
      }
      if (!window.Html5Qrcode) {
        // CDN/library may be blocked (CSP/network). Try to load from alternate CDN, then fallback.
        const loaded = await ensureHtml5QrcodeLoaded();
        if (!loaded || !window.Html5Qrcode) {
          await startNativeQrScanner();
          return;
        }
      }

      if (!html5Qr) {
        html5Qr = new Html5Qrcode('reader');
      }

      try {
        const cameras = await Html5Qrcode.getCameras();
        const cameraId = cameras?.[0]?.id;
        if (!cameraId) {
          showStatus('bad', 'No camera', ['No camera found. Use manual entry.']);
          beepError();
          return;
        }

        showStatus('warn', 'Starting camera…', []);
        await html5Qr.start(
          { facingMode: 'environment' },
          { fps: 10, qrbox: { width: 250, height: 250 } },
          (decodedText) => {
            // auto-fill and do a check (no check-in)
            qrInput.value = decodedText;
            verify('check');
          }
        );
        startBtn.textContent = 'Running';
        startBtn.disabled = true;
      } catch (err) {
        console.error(err);
        showStatus('bad', 'Camera error', ['Unable to start camera. Check permissions, or use manual entry.']);
        beepError();
      }
    });

    scanImageBtn.addEventListener('click', async (e) => {
      e.preventDefault();
      ensureAudioUnlocked();
      // ensure library is ready before opening picker (better UX on slow networks)
      const ok = await ensureHtml5QrcodeLoaded();
      if (!ok || !window.Html5Qrcode) {
        showStatus('bad', 'Scanner unavailable', [
          'QR scanner library failed to load.',
          'Please check your network/CSP or use manual entry.'
        ]);
        beepError();
        return;
      }
      qrImageInput.click();
    });

    qrImageInput.addEventListener('change', async (e) => {
      const file = e.target && e.target.files ? e.target.files[0] : null;
      await scanFromImageFile(file);
    });

    fetchEvents().catch(() => {
      eventSelect.innerHTML = '<option value=\"\">Failed to load events</option>';
    });
  </script>
</body>
</html>
