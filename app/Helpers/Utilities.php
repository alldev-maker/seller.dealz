<?php

if (!function_exists('random_string')) {


    function random_string($length = 64)
    {
        try {
            $token      = '';
            $code_alpha = '';

            $code_alpha .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $code_alpha .= "abcdefghijklmnopqrstuvwxyz";
            $code_alpha .= "0123456789";

            $max = strlen($code_alpha);

            for ($i = 0; $i < $length; $i++) {
                $token .= $code_alpha[random_int(0, $max - 1)];
            }

            return $token;
        } catch (Exception $e) {
            return '';
        }
    }

}