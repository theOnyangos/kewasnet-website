<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);

    // dd($discussion);
?>

<?= $this->extend('frontendV2/ksp/layouts/main') ?>

<?= $this->section('title'); ?><?= esc($discussion->title) ?> - Discussion<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<div class="bg-light relative">
    <!-- Page Header -->
    <section class="relative py-16 bg-gradient-to-r from-primary to-secondary">
        <div class="container mx-auto px-4 text-center text-white">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold mb-4"><?= esc($discussion->title) ?></h1>
                <p class="text-xl md:text-2xl leading-relaxed">
                    <?= !empty($discussion->excerpt) ? esc($discussion->excerpt) : 'Join the conversation and share your thoughts.' ?>
                </p>
            </div>
        </div>
    </section>

        <!-- Breadcrumb -->
    <div class="sticky top-[110px] z-40 bg-white border-b borderColor shadow-sm">
        <div class="container mx-auto px-4 py-3">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-1 sm:gap-2 md:space-x-1 md:space-x-3 text-xs sm:text-sm">
                    <li class="inline-flex items-center flex-shrink-0">
                        <a href="<?= base_url('ksp') ?>" class="inline-flex items-center font-medium text-slate-600 hover:text-primary whitespace-nowrap">
                            <i data-lucide="home" class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2"></i>
                            <span>Home</span>
                        </a>
                    </li>
                    <li class="flex-shrink-0">
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="w-3 h-3 sm:w-4 sm:h-4 text-slate-400"></i>
                            <a href="<?= base_url('ksp/networking-corner') ?>" class="ml-1 cursor-pointer font-medium text-slate-600 hover:text-primary md:ml-2 whitespace-nowrap">Networking Corner</a>
                        </div>
                    </li>
                    <li class="min-w-0 flex-1 sm:flex-initial flex-shrink-0">
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="w-3 h-3 sm:w-4 sm:h-4 text-slate-400 flex-shrink-0"></i>
                            <a href="<?= base_url('ksp/networking-corner-forum-discussion/' . $forumSlug) ?>" class="ml-1 cursor-pointer font-medium text-slate-600 hover:text-primary md:ml-2 truncate block" title="<?= esc($forumName) ?>"><?= esc($forumName) ?></a>
                        </div>
                    </li>
                    <li aria-current="page" class="min-w-0 flex-1 sm:flex-initial flex-shrink-0">
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="w-3 h-3 sm:w-4 sm:h-4 text-slate-400 flex-shrink-0"></i>
                            <span class="ml-1 font-medium text-primary cursor-pointer md:ml-2 truncate block" title="<?= esc($discussion->title) ?>"><?= esc($discussion->title) ?></span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="container mx-auto px-4 pt-4">
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                <div class="flex items-center">
                    <i data-lucide="alert-circle" class="w-5 h-5 mr-2"></i>
                    <p><?= session()->getFlashdata('error') ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('success')): ?>
        <div class="container mx-auto px-4 pt-4">
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
                <div class="flex items-center">
                    <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                    <p><?= session()->getFlashdata('success') ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Discussion Content -->
            <div class="lg:col-span-2">
        <!-- Discussion Header -->
        <div class="bg-white rounded-lg shadow-sm border borderColor mb-6">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <?php if ($author): ?>
                        <div class="flex items-center flex-1">
                            <div class="w-12 h-12 rounded-full mr-4 bg-secondary flex items-center justify-center text-white">
                                <i data-lucide="messages-square" class="w-5 h-5"></i>
                            </div>
                            <div class="flex-1">
                                <h2 class="text-2xl md:text-3xl font-bold text-primary"><?= esc($discussion->title) ?></h2>
                                <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
                                    <span>Posted by <?= esc($author->first_name . ' ' . $author->last_name) ?></span>
                                    <span>•</span>
                                    <span><?= date('M j, Y \a\t g:i a', strtotime($discussion->created_at)) ?></span>
                                    <span>•</span>
                                    <span class="flex items-center">
                                        <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                                        <?= number_format($discussion->view_count) ?> views
                                    </span>
                                    <?php if ($userId && $discussion->user_id != $userId && !$isBlocked): ?>
                                    <span>•</span>
                                    <button onclick='openReportModal("<?= $discussion->user_id ?>", "discussion", "<?= $discussion->id ?>", "<?= esc($author->first_name . ' ' . $author->last_name) ?>")' 
                                            class="text-slate-500 hover:text-red-600 text-sm flex items-center">
                                        <i data-lucide="flag" class="w-4 h-4 mr-1"></i>
                                        Report
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full mr-4 bg-secondary flex items-center justify-center text-white">
                                <i data-lucide="messages-square" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl md:text-3xl font-bold text-primary"><?= esc($discussion->title) ?></h1>
                                <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
                                    <span>Posted by Anonymous</span>
                                    <span>•</span>
                                    <span><?= date('M j, Y \a\t g:i a', strtotime($discussion->created_at)) ?></span>
                                    <span>•</span>
                                    <span class="flex items-center">
                                        <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                                        <?= number_format($discussion->view_count) ?> views
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Lock Status Badge -->
                    <?php if ($discussion->is_locked): ?>
                    <div class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium flex items-center">
                        <i data-lucide="lock" class="w-4 h-4 mr-1"></i>
                        Locked
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Discussion Tags -->
                <div class="flex flex-wrap gap-2 mb-4">
                    <?php 
                    $tags = json_decode($discussion->tags, true) ?? [];
                    if (!empty($tags)): ?>
                        <?php foreach ($tags as $tag): ?>
                            <span class="bg-primaryShades-100 text-primary text-xs px-3 py-1 rounded-full">
                                #<?= esc(trim($tag, '"')) ?>
                            </span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Discussion Content -->
                <div class="prose max-w-none text-slate-700 mb-6">
                    <?= $discussion->content ?>
                </div>

                <!-- File Attachments -->
                <div class="mt-4">
                    <?php if (!empty($discussion->attachments)): ?>
                        <div class="my-4">
                            <h5 class="text-sm font-medium text-slate-700 mb-2">Attachments:</h5>
                            <div class="space-y-2">
                                <?php foreach ($discussion->attachments as $attachment): ?>
                                <div class="flex items-center justify-between bg-gray-50 p-2 rounded-lg">
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="file-text" class="w-4 h-4 text-gray-500"></i>
                                        <span class="text-sm text-gray-700"><?= esc($attachment['original_name']) ?></span>
                                        <span class="text-xs text-gray-500">(<?= format_file_size($attachment['file_size']) ?>)</span>
                                    </div>
                                    <?php if ($isMember): ?>
                                    <div class="flex items-center gap-2">
                                        <a href="<?= $attachment['download_url'] ?>" 
                                        class="text-secondary hover:text-secondaryShades-600 text-sm"
                                        download="<?= esc($attachment['original_name']) ?>">
                                            <i data-lucide="download" class="w-4 h-4"></i>
                                        </a>

                                        <!-- View File -->
                                        <a href="<?= $attachment['view_url'] ?>" target="_blank"
                                            class="text-secondary hover:text-secondaryShades-600 text-sm">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                    <?php else: ?>
                                    <span class="text-xs text-gray-500 italic">Members only</span>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Discussion Stats -->
                <div class="flex flex-wrap justify-between items-center pt-4 border-t borderColor">
                    <div class="flex items-center space-x-4">
                        <?php if ($userId && $isMember && !$isBlocked): ?>
                        <button onclick="likeDiscussion('<?= $discussion->id ?>')" 
                                class="like-discussion-btn flex items-center text-slate-600 hover:text-primary transition-colors">
                            <i data-lucide="thumbs-up" class="w-5 h-5 mr-1"></i>
                            <span class="discussion-like-count"><?= number_format($discussion->like_count) ?></span>
                        </button>
                        <?php else: ?>
                        <span class="flex items-center text-slate-400">
                            <i data-lucide="thumbs-up" class="w-5 h-5 mr-1"></i>
                            <span class="discussion-like-count"><?= number_format($discussion->like_count) ?></span>
                        </span>
                        <?php endif; ?>
                        <span class="flex items-center text-slate-600">
                            <i data-lucide="message-square" class="w-5 h-5 mr-1"></i>
                            <span><?= number_format($discussion->reply_count) ?> replies</span>
                        </span>
                        <?php if ($userId && $isMember && !$isBlocked): ?>
                        <button onclick="toggleBookmark('<?= $discussion->id ?>')" 
                                id="bookmark-btn" 
                                class="flex items-center text-slate-600 hover:text-secondary transition-colors">
                            <i data-lucide="<?= $isBookmarked ? 'bookmark' : 'bookmark' ?>" 
                               class="w-5 h-5 mr-1 <?= $isBookmarked ? 'fill-secondary text-secondary' : '' ?>"></i>
                            <span id="bookmark-text"><?= $isBookmarked ? 'Bookmarked' : 'Bookmark' ?></span>
                        </button>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Last Reply Info -->
                    <?php if ($discussion->last_reply_at): ?>
                    <div class="text-sm text-slate-500">
                        Last reply <?= date('M j, Y', strtotime($discussion->last_reply_at)) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Replies Section -->
        <div class="bg-white rounded-lg shadow-sm border borderColor mb-6">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold">Replies (<?= number_format($discussion->reply_count) ?>)</h2>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-slate-500">Sort by:</span>
                        <select class="select2 bg-white border border-gray-300 rounded-md px-3 py-1 text-sm">
                            <option>Newest first</option>
                            <option>Oldest first</option>
                            <option>Most liked</option>
                        </select>
                    </div>
                </div>

                <!-- Replies List -->
                <div class="space-y-6" id="replies-container">
                    <?php if (!empty($replies)): ?>
                        <?php foreach ($replies as $reply): 
                                $nestedReplies = $nestedReplies[$reply->id] ?? [];
                                $isLocked = $isLocked ?? false;
                                $userId = $userId ?? null;

                                // echo json_encode($reply, JSON_PRETTY_PRINT);
                            ?>
                            
                            <div class="reply-card bg-white rounded-lg border borderColor p-6">
                                <div class="flex items-start mb-4">
                                    <?php if($reply->picture): ?>
                                        <img src="<?= esc($reply->picture) ?>" 
                                        alt="User" class="w-10 h-10 rounded-full mr-4">
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-full mr-4 bg-primary flex items-center justify-center text-white">
                                            <i data-lucide="user-circle" class="w-5 h-5"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-bold"><?= esc($reply->first_name . ' ' . $reply->last_name) ?></h4>
                                                <div class="text-sm text-slate-500">@<?= esc($reply->username) ?></div>
                                                <div class="flex items-center text-sm text-slate-500">
                                                    <span><?= date('M j, Y \a\t g:i a', strtotime($reply->created_at)) ?></span>
                                                    <?php if ($reply->is_best_answer): ?>
                                                    <span class="ml-3 bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium flex items-center">
                                                        <i data-lucide="award" class="w-3 h-3 mr-1"></i>
                                                        Best Answer
                                                    </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php if ($userId && $reply->user_id != $userId): ?>
                                            <div class="flex items-center space-x-2">
                                                <?php if (!$isLocked && !$isBlocked): ?>
                                                <button onclick='replyToComment("<?= $reply->id ?>", <?= json_encode($reply->username) ?>)' 
                                                        class="text-slate-500 hover:text-primary text-sm flex items-center">
                                                    <i data-lucide="reply" class="w-4 h-4 mr-1"></i>
                                                    Reply
                                                </button>
                                                <?php endif; ?>
                                                <?php if (!$isBlocked): ?>
                                                <button onclick='openReportModal("<?= $reply->user_id ?>", "reply", "<?= $reply->id ?>", "<?= esc($reply->first_name . ' ' . $reply->last_name) ?>")' 
                                                        class="text-slate-500 hover:text-red-600 text-sm flex items-center">
                                                    <i data-lucide="flag" class="w-4 h-4 mr-1"></i>
                                                    Report
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                            <?php elseif (!$isLocked && !$isBlocked && $userId && $reply->user_id == $userId): ?>
                                            <button onclick='replyToComment("<?= $reply->id ?>", <?= json_encode($reply->username) ?>)' 
                                                    class="text-slate-500 hover:text-primary text-sm flex items-center">
                                                <i data-lucide="reply" class="w-4 h-4 mr-1"></i>
                                                Reply
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="prose max-w-none text-slate-700 mb-4">
                                    <?= esc($reply->content) ?>
                                </div>

                                <!-- In reply partial view -->
                                <?php if (!empty($reply->attachments)): ?>
                                    <div class="mt-4">
                                        <h5 class="text-sm font-medium text-slate-700 mb-2">Attachments:</h5>
                                        <div class="space-y-2">
                                            <?php foreach ($reply->attachments as $attachment): ?>
                                                <div class="flex items-center justify-between bg-gray-50 p-2 rounded-lg">
                                                    <div class="flex items-center space-x-2">
                                                        <?php if ($attachment['is_image']): ?>
                                                            <i data-lucide="image" class="w-4 h-4 text-blue-500"></i>
                                                        <?php else: ?>
                                                            <i data-lucide="file-text" class="w-4 h-4 text-gray-500"></i>
                                                        <?php endif; ?>
                                                        <span class="text-sm text-gray-700"><?= esc($attachment['original_name']) ?></span>
                                                        <span class="text-xs text-gray-500">(<?= format_file_size($attachment['file_size']) ?>)</span>
                                                    </div>
                                                    <?php if ($isMember): ?>
                                                    <div class="flex items-center gap-2">
                                                        <a href="<?= $attachment['download_url'] ?>" 
                                                        class="text-secondary hover:text-secondaryShades-600 text-sm"
                                                        download="<?= esc($attachment['original_name']) ?>">
                                                            <i data-lucide="download" class="w-4 h-4"></i>
                                                        </a>

                                                        <!-- View File -->
                                                        <a href="<?= $attachment['view_url'] ?>" target="_blank"
                                                            class="text-secondary hover:text-secondaryShades-600 text-sm">
                                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                                        </a>
                                                    </div>
                                                    <?php else: ?>
                                                    <span class="text-xs text-gray-500 italic">Members only</span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="flex items-center justify-between pt-3 border-t borderColor">
                                    <div class="flex items-center space-x-4">
                                        <?php if ($userId && $isMember && !$isBlocked): ?>
                                        <button onclick="likeReply('<?= $reply->id ?>')" 
                                                class="like-btn flex items-center text-slate-600 hover:text-primary text-sm transition-colors">
                                            <i data-lucide="thumbs-up" class="w-4 h-4 mr-1"></i>
                                            <span class="like-count"><?= number_format($reply->like_count) ?></span>
                                        </button>
                                        <?php else: ?>
                                        <span class="flex items-center text-slate-400 text-sm">
                                            <i data-lucide="thumbs-up" class="w-4 h-4 mr-1"></i>
                                            <span class="like-count"><?= number_format($reply->like_count) ?></span>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($userId === $discussion->user_id && !$reply->is_best_answer && !$isBlocked): ?>
                                    <button onclick="markBestAnswer('<?= $reply->id ?>')" 
                                            class="flex items-center px-3 py-1.5 bg-green-50 text-green-700 hover:bg-green-100 hover:text-green-800 text-sm font-medium rounded-lg transition-all duration-200 border border-green-200">
                                        <i data-lucide="award" class="w-4 h-4 mr-1.5"></i>
                                        Mark as Best Answer
                                    </button>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Nested Replies -->
                                <?php if (!empty($nestedReplies)): ?>
                                <div class="mt-4 pl-8 border-l-2 borderColor space-y-4">
                                    <?php foreach ($nestedReplies as $nestedReply): ?>
                                        <div class="bg-slate-50 rounded-lg p-4">
                                            <div class="flex items-start mb-3">
                                                <?php if($nestedReply->picture): ?>
                                                    <img src="<?= esc($nestedReply->picture) ?>" 
                                                    alt="User" class="w-8 h-8 rounded-full mr-3">
                                                <?php else: ?>
                                                    <div class="w-8 h-8 rounded-full mr-3 bg-primary flex items-center justify-center text-white text-xs">
                                                        <i data-lucide="user-circle" class="w-4 h-4"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="flex-1">
                                                    <div class="flex justify-between items-start">
                                                        <div>
                                                            <h5 class="font-semibold text-sm"><?= esc($nestedReply->first_name . ' ' . $nestedReply->last_name) ?></h5>
                                                            <div class="text-xs text-slate-500">@<?= esc($nestedReply->username) ?></div>
                                                            <div class="text-xs text-slate-500">
                                                                <?= date('M j, Y \a\t g:i a', strtotime($nestedReply->created_at)) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="text-slate-700 text-sm mb-3">
                                                <?= esc($nestedReply->content) ?>
                                            </div>

                                            <?php if (!empty($nestedReply->attachments)): ?>
                                                <div class="mt-3">
                                                    <h6 class="text-xs font-medium text-slate-700 mb-2">Attachments:</h6>
                                                    <div class="space-y-2">
                                                        <?php foreach ($nestedReply->attachments as $attachment): ?>
                                                            <div class="flex items-center justify-between bg-white p-2 rounded">
                                                                <div class="flex items-center space-x-2">
                                                                    <?php if ($attachment['is_image']): ?>
                                                                        <i data-lucide="image" class="w-3 h-3 text-blue-500"></i>
                                                                    <?php else: ?>
                                                                        <i data-lucide="file-text" class="w-3 h-3 text-gray-500"></i>
                                                                    <?php endif; ?>
                                                                    <span class="text-xs text-gray-700"><?= esc($attachment['original_name']) ?></span>
                                                                    <span class="text-xs text-gray-500">(<?= format_file_size($attachment['file_size']) ?>)</span>
                                                                </div>
                                                                <?php if ($isMember): ?>
                                                                <div class="flex items-center gap-2">
                                                                    <a href="<?= $attachment['download_url'] ?>" 
                                                                    class="text-secondary hover:text-secondaryShades-600"
                                                                    download="<?= esc($attachment['original_name']) ?>">
                                                                        <i data-lucide="download" class="w-3 h-3"></i>
                                                                    </a>
                                                                    <a href="<?= $attachment['view_url'] ?>" target="_blank"
                                                                        class="text-secondary hover:text-secondaryShades-600">
                                                                        <i data-lucide="eye" class="w-3 h-3"></i>
                                                                    </a>
                                                                </div>
                                                                <?php else: ?>
                                                                <span class="text-xs text-gray-500 italic">Members only</span>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="flex items-center pt-2 border-t border-slate-200 mt-3">
                                                <?php if ($userId && $isMember && !$isBlocked): ?>
                                                <button onclick="likeReply('<?= $nestedReply->id ?>')" 
                                                        class="like-btn flex items-center text-slate-600 hover:text-primary text-xs transition-colors">
                                                    <i data-lucide="thumbs-up" class="w-3 h-3 mr-1"></i>
                                                    <span class="like-count"><?= number_format($nestedReply->like_count) ?></span>
                                                </button>
                                                <?php else: ?>
                                                <span class="flex items-center text-slate-400 text-xs">
                                                    <i data-lucide="thumbs-up" class="w-3 h-3 mr-1"></i>
                                                    <span class="like-count"><?= number_format($nestedReply->like_count) ?></span>
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-slate-500">
                            <i data-lucide="message-square" class="w-12 h-12 mx-auto mb-4 text-slate-300"></i>
                            <p>No replies yet. Be the first to reply!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Reply Form (only show if not locked, user is logged in, and user is a member) -->
        <?php if (!$isLocked && $userId && $isMember && !$isBlocked): ?>
            <div class="bg-white rounded-lg shadow-sm border borderColor p-6" id="reply-section">
                <h3 class="text-lg font-bold mb-4">Post your reply</h3>
                <form id="reply-form" enctype="multipart/form-data">
                    <input type="hidden" name="discussion_id" value="<?= $discussion->id ?>">
                    <input type="hidden" name="parent_id" value="" id="reply-parent-id">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    
                    <div class="mb-4 relative">
                        <!-- Highlight backdrop -->
                        <div id="mention-backdrop" class="mention-backdrop absolute inset-0 px-4 py-3 border border-transparent rounded-lg pointer-events-none whitespace-pre-wrap break-words overflow-hidden" style="color: transparent;"></div>
                        
                        <textarea 
                            name="content" 
                            id="reply-content" 
                            rows="4" 
                            class="message-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary relative bg-transparent z-10"
                            placeholder="Share your thoughts..." 
                            required></textarea>
                    </div>
                    
                    <!-- File Attachment Section -->
                    <div class="mb-4">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-primary transition-colors" id="attachment-dropzone">
                            <input type="file" id="attachment-input" name="attachments[]" multiple class="hidden" accept=".pdf,.doc,.docx,.txt,.xls,.xlsx,.ppt,.pptx,.csv">
                            <i data-lucide="upload" class="w-8 h-8 mx-auto text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-600">Drag & drop documents or <span class="text-primary">click to browse</span></p>
                            <p class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX, TXT, XLS, XLSX, PPT, PPTX, CSV (Max 5MB each)</p>
                        </div>
                        
                        <!-- File Preview Container -->
                        <div id="file-previews" class="mt-3 space-y-2 hidden"></div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-2">
                            <button type="button" class="flex items-center text-slate-500 hover:text-primary hover:bg-gray-100 rounded p-2" onclick="document.getElementById('attachment-input').click()">
                                <i data-lucide="paperclip" class="w-5 h-5"></i>
                                <span class="text-sm ml-1">Attach</span>
                            </button>
                            <span id="file-count" class="text-xs text-slate-500 hidden">0 files</span>
                        </div>
                        <button type="submit" class="gradient-btn px-6 py-2 rounded-md text-white font-medium flex items-center">
                            <i data-lucide="send" class="w-4 h-4 mr-2 z-10"></i>
                            <span>Post Reply</span>
                        </button>
                    </div>
                </form>
            </div>
        <?php elseif ($isBlocked): ?>
            <div class="bg-white rounded-lg shadow-sm border borderColor overflow-hidden">
                <div class="bg-red-50 border-b border-red-200 p-8 text-center">
                    <div class="bg-red-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="ban" class="w-10 h-10 text-red-500"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-red-800 mb-3">Access Blocked</h3>
                    <p class="text-red-600 text-lg">You have been blocked from participating in this forum. You can view discussions but cannot post replies or interact.</p>
                    <p class="text-red-500 text-sm mt-4">If you believe this is a mistake, please contact the forum moderators.</p>
                </div>
            </div>
        <?php elseif ($isLocked): ?>
            <div class="bg-white rounded-lg shadow-sm border borderColor overflow-hidden">
                <div class="bg-red-50 border-b border-red-200 p-8 text-center">
                    <div class="bg-red-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="lock" class="w-10 h-10 text-red-500"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-red-800 mb-3">Discussion Locked</h3>
                    <p class="text-red-600 text-lg">This discussion has been locked by moderators. No new replies can be posted.</p>
                </div>
            </div>
        <?php elseif (!$userId): ?>
            <div class="bg-white rounded-lg shadow-sm border borderColor overflow-hidden">
                <div class="bg-blue-50 border-b border-blue-200 p-8 text-center">
                    <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="log-in" class="w-10 h-10 text-blue-500"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-blue-800 mb-3">Login Required</h3>
                    <p class="text-blue-600 text-lg mb-6">You must be logged in to participate in this discussion.</p>
                    <a href="<?= base_url('ksp/login') ?>" class="gradient-btn px-8 py-3 rounded-lg text-white font-medium inline-flex items-center text-lg hover:opacity-90 transition-opacity">
                        <i data-lucide="log-in" class="w-5 h-5 mr-2"></i>
                        Login to Reply
                    </a>
                </div>
            </div>
        <?php elseif (!$isMember): ?>
            <div class="bg-white rounded-lg shadow-sm border borderColor overflow-hidden">
                <div class="bg-gradient-to-br from-orange-50 to-amber-50 border-b border-orange-200 p-8 text-center">
                    <div class="bg-gradient-to-br from-orange-100 to-amber-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="user-plus" class="w-10 h-10 text-orange-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-orange-900 mb-3">Forum Membership Required</h3>
                    <p class="text-orange-700 text-lg mb-2">You must be a member of this forum to post replies and participate in discussions.</p>
                    <p class="text-orange-600 text-sm mb-6">Join the forum to unlock full access and start contributing!</p>
                    <a href="<?= base_url('ksp/networking-corner-forum-discussion/' . $forumSlug) ?>" class="gradient-btn px-8 py-3 rounded-lg text-white font-medium inline-flex items-center text-lg hover:opacity-90 transition-opacity">
                        <i data-lucide="users" class="w-5 h-5 mr-2"></i>
                        Join Forum Now
                    </a>
                </div>
            </div>
        <?php endif; ?>
            </div>

            <!-- Right Column - Sidebar -->
            <div class="lg:col-span-1">
                <!-- Forum Moderators -->
                <?php 
                $moderatorEmails = [];
                if (!empty($moderators)): 
                ?>
                    <div class="bg-white rounded-lg shadow-sm border borderColor p-6 mb-6 sticky top-6">
                        <h3 class="text-lg font-bold mb-4 text-primary flex items-center">
                            <i data-lucide="shield-check" class="w-5 h-5 mr-2"></i>
                            Forum Moderators
                        </h3>
                        <div class="space-y-4">
                            <?php
                            foreach ($moderators as $moderator):
                                $moderatorEmails[] = $moderator->user_email;
                            ?>
                            <div class="flex items-start">
                                <?php if ($moderator->user_image): ?>
                                    <img src="<?= esc($moderator->user_image) ?>" alt="Moderator" class="w-10 h-10 rounded-full mr-3 flex-shrink-0">
                                <?php else: ?>
                                    <div class="w-10 h-10 rounded-full mr-3 bg-primaryShades-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-primary font-semibold"><?= esc(strtoupper(substr($moderator->user_name, 0, 1))) ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-grow min-w-0">
                                    <h4 class="font-medium text-sm text-slate-800 truncate"><?= esc($moderator->user_name) ?></h4>
                                    <p class="text-xs text-secondary truncate"><?= esc($moderator->moderator_title ?? 'Moderator') ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button 
                            type="button"
                            onclick="openModeratorContactModal()"
                            class="w-full mt-4 px-4 py-2 text-primary font-medium flex items-center justify-center border borderColor rounded-lg hover:bg-primaryShades-50 transition-colors">
                            <i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                            Contact Moderators
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <!-- Contact Moderators Modal -->
    <?php if (!empty($moderators)): ?>
    <div id="moderator-contact-modal" class="hidden fixed inset-0" style="z-index: 9999 !important;">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300" style="z-index: 1;"></div>
        
        <!-- Modal container -->
        <div class="fixed inset-0 flex items-center justify-center overflow-y-auto p-2 sm:p-4" style="z-index: 2; pointer-events: none;">
            <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-2xl max-h-[calc(100vh-2rem)] flex flex-col" style="pointer-events: auto;">
            <!-- Modal Header -->
            <div class="bg-white px-6 py-4 border-b border-gray-200 flex-shrink-0">
                <div class="flex justify-between items-start w-full">
                    <h3 class="text-2xl font-bold text-gray-900">Contact Forum Moderators</h3>
                    <button onclick="closeModeratorContactModal()" type="button" class="text-slate-400 hover:text-slate-500 p-1">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <form id="moderator-contact-form" class="flex-1 overflow-y-auto px-6 py-4">
                <div class="space-y-4 sm:space-y-6">
                    <div>
                        <label for="mod-subject" class="block text-sm font-medium text-slate-700 mb-1">Subject <span class="text-red-500">*</span></label>
                        <input 
                            type="text" 
                            id="mod-subject" 
                            name="subject"
                            required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" 
                            placeholder="Enter subject">
                        <p class="mt-1 text-xs text-slate-500">Brief summary of your message</p>
                    </div>
                    <div>
                        <label for="mod-message" class="block text-sm font-medium text-slate-700 mb-1">Message <span class="text-red-500">*</span></label>
                        <textarea 
                            id="mod-message" 
                            name="message"
                            required 
                            rows="6" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-h-[150px] transition-all duration-200 resize-none" 
                            placeholder="Type your message..."></textarea>
                        <p class="mt-1 text-xs text-slate-500">Your message will be sent to all forum moderators</p>
                    </div>
                    <input type="hidden" id="mod-emails" value='<?= json_encode($moderatorEmails) ?>'>
                </div>
                
                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button 
                        type="button" 
                        onclick="closeModeratorContactModal()"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-slate-600 hover:bg-slate-50 font-medium transition-colors">
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        class="gradient-btn px-6 py-3 rounded-lg text-white font-medium flex items-center">
                        <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                        Send Message
                    </button>
                </div>
            </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Initialize Lucide icons
