block-referer-php
=================

A PHP lib for blocking referer

Usage:

    <?php
    require_once('BlockReferer.class.php');
      
    $fullUrl    = BlockReferer::getFullUrl();
    $targetUrl  = BlockReferer::getTargetUrl($fullUrl);
      
    BlockReferer::allBlankMethods($fullUrl, $targetUrl);
    ?>
