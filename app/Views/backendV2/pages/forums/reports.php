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
    <main class="flex-1 overflow-y-auto">
        <!-- Hero Banner with Gradient Overlay -->
        <div class="relative h-64 mb-6 overflow-hidden bg-cover bg-center" style="background-image: linear-gradient(to right, rgba(0,0,0,0) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.8) 100%), url('<?= base_url('images/dashboard-breadcrumb.jpg') ?>');">
            <!-- Content -->
            <div class="relative z-10 h-full flex flex-col justify-center px-6 md:px-12">
                <!-- Breadcrumbs -->
                <nav class="mb-4" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="<?= base_url('dashboard') ?>" class="inline-flex items-center text-sm font-medium text-white/80 hover:text-white transition-colors">
                                <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i data-lucide="chevron-right" class="w-4 h-4 text-white/60"></i>
                                <a href="<?= base_url('auth/forums') ?>" class="ml-1 text-sm font-medium text-white/80 hover:text-white md:ml-2 transition-colors">Forums</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <i data-lucide="chevron-right" class="w-4 h-4 text-white/60"></i>
                                <span class="ml-1 text-sm font-medium text-white md:ml-2">Content Reports</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <!-- Title and Description -->
                <div class="flex flex-col md:flex-row md:items-end md:justify-between">
                    <div>
                        <h1 class="text-4xl md:text-5xl font-bold text-white mb-2 drop-shadow-lg">
                            Content Reports
                        </h1>
                        <p class="text-lg text-white/90 max-w-2xl">
                            Review and moderate reported forum content and discussions
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6">
        <!-- Header Section -->
        <?= $this->include('backendV2/pages/forums/partials/header_section') ?>

        <!-- Quick Stats Cards -->
        <?= $this->include('backendV2/pages/forums/partials/quick_stats_section') ?>

        <!-- Navigation Tabs -->
        <?= $this->include('backendV2/pages/forums/partials/navigation_section') ?>

        <!-- Reports Tab -->
        <div id="reports" class="">
            <div class="bg-white rounded-b-xl shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Content Reports</h3>
                    <p class="text-gray-600">Review and moderate reported content</p>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php if (!empty($reports)): ?>
                        <?php foreach ($reports as $report): ?>
                            <div class="p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <?php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'reviewed' => 'bg-blue-100 text-blue-800',
                                                'resolved' => 'bg-green-100 text-green-800',
                                                'dismissed' => 'bg-gray-100 text-gray-800'
                                            ];
                                            $statusColor = $statusColors[$report->status] ?? 'bg-gray-100 text-gray-800';
                                            ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusColor ?>">
                                                <i data-lucide="<?= $report->status === 'pending' ? 'alert-triangle' : 'check-circle' ?>" class="w-3 h-3 mr-1"></i>
                                                <?= ucfirst($report->status) ?>
                                            </span>
                                            <span class="text-sm text-gray-500">Reported <?= date('M j, Y \a\t g:i a', strtotime($report->created_at)) ?></span>
                                        </div>
                                        <h4 class="font-medium text-gray-900"><?= esc($report->reason) ?></h4>
                                        <?php if ($report->description): ?>
                                        <p class="text-sm text-gray-600 mt-1"><?= esc($report->description) ?></p>
                                        <?php endif; ?>
                                        <div class="mt-3 flex items-center space-x-4">
                                            <div class="text-sm text-gray-500">
                                                <strong>Reporter:</strong> <?= esc($report->reporter_first_name . ' ' . $report->reporter_last_name) ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <strong>Reported User:</strong> <?= esc($report->reported_first_name . ' ' . $report->reported_last_name) ?>
                                            </div>
                                        </div>
                                        <?php if ($report->action_taken): ?>
                                        <div class="mt-2 text-sm text-gray-500">
                                            <strong>Action Taken:</strong> <?= esc($report->action_taken) ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($report->status === 'pending'): ?>
                                    <div class="flex space-x-2">
                                        <button onclick="resolveReport('<?= $report->id ?>')" class="px-4 py-2 text-sm font-medium text-green-700 bg-green-100 rounded-lg hover:bg-green-200">
                                            Resolve
                                        </button>
                                        <button onclick="dismissReport('<?= $report->id ?>')" class="px-4 py-2 text-sm font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200">
                                            Dismiss
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-12 text-center">
                            <div class="bg-gray-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="inbox" class="w-10 h-10 text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Reports Yet</h3>
                            <p class="text-gray-600">There are no reports to review at this time.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        </div>
    </main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
function resolveReport(reportId) {
    Swal.fire({
        title: 'Resolve Report',
        html: `
            <div class="text-left">
                <label class="block text-sm font-medium text-gray-700 mb-2">Action Taken</label>
                <textarea id="actionTaken" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" 
                          rows="3" placeholder="Describe the action taken..."></textarea>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Resolve',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#10b981',
        preConfirm: () => {
            const actionTaken = document.getElementById('actionTaken').value;
            if (!actionTaken) {
                Swal.showValidationMessage('Please describe the action taken');
                return false;
            }
            return { actionTaken };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            updateReportStatus(reportId, 'resolved', result.value.actionTaken);
        }
    });
}

function dismissReport(reportId) {
    Swal.fire({
        title: 'Dismiss Report',
        html: `
            <div class="text-left">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Dismissal</label>
                <textarea id="dismissReason" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500" 
                          rows="3" placeholder="Why is this report being dismissed?"></textarea>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Dismiss',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#ef4444',
        preConfirm: () => {
            const dismissReason = document.getElementById('dismissReason').value;
            if (!dismissReason) {
                Swal.showValidationMessage('Please provide a reason for dismissal');
                return false;
            }
            return { dismissReason };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            updateReportStatus(reportId, 'dismissed', result.value.dismissReason);
        }
    });
}

function updateReportStatus(reportId, status, actionTaken) {
    $.ajax({
        url: '<?= base_url('auth/forums/reports/update-status') ?>',
        type: 'POST',
        data: {
            report_id: reportId,
            status: status,
            action_taken: actionTaken,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to update report status'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while updating the report'
            });
        }
    });
}
</script>
<?= $this->endSection() ?>