lucide.createIcons();

// Store uploaded files
let uploadedFiles = [];
let currentReplyParentId = '';

// File attachment handling
document.getElementById('attachment-dropzone').addEventListener('click', function() {
    document.getElementById('attachment-input').click();
});

document.getElementById('attachment-input').addEventListener('change', handleFileSelect);
document.getElementById('attachment-dropzone').addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('border-primary', 'bg-blue-50');
});

document.getElementById('attachment-dropzone').addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.classList.remove('border-primary', 'bg-blue-50');
});

document.getElementById('attachment-dropzone').addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('border-primary', 'bg-blue-50');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        handleFiles(files);
    }
});

function handleFileSelect(e) {
    const files = e.target.files;
    handleFiles(files);
}

function handleFiles(files) {
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = [
        'application/pdf', 
        'application/msword', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain',
        'text/csv'
    ];

    for (let file of files) {
        if (file.size > maxSize) {
            showNotification('error', `File "${file.name}" exceeds 5MB limit`);
            continue;
        }
        
        if (!allowedTypes.includes(file.type)) {
            showNotification('error', `File type not supported: ${file.name}`);
            continue;
        }

        if (uploadedFiles.length >= 5) {
            showNotification('error', 'Maximum 5 files allowed');
            break;
        }

        uploadedFiles.push(file);
        createFilePreview(file);
    }
    
    updateFileCount();
    document.getElementById('attachment-input').value = '';
}

