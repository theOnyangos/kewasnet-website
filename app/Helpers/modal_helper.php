<?php

if (!function_exists('modal_trigger')) {
    function modal_trigger($text, $options = []) {
        $defaults = [
            'class' => 'btn btn-primary',
            'onclick' => 'openModal()'
        ];
        $options = array_merge($defaults, $options);
        
        $attrs = '';
        foreach ($options as $key => $value) {
            if ($key !== 'text') {
                $attrs .= " $key=\"$value\"";
            }
        }
        
        return "<button $attrs>$text</button>";
    }
}