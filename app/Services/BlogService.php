<?php

namespace App\Services;

use App\Models\Blog;
use Carbon\Carbon;
use App\Libraries\ClientAuth;

class BlogService
{
   protected $blogModel;
   protected $filePath;

   public function __construct()
   {
      $this->blogModel = new Blog();
      $this->filePath = 'public/uploads/blogImages/';
   }

   // This method uploads blog cover image
   public function uploadBlogImage($file, $title, $currentImage = null)
   {
      // Check if there is a file uploaded then unlink first
      if ($currentImage != null) {
         if (file_exists($currentImage)) {
               $fileName = basename(parse_url($currentImage, PHP_URL_PATH));

               unlink(ROOTPATH . $this->filePath . $fileName);
         }
      }

      // Get the file extension
      $extension = $file->getClientExtension();

      // Generate a custom name for the file (you can adjust this as needed)
      $fileName = time() . '-' . url_title($title) . '.' . $extension;

      // Set the path to the uploads folder
      $uploadPath = ROOTPATH . $this->filePath;

      // Move the file to the uploads folder
      if ($file->move($uploadPath, $fileName)) {
         // Image path
         $imagePath = base_url() . 'uploads/blogImages/' . $fileName;

         return $imagePath;
      }

      return false;
   }

   public function updateBlogImage($postId, $file)
   {

      // Get the file extension
      $extension = $file->getClientExtension();
      $name = $file->getBasename();

      // Generate a custom name for the file (you can adjust this as needed)
      $fileName = time() . '' .$postId . '-' . $name . '.' . $extension;

      // Set the path to the uploads folder
      $uploadPath = ROOTPATH . $this->filePath;

      // Check if the file already exists
      if (file_exists($uploadPath . $fileName)) {
         // If the file exists, remove it
         unlink($uploadPath . $fileName);
      }

      // Move the file to the uploads folder
      if ($file->move($uploadPath, $fileName)) {
         // Image path
         $imagePath = base_url() . 'uploads/blogImages/' . $fileName;

         return $imagePath;
      }

      return false;
   }

   // Check if the category has blogs
   public function deletePost($categoryId)
   {
      // Check if the category has blogs
      $blogsCount = $this->blogModel->where('category_id', $categoryId)->countAllResults();
      return $blogsCount;
   }

   // Render blog HTML
   public function renderBlogHtml($blogs)
   {
      if (empty($blogs)) {
         return '<div class="h-[200px] w-full flex justify-center items-center gap-3 bg-white/50 rounded-md">
            <ion-icon name="warning-outline" class="text-[30px] text-gray-500"></ion-icon>
            <p class="text-center text-gray-500 roboto">No blogs found!</p>
         </div>';
      }

      $html = '<div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-10">';

      foreach ($blogs as $blog) {
         // Human readable publishing date
         $publishDate = Carbon::parse($blog['created_at'])->diffForHumans();

         $html .= '<!-- Blog 1 -->
          <div class="bg-white/50 p-3 rounded-md shadow-md">
            <div class="relative">
              <!-- Blog Image -->
              <img src="'. $blog['blog_cover_img'].'" alt="" class="w-full h-[200px] object-cover rounded-t-md">

              <!-- Blog Category -->
              <div class="p-3" style="position: absolute; bottom: 0; left: 0;">
                <span class="bg-gradient-to-r from-primary to-secondary text-white text-md py-1 px-3 rounded-full">'. $blog['category_name'] .'</span>
              </div>
            </div>

            <div class="py-3">
              <!-- Blog Tags -->
              <h5 class="text-xl text-primary roboto font-medium mb-2 capitalize">'. substr($blog['title'], 0, 40) . '...' .'</h5>
              <p class="text-base text-gray-800 roboto mb-5">'. substr($blog['summary'], 0, 100) . '...' .'</p>
              <a href="'. base_url("client/blogs/".$blog['slug']) .'" class="text-primary shadow-md rounded-full roboto py-3 flex justify-center items-center gap-2 bg-slate-200 hover:bg-primary hover:text-slate-100">
                Read More <ion-icon name="arrow-forward-outline" class="text-[21px]"></ion-icon>
              </a>

              <!-- Date & User -->
              <div class="flex justify-between items-center mt-5">
                <div class="flex justify-start items-center">
                  <ion-icon name="calendar-outline" class="text-[20px] text-gray-500"></ion-icon>
                  <span class="text-base text-gray-800 roboto ml-2">'.$publishDate.'</span>
                </div>

                <!-- <div class="flex justify-start items-center">
                  <ion-icon name="chatbox-outline" class="text-[20px] text-gray-500"></ion-icon>
                  <span class="text-base text-gray-800 roboto ml-2">65 comments</span>
                </div> -->

                <div class="flex justify-start items-center">
                  <ion-icon name="person-outline" class="text-[20px] text-gray-500"></ion-icon>
                  <span class="text-base text-gray-800 roboto ml-2">'.$blog['author_name'].'</span>
                </div>
              </div>
            </div>
          </div>';
      }

      $html .= '</div>';

      return $html;
   }