function createFilePreview(file) {
    const previewContainer = document.getElementById('file-previews');
    previewContainer.classList.remove('hidden');
    
    const previewDiv = document.createElement('div');
    previewDiv.className = 'flex items-center justify-between bg-gray-50 p-2 rounded-lg';
    previewDiv.innerHTML = `
        <div class="flex items-center space-x-2">
            <i data-lucide="${getFileIcon(file.type)}" class="w-4 h-4 text-gray-500"></i>
            <span class="text-sm text-gray-700 truncate max-w-xs">${file.name}</span>
            <span class="text-xs text-gray-500">(${formatFileSize(file.size)})</span>
        </div>
        <button type="button" onclick="removeFile('${file.name}')" class="text-red-500 hover:text-red-700">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    `;
    
    previewContainer.appendChild(previewDiv);
    lucide.createIcons();
}

function getFileIcon(type) {
    if (type === 'application/pdf') return 'file-text';
    if (type.includes('word') || type === 'text/plain') return 'file-text';
    if (type.includes('sheet') || type.includes('excel')) return 'file-spreadsheet';
    if (type.includes('presentation') || type.includes('powerpoint')) return 'file-presentation';
    if (type === 'text/csv') return 'file-spreadsheet';
    return 'file';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function removeFile(fileName) {
    uploadedFiles = uploadedFiles.filter(file => file.name !== fileName);
    updateFilePreviews();
    updateFileCount();
}

function updateFilePreviews() {
    const previewContainer = document.getElementById('file-previews');
    previewContainer.innerHTML = '';
    
    if (uploadedFiles.length === 0) {
        previewContainer.classList.add('hidden');
        return;
    }
    
    uploadedFiles.forEach(file => createFilePreview(file));
}

function updateFileCount() {
    const fileCount = document.getElementById('file-count');
    fileCount.textContent = `${uploadedFiles.length} file${uploadedFiles.length !== 1 ? 's' : ''}`;
    fileCount.classList.toggle('hidden', uploadedFiles.length === 0);
}

// Enhanced AJAX reply submission
$('#reply-form').on('submit', function(e) {
    e.preventDefault();
    
    if (!validateForm()) return;
    
    const formData = new FormData(this);
    
    // Append files to FormData
    uploadedFiles.forEach((file) => {
        formData.append('attachments[]', file);
    });
    
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    
    submitBtn.html('<i data-lucide="loader" class="w-4 h-4 animate-spin mr-2"></i> Posting...');
    submitBtn.prop('disabled', true);
    lucide.createIcons();
    
    $.ajax({
        url: '<?= base_url('ksp/discussion/add-reply') ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                showNotification('success', response.message);
                resetForm();
                if (response.reply_html) {
                    // Append new reply without reloading
                    $('#replies-container').append(response.reply_html);
                    lucide.createIcons();
                    // Scroll to new reply
                    $('html, body').animate({
                        scrollTop: $('#replies-container').children().last().offset().top - 100
                    }, 500);
                } else {
                    // Fallback: reload after 1 second
                    setTimeout(() => location.reload(), 1000);
                }
            } else {
                showNotification('error', response.message);
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
                lucide.createIcons();
            }
        },
        error: function(xhr) {
            const errorMsg = xhr.responseJSON?.message || 'An error occurred. Please try again.';
            showNotification('error', errorMsg);
            submitBtn.html(originalText);
            submitBtn.prop('disabled', false);
            lucide.createIcons();
        }
    });
});

