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
                        <h3 class="text-2xl font-bold mt-1">KES 124,780</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="dollar-sign" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> 12.5% from last month
                </p>
            </div>
            
            <!-- Users Card -->
            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">New Users</p>
                        <h3 class="text-2xl font-bold mt-1">1,245</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> 8.2% from last month
                </p>
            </div>
            
            <!-- Blogs Card -->
            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Blog Articles</p>
                        <h3 class="text-2xl font-bold mt-1">48</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="newspaper" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> 5 new this month
                </p>
            </div>
            
            <!-- Events Card -->
            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Upcoming Events</p>
                        <h3 class="text-2xl font-bold mt-1">7</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="calendar-days" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="calendar-clock" class="w-4 h-4 mr-1"></i> 2 happening this week
                </p>
            </div>
            
            <!-- Resources Card -->
            <div class="bg-gradient-to-br from-teal-300 to-teal-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-teal-700">Resources</p>
                        <h3 class="text-2xl font-bold mt-1">136</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-teal-200/50 flex items-center justify-center text-teal-700">
                        <i data-lucide="book-text" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="download" class="w-4 h-4 mr-1"></i> 32 downloads today
                </p>
            </div>
            
            <!-- Forums Card -->
            <div class="bg-gradient-to-br from-teal-300 to-teal-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-teal-700">Forum Discussions</p>
                        <h3 class="text-2xl font-bold mt-1">89</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-teal-200/50 flex items-center justify-center text-teal-700">
                        <i data-lucide="messages-square" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="message-circle" class="w-4 h-4 mr-1"></i> 12 active today
                </p>
            </div>
            
            <!-- Courses Card -->
            <div class="bg-gradient-to-br from-teal-300 to-teal-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-teal-700">Online Courses</p>
                        <h3 class="text-2xl font-bold mt-1">24</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-teal-200/50 flex items-center justify-center text-teal-700">
                        <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="user-check" class="w-4 h-4 mr-1"></i> 56 enrollments
                </p>
            </div>
            
            <!-- Partners Card -->
            <div class="bg-gradient-to-br from-teal-300 to-teal-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-teal-700">Strategic Partners</p>
                        <h3 class="text-2xl font-bold mt-1">18</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-teal-200/50 flex items-center justify-center text-teal-700">
                        <i data-lucide="handshake" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="plus-circle" class="w-4 h-4 mr-1"></i> 2 new partnerships
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
                            <!-- Discussion 1 -->
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-4 flex items-center gap-2">
                                    <a href="#" class="font-medium text-secondary hover:underline flex items-start">
                                        <i data-lucide="message-square" class="w-4 h-4 mt-1 mr-2 text-secondary"></i>
                                        Water conservation techniques in urban areas
                                    </a>
                                </td>
                                <td class="py-4 text-sm text-slate-600">Water Conservation</td>
                                <td class="py-4 text-sm text-slate-600">24</td>
                                <td class="py-4 text-sm text-slate-600">
                                    <div class="flex items-center">
                                        <span>2 hours ago</span>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Discussion 2 -->
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-4 flex items-center gap-2">
                                    <a href="#" class="font-medium text-secondary hover:underline flex items-start">
                                        <i data-lucide="message-square" class="w-4 h-4 mt-1 mr-2 text-secondary"></i>
                                        Community engagement success stories
                                    </a>
                                </td>
                                <td class="py-4 text-sm text-slate-600">Community</td>
                                <td class="py-4 text-sm text-slate-600">15</td>
                                <td class="py-4 text-sm text-slate-600">
                                    <div class="flex items-center">
                                        <span>5 hours ago</span>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Discussion 3 -->
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-4 flex items-center gap-2">
                                    <a href="#" class="font-medium text-secondary hover:underline flex items-start">
                                        <i data-lucide="message-square" class="w-4 h-4 mt-1 mr-2 text-secondary"></i>
                                        Policy changes affecting water management
                                    </a>
                                </td>
                                <td class="py-4 text-sm text-slate-600">Policy</td>
                                <td class="py-4 text-sm text-slate-600">32</td>
                                <td class="py-4 text-sm text-slate-600">
                                    <div class="flex items-center">
                                        <span>1 day ago</span>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Discussion 4 -->
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-4 flex items-center gap-2">
                                    <a href="#" class="font-medium text-secondary hover:underline flex items-start">
                                        <i data-lucide="message-square" class="w-4 h-4 mt-1 mr-2 text-secondary"></i>
                                        New sanitation technology innovations
                                    </a>
                                </td>
                                <td class="py-4 text-sm text-slate-600">Technology</td>
                                <td class="py-4 text-sm text-slate-600">8</td>
                                <td class="py-4 text-sm text-slate-600">
                                    <div class="flex items-center">
                                        <span>2 days ago</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Activities (Existing) -->
            <div class="bg-white rounded-xl shadow-md p-6 activity-container col-span-2">
                <h2 class="text-lg font-semibold mb-6">Recent Activity</h2>
                <div class="space-y-4">
                    <div class="flex items-start activity-item">
                        <div class="w-10 h-10 rounded-full bg-primaryShades-100 flex items-center justify-center text-primary mr-4">
                            <i data-lucide="user-plus" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="font-medium">New user registered</p>
                            <p class="text-sm text-slate-500">Sarah Johnson joined as Content Creator</p>
                            <p class="text-xs text-slate-400 mt-1">35 minutes ago</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start activity-item">
                        <div class="w-10 h-10 rounded-full bg-secondaryShades-100 flex items-center justify-center text-secondary mr-4">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="font-medium">New blog published</p>
                            <p class="text-sm text-slate-500">"Water Conservation Techniques" by David Kim</p>
                            <p class="text-xs text-slate-400 mt-1">2 hours ago</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start activity-item">
                        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 mr-4">
                            <i data-lucide="calendar-check" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="font-medium">Event created</p>
                            <p class="text-sm text-slate-500">"Community Cleanup Day" on March 15</p>
                            <p class="text-xs text-slate-400 mt-1">5 hours ago</p>
                        </div>
                    </div>

                    <div class="flex items-start activity-item">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 mr-4">
                            <i data-lucide="message-square" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="font-medium">New discussion</p>
                            <p class="text-sm text-slate-500">"Policy changes" started by Admin</p>
                            <p class="text-xs text-slate-400 mt-1">1 day ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);

        gradient.addColorStop(0, 'rgba(54, 67, 145, 0.8)');
        gradient.addColorStop(1, 'rgba(54, 67, 145, 0.4)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Revenue',
                    data: [45000, 52000, 48000, 62000, 75000, 82000],
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
        const ctx = document.getElementById('trafficChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Direct', 'Social'],
                datasets: [{
                    data: [35, 20],
                    backgroundColor: [
                        '#364391',
                        '#27aae0'
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