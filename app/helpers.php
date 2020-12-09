<?php

if (! function_exists('whatsapp_link')) {
    function whatsapp_link(string $value, ?string $label = null, ?string $text = null): string
    {
        // See https://medium.com/@jeanlivino/how-to-fix-whatsapp-api-in-desktop-browsers-fc661b513dc
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $iphone = strpos($user_agent, 'iPhone');
        $android = strpos($user_agent, 'Android');
        $palmpre = strpos($user_agent, 'webOS');
        $berry = strpos($user_agent, 'BlackBerry');
        $ipod = strpos($user_agent, 'iPod');
        $chrome = strpos($user_agent, 'Chrome');
        if ($android || $iphone) {
            $prefix = '<a href="whatsapp://send?phone=';
        } elseif ($palmpre || $ipod || $berry || $chrome) {
            $prefix = '<a href="https://api.whatsapp.com/send?phone=';
        } else {
            $prefix = '<a target="_blank" href="https://web.whatsapp.com/send?phone=';
        }
        $suffix = $text !== null ? '&text=' . urlencode($text) : '';
        return $prefix . preg_replace('/[^0-9]/', '', $value) . $suffix . '">' . ($label ?? $value) . '</a>';
    }
}
