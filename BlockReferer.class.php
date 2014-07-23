<?php
/**
 * BlockReferer.class.php
 *
 * @author Nuno Barreto <nbarreto@gmail.com>
 */
class BlockReferer {
    private static function _remove_try($url) {
        $i = strpos($url, '&try=');
        if ($i > 0) {
            $url = substr($url, 0, $i);
        }
        return $url;
    }

    public static function getFullUrl() {
        $isHTTP = false;
        $isSSL = false;
        if (!empty($SERVER['HTTPS']) && $SERVER['HTTPS'] == 'on') {
            $isSSL = true;
        }

        // Detect which protocol is used. Only works for http and https
        $protocol = strtolower($_SERVER["SERVER_PROTOCOL"]);
        $protocol = substr($protocol, 0, strpos($protocol, '/'));
        if ($protocol == 'http' && $isSSL) {
            $protocol .= 's';
        } elseif ($protocol == 'http') {
            $isHTTP = true;
        }

        // Detect if it's not using standard http/https ports
        $port = $_SERVER["SERVER_PORT"];
        if ($port == '800' && $isHTTP == true) {
            $port = '';
        } elseif ($port == '443' && $isSSL) {
            $port = '';
        }
        if ($port != '') {
            $port = ':'.$port;
        }

        $domain = $_SERVER["SERVER_NAME"];
        $uri = $_SERVER["REQUEST_URI"];


        // Compile complete url
        $fullUrl =  $protocol . "://" . $domain . $port. $uri;

        return $fullUrl;
    }

    public static function getTargetUrl($fullUrl='') {
        if ($fullUrl == '') {
            $fullUrl = self::getFullUrl();
        }

        $targetUrl = '';
        $start = strpos($fullUrl, '?');
        if ($start > 0) {
            $targetUrl = substr($fullUrl, $start + 1);
            $targetUrl = self::_remove_try($targetUrl);
        }

        return $targetUrl;
    }

    public static function refererRedirect($targetUrl) {
        if (!(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '')) {
            header('Location: ' . $targetUrl);
            exit;
        }
    }

    public static function refreshMethod($fullUrl, $targetUrl) {
        self::refererRedirect($targetUrl);
        $html = '<meta http-equiv="refresh" content="0; url=' . $fullUrl . '&try=1">';
        echo $html;
    }

    public static function formMethod($fullUrl, $targetUrl) {
        self::refererRedirect($targetUrl);
        $html  = '<script> function go(){ window.frames[0].document.body.innerHTML=\'<form target="_parent" method="post" action="' . $fullUrl . '&try=2">';
        $html .= '</form>\'; window.frames[0].document.forms[0].submit() } </script> <iframe onload="window.setTimeout(\'go()\', 99)" src="about:blank" style="visibility:hidden"></iframe>';
        echo $html;
    }

    public static function iframeMethod($fullUrl, $targetUrl) {
        self::refererRedirect($targetUrl);
        $html = '<iframe src="javascript:parent.location=\'' . $fullUrl . '&try=3\'" style="visibility:hidden"></iframe>';
        echo $html;
    }

    public static function giveUp($targetUrl, $fallback='') {
        self::refererRedirect($targetUrl);

        // If no fallback set, go to target url
        if ($fallback = '') {
            $fallback = $targetUrl;
        }
        // If still has referer, send to fallback
        header('Location: ' . $fallback);
    }

    public static function allMethods($fullUrl, $targetUrl, $fallback='') {
        $try = 0;
        if (isset($_REQUEST['try'])) {
            $try = $_REQUEST['try'];
        }

        switch ($try) {
            case 1:
                BlockReferer::formMethod($fullUrl, $targetUrl);
                break;

            case 2:
                BlockReferer::iframeMethod($fullUrl, $targetUrl);
                break;

            case 3:
                BlockReferer::giveUp($targetUrl, $fallback);
                break;

            case 0:
            default:
                echo BlockReferer::refreshMethod($fullUrl, $targetUrl);
                break;
        }
    }

}

?>