   // Render post comments
   public function renderPostComments($comments)
   {
      if (empty($comments)) {
         return '<div class="h-[200px] w-full flex justify-center items-center gap-3 bg-white/50 rounded-md">
            <ion-icon name="warning-outline" class="text-[30px] text-gray-500"></ion-icon>
            <p class="text-center text-gray-500 roboto">No comments! Be the first to comment.</p>
         </div>';
      }

      $html = '';

      foreach ($comments as $comment) {
         // Human readable publishing date
         $publishDate = Carbon::parse($comment['created_at'])->format("dS M, Y");

         $totalReplies = count($comment['replies']);

         $html .= '<div class="flex gap-2 mb-5">
                     <!-- User Image -->
                     <div class="w-[40px] md:w-[60px]">
                        <img src="'. base_url("profile-avatar.png") .'" alt="user profile image" class="w-[30px] md:w-[50px] rounded-full">
                     </div>

                     <!-- Comment Description -->
                     <div class="flex-1">
                        <div class="flex flex-col md:flex-row md:gap-2 justify-start items-start">
                              <!-- User name -->
                              <h5 class="text-base text-gray-800 roboto font-bold mb-2 capitalize">'.$comment['name'].'</h5>

                              <!-- Comment time -->
                              <p class="text-base text-gray-500 font-normal">Posted on: <span class="">'.$publishDate.'</span></p>
                        </div>

                        <p class="text-sm md:text-base text-gray-800 roboto font-normal">'.$comment['comment'].'</p>

                        <!-- Reply & Linke Button -->
                        <div class="flex gap-3 justify-start items-center mt-3">
                              <a href="javascript:;" onclick="handleShowReplyForm(this, '.htmlspecialchars(json_encode($comment['id'])).')" class="text-base text-gray-500 roboto hover:underline hover:text-primary cursor-pointer flex gap-2 items-center">
                                 <ion-icon name="arrow-undo-outline"></ion-icon>
                                 Reply
                              </a>
                              <a href="'. base_url("") .'" class="text-base text-gray-500 roboto hover:underline hover:text-primary cursor-pointer flex gap-2 items-center">
                                 <ion-icon name="heart-outline"></ion-icon>
                                 Like
                              </a>
                              <a href="'. base_url("") .'" class="text-base text-gray-500 roboto hover:underline hover:text-primary cursor-pointer flex gap-2 items-center">
                                 <ion-icon name="chatbox-ellipses-outline"></ion-icon>
                                 '. $totalReplies .' Replies
                              </a>
                        </div>

                        <!-- Reply Form -->
                        <div id="replyForm_'.$comment['id'].'" class="show-reply-input mt-5 border border-borderColor rounded-md p-3 hidden">
                              <form id="replyCommentForm_'.$comment['id'].'" action="'. base_url('client/post_comment_reply/'. $comment['id']) .'" method="POST" class="handleCommentReply">
                                 <!-- Reply to user -->
                                 <h5 class="text-base text-primary roboto font-bold mb-2 capitalize underline">Reply to '. $comment['name'] .'</h5>
                                 <div class="flex gap-2">
                                    <!-- User Image -->
                                    <div class="w-[40px] md:w-[60px]">
                                          <img src="'. base_url("profile-avatar.png") .'" alt="user profile image" class="w-[30px] md:w-[50px] rounded-full">
                                    </div>

                                    <!-- Comment Description -->
                                    <div class="flex-1">
                                          '. (ClientAuth::isLoggedIn() ? '' : '
                                          <!-- Full Name & Email Inputs -->
                                          <div class="flex flex-col md:flex-row md:gap-2 justify-start items-start mb-3">
                                             <div class="form-group w-full">
                                                <!-- Full Name Input -->
                                                <input type="text" name="full_name" id="full_name" class="blog-input w-full border border-borderColor rounded-md p-3 roboto font-normal text-base text-gray-800" placeholder="Full Name">
                                             </div>

                                             <div class="form-group w-full">
                                                <!-- Email Input -->
                                                <input type="email" name="email" id="email" class="blog-input w-full border border-borderColor rounded-md p-3 roboto font-normal text-base text-gray-800" placeholder="Email">
                                             </div>
                                          </div>
                                          ') .'

                                          <div class="">
                                             <div class="form-group">
                                                <!-- Comment reply Input -->
                                                <textarea name="reply" id="reply" cols="30" rows="3" class="blog-input w-full border border-borderColor rounded-md p-3 roboto font-normal text-base text-gray-800" placeholder="Write a comment..."></textarea>
                                             </div>

                                             <div class="float-right flex justify-center items-center gap-2">
                                                <!-- Hide Form Button -->
                                                <button onclick="handleShowReplyForm(this, '.htmlspecialchars(json_encode($comment['id'])).')" type="button" class="text-base text-slate-100 px-4 py-1 rounded bg-slate-500 roboto hover:text-slate-100 cursor-pointer flex gap-2 items-center">
                                                      <ion-icon name="close-outline"></ion-icon>
                                                      Hide
                                                </button>

                                                <!-- Reply Button -->
                                                <button onclick="handleReplyComment(this, event, '.htmlspecialchars(json_encode($comment['id'])).')" type="submit" class="text-base text-slate-100 px-4 py-1 rounded bg-primary roboto hover:text-slate-100 cursor-pointer flex gap-2 items-center">
                                                      <ion-icon name="send-outline"></ion-icon>
                                                      Reply
                                                </button>
                                             </div>
                                          </div>
                                    </div>
                                 </div>
                              </form>
                        </div>

                        '.(count($comment["replies"]) > 0 ? '
                        <!-- Replies Section -->
                        <div class="mt-5 border border-borderColor rounded-md p-3">
                              '. $this->renderCommentReplies($comment["replies"]).'
                        </div>
                        ' : '').'

                     </div>
                  </div>';
      }


      return $html;
   }

