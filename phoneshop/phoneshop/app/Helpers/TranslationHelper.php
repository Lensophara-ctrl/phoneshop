<?php

if (!function_exists('__t')) {
    /**
     * Translate the given message with fallback.
     *
     * @param  string  $key
     * @param  array   $replace
     * @param  string|null  $locale
     * @return string
     */
    function __t($key, $replace = [], $locale = null)
    {
        return __('messages.' . $key, $replace, $locale);
    }
}
