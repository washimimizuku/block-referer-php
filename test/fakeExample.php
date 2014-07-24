<?php
    require_once('../BlockReferer.class.php');

    $referer = 'http://www.example.com';
    $userAgent = $_SERVER['HTTP_USER_AGENT'];

    BlockReferer::fakeReferer('', $referer, $userAgent);
?>
