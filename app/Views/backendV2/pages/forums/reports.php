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
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Content Reports',
            'pageDescription' => 'Review and moderate reported forum content and discussions',
            'breadcrumbs' => [
                ['label' => 'Forums', 'url' => base_url('auth/forums')],
                ['label' => 'Content Reports']
            ]
        ]) ?>

        <div class="px-6 pb-6">

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