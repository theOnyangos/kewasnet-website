<?php 
    use App\Libraries\CIAuth;
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();

    // dd($resource);
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('frontendV2/ksp/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?php if (isset($resource) && !empty($resource['title'])): ?>
<?= esc($resource['title']) ?> - <?= esc($pillar['title'] ?? $pillar['name'] ?? 'KSP') ?>
<?php else: ?>
<?= $title ?>
<?php endif; ?>
<?= $this->endSection(); ?>

<!--  Section Meta Description Block  -->
<?= $this->section('meta_description'); ?>
<?php if (isset($resource) && !empty($resource['description'])): ?>
<?= esc(substr($resource['description'], 0, 160)) ?>
<?php else: ?>
Explore research articles, case studies, and policy briefs from the Kenya Sanitation Program knowledge sharing platform.
<?php endif; ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
     <!-- Breadcrumb -->
    <div class="bg-white border-b borderColor">
        <div class="container mx-auto px-4 py-3">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="/" class="inline-flex items-center text-sm font-medium text-slate-600 hover:text-primary">
                            <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                            <a href="/ksp/pillars" class="ml-1 text-sm font-medium text-slate-600 hover:text-primary md:ml-2">Pillars</a>
                        </div>
                    </li>
                    <?php if (isset($breadcrumb) && !empty($breadcrumb['pillar_slug'])): ?>
                    <li>
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                            <a href="/ksp/pillar-articles/<?= esc($breadcrumb['pillar_slug']) ?>" class="ml-1 text-sm font-medium text-slate-600 hover:text-primary md:ml-2"><?= esc($breadcrumb['pillar_title'] ?? 'Pillar') ?></a>
                        </div>
                    </li>
                    <?php endif; ?>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                            <span class="ml-1 text-sm font-medium text-primary md:ml-2"><?= esc($breadcrumb['article_title'] ?? $resource['title'] ?? 'Article') ?></span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <?php if (!isset($resource) || empty($resource)): ?>
            <!-- Resource Not Found -->
            <div class="max-w-2xl mx-auto text-center py-16">
                <div class="bg-white rounded-lg shadow-sm border borderColor p-8">
                    <i data-lucide="file-x" class="w-16 h-16 mx-auto mb-4 text-slate-400"></i>
                    <h1 class="text-2xl font-bold text-dark mb-4">Article Not Found</h1>
                    <p class="text-slate-600 mb-6">
                        The article you're looking for could not be found. It may have been moved, deleted, or the URL might be incorrect.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="<?= base_url('ksp/pillars') ?>" 
                        class="bg-gradient-to-r from-primary to-secondary text-white px-6 py-3 rounded-lg hover:shadow-lg transition-all duration-300">
                            <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
                            Browse All Pillars
                        </a>
                        <a href="<?= base_url('ksp') ?>" 
                        class="border border-slate-300 text-slate-700 px-6 py-3 rounded-lg hover:bg-slate-50 transition-all duration-300">
                            Go to KSP Home
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Article Content -->
            <article class="w-full lg:w-2/3">
                <!-- Article Header -->
                <header class="mb-8">
                    <?php if (isset($resource)): ?>
                    <div class="flex items-center space-x-2 mb-4">
                        <span class="text-xs font-medium px-3 py-1 bg-primaryShades-100 text-primary rounded-full">
                            <?= esc($resource['file_type'] ?? 'Document') ?>
                        </span>
                        <?php if (!empty($resource['publication_year'])): ?>
                        <span class="text-sm text-slate-500">Published: <?= esc($resource['publication_year']) ?></span>
                        <?php endif; ?>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold text-dark mb-4"><?= esc($resource['title']) ?></h1>
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="flex items-center space-x-2">
                            <i data-lucide="eye" class="w-5 h-5 text-slate-500"></i>
                            <span class="text-sm text-slate-600"><?= number_format($resource['view_count'] ?? 0) ?> views</span>
                        </div>
                        <?php if (!empty($resource['attachments'])): ?>
                            <div class="flex items-center space-x-2">
                                <i data-lucide="download" class="w-5 h-5 text-slate-500"></i>
                                <span class="text-sm text-slate-600"><?= number_format($resource['total_downloads'] ?? 0) ?> downloads</span>
                            </div>
                            <?php if (!empty($resource['file_size'])): ?>
                            <div class="flex items-center space-x-2">
                                <i data-lucide="file" class="w-5 h-5 text-slate-500"></i>
                                <span class="text-sm text-slate-600"><?= esc($resource['file_size']) ?></span>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Author/Organization Information -->
                    <?php if (!empty($contributors)): ?>
                    <div class="flex flex-wrap items-center gap-4 mb-6">
                        <?php foreach($contributors as $contributor): ?>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-slate-600 font-medium"><?= esc($contributor['name']) ?></span>
                            <?php if (!empty($contributor['organization'])): ?>
                            <span class="text-sm text-slate-500">- <?= esc($contributor['organization']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($contributor['role'])): ?>
                            <span class="text-xs px-2 py-1 bg-slate-100 text-slate-600 rounded"><?= esc($contributor['role']) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Featured Image -->
                    <?php if (!empty($resource['image_url'])): ?>
                    <div class="rounded-xl overflow-hidden shadow-md mb-6">
                        <img src="<?= esc($resource['image_url']) ?>" 
                             alt="<?= esc($resource['title']) ?>" 
                             class="w-full h-auto object-cover">
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </header>
                
                <!-- Article Abstract -->
                <?php if (isset($resource) && !empty($resource['description'])): ?>
                <div class="bg-primaryShades-50 border border-primaryShades-200 rounded-lg p-6 mb-8">
                    <h2 class="text-xl font-bold text-primary mb-3 flex items-center">
                        <i data-lucide="clipboard-list" class="w-5 h-5 mr-2"></i>
                        Abstract
                    </h2>
                    <p class="text-slate-700"><?= esc($resource['description']) ?></p>
                </div>
                <?php endif; ?>
                
                <!-- Article Content -->
                <div class="article-content bg-white p-6 rounded-lg shadow-sm border borderColor mb-8">
                    <?php if (isset($resource)): ?>
                        <?php if (!empty($resource['content'])): ?>
                            <div class="prose prose-lg max-w-none">
                                <?= $resource['content'] ?> <!-- Assuming content is already sanitized from CMS -->
                            </div>
                        <?php elseif (!empty($resource['description'])): ?>
                            <div class="prose prose-lg max-w-none">
                                <p><?= nl2br(esc($resource['description'])) ?></p>
                            </div>
                        <?php else: ?>
                            <div class="text-slate-500 italic text-center py-8">
                                <i data-lucide="file-text" class="w-8 h-8 mx-auto mb-3 opacity-50"></i>
                                <p>No detailed content available for this resource.</p>
                                <?php if (!empty($resource['file_url'])): ?>
                                <p class="mt-2">
                                    <a href="<?= esc($resource['file_url']) ?>" 
                                       class="text-primary hover:text-primaryShades-700 underline"
                                       target="_blank">
                                        Download the full document to view content
                                    </a>
                                </p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-slate-500 italic text-center py-8">
                            <i data-lucide="alert-circle" class="w-8 h-8 mx-auto mb-3 opacity-50"></i>
                            <p>Resource content not available.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Article Tags -->
                <?php if (isset($resource) && !empty($resource['tags'])): ?>
                    <div class="flex flex-wrap gap-2 mb-8">
                        <?php 
                        $tags = is_string($resource['tags']) ? explode(',', $resource['tags']) : $resource['tags'];
                        foreach($tags as $tag): 
                            $tag = trim($tag);
                            if (!empty($tag)):
                        ?>
                        <span class="text-xs font-medium px-3 py-1 bg-slate-100 text-slate-600 rounded-full">
                            <?= esc($tag) ?>
                        </span>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                <?php endif; ?>
                
                <!-- Article Actions -->
                 <div class="mb-3 flex flex-wrap gap-4">
                    <?php if (!empty($resource['attachments'])): ?>
                        <?php foreach($resource['attachments'] as $attachment): ?>
                            <a href="<?= base_url('ksp/attachments/download/' . $attachment->file_path) ?>" 
                            class="flex items-center space-x-2 bg-gradient-to-r from-primary to-secondary text-white px-4 py-2 rounded-lg hover:shadow-lg transition-all duration-300">
                                <i data-lucide="download" class="w-5 h-5"></i>
                                <span>Download <?= esc($attachment->original_name ?? 'Document') ?></span>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="flex flex-wrap justify-between items-center gap-4 mb-12">
                    <div class="flex items-center space-x-4">
                        <button class="flex items-center space-x-2 text-slate-600 hover:text-primary">
                            <i data-lucide="bookmark" class="w-5 h-5"></i>
                            <span>Save for later</span>
                        </button>
                        <button class="flex items-center space-x-2 text-slate-600 hover:text-primary">
                            <i data-lucide="share-2" class="w-5 h-5"></i>
                            <span>Share</span>
                        </button>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button class="flex items-center space-x-2 text-slate-600 hover:text-primary">
                            <i data-lucide="thumbs-up" class="w-5 h-5"></i>
                            <span>Helpful</span>
                        </button>
                        <button class="flex items-center space-x-2 text-slate-600 hover:text-primary">
                            <i data-lucide="message-square" class="w-5 h-5"></i>
                            <span>Comment</span>
                        </button>
                    </div>
                </div>
                
                <!-- Comments Section -->
                <div class="bg-white p-6 rounded-lg shadow-sm border borderColor mb-8">
                    <h2 class="text-xl font-bold text-dark mb-6 flex items-center">
                        <i data-lucide="message-square" class="w-5 h-5 mr-2"></i>
                        Comments (<?= $commentCount ?? 0 ?>)
                    </h2>
                    
                    <!-- Comment Form -->
                    <?php if (isset($isLoggedIn) && $isLoggedIn || CIAuth::isLoggedIn()): ?>
                        <div class="mb-8">
                            <form id="commentForm" class="comment-form">
                                <input type="hidden" id="resourceId" value="<?= esc($resource['id'] ?? '') ?>">
                                <input type="hidden" id="parentId" value="">
                                <textarea 
                                    id="commentContent" 
                                    name="content"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" 
                                    rows="3" 
                                    placeholder="Add your comment..."
                                    maxlength="1000"></textarea>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-xs text-slate-500">
                                        <span id="charCount">0</span>/1000 characters
                                    </span>
                                    <div class="flex space-x-2">
                                        <button type="button" id="cancelReply" class="hidden px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                                            Cancel Reply
                                        </button>
                                        <button type="submit" class="flex items-center bg-gradient-to-r from-primary to-secondary px-6 py-2 rounded-lg text-white font-medium hover:shadow-lg transition-all duration-300">
                                            <span id="submitButtonText">Post Comment</span>
                                            <i data-lucide="send" class="w-4 h-4 ml-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="mb-8 text-center py-8 bg-slate-50 rounded-lg">
                            <i data-lucide="lock" class="w-8 h-8 mx-auto mb-3 text-slate-400"></i>
                            <p class="text-slate-600 mb-4">You must be logged in to post comments.</p>
                            <a href="<?= base_url('ksp/login') ?>" class="inline-flex items-center bg-gradient-to-r from-primary to-secondary text-white px-6 py-2 rounded-lg hover:shadow-lg transition-all duration-300">
                                <i data-lucide="log-in" class="w-4 h-4 mr-2"></i>
                                Login to Comment
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Comments List -->
                    <div id="commentsList" class="space-y-6">
                        <?php if (!empty($comments)): ?>
                            <?php foreach ($comments as $comment): ?>
                            <div class="comment" data-comment-id="<?= esc($comment['id']) ?>">
                                <div class="flex space-x-4">
                                    <img src="<?= !empty($comment['picture']) ? esc($comment['picture']) : base_url('assets/new/profile-avatar.jpg') ?>" 
                                         alt="<?= esc($comment['first_name'] . ' ' . $comment['last_name']) ?>" 
                                         class="w-10 h-10 rounded-full object-cover">
                                    <div class="flex-1">
                                        <div class="bg-slate-50 p-4 rounded-lg">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center space-x-2">
                                                    <span class="font-medium"><?= esc($comment['first_name'] . ' ' . $comment['last_name']) ?></span>
                                                    <?php if (!empty($comment['role_name'])): ?>
                                                    <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full">
                                                        <?= esc($comment['role_name']) ?>
                                                    </span>
                                                    <?php endif; ?>
                                                </div>
                                                <span class="text-xs text-slate-500"><?= timeAgo($comment['created_at']) ?></span>
                                            </div>
                                            <p class="text-slate-700"><?= nl2br(esc($comment['content'])) ?></p>
                                        </div>
                                        <div class="flex items-center space-x-4 mt-2 ml-4">
                                            <?php if (isset($isLoggedIn) && $isLoggedIn || CIAuth::isLoggedIn()): ?>
                                            <button class="reply-btn text-xs text-slate-500 hover:text-primary" data-comment-id="<?= esc($comment['id']) ?>">
                                                Reply
                                            </button>
                                            <?php endif; ?>
                                            <?php if (isset($isLoggedIn) && $isLoggedIn || CIAuth::isLoggedIn()): ?>
                                            <button class="vote-helpful-btn text-xs text-slate-500 hover:text-primary flex items-center <?= $comment['user_has_voted'] ? 'text-primary' : '' ?>" 
                                                    data-comment-id="<?= esc($comment['id']) ?>"
                                                    <?= $comment['user_has_voted'] ? 'disabled' : '' ?>>
                                                <i data-lucide="thumbs-up" class="w-3 h-3 mr-1"></i>
                                                Helpful (<span class="helpful-count"><?= $comment['helpful_count'] ?></span>)
                                            </button>
                                            <?php else: ?>
                                            <span class="text-xs text-slate-400 flex items-center">
                                                <i data-lucide="thumbs-up" class="w-3 h-3 mr-1"></i>
                                                Helpful (<span class="helpful-count"><?= $comment['helpful_count'] ?></span>)
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Replies -->
                                        <?php if (!empty($comment['replies'])): ?>
                                        <div class="ml-6 mt-4 space-y-4">
                                            <?php foreach ($comment['replies'] as $reply): ?>
                                            <div class="reply" data-comment-id="<?= esc($reply['id']) ?>">
                                                <div class="flex space-x-3">
                                                    <img src="<?= !empty($reply['picture']) ? esc($reply['picture']) : base_url('assets/new/profile-avatar.jpg') ?>" 
                                                         alt="<?= esc($reply['first_name'] . ' ' . $reply['last_name']) ?>" 
                                                         class="w-8 h-8 rounded-full object-cover">
                                                    <div class="flex-1">
                                                        <div class="bg-primaryShades-50 p-3 rounded-lg">
                                                            <div class="flex items-center justify-between mb-1">
                                                                <div class="flex items-center space-x-2">
                                                                    <span class="font-medium text-sm"><?= esc($reply['first_name'] . ' ' . $reply['last_name']) ?></span>
                                                                    <?php if (!empty($reply['role_name'])): ?>
                                                                    <span class="text-xs px-1 py-0.5 bg-blue-100 text-blue-800 rounded">
                                                                        <?= esc($reply['role_name']) ?>
                                                                    </span>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <span class="text-xs text-slate-500"><?= timeAgo($reply['created_at']) ?></span>
                                                            </div>
                                                            <p class="text-slate-700 text-sm"><?= nl2br(esc($reply['content'])) ?></p>
                                                        </div>
                                                        <div class="flex items-center space-x-4 mt-1 ml-3">
                                                            <?php if (isset($isLoggedIn) && $isLoggedIn || CIAuth::isLoggedIn()): ?>
                                                            <button class="vote-helpful-btn text-xs text-slate-500 hover:text-primary flex items-center <?= $reply['user_has_voted'] ? 'text-primary' : '' ?>" 
                                                                    data-comment-id="<?= esc($reply['id']) ?>"
                                                                    <?= $reply['user_has_voted'] ? 'disabled' : '' ?>>
                                                                <i data-lucide="thumbs-up" class="w-3 h-3 mr-1"></i>
                                                                Helpful (<span class="helpful-count"><?= $reply['helpful_count'] ?></span>)
                                                            </button>
                                                            <?php else: ?>
                                                            <span class="text-xs text-slate-400 flex items-center">
                                                                <i data-lucide="thumbs-up" class="w-3 h-3 mr-1"></i>
                                                                Helpful (<span class="helpful-count"><?= $reply['helpful_count'] ?></span>)
                                                            </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <div class="text-center py-8 text-slate-500">
                            <i data-lucide="message-circle" class="w-12 h-12 mx-auto mb-3 opacity-50"></i>
                            <p>No comments yet. Be the first to share your thoughts!</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
            
            <!-- Sidebar -->
            <aside class="w-full lg:w-1/3">
                <!-- Download Section -->
                <div class="bg-white p-6 rounded-lg shadow-sm border borderColor mb-8 sticky top-24 z-10">
                    <h2 class="text-xl font-bold text-dark mb-4 flex items-center">
                        <i data-lucide="download" class="w-5 h-5 mr-2"></i>
                        Download Resources
                    </h2>
                    
                    <?php if (!empty($resource['attachments'])): ?>
                        <!-- Main Document -->
                        <?php foreach($resource['attachments'] as $attachment): ?>
                            <div class="document-card bg-white p-4 rounded-lg border borderColor mb-4">
                                <div class="flex items-start space-x-4">
                                    <div class="bg-primaryShades-100 p-3 rounded-lg">
                                        <i data-lucide="file-text" class="w-6 h-6 text-primary"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold mb-1"><?= esc($attachment->original_name) ?></h3>
                                        <p class="text-sm text-slate-500 mb-2">
                                            <?= esc($attachment->file_type ?? 'Document') ?>
                                            <?php if (!empty($attachment->file_size)): ?>
                                                <?= esc(format_file_size($attachment->file_size)) ?>
                                            <?php endif; ?>
                                        </p>
                                        <a href="<?= base_url('ksp/attachments/download/' . $attachment->file_path) ?>" 
                                        class="text-sm font-medium text-secondary flex items-center hover:text-primaryShades-700">
                                            <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                                            Download Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-slate-500">
                            <i data-lucide="file-x" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                            <p class="text-sm">No downloadable resources available</p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Download Statistics -->
                    <?php if (isset($resource)): ?>
                        <div class="bg-primaryShades-50 p-4 rounded-lg">
                            <h4 class="font-medium text-dark mb-2">Resource Statistics</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Total Downloads:</span>
                                    <span class="font-medium"><?= number_format($resource['total_downloads'] ?? 0) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Total Views:</span>
                                    <span class="font-medium"><?= number_format($resource['view_count'] ?? 0) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Helpful Votes:</span>
                                    <span class="font-medium"><?= number_format($resource['helpful_count'] ?? 0) ?></span>
                                </div>
                                <?php if (!empty($resource['publication_year'])): ?>
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Published:</span>
                                    <span class="font-medium"><?= esc($resource['publication_year']) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Mark as Helpful Button -->
                            <?php if (isset($isLoggedIn) && $isLoggedIn || CIAuth::isLoggedIn()): ?>
                                <div class="mt-4 pt-4 border-t border-slate-200">
                                    <button class="vote-helpful-btn w-full flex items-center justify-center px-4 py-2 bg-white border border-primary text-primary rounded-lg hover:bg-primaryShades-50 transition-colors duration-200 <?= isset($helpfulStatus['user_has_voted']) && $helpfulStatus['user_has_voted'] ? 'bg-primaryShades-50 text-primaryShades-700' : '' ?>" 
                                            data-resource-id="<?= esc($resource['id']) ?>"
                                            <?= isset($helpfulStatus['user_has_voted']) && $helpfulStatus['user_has_voted'] ? 'disabled title="You have already marked this as helpful"' : '' ?>>
                                        <i data-lucide="thumbs-up" class="w-4 h-4 mr-2"></i>
                                        <?= isset($helpfulStatus['user_has_voted']) && $helpfulStatus['user_has_voted'] ? 'Marked as Helpful' : 'Mark as Helpful' ?>
                                        <span class="helpful-count ml-2 text-sm">
                                            (<?= number_format($resource['helpful_count'] ?? 0) ?>)
                                        </span>
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="mt-4 pt-4 border-t border-slate-200">
                                    <a onclick="handleMarkHelpful(<?= esc($resource['id']) ?>, event)" href="#" class="w-full flex items-center justify-center px-4 py-2 bg-white border border-slate-300 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors duration-200">
                                        <i data-lucide="log-in" class="w-4 h-4 mr-2"></i>
                                        Login to Rate Helpful
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Related Articles -->
                <div class="bg-white p-6 rounded-lg shadow-sm border borderColor mb-8">
                    <h2 class="text-xl font-bold text-dark mb-4 flex items-center">
                        <i data-lucide="book-open" class="w-5 h-5 mr-2"></i>
                        Related Articles
                    </h2>
                    
                    <?php if (!empty($relatedResources)): ?>
                    <div class="space-y-4">
                        <?php foreach($relatedResources as $related): ?>
                            <!-- Related Article -->
                            <a href="<?= base_url('pillar-article/' . $related['slug']) ?>" 
                                class="related-article-card block bg-white p-4 rounded-lg border borderColor hover:border-secondary transition-colors">
                                <div class="w-full">
                                    <h3 class="font-bold mb-2 text-primary hover:text-secondary"><?= esc($related['title']) ?></h3>
                                    <?php if (!empty($related['description'])): ?>
                                    <p class="text-sm text-slate-600 mb-2 line-clamp-2"><?= esc(substr($related['description'], 0, 120)) ?>...</p>
                                    <?php endif; ?>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-slate-500"><?= esc($related['file_type'] ?? 'Document') ?></span>
                                        <?php if (!empty($related['publication_year'])): ?>
                                        <span class="text-xs text-slate-500"><?= esc($related['publication_year']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (isset($pillar)): ?>
                        <a href="<?= base_url('ksp/pillars/' . $pillar['slug']) ?>" 
                        class="mt-4 inline-flex items-center text-sm font-medium text-primary hover:text-primaryShades-700">
                            View all <?= esc($pillar['title'] ?? $pillar['name'] ?? 'Pillar') ?> articles
                            <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                        </a>
                    <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-slate-500">
                            <i data-lucide="book-open" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                            <p class="text-sm">No related articles found</p>
                            <?php if (isset($pillar)): ?>
                            <a href="<?= base_url('ksp/pillars/' . $pillar['slug']) ?>" 
                            class="mt-2 inline-block text-sm text-primary hover:text-primaryShades-700">
                                Browse <?= esc($pillar['title'] ?? $pillar['name'] ?? 'Pillar') ?> articles
                            </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Article Statistics -->
                <div class="bg-white p-6 rounded-lg shadow-sm border borderColor">
                    <h2 class="text-xl font-bold text-dark mb-4 flex items-center">
                        <i data-lucide="bar-chart-2" class="w-5 h-5 mr-2"></i>
                        Article Statistics
                    </h2>
                    
                    <?php if (isset($resource)): ?>
                    <div class="space-y-4">
                        <!-- Views Statistics -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium">Views</span>
                                <span class="text-sm font-medium"><?= number_format($resource['view_count'] ?? 0) ?></span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2">
                                <?php 
                                $maxViews = 2000; // Adjust this based on your typical max views
                                $viewPercent = min(100, (($resource['view_count'] ?? 0) / $maxViews) * 100);
                                ?>
                                <div class="bg-primary h-2 rounded-full" style="width: <?= $viewPercent ?>%"></div>
                            </div>
                        </div>
                        
                        <!-- Downloads Statistics -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium">Downloads</span>
                                <span class="text-sm font-medium"><?= number_format($resource['total_downloads'] ?? 0) ?></span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2">
                                <?php 
                                $maxDownloads = 500; // Adjust this based on your typical max downloads
                                $downloadPercent = min(100, (($resource['total_downloads'] ?? 0) / $maxDownloads) * 100);
                                ?>
                                <div class="bg-secondary h-2 rounded-full" style="width: <?= $downloadPercent ?>%"></div>
                            </div>
                        </div>
                        
                        <!-- File Information -->
                        <?php if (!empty($resource['file_size'])): ?>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium">File Size</span>
                                <span class="text-sm font-medium"><?= esc($resource['file_size']) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Publication Year -->
                        <?php if (!empty($resource['publication_year'])): ?>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium">Published</span>
                                <span class="text-sm font-medium"><?= esc($resource['publication_year']) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- File Type -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium">Format</span>
                                <span class="text-sm font-medium"><?= esc($resource['file_type'] ?? 'Document') ?></span>
                            </div>
                        </div>
                        
                        <!-- Category -->
                        <?php if (!empty($resource['category_name'])): ?>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium">Category</span>
                                <span class="text-sm font-medium"><?= esc($resource['category_name']) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Featured Status -->
                        <?php if (!empty($resource['is_featured']) && $resource['is_featured'] == 1): ?>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium">Status</span>
                                <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full">Featured Article</span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8 text-slate-500">
                        <i data-lucide="bar-chart-2" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                        <p class="text-sm">Statistics not available</p>
                    </div>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
        <?php endif; ?>
    </main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Initialize GSAP animations
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof gsap !== 'undefined') {
            gsap.registerPlugin(ScrollTrigger);
            
            // Animate article sections as they come into view
            gsap.utils.toArray(".article-content h2, .article-content h3, .article-content p, .article-content ul, .article-content ol, .article-content blockquote").forEach((element) => {
                gsap.from(element, {
                    scrollTrigger: {
                        trigger: element,
                        start: "top 80%",
                        toggleActions: "play none none none"
                    },
                    y: 30,
                    opacity: 0,
                    duration: 0.6,
                    ease: "power2.out"
                });
            });
            
            // Animate sidebar elements
            gsap.from(".document-card", {
                scrollTrigger: {
                    trigger: ".document-card",
                    start: "top 80%",
                    toggleActions: "play none none none"
                },
                y: 50,
                opacity: 0,
                duration: 0.6,
                stagger: 0.1,
                ease: "power2.out"
            });
            
            gsap.from(".related-article-card", {
                scrollTrigger: {
                    trigger: ".related-article-card",
                    start: "top 80%",
                    toggleActions: "play none none none"
                },
                y: 50,
                opacity: 0,
                duration: 0.6,
                stagger: 0.1,
                ease: "power2.out"
            });
        }
    });

    // Comment System JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        const commentForm = document.getElementById('commentForm');
        const commentContent = document.getElementById('commentContent');
        const charCount = document.getElementById('charCount');
        const submitButtonText = document.getElementById('submitButtonText');
        const cancelReplyBtn = document.getElementById('cancelReply');
        const parentIdInput = document.getElementById('parentId');
        
        // Character count
        if (commentContent && charCount) {
            commentContent.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });
        }
        
        // Reply button functionality
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('reply-btn')) {
                e.preventDefault();
                
                const commentId = e.target.getAttribute('data-comment-id');
                const comment = e.target.closest('.comment');
                const userName = comment.querySelector('.font-medium').textContent;
                
                // Set parent ID for reply
                parentIdInput.value = commentId;
                
                // Update form UI
                commentContent.placeholder = `Reply to ${userName}...`;
                submitButtonText.textContent = 'Post Reply';
                cancelReplyBtn.classList.remove('hidden');
                
                // Focus on textarea
                commentContent.focus();
                
                // Scroll to form
                commentForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
        
        // Cancel reply
        if (cancelReplyBtn) {
            cancelReplyBtn.addEventListener('click', function() {
                parentIdInput.value = '';
                commentContent.placeholder = 'Add your comment...';
                submitButtonText.textContent = 'Post Comment';
                cancelReplyBtn.classList.add('hidden');
            });
        }
        
        // Submit comment form
        if (commentForm) {
            commentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData();
                formData.append('resource_id', document.getElementById('resourceId').value);
                formData.append('content', commentContent.value.trim());
                
                if (parentIdInput.value) {
                    formData.append('parent_id', parentIdInput.value);
                }
                
                if (!commentContent.value.trim()) {
                    showNotification('error', 'Please enter a comment.');
                    return;
                }
                
                // Disable submit button
                const submitBtn = commentForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin mr-2"></i>Posting...';
                
                fetch('<?= base_url('ksp/api/comment/add') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reset form
                        commentContent.value = '';
                        charCount.textContent = '0';
                        parentIdInput.value = '';
                        commentContent.placeholder = 'Add your comment...';
                        submitButtonText.textContent = 'Post Comment';
                        cancelReplyBtn.classList.add('hidden');
                        
                        // Show success message
                        showNotification('Comment posted successfully!', 'success');
                        
                        // Reload page to show new comment
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showNotification(data.message || 'Failed to post comment.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while posting your comment.', 'error');
                })
                .finally(() => {
                    // Re-enable submit button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    
                    // Re-initialize Lucide icons
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                });
            });
        }
        
        // Vote helpful functionality
        document.addEventListener('click', function(e) {
            if (e.target.closest('.vote-helpful-btn') && !e.target.closest('.vote-helpful-btn').disabled) {
                e.preventDefault();
                
                const btn = e.target.closest('.vote-helpful-btn');
                const commentId = btn.getAttribute('data-comment-id');
                const isResource = btn.hasAttribute('data-resource-id');
                const helpfulCountSpan = btn.querySelector('.helpful-count');
                
                // Disable button
                btn.disabled = true;
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i data-lucide="loader-2" class="w-3 h-3 animate-spin mr-1"></i>Voting...';
                
                const url = isResource ? '<?= base_url('ksp/api/vote/resource-helpful') ?>' : '<?= base_url('ksp/api/vote/comment-helpful') ?>';
                const formData = new FormData();
                
                if (isResource) {
                    formData.append('resource_id', btn.getAttribute('data-resource-id'));
                } else {
                    formData.append('comment_id', commentId);
                }
                
                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update count
                        if (helpfulCountSpan) {
                            helpfulCountSpan.textContent = data.helpful_count;
                        }
                        
                        // Update button state
                        btn.classList.add('text-primary', 'bg-primaryShades-50', 'text-primaryShades-700');
                        btn.disabled = true;
                        btn.title = isResource ? 'You have already marked this as helpful' : 'You have already voted on this comment';
                        
                        if (isResource) {
                            btn.innerHTML = '<i data-lucide="thumbs-up" class="w-4 h-4 mr-2"></i>Marked as Helpful <span class="helpful-count ml-2 text-sm">(' + data.helpful_count + ')</span>';
                        } else {
                            btn.innerHTML = '<i data-lucide="thumbs-up" class="w-3 h-3 mr-1"></i>Helpful (<span class="helpful-count">' + data.helpful_count + '</span>)';
                        }
                        
                        showNotification('Thank you for your feedback!', 'success');
                        
                        // Re-initialize Lucide icons
                        if (typeof lucide !== 'undefined') {
                            lucide.createIcons();
                        }
                    } else {
                        // Re-enable button on error
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                        showNotification(data.message || 'Failed to vote.', 'error');
                        
                        // Re-initialize Lucide icons
                        if (typeof lucide !== 'undefined') {
                            lucide.createIcons();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    showNotification('An error occurred while voting.', 'error');
                    
                    // Re-initialize Lucide icons
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                });
            }
        });
    });
</script>
<?= $this->endSection() ?>