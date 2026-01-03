<?php

namespace App\Validation;

class ValidateTags
{
    public function validateTags($tagsJson)
    {
        if (empty($tagsJson)) return true;
        
        $tags = json_decode($tagsJson, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }
        
        if (!is_array($tags)) {
            return false;
        }
        
        if (count($tags) > 5) {
            $this->validator->setError('tags', 'Maximum 5 tags allowed');
            return false;
        }
        
        foreach ($tags as $tag) {
            if (strlen($tag) > 50) {
                $this->validator->setError('tags', 'Each tag must be less than 50 characters');
                return false;
            }
        }
        
        return true;
    }
}
