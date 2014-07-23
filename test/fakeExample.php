<?php
    require_once('../BlockReferer.class.php');

    $fullUrl   = BlockReferer::getFullUrl();
    $targetUrl = BlockReferer::getTargetUrl($fullUrl);

    $referer = 'http://www.mobyd.com';
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    
    BlockReferer::fakeReferer($targetUrl, $referer, $userAgent);
?>
