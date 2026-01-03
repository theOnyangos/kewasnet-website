<?php 
    use Carbon\Carbon;
    use App\Libraries\CIAuth;
    use App\Helpers\UrlHelper;

    $currentUrl = new UrlHelper();

    // dd($forums);
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('frontendV2/ksp/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('description'); ?>
<?= $description ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <div class="bg-light relative">
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
                                <a href="<?= base_url('ksp/networking-corner') ?>" class="ml-1 text-sm cursor-pointer font-medium text-slate-600 hover:text-secondary md:ml-2">Networking Corner</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                                <a href="<?= base_url('ksp/networking-corner/forums') ?>" class="ml-1 text-sm cursor-pointer font-medium text-slate-600 hover:text-secondary md:ml-2">Forums</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                                <span class="ml-1 text-sm font-medium text-primary cursor-pointer md:ml-2"><?= esc($forum->name) ?></span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            <!-- Forum Header -->
            <div class="bg-white rounded-lg shadow-sm border borderColor mb-8">
                <div class="p-6 md:p-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <div class="flex items-center mb-4 md:mb-0 max-w-[80%]">
                            <div class="bg-secondaryShades-100 p-5 rounded-full mr-4">
                                <i data-lucide="<?= esc($forum->icon) ?>" class="w-6 h-6 text-secondary"></i>
                            </div>
                            <div class="max-w-[80%]">
                                <h1 class="text-2xl md:text-3xl font-bold text-primary"><?= esc($forum->name) ?></h1>
                                <p class="text-slate-600"><?= esc($forum->description) ?></p>
                            </div>
                        </div>

                        <!-- Show add discussion button is user is moderator -->
                        <?php if (((is_array($moderator) && isset($moderator['is_moderator']) && $moderator['is_moderator']) || (is_object($moderator) && isset($moderator->is_moderator) && $moderator->is_moderator) || CIAuth::isAdmin()) && (!$member['is_blocked'])): ?>
                            <a href="#" class="gradient-btn px-6 py-3 rounded-[50px] text-white font-medium flex items-center" id="newDiscussionBtn" data-modal-target="addDiscussionModal">
                                <i data-lucide="plus" class="w-5 h-5 mr-2 z-10"></i>
                                <span>New Discussion</span>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex justify-between items-center border-t borderColor pt-4">
                        <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500">
                            <span class="flex items-center">
                                <i data-lucide="message-square" class="w-4 h-4 mr-1"></i>
                                <?= esc($forum->discussion_count) ?> discussions
                            </span>
                            <span class="flex items-center">
                                <i data-lucide="users" class="w-4 h-4 mr-1"></i>
                                <?= esc($forum->member_count) ?> active members
                            </span>
                            <?php if ($member['joined_at']): ?>
                                <span>Joined: <?= Carbon::parse($member['joined_at'])->diffForHumans() ?></span>
                                <?php else: ?>
                                <span class="text-orange-500 bg-orange-50 py-2 px-8 rounded"> <i data-lucide="info" class="w-4 h-4 mr-1 inline-block"></i> You are not a member. Join using the button below.</span>
                            <?php endif; ?>
                        </div>

                        <?php if (!$member['is_member']): ?>
                            <button type="button" onclick="showJoinForumModal()" id="joinForumPulseBtn" class="animate-pulse bg-secondary hover:bg-secondaryShades-600 px-6 py-2 text-sm rounded-[50px] text-white font-medium flex items-center">
                                <i data-lucide="user-plus" class="w-5 h-5 mr-2 z-10"></i>
                                <span>Join Forum</span>
                            </button>
                        <?php endif; ?>

                        <?php if ($member['is_member']): ?>
                            <button type="button" onclick="showLeaveForumModal()" class="bg-red-600 hover:bg-red-700 px-6 py-2 text-sm rounded-[50px] text-white font-medium flex items-center">
                                <i data-lucide="user-minus" class="w-5 h-5 mr-2 z-10"></i>
                                <span>Leave Forum</span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Blocked Member Alert -->
            <?php if ($member['is_member'] && $member['is_blocked']): ?>
            <div class="bg-white rounded-lg shadow-sm border border-red-200 mb-6">
                <div class="bg-red-50 border-b border-red-200 p-8 text-center">
                    <div class="bg-red-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="ban" class="w-10 h-10 text-red-500"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-red-800 mb-3">Access Blocked</h3>
                    <p class="text-red-600 text-lg">You have been blocked from participating in this forum.</p>
                    <p class="text-red-500 text-sm mt-4">You can view discussions but cannot post, reply, or interact. If you believe this is a mistake, please contact the forum moderators.</p>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Discussion Content -->
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Main Discussion Area -->
                <div class="lg:w-2/3">
                    <!-- Discussion Thread -->
                    <?php if ($paginatedDiscussions): ?>
                        <?php foreach ($paginatedDiscussions['discussions'] as $discussion): ?>
                        <div class="bg-white rounded-lg shadow-sm border borderColor mb-6">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 rounded-full mr-4 bg-secondary flex items-center justify-center text-white">
                                            <i data-lucide="messages-square" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <h2 class="text-xl font-bold">
                                                <a href="<?= base_url('ksp/discussion/' . $discussion->id) ?>" class="hover:text-primary">
                                                    <?= esc($discussion->title) ?>
                                                </a>
                                            </h2>
                                            <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
                                                <span>Posted by <?= esc($discussion->author_name ?? 'User') ?></span>
                                                <span>•</span>
                                                <span><?= Carbon::parse($discussion->created_at)->diffForHumans() ?></span>
                                                <span>•</span>
                                                <span class="flex items-center">
                                                    <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                                                    <?= esc($discussion->view_count ?? 0) ?> views
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (session()->get('id') && $member['is_member'] && !$member['is_blocked']): ?>
                                    <button 
                                        onclick="toggleBookmark('<?= $discussion->id ?>')" 
                                        id="bookmark-btn-<?= $discussion->id ?>" 
                                        class="text-slate-400 hover:text-primary transition-colors"
                                        title="<?= isset($discussion->isBookmarked) && $discussion->isBookmarked ? 'Remove bookmark' : 'Bookmark discussion' ?>">
                                        <i 
                                            id="bookmark-icon-<?= $discussion->id ?>" 
                                            data-lucide="bookmark" 
                                            class="w-5 h-5 <?= isset($discussion->isBookmarked) && $discussion->isBookmarked ? 'fill-secondary text-secondary' : '' ?>"></i>
                                    </button>
                                    <?php else: ?>
                                    <button class="text-slate-400 cursor-not-allowed" disabled title="Login to bookmark">
                                        <i data-lucide="bookmark" class="w-5 h-5"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Discussion Content Preview -->
                                <div class="prose max-w-none text-slate-700 mb-4 line-clamp-3">
                                    <?= $discussion->content ?? '' ?>
                                </div>

                                <!-- Discussion Tags -->
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <?php 
                                    $tags = [];
                                    
                                    // Safely decode the JSON tags
                                    if (!empty($discussion->tags)) {
                                        $decodedTags = json_decode($discussion->tags, true);
                                        
                                        // Check if decoding was successful
                                        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedTags)) {
                                            $tags = $decodedTags;
                                        }
                                    }
                                    
                                    // Display tags if we have any
                                    if (!empty($tags)): ?>
                                        <?php foreach ($tags as $tag): ?>
                                            <span class="bg-secondaryShades-50 text-secondary text-xs px-4 py-1 rounded-full">
                                                #<?= esc(trim($tag, '"')) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Discussion Actions -->
                                <div class="flex flex-wrap justify-between items-center pt-4 border-t borderColor">
                                    <div class="flex items-center space-x-4">
                                        <?php if ($member['is_member'] && !$member['is_blocked']): ?>
                                        <button onclick="likeDiscussion('<?= $discussion->id ?>')" class="like-discussion-btn flex items-center text-slate-600 hover:text-primary transition-colors">
                                            <i data-lucide="thumbs-up" class="w-5 h-5 mr-1"></i>
                                            <span class="like-count"><?= esc($discussion->like_count ?? 0) ?></span>
                                        </button>
                                        <?php else: ?>
                                        <span class="flex items-center text-slate-400">
                                            <i data-lucide="thumbs-up" class="w-5 h-5 mr-1"></i>
                                            <span class="like-count"><?= esc($discussion->like_count ?? 0) ?></span>
                                        </span>
                                        <?php endif; ?>
                                        <a href="<?= base_url('ksp/discussion/' . $discussion->slug . '/view') ?>" class="flex items-center text-slate-600 hover:text-primary">
                                            <i data-lucide="message-square" class="w-5 h-5 mr-1"></i>
                                            <span><?= esc($discussion->comment_count) ?> replies</span>
                                        </a>
                                        <button class="flex items-center text-slate-600 hover:text-primary">
                                            <i data-lucide="share-2" class="w-5 h-5 mr-1"></i>
                                            <span>Share</span>
                                        </button>
                                    </div>
                                    <a href="<?= base_url('ksp/discussion/' . $discussion->slug . '/view') ?>" class="text-primary font-medium hover:underline hover:text-secondary">
                                        Read more →
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <!-- Pagination -->
                        <?php if ($paginatedDiscussions['pager']->getPageCount() > 1): ?>
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mt-6">
                                <div class="flex justify-center items-center space-x-2">
                                    <?= $paginatedDiscussions['pager']->links('default', 'custom_pagination') ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <div class="bg-white rounded-lg shadow-sm border borderColor p-8 text-center">
                            <i data-lucide="message-square" class="w-16 h-16 text-slate-300 mx-auto mb-4"></i>
                            <h3 class="text-xl font-bold text-slate-600 mb-2">No discussions yet</h3>
                            <p class="text-slate-500 mb-4">Be the first to start a discussion in this forum</p>
                            <button class="gradient-btn px-6 py-2 rounded-md text-white font-medium" id="newDiscussionBtn">
                                Start a Discussion
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Sidebar -->
                <div class="lg:w-1/3 sticky top-24">
                    <!-- About This Forum -->
                    <div class="bg-white rounded-lg shadow-sm border borderColor p-6 mb-6">
                        <h3 class="text-lg font-bold mb-4"><?= esc($forum->name) ?></h3>
                        <p class="text-slate-600 mb-4"><?= esc($forum->description) ?></p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="bg-slate-100 text-slate-700 text-xs px-3 py-1 rounded-full">#water-management</span>
                            <span class="bg-slate-100 text-slate-700 text-xs px-3 py-1 rounded-full">#conservation</span>
                            <span class="bg-slate-100 text-slate-700 text-xs px-3 py-1 rounded-full">#sustainability</span>
                            <span class="bg-slate-100 text-slate-700 text-xs px-3 py-1 rounded-full">#irrigation</span>
                            <span class="bg-slate-100 text-slate-700 text-xs px-3 py-1 rounded-full">#leak-detection</span>
                        </div>
                        <div class="flex items-center justify-between text-sm text-slate-500 border-t borderColor pt-4">
                            <span>Created: <?= Carbon::parse($forum->created_at)->format('F Y') ?></span>
                            <span><?= $forum->discussion_count ?> discussions</span>
                        </div>
                    </div>
                    
                    <!-- Forum Moderators -->
                    <?php if ($moderators): ?>
                        <div class="bg-white rounded-lg shadow-sm border borderColor p-6 mb-6">
                            <h3 class="text-lg font-bold mb-4">Forum Moderators</h3>
                            <div class="space-y-4">
                                <?php
                                $moderatorEmails = [];

                                foreach ($moderators as $moderator):
                                    $moderatorEmails[] = $moderator->user_email;
                                ?>
                                <div class="flex items-center">
                                    <?php if ($moderator->user_image): ?>
                                        <img src="<?= esc($moderator->user_image) ?>" alt="Moderator" class="w-10 h-10 rounded-full mr-3">
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-full mr-3 bg-primaryShades-100 flex items-center justify-center">
                                            <span class="text-primary"><?= esc(substr($moderator->user_name, 0, 1)) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h4 class="font-medium"><?= esc($moderator->user_name) ?></h4>
                                        <p class="text-sm text-secondary"><?= esc($moderator->moderator_title) ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <button onclick="openContactModal()" type="button" class="w-full mt-4 text-primary font-medium flex items-center justify-center hover:bg-primaryShades-50 py-2 rounded-lg border borderColor transition-colors">
                                <i data-lucide="mail" class="w-5 h-5 mr-2"></i>
                                Contact Moderators
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="bg-white rounded-lg shadow-sm border borderColor p-6 mb-6">
                            <h3 class="text-lg font-bold mb-4">No Moderators Assigned</h3>
                            <p class="text-sm text-slate-500">There are no moderators assigned to this forum yet.</p>
                        </div>
                    <?php endif; ?>

                    <!-- Related Discussions -->
                    <?php if ($discussions): ?>
                        <div class="bg-white rounded-lg shadow-sm border borderColor p-6">
                            <h3 class="text-lg font-bold mb-4">Recent Discussions</h3>
                            <div class="space-y-4">
                                <?php foreach ($discussions as $discussion): ?>
                                <a href="#" class="discussion-card block p-3 rounded-md hover:bg-slate-50">
                                    <h4 class="font-medium mb-1"><?= esc($discussion->title) ?></h4>
                                    <div class="flex items-center text-sm text-slate-500">
                                        <span><?= esc($discussion->comment_count) ?> replies</span>
                                        <span class="mx-2">•</span>
                                        <span><?= Carbon::parse($discussion->created_at)->diffForHumans() ?></span>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <a href="#" class="block mt-4 text-primary font-medium text-center">
                                View all discussions in this forum
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="bg-white rounded-lg shadow-sm border borderColor p-6">
                            <h3 class="text-lg font-bold mb-4">No Related Discussions Found</h3>
                            <p class="text-sm text-slate-500">Be the first to start a discussion in this forum.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>

        <!-- Contact Moderators Modal -->
        <?php if (isset($moderators) && !empty($moderators)): ?>
        <?php
            // Debug: log forum data
            if (isset($forum)) {
                log_message('info', 'Forum object in contact modal: ' . print_r($forum, true));
            } else {
                log_message('error', 'Forum object not set in view for contact modal');
            }
        ?>
        <div id="contact-modal" class="hidden fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-2 sm:p-4">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity duration-300"></div>
            
            <!-- Modal container -->
            <div class="relative z-50 bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-2xl max-h-[calc(100vh-2rem)] flex flex-col">
                <!-- Modal Header -->
                <div class="bg-white px-6 py-4 border-b border-gray-200 flex-shrink-0">
                    <div class="flex justify-between items-start w-full">
                        <h3 class="text-2xl font-bold text-gray-900">Contact Forum Moderators</h3>
                        <button onclick="closeContactModal()" type="button" class="text-slate-400 hover:text-slate-500 p-1">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Modal Body -->
                <form id="contact-form" data-forum-id="<?= isset($forum->id) ? esc($forum->id) : (isset($forum) && is_object($forum) && property_exists($forum, 'id') ? esc($forum->id) : '') ?>" class="flex-1 overflow-y-auto px-6 py-4">
                    <div class="space-y-4 sm:space-y-6">
                        <div>
                            <label for="contact-subject" class="block text-sm font-medium text-slate-700 mb-1">Subject <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                id="contact-subject" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" 
                                placeholder="Enter subject">
                            <p class="mt-1 text-xs text-slate-500">Brief summary of your message</p>
                        </div>
                        <div>
                            <label for="contact-message" class="block text-sm font-medium text-slate-700 mb-1">Message <span class="text-red-500">*</span></label>
                            <textarea 
                                id="contact-message" 
                                required 
                                rows="6" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-h-[150px] transition-all duration-200 resize-none" 
                                placeholder="Type your message..."></textarea>
                            <p class="mt-1 text-xs text-slate-500">Your message will be sent to all forum moderators</p>
                        </div>
                        <input type="hidden" id="moderator-emails" value='<?= isset($moderatorEmails) ? json_encode($moderatorEmails) : '[]' ?>'>
                        <input type="hidden" id="forum-id" value="<?= isset($forum) && is_object($forum) && property_exists($forum, 'id') ? esc($forum->id) : '' ?>">
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                        <button 
                            type="button" 
                            onclick="closeContactModal()"
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
        <?php endif; ?>

        <!-- Add Discussion Modal (place this just before the closing </body> tag) -->
        <div id="addDiscussionModal" class="fixed inset-0 z-50 overflow-y-auto flex hidden items-center justify-center p-2 sm:p-4">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity duration-300"></div>

            <!-- Modal container with responsive height -->
            <div class="modal-container relative z-50 bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-3xl max-h-[calc(100vh-2rem)] flex flex-col">
                
                <!-- Modal Header (fixed) -->
                <div class="modal-header bg-white px-6 py-4 border-b border-gray-200 flex-shrink-0">
                    <div class="flex justify-between items-start w-full">
                        <h3 class="text-2xl font-bold text-gray-900">Start a New Discussion</h3>
                        <button id="closeModalBtn" class="text-slate-400 hover:text-slate-500 p-1">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body (scrollable) -->
                <div class="modal-body flex-1 overflow-y-auto custom-scrollbar px-6 py-4">
                    <!-- Discussion Form -->
                    <form id="discussionForm" class="space-y-4 sm:space-y-6" enctype="multipart/form-data">
                        <!-- Forum ID -->
                        <input type="hidden" name="forum_id" value="<?= esc($forum->id) ?>">

                        <!-- Discussion Title -->
                        <div>
                            <label for="discussionTitle" class="block text-sm font-medium text-slate-700 mb-1">Discussion Title <span class="text-red-500">*</span></label>
                            <input type="text" id="discussionTitle" name="title" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="What's your question or topic?" required>
                            <p class="mt-1 text-xs text-slate-500">Be specific and concise with your title</p>
                        </div>

                        <!-- Slug -->
                        <div>
                            <label for="discussionSlug" class="block text-sm font-medium text-slate-700 mb-2">URL Slug <span class="text-red-500">*</span></label>
                            <div class="flex w-full">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-700 text-sm whitespace-nowrap">
                                    kewasnet.co.ke/discussion/
                                </span>
                                <input 
                                    type="text" 
                                    id="discussionSlug" 
                                    name="slug"
                                    pattern="^[a-z0-9-]+$"
                                    class="flex-1 min-w-0 px-4 py-3 rounded-none rounded-r-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    placeholder="water-conservation-tips"
                                >
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Auto-generated from discussion title. Use lowercase letters, numbers, and hyphens only.</p>
                        </div>
                        
                        <!-- Discussion Content -->
                        <div>
                            <label for="discussionContent" class="block text-sm font-medium text-slate-700 mb-1">Discussion Details <span class="text-red-500">*</span></label>
                            <div class="flex-1">
                                <textarea 
                                    id="discussionContent" 
                                    name="content" 
                                    placeholder="Type your message..." 
                                    rows="4" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-h-[100px] transition-all duration-200 resize-none"
                                    required
                                ></textarea>
                            </div>
                            <div class="flex justify-between items-center mt-1">
                                <p class="text-xs text-slate-500">Minimum 20 characters. Maximum 1000 characters.</p>
                                <span id="charCount" class="text-sm text-slate-500">0/1000</span>
                            </div>
                        </div>
                        
                        <!-- Tags -->
                        <div>
                            <label for="discussionTags" class="block text-sm font-medium text-slate-700 mb-1">Tags</label>
                            <div class="flex flex-wrap items-center gap-2 p-1 border borderColor rounded-md min-h-[46px]">
                                <input type="text" id="discussionTags" name="tag_input" 
                                    class="flex-1 min-w-[100px] w-full px-4 py-3 rounded-lg border-none focus:outline-none focus:ring-2 focus:ring-secondary" 
                                    placeholder="Add tags (max 5)">
                            </div>
                            <!-- Hidden input to store tags as JSON -->
                            <input type="hidden" id="tagsHidden" name="tags" value="[]">
                            <div class="mt-2 flex flex-wrap gap-2">
                                <span class="bg-slate-100 text-slate-700 text-xs px-3 py-1 rounded-full cursor-pointer hover:bg-primaryShades-100 hover:text-primary">#water-management</span>
                                <span class="bg-slate-100 text-slate-700 text-xs px-3 py-1 rounded-full cursor-pointer hover:bg-primaryShades-100 hover:text-primary">#conservation</span>
                                <span class="bg-slate-100 text-slate-700 text-xs px-3 py-1 rounded-full cursor-pointer hover:bg-primaryShades-100 hover:text-primary">#best-practices</span>
                            </div>
                            <p class="mt-1 text-xs text-slate-500">Add up to 5 tags to help others find your discussion</p>
                        </div>
                        
                        <!-- File Attachment -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Attachments</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                                <input type="file" id="file-upload" class="hidden" accept=".pdf,.doc,.docx,.txt,.xls,.xlsx,.ppt,.pptx" multiple name="attachments[]">
                                <label for="file-upload" class="cursor-pointer">
                                    <i data-lucide="upload" class="w-8 h-8 mx-auto text-slate-400 mb-2"></i>
                                    <p class="text-sm text-slate-600">Drag & drop documents or <span class="text-secondary font-medium">browse files</span></p>
                                    <p class="text-xs text-slate-400 mt-1">PDF, DOC, DOCX, TXT, XLS, XLSX, PPT, PPTX up to 5MB</p>
                                </label>
                                <div id="file-preview-container" class="mt-4 flex flex-wrap gap-2"></div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Progress Bar (initially hidden) -->
                <div id="uploadProgressContainer" class="hidden mt-4 px-6">
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div id="uploadProgressBar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <p id="uploadProgressText" class="text-sm text-slate-600 mt-1">Uploading: 0%</p>
                </div>

                <!-- Modal Footer (fixed) -->
                <div class="bg-white px-6 py-4 border-t border-gray-200 flex-shrink-0">
                    <div class="flex flex-col sm:flex-row justify-end gap-3">
                        <button type="button" id="cancelDiscussionBtn" class="px-6 py-2 border border-gray-300 rounded-md text-slate-700 hover:bg-slate-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" id="submitDiscussionBtn" class="gradient-btn px-6 py-2 rounded-md text-white font-medium transition-all">
                            <span>Post Discussion</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Join Forum Modal -->
        <div id="joinForumModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg shadow-lg w-11/12 md:w-1/2 lg:w-1/3 max-w-md mx-auto p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-dark">Join Forum</h3>
                    <button onclick="closeJoinForumModal()" class="text-slate-500 hover:text-slate-700">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div class="mb-6">
                    <p class="text-slate-600 mb-4">Would you like to join this forum to participate in discussions?</p>
                    <div class="bg-primaryShades-50 p-4 rounded-lg">
                        <h4 id="forumModalTitle" class="font-medium text-primary mb-2"></h4>
                        <p id="forumModalDescription" class="text-sm text-slate-600"></p>
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <button onclick="closeJoinForumModal()" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50">
                        Cancel
                    </button>
                    <button id="joinForumBtn" onclick="joinForum()" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">
                        Join Forum
                    </button>
                </div>
            </div>
        </div>

        <!-- Leave Forum Modal -->
        <div id="leaveForumModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg shadow-lg w-11/12 md:w-1/2 lg:w-1/3 max-w-md mx-auto p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-dark">Leave Forum</h3>
                    <button onclick="closeLeaveForumModal()" class="text-slate-500 hover:text-slate-700">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div class="mb-6">
                    <p class="text-slate-600 mb-4">Are you sure you want to leave this forum?</p>
                    <div class="bg-red-50 p-4 rounded-lg">
                        <h4 id="forumModalTitle" class="font-medium text-red-600 mb-2">
                            <?= esc($forum->name ?? '') ?>
                        </h4>
                        <p id="forumModalDescription" class="text-sm text-slate-600">
                            <?= esc($forum->description ?? '') ?>
                        </p>
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <button onclick="closeLeaveForumModal()" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50">
                        Cancel
                    </button>
                    <button id="leaveForumBtn" onclick="leaveForum()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Leave Forum
                    </button>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<style>
    /* Error input styling */
    .is-invalid {
        border-color: #DC2626 !important;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important;
    }
    
    .is-invalid:focus {
        border-color: #DC2626 !important;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2) !important;
    }
    
    .invalid-feedback {
        display: block;
        color: #DC2626;
        font-size: 0.75rem;
        margin-top: 0.25rem;
        line-height: 1.25;
    }

    /* If not using Tailwind, fallback custom pulse animation */
    .animate-pulse {
        animation: pulse 1.5s infinite;
    }

    .ping-dot {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: white;
        transform: translate(-50%, -50%);
    }

    @keyframes ping {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        75%, 100% {
            transform: scale(2);
            opacity: 0;
        }
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
</style>
<script>
    // Main document ready function
    document.addEventListener('DOMContentLoaded', () => {
        // Modal functionality
        const modal = document.getElementById('addDiscussionModal');
        const newDiscussionBtn = document.getElementById('newDiscussionBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const cancelBtn = document.getElementById('cancelDiscussionBtn');
        
        // Open modal when "New Discussion" is clicked
        if (newDiscussionBtn) {
            newDiscussionBtn.addEventListener('click', (e) => {
                e.preventDefault();
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            });
        }
        
        // Close modal
        const closeModal = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
            
            // Reset form when closing
            const form = document.getElementById('discussionForm');
            if (form) {
                form.reset();
                // Reset tags
                tags = [];
                renderTags();
                // Reset character counter
                const charCount = document.getElementById('charCount');
                if (charCount) charCount.textContent = '0/1000';
            }
        };
        
        if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
        
        // Close when clicking outside modal
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });
        
        // Slug generation functionality
        const titleInput = document.getElementById('discussionTitle');
        const slugInput = document.getElementById('discussionSlug');
        
        // Helper function to generate slug
        function generateSlug(text) {
            return text.toLowerCase()
                .replace(/[^\w\s-]/g, '') // Remove all non-word characters (except spaces and hyphens)
                .replace(/\s+/g, '-')     // Replace spaces with hyphens
                .replace(/--+/g, '-')     // Replace multiple hyphens with single hyphen
                .replace(/^-+|-+$/g, '')  // Remove leading/trailing hyphens
                .trim();
        }
        
        if (titleInput && slugInput) {
            // Auto-generate slug from title on input
            titleInput.addEventListener('input', function() {
                // Only auto-generate if slug field is empty or hasn't been manually edited
                if (!slugInput.value.trim() || slugInput.dataset.manual !== 'true') {
                    const title = this.value;
                    const slug = generateSlug(title);
                    slugInput.value = slug;
                }
            });
            
            // Mark slug as manually edited when user types in it
            slugInput.addEventListener('input', function() {
                this.dataset.manual = 'true';
                
                // Clean slug input in real-time
                const value = this.value;
                const cleanValue = value.toLowerCase().replace(/[^a-z0-9-]/g, '').replace(/--+/g, '-');
                if (value !== cleanValue) {
                    this.value = cleanValue;
                }
            });
            
            // Reset manual flag when slug is cleared
            slugInput.addEventListener('blur', function() {
                if (!this.value.trim()) {
                    this.dataset.manual = 'false';
                }
            });
            
            // AJAX slug validation on blur
            slugInput.addEventListener('blur', function() {
                const slug = this.value.trim();
                if (!slug || slug.length === 0) return;

                // Show loading state
                this.style.borderColor = '#3B82F6';

                fetch('<?= base_url('ksp/discussion/check-slug') ?>?slug=' + encodeURIComponent(slug))
                    .then(response => response.json())
                    .then(data => {
                        if (!data.available) {
                            this.style.borderColor = '#EF4444';
                            this.dataset.slugInvalid = 'true';
                            showNotification('warning', 'This slug is already taken. Please choose another one.');
                        } else {
                            this.style.borderColor = '#10B981';
                            this.dataset.slugInvalid = 'false';
                        }
                    })
                    .catch(error => {
                        console.error('Error checking slug:', error);
                        this.style.borderColor = '#6B7280';
                    });
            });
        }
        
        // Character counter
        const contentInput = document.getElementById('discussionContent');
        const charCount = document.getElementById('charCount');
        
        if (contentInput && charCount) {
            contentInput.addEventListener('input', function() {
                const length = this.value.length;
                charCount.textContent = `${length}/1000`;
                
                if (length > 1000) {
                    charCount.classList.add('text-red-500');
                    charCount.classList.remove('text-slate-500');
                } else if (length < 20) {
                    charCount.classList.add('text-orange-500');
                    charCount.classList.remove('text-slate-500', 'text-red-500');
                } else {
                    charCount.classList.remove('text-red-500', 'text-orange-500');
                    charCount.classList.add('text-slate-500');
                }
            });
        }
        
        // File upload validation (existing code remains the same)
        const fileUpload = document.getElementById('file-upload');
        const filePreview = document.getElementById('file-preview-container');
        
        if (fileUpload && filePreview) {
            fileUpload.addEventListener('change', function(e) {
                filePreview.innerHTML = '';
                const files = e.target.files;
                
                if (files.length > 0) {
                    Array.from(files).forEach(file => {
                        // Validate file type
                        const validTypes = [
                            'application/pdf', 
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'text/plain',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-powerpoint',
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation'
                        ];
                        
                        if (!validTypes.includes(file.type)) {
                            showNotification('error', 'Only document files are allowed (PDF, DOC, DOCX, TXT, XLS, XLSX, PPT, PPTX)');
                            this.value = '';
                            filePreview.innerHTML = '';
                            return;
                        }
                        
                        // Validate file size (5MB)
                        if (file.size > 5 * 1024 * 1024) {
                            showNotification('error', 'File size must be less than 5MB');
                            this.value = '';
                            filePreview.innerHTML = '';
                            return;
                        }
                        
                        // Create file preview
                        const fileElement = document.createElement('div');
                        fileElement.className = 'flex items-center bg-slate-100 rounded-md p-2 w-full';
                        fileElement.innerHTML = `
                            <div class="flex justify-between items-center w-full">
                            <span class="flex items-center">
                                <i data-lucide="file-text" class="w-5 h-5 text-slate-600 mr-2"></i>
                                <span class="text-sm truncate max-w-xs">${file.name}</span>
                            </span>
                            <button type="button" class="ml-2 text-red-500 hover:text-red-700 remove-file" data-file="${file.name}">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                            </div>
                        `;
                        filePreview.appendChild(fileElement);
                    });
                    
                    lucide.createIcons();
                    
                    // Add remove file functionality
                    document.querySelectorAll('.remove-file').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const fileName = this.getAttribute('data-file');
                            const files = Array.from(fileUpload.files);
                            const newFiles = files.filter(file => file.name !== fileName);
                            
                            // Create new FileList
                            const dataTransfer = new DataTransfer();
                            newFiles.forEach(file => dataTransfer.items.add(file));
                            fileUpload.files = dataTransfer.files;
                            
                            this.parentElement.remove();
                        });
                    });
                }
            });
        }
        
        // AJAX form submission
        const discussionForm = document.getElementById('discussionForm');
        const progressContainer = document.getElementById('uploadProgressContainer');
        const progressBar = document.getElementById('uploadProgressBar');
        const progressText = document.getElementById('uploadProgressText');
        const submitBtn = document.getElementById('submitDiscussionBtn');
        
        // jQuery-based form submit for discussion
        $(document).ready(function() {
            var $form = $('#discussionForm');
            var $submitBtn = $('#submitDiscussionBtn');
            var $progressContainer = $('#uploadProgressContainer');
            var $progressBar = $('#uploadProgressBar');
            var $progressText = $('#uploadProgressText');
            var $tagsInput = $('#discussionTags');
            var $tagsHidden = $('#tagsHidden');
            var tags = [];

            // Tag input logic (jQuery)
            $tagsInput.on('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    var tag = $tagsInput.val().trim();
                    if (tag && !tags.includes(tag) && tags.length < 5) {
                        tags.push(tag);
                        renderTags();
                        $tagsInput.val('');
                    }
                }
            });

            function renderTags() {
                var $container = $tagsInput.parent();
                $container.find('.tag-badge').remove();
                tags.forEach(function(tag) {
                    var $tag = $('<span class="tag-badge bg-primaryShades-100 text-primary text-xs px-3 py-1 rounded-full flex items-center"></span>');
                    $tag.html(`${tag}<button type="button" class="ml-1 text-primaryShades-400 hover:text-primary" data-tag="${tag}"><i data-lucide="x" class="w-3 h-3"></i></button>`);
                    $tag.insertBefore($tagsInput);
                });
                lucide.createIcons();
                $('.tag-badge button').off('click').on('click', function() {
                    var tagToRemove = $(this).data('tag');
                    tags = tags.filter(t => t !== tagToRemove);
                    renderTags();
                });
                $tagsHidden.val(JSON.stringify(tags));
            }

            $('#submitDiscussionBtn').on('click', function(e) {
                e.preventDefault();
                clearFormErrors && clearFormErrors();
                if ($progressContainer.length) $progressContainer.removeClass('hidden');

                $submitBtn.prop('disabled', true).html('<span class="flex items-center"><i data-lucide="loader" class="w-4 h-4 animate-spin mr-2"></i> Posting...</span>');
                lucide.createIcons();

                // Always get latest tags from hidden input (in case of multiple renderTags implementations)
                var tagsVal = $('#tagsHidden').val();
                var tagsArr = [];
                try { tagsArr = JSON.parse(tagsVal); } catch (e) { tagsArr = tags; }

                // Prepare FormData
                const form = $('#discussionForm')[0];
                var formData = new FormData(form);
                formData.set('tags', JSON.stringify(tagsArr));
                formData.append('csrf_test_name', '<?= csrf_hash() ?>');

                $.ajax({
                    url: '<?= base_url('ksp/discussion/create') ?>',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    xhr: function() {
                        var xhr = $.ajaxSettings.xhr();
                        if (xhr.upload && $progressBar.length && $progressText.length) {
                            xhr.upload.addEventListener('progress', function(e) {
                                if (e.lengthComputable) {
                                    var percentComplete = (e.loaded / e.total) * 100;
                                    $progressBar.css('width', percentComplete + '%');
                                    $progressText.text('Uploading: ' + Math.round(percentComplete) + '%');
                                }
                            }, false);
                        }
                        return xhr;
                    },
                    dataType: 'json',
                    success: function(response) {
                        $submitBtn.prop('disabled', false).html('<span>Post Discussion</span>');
                        if ($progressContainer.length) $progressContainer.addClass('hidden');
                        if (response.status === 'success') {
                            showNotification('success', response.message);
                            if ($form && $form.reset) $form.reset();
                            tags = [];
                            renderTags();
                            if (typeof closeModal === 'function') closeModal();
                            setTimeout(function() {
                                if (response.redirect) {
                                    window.location.href = response.redirect;
                                } else {
                                    window.location.reload();
                                }
                            }, 1500);
                        } else {
                            if (response.errors && typeof showFormErrors === 'function') {
                                showFormErrors(response.errors);
                            }
                            showNotification('error', response.message || 'Failed to create discussion');
                        }
                    },
                    error: function(xhr) {
                        $submitBtn.prop('disabled', false).html('<span>Post Discussion</span>');
                        if ($progressContainer.length) $progressContainer.addClass('hidden');
                        const response = xhr.responseJSON || {};
                        const errors = response.errors || {};
                        if (Object.keys(errors).length > 0) {
                            showFormErrors(errors);
                            showNotification('error', 'Please fix the errors and try again');
                        } else {
                            showNotification('error', response.message || 'Failed to create discussion');
                        }
                    }
                });
            });
        });
    });

    // =============== Tag functionality (Initialize outside modal context) ================
    const tagsInput = document.getElementById('discussionTags');
    const tagsContainer = tagsInput.parentElement;
    let tags = [];
    let uploadedFiles = [];

    // Cancel upload handler
    $('#cancelUploadBtn').on('click', function() {
        if (currentXHR) {
            currentXHR.abort();
            showToast('Upload canceled', 'warning');
            progressModal.hide();
            currentXHR = null;
        }
    });
    
    tagsInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            const tag = tagsInput.value.trim();
            if (tag && !tags.includes(tag)) {
                if (tags.length < 5) {
                    tags.push(tag);
                    renderTags();
                    tagsInput.value = '';
                }
            }
        }
    });
    
    function renderTags() {
        // Clear existing tag elements
        const existingTags = tagsContainer.querySelectorAll('.tag-badge');
        existingTags.forEach(tag => tag.remove());
        
        // Add new tags
        tags.forEach(tag => {
            const tagElement = document.createElement('span');
            tagElement.className = 'tag-badge bg-primaryShades-100 text-primary text-xs px-3 py-1 rounded-full flex items-center';
            tagElement.innerHTML = `
                ${tag}
                <button type="button" class="ml-1 text-primaryShades-400 hover:text-primary" data-tag="${tag}">
                    <i data-lucide="x" class="w-3 h-3"></i>
                </button>
            `;
            tagsContainer.insertBefore(tagElement, tagsInput);
        });
        
        // Update hidden input with tags JSON
        document.getElementById('tagsHidden').value = JSON.stringify(tags);
        
        // Initialize Lucide icons for the new tags
        lucide.createIcons();
        
        // Add event listeners to remove buttons
        document.querySelectorAll('.tag-badge button').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const tagToRemove = e.currentTarget.getAttribute('data-tag');
                tags = tags.filter(t => t !== tagToRemove);
                renderTags();
            });
        });

        // Auto-resize textarea
        $('#discussionContent').on('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }

    // Function to show form errors
    function showFormErrors(errors) {
        clearFormErrors();
        
        $.each(errors, function(fieldName, message) {
            const $field = $('[name="' + fieldName + '"]');
            
            if ($field.length) {
                $field.addClass('is-invalid');
                
                const $errorDiv = $('<div>', {
                    class: 'invalid-feedback text-red-500 text-xs mt-1',
                    text: message
                });
                
                $field.after($errorDiv);
                
                if ($('.is-invalid').first().is($field)) {
                    $field.focus();
                }
            }
        });
    }

    // Function to clear form errors
    function clearFormErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
    }

    // ========== Like Discussion Function ==========
    window.likeDiscussion = function(discussionId) {
        const csrfName = '<?= csrf_token() ?>';
        const csrfHash = '<?= csrf_hash() ?>';
        
        fetch('<?= base_url('ksp/discussion/like-discussion') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                discussion_id: discussionId,
                [csrfName]: csrfHash
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Find the button that was clicked using the discussion ID
                const buttons = document.querySelectorAll('.like-discussion-btn');
                buttons.forEach(button => {
                    const onclick = button.getAttribute('onclick');
                    const match = onclick.match(/likeDiscussion\(['"](.+?)['"]\)/);
                    if (match && match[1] === discussionId) {
                        const countSpan = button.querySelector('.like-count');
                        if (countSpan) {
                            const currentCount = parseInt(countSpan.textContent) || 0;
                            countSpan.textContent = currentCount + 1;
                        }
                        button.classList.add('text-primary');
                        button.disabled = true;
                    }
                });
                showNotification('success', data.message || 'Discussion liked successfully!');
            } else {
                showNotification('error', data.message || 'Failed to like discussion');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'An error occurred while liking the discussion');
        });
    };

    // ========== Toggle Bookmark Function ==========
    window.toggleBookmark = function(discussionId) {
        const csrfName = '<?= csrf_token() ?>';
        const csrfHash = '<?= csrf_hash() ?>';
        
        const button = document.getElementById(`bookmark-btn-${discussionId}`);
        const icon = document.getElementById(`bookmark-icon-${discussionId}`);
        
        // Disable button during request
        if (button) button.disabled = true;
        
        fetch('<?= base_url('ksp/discussion/toggle-bookmark') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                discussion_id: discussionId,
                [csrfName]: csrfHash
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update icon based on bookmark status
                if (icon) {
                    if (data.bookmarked) {
                        icon.classList.add('fill-secondary', 'text-secondary');
                        button.title = 'Remove bookmark';
                    } else {
                        icon.classList.remove('fill-secondary', 'text-secondary');
                        button.title = 'Bookmark discussion';
                    }
                    // Refresh Lucide icons
                    lucide.createIcons();
                }
                showNotification('success', data.message || 'Bookmark updated successfully!');
            } else {
                showNotification('error', data.message || 'Failed to update bookmark');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'An error occurred while updating bookmark');
        })
        .finally(() => {
            // Re-enable button
            if (button) button.disabled = false;
        });
    };

    // ========== Join/Leave Forum Logic (Restored) ==========
    var currentForumId = '<?= $forum->id ?>';
    var currentForumTitle = "<?= esc($forum->name ?? '') ?>";
    var currentForumDescription = "<?= esc($forum->description ?? '') ?>";

    function showJoinForumModal() {
        $('#forumModalTitle').text(currentForumTitle);
        $('#forumModalDescription').text(currentForumDescription);
        $('#joinForumModal').removeClass('hidden');
    }

    function showLeaveForumModal() {
        $('#forumModalTitle').text(currentForumTitle);
        $('#forumModalDescription').text(currentForumDescription);
        $('#leaveForumModal').removeClass('hidden');
    }

    function closeJoinForumModal() {
        $('#joinForumModal').addClass('hidden');
    }

    function closeLeaveForumModal() {
        $('#leaveForumModal').addClass('hidden');
    }

    // Join forum
    window.joinForum = function() {
        if (!currentForumId) {
            alert('No forum selected!');
            return;
        }
        var $joinBtn = $('#joinForumBtn');
        var originalText = $joinBtn.html();
        $joinBtn.html('<span class="flex items-center"><i data-lucide="loader" class="w-4 h-4 animate-spin mr-2"></i> Joining...</span>');
        $joinBtn.prop('disabled', true);
        var formData = new FormData();
        formData.append('forum_id', currentForumId);
        formData.append('csrf_test_name', '<?= csrf_hash() ?>');
        $.ajax({
            url: '<?= base_url('ksp/join-forum') ?>',
            type: 'POST',
            dataType: 'json',
            contentType: false,
            processData: false,
            data: formData,
            success: function(data) {
                if (data.status === 'success') {
                    showNotification('success', data.message);
                    $joinBtn.html(originalText);
                    $joinBtn.prop('disabled', false);
                    setTimeout(function() {
                        window.location.reload();
                        closeJoinForumModal();
                    }, 2000);
                } else {
                    showNotification('error', data.message || 'Failed to join forum. Please try again.');
                    $joinBtn.html(originalText);
                    $joinBtn.prop('disabled', false);
                }
            },
            error: function() {
                showNotification('error', 'An error occurred. Please try again.');
                $joinBtn.html(originalText);
                $joinBtn.prop('disabled', false);
            }
        });
    };

    // Leave forum
    window.leaveForum = function() {
        if (!currentForumId) {
            alert('No forum selected!');
            return;
        }
        var $leaveBtn = $('#leaveForumBtn');
        var originalText = $leaveBtn.html();
        $leaveBtn.html('<span class="flex items-center"><i data-lucide="loader" class="w-4 h-4 animate-spin mr-2"></i> Leaving...</span>');
        $leaveBtn.prop('disabled', true);
        var formData = new FormData();
        formData.append('forum_id', currentForumId);
        formData.append('csrf_test_name', '<?= csrf_hash() ?>');
        $.ajax({
            url: '<?= base_url('ksp/leave-forum') ?>',
            type: 'POST',
            dataType: 'json',
            contentType: false,
            processData: false,
            data: formData,
            success: function(data) {
                if (data.status === 'success') {
                    showNotification('success', data.message);
                    $leaveBtn.html(originalText);
                    $leaveBtn.prop('disabled', false);
                    setTimeout(function() {
                        window.location.href = '<?= base_url('ksp/networking-corner') ?>';
                        closeLeaveForumModal();
                    }, 2000);
                } else {
                    showNotification('error', data.message || 'Failed to leave forum. Please try again.');
                    $leaveBtn.html(originalText);
                    $leaveBtn.prop('disabled', false);
                }
            },
            error: function() {
                showNotification('error', 'An error occurred. Please try again.');
                $leaveBtn.html(originalText);
                $leaveBtn.prop('disabled', false);
            }
        });
    };
    
    // Contact Modal Functions
    window.openContactModal = function() {
        document.getElementById('contact-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        lucide.createIcons();
    };
    
    window.closeContactModal = function() {
        document.getElementById('contact-modal').classList.add('hidden');
        document.body.style.overflow = '';
        document.getElementById('contact-form').reset();
    };
    
    // Handle contact form submission
    document.getElementById('contact-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const subject = document.getElementById('contact-subject').value;
        const message = document.getElementById('contact-message').value;
        const emailsElement = document.getElementById('moderator-emails');
        const forumIdElement = document.getElementById('forum-id');
        
        // Try to get forum ID from hidden field first, then from data attribute
        let forumIdValue = forumIdElement ? forumIdElement.value : null;
        if (!forumIdValue || forumIdValue === '') {
            forumIdValue = this.getAttribute('data-forum-id');
        }
        
        // Debug: Log element values
        console.log('Forum ID element:', forumIdElement);
        console.log('Forum ID value (raw from hidden field):', forumIdElement ? forumIdElement.value : 'element not found');
        console.log('Forum ID value (from data attribute):', this.getAttribute('data-forum-id'));
        console.log('Forum ID value (final):', forumIdValue);
        console.log('Moderator emails element:', emailsElement);
        console.log('Moderator emails value (raw):', emailsElement ? emailsElement.value : 'element not found');
        
        // Validate forum ID element exists and has a value
        if (!forumIdValue || forumIdValue === '') {
            console.error('Forum ID not found in hidden field or data attribute');
            showNotification('error', 'Unable to send message: Forum information missing');
            return;
        }
        
        // Forum ID can be a UUID string, so no need to parse as integer
        const forumId = forumIdValue.trim();
        console.log('Forum ID (trimmed):', forumId);
        
        // Parse emails
        let emails = [];
        try {
            emails = JSON.parse(emailsElement.value);
            console.log('Parsed emails:', emails);
        } catch (error) {
            console.error('Error parsing moderator emails:', error);
            showNotification('error', 'Unable to send message: Invalid moderator information');
            return;
        }
        
        const payload = {
            subject: subject,
            message: message,
            moderator_emails: emails,
            forum_id: forumId
        };
        
        console.log('Final payload to send:', JSON.stringify(payload, null, 2));
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalHtml = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 inline mr-2 animate-spin"></i> Sending...';
        lucide.createIcons();
        
        fetch('<?= base_url('ksp/forum/contact-moderators') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Response:', data);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
            lucide.createIcons();
            
            if (data.success) {
                showNotification('success', 'Your message has been sent to the moderators');
                closeContactModal();
            } else {
                let errorMsg = data.message || 'Failed to send message';
                if (data.errors) {
                    console.error('Validation errors:', data.errors);
                    errorMsg += ': ' + Object.values(data.errors).join(', ');
                }
                showNotification('error', errorMsg);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
            lucide.createIcons();
            showNotification('error', 'An error occurred while sending your message');
        });
    });
</script>
<?= $this->endSection() ?>