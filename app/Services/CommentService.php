<?php

namespace App\Services;

use App\Models\BlogComment;

class CommentService
{
   protected $commentModel;

   public function __construct()
   {
      $this->commentModel = new BlogComment();
   }
   //fetch blog post comment
   public function getPostComments($postId)
   {
      return $this->commentModel->getPostComments($postId);
   }
   //save comments to database
   public function insertComment($data)
   {
      $this->commentModel->insert($data);
   }
}