function validateForm() {
    const content = $('#reply-content').val().trim();
    if (content.length < 10) {
        showNotification('error', 'Reply must be at least 10 characters long');
        return false;
    }
    return true;
}

function resetForm() {
    $('#reply-content').val('');
    $('#reply-parent-id').val('');
    uploadedFiles = [];
    updateFilePreviews();
    updateFileCount();
    
    const submitBtn = $('#reply-form').find('button[type="submit"]');
    submitBtn.html('<i data-lucide="send" class="w-4 h-4 mr-2"></i> Post Reply');
    submitBtn.prop('disabled', false);
    lucide.createIcons();
}

// Real-time character count
$('#reply-content').on('input', function() {
    const length = $(this).val().length;
    const minLength = 10;
    
    if (length > 0 && length < minLength) {
        $(this).addClass('border-yellow-400').removeClass('border-gray-300');
    } else {
        $(this).removeClass('border-yellow-400').addClass('border-gray-300');
    }
    
    // Highlight mentions
    highlightMentions();
});

// Auto-resize textarea
$('#reply-content').on('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
    
    // Sync backdrop height
    const backdrop = document.getElementById('mention-backdrop');
    if (backdrop) {
        backdrop.style.height = this.style.height;
    }
});

// Sync scroll position between textarea and backdrop
$('#reply-content').on('scroll', function() {
    const backdrop = document.getElementById('mention-backdrop');
    if (backdrop) {
        backdrop.scrollTop = this.scrollTop;
    }
});

