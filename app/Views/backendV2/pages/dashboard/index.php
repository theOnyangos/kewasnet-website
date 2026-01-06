<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('backendV2/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <!-- Dashboard Content -->
    <main class="flex-1 overflow-y-auto p-6">
        <!-- Breadcrumbs -->
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="<?= base_url('dashboard') ?>" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-primary">
                        <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                        Dashboard
                    </a>
                </li>
            </ol>
        </nav>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 stats-container">
            <!-- Revenue Card -->
            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Total Revenue</p>
                        <h3 class="text-2xl font-bold mt-1">KES <?= number_format($statistics['total_revenue'] ?? 0, 0) ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="dollar-sign" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> <?= number_format($statistics['revenue_change'] ?? 0, 1) ?>% from last month
                </p>
            </div>
            
            <!-- Users Card -->
            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">New Users</p>
                        <h3 class="text-2xl font-bold mt-1"><?= number_format($statistics['new_users'] ?? 0) ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> <?= number_format($statistics['users_change'] ?? 0, 1) ?>% from last month
                </p>
            </div>
            
            <!-- Blogs Card -->
            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Blog Articles</p>
                        <h3 class="text-2xl font-bold mt-1"><?= number_format($statistics['blog_articles'] ?? 0) ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="newspaper" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> <?= number_format($statistics['blog_change'] ?? 0) ?> new this month
                </p>
            </div>
            
            <!-- Events Card -->
            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Upcoming Events</p>
                        <h3 class="text-2xl font-bold mt-1"><?= number_format($statistics['upcoming_events'] ?? 0) ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="calendar-days" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="calendar-clock" class="w-4 h-4 mr-1"></i> <?= number_format($statistics['events_this_week'] ?? 0) ?> happening this week
                </p>
            </div>
            
            <!-- Resources Card -->
            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Resources</p>
                        <h3 class="text-2xl font-bold mt-1"><?= number_format($statistics['resources'] ?? 0) ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="book-text" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="download" class="w-4 h-4 mr-1"></i> <?= number_format($statistics['downloads_today'] ?? 0) ?> downloads today
                </p>
            </div>
            
            <!-- Forums Card -->
            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Forum Discussions</p>
                        <h3 class="text-2xl font-bold mt-1"><?= number_format($statistics['forum_discussions'] ?? 0) ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="messages-square" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="message-circle" class="w-4 h-4 mr-1"></i> <?= number_format($statistics['active_today'] ?? 0) ?> active today
                </p>
            </div>
            
            <!-- Courses Card -->
            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Online Courses</p>
                        <h3 class="text-2xl font-bold mt-1"><?= number_format($statistics['online_courses'] ?? 0) ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="user-check" class="w-4 h-4 mr-1"></i> <?= number_format($statistics['enrollments'] ?? 0) ?> enrollments
                </p>
            </div>
            
            <!-- Partners Card -->
            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Strategic Partners</p>
                        <h3 class="text-2xl font-bold mt-1"><?= number_format($statistics['strategic_partners'] ?? 0) ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="handshake" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="plus-circle" class="w-4 h-4 mr-1"></i> <?= number_format($statistics['new_partnerships'] ?? 0) ?> new partnerships
                </p>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 lg:col-span-2">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold">Revenue Overview (KES)</h2>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 text-sm rounded-md bg-primaryShades-100 text-primary">Monthly</button>
                        <button class="px-3 py-1 text-sm rounded-md bg-lightGray text-slate-600">Yearly</button>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6 chart-container">
                <h2 class="text-lg font-semibold mb-6">Content Distribution</h2>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="trafficChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Add this below your charts section -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">
            <!-- Recent Discussions Table -->
            <div class="bg-white rounded-xl shadow-md p-6 col-span-3">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold">Recent Discussions</h2>
                    <a href="<?= base_url('auth/forums') ?>" class="text-sm text-primary hover:underline flex items-center">
                        View All <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i>
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left border-b borderColor text-slate-500 text-sm">
                                <th class="pb-3 font-medium">Topic</th>
                                <th class="pb-3 font-medium">Category</th>
                                <th class="pb-3 font-medium">Replies</th>
                                <th class="pb-3 font-medium">Last Activity</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divideColor">
                            <?php if (!empty($recentDiscussions)): ?>
                                <?php foreach ($recentDiscussions as $discussion): ?>
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="py-4 flex items-center gap-2">
                                            <a href="<?= base_url('forums/discussion/' . ($discussion['slug'] ?? '')) ?>" class="font-medium text-secondary hover:underline flex items-start">
                                                <i data-lucide="message-square" class="w-4 h-4 mt-1 mr-2 text-secondary"></i>
                                                <?= esc($discussion['title'] ?? 'Untitled Discussion') ?>
                                            </a>
                                        </td>
                                        <td class="py-4 text-sm text-slate-600"><?= esc($discussion['forum_name'] ?? 'General') ?></td>
                                        <td class="py-4 text-sm text-slate-600"><?= number_format($discussion['reply_count'] ?? 0) ?></td>
                                        <td class="py-4 text-sm text-slate-600">
                                            <div class="flex items-center">
                                                <span><?= esc($discussion['last_activity'] ?? 'N/A') ?></span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-500">
                                        <i data-lucide="message-square-off" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                        <p>No recent discussions</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Activities (Existing) -->
            <div class="bg-white rounded-xl shadow-md p-6 activity-container col-span-2">
                <h2 class="text-lg font-semibold mb-6">Recent Activity</h2>
                <div class="space-y-4">
                    <?php if (!empty($recentActivities)): ?>
                        <?php foreach ($recentActivities as $activity): ?>
                            <?php
                            // Determine color classes based on activity type
                            $colorClasses = [
                                'primary' => 'bg-primaryShades-100 text-primary',
                                'secondary' => 'bg-secondaryShades-100 text-secondary',
                                'amber' => 'bg-amber-100 text-amber-600',
                                'emerald' => 'bg-emerald-100 text-emerald-600',
                            ];
                            $colorClass = $colorClasses[$activity['color'] ?? 'primary'] ?? $colorClasses['primary'];
                            ?>
                            <div class="flex items-start activity-item">
                                <div class="w-10 h-10 rounded-full <?= $colorClass ?> flex items-center justify-center mr-4">
                                    <i data-lucide="<?= esc($activity['icon'] ?? 'bell') ?>" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <p class="font-medium"><?= esc($activity['title'] ?? 'Activity') ?></p>
                                    <p class="text-sm text-slate-500"><?= esc($activity['description'] ?? '') ?></p>
                                    <p class="text-xs text-slate-400 mt-1"><?= esc($activity['time'] ?? 'N/A') ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-slate-500">
                            <i data-lucide="activity" class="w-12 h-12 mx-auto mb-2 text-slate-300"></i>
                            <p>No recent activity</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Revenue Chart Data from PHP
        const revenueLabels = <?= json_encode($revenueChartData['labels'] ?? []) ?>;
        const revenueData = <?= json_encode($revenueChartData['data'] ?? []) ?>;
        
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);

        gradient.addColorStop(0, 'rgba(54, 67, 145, 0.8)');
        gradient.addColorStop(1, 'rgba(54, 67, 145, 0.4)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Revenue',
                    data: revenueData,
                    borderColor: '#364391',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Content Distribution Chart Data from PHP
        const contentLabels = <?= json_encode($contentDistributionData['labels'] ?? []) ?>;
        const contentData = <?= json_encode($contentDistributionData['data'] ?? []) ?>;
        
        const ctx = document.getElementById('trafficChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: contentLabels,
                datasets: [{
                    data: contentData,
                    backgroundColor: [
                        '#364391',
                        '#27aae0',
                        '#10b981',
                        '#f59e0b',
                        '#8b5cf6'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 20
                        }
                    }
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>