   // Render comment replies
   public function renderCommentReplies($replies)
   {
      if (empty($replies)) {
         return;
      }

      $html = '';


      foreach ($replies as $key => $reply) {
         // Human readable publishing date
         $publishDate = Carbon::parse($reply['created_at'])->diffForHumans();

         // Apply border bottom class to comments except for the last one
         $borderClass = $key < count($replies) - 1 ? 'border-b border-borderColor pb-3 mb-3' : '';

         $html .= '<div class="flex gap-2 mb-5 '. $borderClass .'">
            <!-- User Image -->
            <div class="w-[40px] md:w-[60px]">
               <img src="'. base_url("profile-avatar.png") .'" alt="user profile image" class="w-[30px] md:w-[50px] rounded-full">
            </div>

            <!-- Comment Description -->
            <div class="flex-1">
               <div class="flex flex-col md:flex-row md:gap-2 justify-start items-start">
                     <!-- User name -->
                     <h5 class="text-base text-gray-800 roboto font-bold mb-2 capitalize">'.$reply["name"].'</h5>

                     <!-- Comment time -->
                     <p class="text-base text-gray-500 font-normal"><span class="font-normal">'.$publishDate.'</span></p>
               </div>

               <p class="text-sm md:text-base text-gray-800 roboto font-normal">'.$reply["reply"].'</p>

               <!-- Reply & Linke Button -->
               <div class="flex gap-2 justify-start items-center mt-3">
                     <a href="'. base_url("") .'" class="text-base text-gray-500 roboto hover:underline hover:text-primary cursor-pointer flex gap-2 items-center">
                        <ion-icon name="heart-outline"></ion-icon>
                        Like
                     </a>
               </div>
            </div>
         </div>';
      }

      return $html;
   }
}
