<?php
    require_once('../BlockReferer.class.php');

    $fullUrl    = BlockReferer::getFullUrl();
    $targetUrl  = BlockReferer::getTargetUrl($fullUrl);

    BlockReferer::allBlankMethods($fullUrl, $targetUrl);
?>