// Keyboard shortcuts
$(document).on('keydown', function(e) {
    // Ctrl+Enter to submit
    if (e.ctrlKey && e.key === 'Enter') {
        $('#reply-form').submit();
    }
    
    // Escape to clear parent reply
    if (e.key === 'Escape' && $('#reply-parent-id').val()) {
        $('#reply-parent-id').val('');
        showNotification('info', 'Reply context cleared');
    }
});

// Like reply function
function likeReply(replyId) {
    const likeBtn = $(`button[onclick="likeReply('${replyId}')"]`);
    const icon = likeBtn.find('i[data-lucide]');
    const originalIcon = icon.attr('data-lucide') || 'thumbs-up';
    
    // Show loading state
    likeBtn.prop('disabled', true);
    if (icon.length) {
        icon.attr('data-lucide', 'loader').addClass('animate-spin');
        lucide.createIcons();
    }
    
    $.ajax({
        url: '<?= base_url('ksp/discussion/like-reply') ?>',
        type: 'POST',
        data: {
            reply_id: replyId,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.success) {
                showNotification('success', response.message);
                
                // Update like count visually
                const likeCountSpan = likeBtn.find('.like-count');
                const currentLikes = parseInt(likeCountSpan.text().replace(/,/g, '')) || 0;
                likeCountSpan.text(currentLikes + 1);
                
                // Change button style to indicate liked
                likeBtn.addClass('text-primary').removeClass('text-slate-600');
                
                // Restore icon
                if (icon.length) {
                    icon.attr('data-lucide', originalIcon).removeClass('animate-spin');
                    lucide.createIcons();
                }
            } else {
                showNotification('error', response.message);
                // Restore icon on error
                if (icon.length) {
                    icon.attr('data-lucide', originalIcon).removeClass('animate-spin');
                    lucide.createIcons();
                }
            }
        },
        error: function(xhr) {
            const errorMsg = xhr.responseJSON?.message || 'An error occurred. Please try again.';
            showNotification('error', errorMsg);
            // Restore icon on error
            if (icon.length) {
                icon.attr('data-lucide', originalIcon).removeClass('animate-spin');
                lucide.createIcons();
            }
        },
        complete: function() {
            // Re-enable button
            likeBtn.prop('disabled', false);
        }
    });
}

