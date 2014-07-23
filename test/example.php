<pre>
<?php
    require_once('../BlockReferer.class.php');

    $fullUrl    = BlockReferer::getFullUrl();
    $targetUrl  = BlockReferer::getTargetUrl($fullUrl);

    BlockReferer::allMethods($fullUrl, $targetUrl);

// test with http://10.0.0.169/~nbarr/block-referer-php/example.php?http://go.ydigitalmedia.com/br-154-skyoutbound-114-1-TESTEREFERER

    /*var_dump($fullUrl);
    var_dump($targetUrl);die();*/

/*    if (!isset($_REQUEST['attempt'])) {
        $_REQUEST['attempt'] = 0;
    }

    switch ($_REQUEST['attempt']) {
        case 0:
            echo BlockReferer::refreshMethod($fullUrl, $targetUrl);
            break;

        case 1:
            echo BlockReferer::formMethod($fullUrl, $targetUrl);
            break;

        case 2:
            echo BlockReferer::iframeMethod($fullUrl, $targetUrl);
            break;

        case 3:
            echo BlockReferer::giveUp($targetUrl);
            break;

        default:
            echo BlockReferer::refreshMethod($fullUrl, $targetUrl);
    }*/

/*
<a href="http://go.ydigitalmedia.com/br-154-skyoutbound-114-1-TESTEREFERER">With Referrer</a><br />
<a rel="noreferrer" href="http://go.ydigitalmedia.com/br-154-skyoutbound-114-1-TESTEREFERER">Without Referrer</a><br />
<a href="http://10.0.0.169/~nbarr/adserver/trunk/www/redirect/?http://go.ydigitalmedia.com/br-154-skyoutbound-114-1-TESTEREFERER">With redirect</a><br />
*/
?>
</pre>
