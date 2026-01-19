<?= $this->include('frontendV2/ksp/layouts/constants/auth-header') ?>
  <div class="min-h-screen flex items-center justify-center p-4">
    <div class="auth-container w-full bg-white rounded-xl p-8 sm:p-10 shadow-md z-10 max-w-md">
      <div class="text-center mb-8">
        <img src="<?= base_url('assets/new/site-logo.png') ?>" alt="KEWASNET Logo" class="h-12 mx-auto mb-4">
        <h1 class="text-2xl font-bold text-slate-800">Ticket Scanner</h1>
        <p class="text-slate-600">Sign in with your admin account</p>
      </div>

      <!-- Flash Messages -->
      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger mb-4">
          <i data-lucide="alert-circle" class="w-8 h-8 mr-2"></i>
          <?= esc(session()->getFlashdata('error')) ?>
        </div>
      <?php endif; ?>

      <form action="<?= base_url('scanner/login') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="space-y-4">
          <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Admin Email address</label>
            <input
              type="email"
              id="email"
              name="email"
              class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              placeholder="Enter your email"
              required
              autocomplete="username"
            >
          </div>
          <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input
              type="password"
              id="password"
              name="password"
              class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              placeholder="Enter your password"
              required
              autocomplete="current-password"
            >
          </div>
          <button type="submit" class="flex items-center justify-center gap-2 w-full gradient-btn text-white font-medium py-3 px-4 rounded-lg transition duration-200">
            <span>Sign in</span>
            <i data-lucide="qr-code" class="w-5 h-5 ml-2 z-20"></i>
          </button>
        </div>
      </form>

      <div class="text-center text-xs text-slate-500 mt-6">
        This scanner is restricted to authorized admins only.
      </div>
    </div>
  </div>
<?= $this->include('frontendV2/ksp/layouts/constants/auth-footer') ?>