// Mark as best answer function
function markBestAnswer(replyId) {
    Swal.fire({
        title: 'Mark as Best Answer?',
        text: 'Are you sure you want to mark this as the best answer?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, mark it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const bestAnswerBtn = $(`button[onclick="markBestAnswer('${replyId}')"]`);
            const originalText = bestAnswerBtn.html();
            const icon = bestAnswerBtn.find('i');
            const originalIcon = icon.attr('data-lucide') || 'award';
            
            // Show loading state
            bestAnswerBtn.prop('disabled', true);
            if (icon.length) {
                icon.attr('data-lucide', 'loader').addClass('animate-spin');
                lucide.createIcons();
            }
            
            $.ajax({
                url: '<?= base_url('ksp/discussion/mark-best-answer') ?>',
                type: 'POST',
                data: {
                    reply_id: replyId,
                    discussion_id: <?= json_encode($discussion->id) ?>,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        showNotification('success', response.message);
                        // Reload to see the changes
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showNotification('error', response.message);
                        // Restore button on error
                        bestAnswerBtn.prop('disabled', false);
                        if (icon.length) {
                            icon.attr('data-lucide', originalIcon).removeClass('animate-spin');
                            lucide.createIcons();
                        }
                    }
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON?.message || 'An error occurred. Please try again.';
                    showNotification('error', errorMsg);
                    // Restore button on error
                    bestAnswerBtn.prop('disabled', false);
                    if (icon.length) {
                        icon.attr('data-lucide', originalIcon).removeClass('animate-spin');
                        lucide.createIcons();
                    }
                }
            });
        }
    });
}

// Handle nested replies
function replyToComment(replyId, userName) {
    $('#reply-parent-id').val(replyId);
    
    // Clear any existing content and focus
    $('#reply-content').val('').focus();
    
    // Add mention with proper formatting
    const mention = `@${userName} `;
    $('#reply-content').val(mention);
    
    // Set cursor position after the mention
    const textarea = document.getElementById('reply-content');
    textarea.setSelectionRange(mention.length, mention.length);
    
    // Scroll to reply form
    $('html, body').animate({
        scrollTop: $('#reply-section').offset().top - 100
    }, 500);
    
    // Show notification
    showNotification('success', `Replying to @${userName}`);
    
    // Highlight the form to indicate reply mode
    $('#reply-section').addClass('ring-2 ring-primary').delay(2000).queue(function() {
        $(this).removeClass('ring-2 ring-primary').dequeue();
    });
}

// Highlight @mentions in textarea
function highlightMentions() {
    const textarea = document.getElementById('reply-content');
    const backdrop = document.getElementById('mention-backdrop');
    if (!textarea || !backdrop) return;
    
    const value = textarea.value;
    
    // Replace @mentions with highlighted spans
    const highlightedText = value.replace(/@([\w]+)/g, '<span class="mention-highlight">@$1</span>');
    
    // Update backdrop with highlighted content
    backdrop.innerHTML = highlightedText || '&nbsp;';
    
    // Sync scroll position
    backdrop.scrollTop = textarea.scrollTop;
}

// Like discussion function
function likeDiscussion(discussionId) {
    const likeBtn = $(`button[onclick="likeDiscussion('${discussionId}')"]`);
    const icon = likeBtn.find('i[data-lucide]');
    const originalIcon = icon.attr('data-lucide') || 'thumbs-up';
    
    // Show loading state
    likeBtn.prop('disabled', true);
    if (icon.length) {
        icon.attr('data-lucide', 'loader').addClass('animate-spin');
        lucide.createIcons();
    }
    
    $.ajax({
        url: '<?= base_url('ksp/discussion/like-discussion') ?>',
        type: 'POST',
        data: {
            discussion_id: discussionId,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        },
        success: function(response) {
            // Check if response exists and success is explicitly true
            if (response && response.hasOwnProperty('success') && response.success === true) {
                // Show success message - always show the message from server
                const successMessage = (response.message && typeof response.message === 'string' && response.message.trim()) ? response.message : 'Discussion liked successfully!';
                showNotification('success', successMessage);
                
                // Update like count visually
                const likeCountSpan = likeBtn.find('.discussion-like-count');
                const currentLikes = parseInt(likeCountSpan.text().replace(/,/g, '')) || 0;
                likeCountSpan.text(currentLikes + 1);
                
                // Change button style to indicate liked
                likeBtn.addClass('text-primary').removeClass('text-slate-600');
                
                // Restore icon
                if (icon.length) {
                    icon.attr('data-lucide', originalIcon).removeClass('animate-spin');
                    lucide.createIcons();
                }
            } else {
                // Handle failure case - always show the message
                // This covers: success: false, success: undefined, or response is null/undefined
                let message = 'Failed to like discussion';
                if (response && response.message && typeof response.message === 'string' && response.message.trim()) {
                    message = response.message;
                }
                // Check if it's an "already liked" message or similar, use warning instead of error
                const notificationType = message.toLowerCase().includes('already') ? 'warning' : 'error';
                showNotification(notificationType, message);
                // Restore icon on error/warning
                if (icon.length) {
                    icon.attr('data-lucide', originalIcon).removeClass('animate-spin');
                    lucide.createIcons();
                }
            }
        },
        error: function(xhr) {
            const errorMsg = xhr.responseJSON?.message || 'An error occurred. Please try again.';
            showNotification('error', errorMsg);
            // Restore icon on error
            if (icon.length) {
                icon.attr('data-lucide', originalIcon).removeClass('animate-spin');
                lucide.createIcons();
            }
        },
        complete: function() {
            // Re-enable button
            likeBtn.prop('disabled', false);
        }
    });
}

function toggleBookmark(discussionId) {
    const bookmarkBtn = $('#bookmark-btn');
    const bookmarkIcon = $('#bookmark-icon');
    const bookmarkText = $('#bookmark-text');
    const originalIcon = bookmarkIcon.attr('data-lucide') || 'bookmark';
    
    // Show loading state
    bookmarkBtn.prop('disabled', true);
    if (bookmarkIcon.length) {
        bookmarkIcon.attr('data-lucide', 'loader').addClass('animate-spin');
        lucide.createIcons();
    }
    
    $.ajax({
        url: '<?= base_url('ksp/discussion/toggle-bookmark') ?>',
        type: 'POST',
        data: {
            discussion_id: discussionId,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.success) {
                showNotification('success', response.message);
                
                // Update bookmark state
                if (bookmarkIcon.length) {
                    bookmarkIcon.attr('data-lucide', 'bookmark').removeClass('animate-spin');
                    if (response.bookmarked) {
                        bookmarkIcon.addClass('fill-secondary text-secondary');
                        bookmarkText.text('Bookmarked');
                    } else {
                        bookmarkIcon.removeClass('fill-secondary text-secondary');
                        bookmarkText.text('Bookmark');
                    }
                }
                
                lucide.createIcons();
            } else {
                showNotification('error', response.message);
                // Restore icon on error
                if (bookmarkIcon.length) {
                    bookmarkIcon.attr('data-lucide', originalIcon).removeClass('animate-spin');
                    lucide.createIcons();
                }
            }
        },
        error: function(xhr) {
            const errorMsg = xhr.responseJSON?.message || 'An error occurred. Please try again.';
            showNotification('error', errorMsg);
            // Restore icon on error
            if (bookmarkIcon.length) {
                bookmarkIcon.attr('data-lucide', originalIcon).removeClass('animate-spin');
                lucide.createIcons();
            }
        },
        complete: function() {
            bookmarkBtn.prop('disabled', false);
        }
    });
}

// Initialize when document is ready
$(document).ready(function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Add click handler for like buttons
    $(document).on('click', '.like-btn', function(e) {
        e.preventDefault();
        const onclick = $(this).attr('onclick');
        if (onclick && onclick.includes('likeReply')) {
            const replyId = onclick.match(/'([^']+)'/)[1];
            likeReply(replyId);
        }
    });
    
    // Add click handler for discussion like button
    $(document).on('click', '.like-discussion-btn', function(e) {
        e.preventDefault();
        const onclick = $(this).attr('onclick');
        if (onclick && onclick.includes('likeDiscussion')) {
            const match = onclick.match(/likeDiscussion\((.+?)\)/);
            if (match) {
                const discussionId = match[1];
                likeDiscussion(discussionId);
            }
        }
    });
    
});

// Modal Functions - defined at global scope
window.openModeratorContactModal = function() {
    const modal = document.getElementById('moderator-contact-modal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
};

window.closeModeratorContactModal = function() {
    const modal = document.getElementById('moderator-contact-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        document.getElementById('moderator-contact-form').reset();
    }
};

// Handle form submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('moderator-contact-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const subject = document.getElementById('mod-subject').value;
            const message = document.getElementById('mod-message').value;
            const emailsInput = document.getElementById('mod-emails').value;
            
            if (!subject.trim() || !message.trim()) {
                showNotification('error', 'Please fill in all fields');
                return;
            }
            
            let moderatorEmails = [];
            try {
                moderatorEmails = JSON.parse(emailsInput);
            } catch (e) {
                showNotification('error', 'Error processing moderator emails');
                return;
            }
            
            // Disable submit button
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 inline mr-2 animate-spin"></i> Sending...';
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            
            // Send via fetch
            fetch('<?= base_url('ksp/discussion/contact-moderators') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    subject: subject,
                    message: message,
                    moderator_emails: moderatorEmails,
                    discussion_id: <?= json_encode($discussion->id) ?>,
                    discussion_title: <?= json_encode($discussion->title) ?>
                })
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
                
                if (data.success) {
                    showNotification('success', 'Your message has been sent to the moderators');
                    closeModeratorContactModal();
                } else {
                    showNotification('error', data.message || 'Failed to send message');
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
                showNotification('error', 'An error occurred while sending your message');
            });
        });
    }
});

// Report User Modal Functions
window.openReportModal = function(userId, contextType, contextId, userName) {
    const modal = document.getElementById('report-user-modal');
    document.getElementById('reported-user-id').value = userId;
    document.getElementById('context-type').value = contextType;
    document.getElementById('context-id').value = contextId;
    
    // Set the user name in the modal
    const userNameElement = document.getElementById('reported-user-name');
    if (userNameElement && userName) {
        userNameElement.textContent = userName;
    }
    
    // Clear previous messages and form
    document.getElementById('report-error').classList.add('hidden');
    document.getElementById('report-success').classList.add('hidden');
    document.getElementById('report-user-form').reset();
    document.getElementById('reported-user-id').value = userId;
    document.getElementById('context-type').value = contextType;
    document.getElementById('context-id').value = contextId;
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
};

window.closeReportModal = function() {
    const modal = document.getElementById('report-user-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
};

window.submitReport = function() {
    const form = document.getElementById('report-user-form');
    const formData = new FormData(form);
    
    const data = {
        reported_user_id: formData.get('reported_user_id'),
        reason: formData.get('reason'),
        description: formData.get('description'),
        context_type: formData.get('context_type'),
        context_id: formData.get('context_id')
    };
    
    // Validation
    if (!data.reason) {
        document.getElementById('report-error').textContent = 'Please select a reason for reporting';
        document.getElementById('report-error').classList.remove('hidden');
        return;
    }
    
    // Hide previous messages
    document.getElementById('report-error').classList.add('hidden');
    document.getElementById('report-success').classList.add('hidden');
    
    // Send report
    fetch('<?= base_url('ksp/discussion/report-user') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            document.getElementById('report-success').textContent = result.message;
            document.getElementById('report-success').classList.remove('hidden');
            
            // Close modal after 2 seconds
            setTimeout(() => {
                closeReportModal();
                showNotification('success', result.message);
            }, 2000);
        } else {
            document.getElementById('report-error').textContent = result.message || 'Failed to submit report';
            document.getElementById('report-error').classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('report-error').textContent = 'An error occurred while submitting your report';
        document.getElementById('report-error').classList.remove('hidden');
    });
};
</script>

<style>
    #attachment-dropzone.dragover {
        border-color: #3b82f6;
        background-color: #eff6ff;
    }

    .file-preview {
        transition: all 0.2s ease;
    }

    .file-preview:hover {
        background-color: #f8fafc;
    }

    #reply-content:focus {
        outline: none;
        ring: 2px;
        ring-color: #3b82f6;
    }

    .message-input:focus {
        outline: none !important;
        box-shadow: 0 0 0 1px #d1d5da !important;
        border-color: transparent !important;
    }

    .like-discussion-btn:hover {
        transform: scale(1.05);
        transition: transform 0.2s ease;
    }

    .like-btn:hover {
        transform: scale(1.05);
        transition: transform 0.2s ease;
    }

    #reply-section.ring-2 {
        transition: all 0.3s ease;
        border-radius: 0.5rem;
    }

    /* Mention highlight overlay */
    .mention-backdrop {
        font-family: inherit;
        font-size: inherit;
        line-height: inherit;
        letter-spacing: inherit;
        word-spacing: inherit;
        background: white;
        z-index: 1;
    }

    #reply-content {
        background: white;
    }

    /* Highlighted mention style */
    .mention-highlight {
        background: linear-gradient(120deg, #84cc16 0%, #65a30d 100%);
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(132, 204, 22, 0.2);
    }
</style>

<!-- Report User Modal -->
<div id="report-user-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-black/50 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeReportModal()"></div>

        <!-- Center modal -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <div class="sm:flex sm:items-center space-x-4">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i data-lucide="alert-circle" class="h-6 w-6 text-red-600"></i>
                            </div>

                            <div class="">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Report User
                                </h3>

                                <p class="text-sm text-gray-600">
                                    Reporting: <span id="reported-user-name" class="font-semibold text-gray-900"></span>
                                </p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <form id="report-user-form">
                                <input type="hidden" id="reported-user-id" name="reported_user_id">
                                <input type="hidden" id="context-type" name="context_type">
                                <input type="hidden" id="context-id" name="context_id">
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Reason for reporting <span class="text-red-500">*</span>
                                    </label>
                                    <select name="reason" id="report-reason" required class="select2 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                                        <option value="">Select a reason</option>
                                        <option value="Spam or misleading">Spam or misleading</option>
                                        <option value="Harassment or hate speech">Harassment or hate speech</option>
                                        <option value="Inappropriate content">Inappropriate content</option>
                                        <option value="Off-topic">Off-topic</option>
                                        <option value="False information">False information</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Additional details (optional)
                                    </label>
                                    <textarea name="description" id="report-description" rows="3" maxlength="500" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Provide more context about why you're reporting this user..."></textarea>
                                    <p class="text-xs text-gray-500 mt-1">Maximum 500 characters</p>
                                </div>
                                
                                <div id="report-error" class="hidden mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-md text-sm"></div>
                                <div id="report-success" class="hidden mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-md text-sm"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="submitReport()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Submit Report
                </button>
                <button type="button" onclick="closeReportModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>