<?php
/**
 * BlockReferer.class.php
 *
 * @author Nuno Barreto <nbarreto@gmail.com>
 */
class BlockReferer {
  /**
   * Remove 'try' parameter from a url.
   * 'try' is used whenever we need to use more than one blank referer method.
   *
   * @param string $url Url to remove try parameter.
   *
   * @return string Url without try parameter.
   */
   private static function _remove_try($url) {
        $i = strpos($url, '&try=');
        if ($i > 0) {
            $url = substr($url, 0, $i);
        }
        return $url;
    }

    /**
     * Get full url.
     *
     * @return string Full url
     */
    public static function getFullUrl() {
        $isHTTP = false;
        $isSSL = false;
        if (!empty($SERVER['HTTPS']) && $SERVER['HTTPS'] == 'on') {
            $isSSL = true;
        }

        // Detect which protocol is used. Only works for http and https.
        $protocol = strtolower($_SERVER["SERVER_PROTOCOL"]);
        $protocol = substr($protocol, 0, strpos($protocol, '/'));
        if ($protocol == 'http' && $isSSL) {
            $protocol .= 's';
        } elseif ($protocol == 'http') {
            $isHTTP = true;
        }

        // Detect if it's not using standard http/https ports.
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

    /**
     * Get target url.
     *
     * @param string $fullUrl Url from which to retrieve the target url.
     *
     * @return string Target Url.
     */
    public static function getTargetUrl($fullUrl='') {
        // When no full url is provided, get it.
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

    /**
     * Redirect when referer is empty. When referer exists, do nothing.
     *
     * @param string $targetUrl Url to remove try parameter.
     */
    public static function refererRedirect($targetUrl) {
        if (!(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '')) {
            header('Location: ' . $targetUrl);
            exit;
        }
    }

    /**
     * Refresh Blank Method: try to blank referer by using a meta refresh
     *
     * @param string $fullUrl Full Url.
     * @param string $targetUrl Target Url.
     */
    public static function refreshBlankMethod($fullUrl, $targetUrl) {
        self::refererRedirect($targetUrl);
        $html = '<meta http-equiv="refresh" content="0; url=' . $fullUrl . '&try=1">';
        echo $html;
    }

    /**
     * Form Blank Method: try to blank referer by using a form submit
     *
     * @param string $fullUrl Full Url.
     * @param string $targetUrl Target Url.
     */
    public static function formBlankMethod($fullUrl, $targetUrl) {
        self::refererRedirect($targetUrl);
        $html  = '<script> function go(){ window.frames[0].document.body.innerHTML=\'<form target="_parent" method="post" action="' . $fullUrl . '&try=2">';
        $html .= '</form>\'; window.frames[0].document.forms[0].submit() } </script> <iframe onload="window.setTimeout(\'go()\', 99)" src="about:blank" style="visibility:hidden"></iframe>';
        echo $html;
    }

    /**
     * Iframe Blank Method: try to blank referer by using a iframe
     *
     * @param string $fullUrl Full Url.
     * @param string $targetUrl Target Url.
     */
    public static function iframeBlankMethod($fullUrl, $targetUrl) {
        self::refererRedirect($targetUrl);
        $html = '<iframe src="javascript:parent.location=\'' . $fullUrl . '&try=3\'" style="visibility:hidden"></iframe>';
        echo $html;
    }

    /**
     * When no method is able to blank the referer, this should be called.
     * There are two options: Go to the target url with referer set,
     * or use a fallback url of your choosing.
     *
     * @param string $targetUrl Target Url.
     * @param string $fallback Fallback Url.
     *
     * @return string Url without try parameter.
     */
    public static function giveUp($targetUrl, $fallback='') {
        self::refererRedirect($targetUrl);

        // If no fallback url set, go to target url
        if ($fallback = '') {
            $fallback = $targetUrl;
        }
        // If still has referer, send to fallback
        header('Location: ' . $fallback);
    }

    /**
     * Try all blank methods available.
     *
     * @param string $fullUrl Full url.
     * @param string $targetUrl Target url.
     * @param string $fallback Fallback url.
     */
    public static function allBlankMethods($fullUrl='', $targetUrl='', $fallback='') {
        if ($fullUrl == '') {
            $fullUrl = self::getFullUrl();
        }
        if ($targetUrl == '') {
            $targetUrl = self::getTargetUrl($fullUrl);
        }

        $try = 0;
        if (isset($_REQUEST['try'])) {
            $try = $_REQUEST['try'];
        }

        switch ($try) {
            case 1:
                BlockReferer::formBlankMethod($fullUrl, $targetUrl);
                break;

            case 2:
                BlockReferer::iframeBlankMethod($fullUrl, $targetUrl);
                break;

            case 3:
                BlockReferer::giveUp($targetUrl, $fallback);
                break;

            case 0:
            default:
                echo BlockReferer::refreshBlankMethod($fullUrl, $targetUrl);
                break;
        }
    }

    /**
     * Try to fake the referer. See http://kbeezie.com/cloaking-and-faking-referrer/2/
     *
     * @param string $targetUrl Target url.
     * @param string $fakeReferer Fake Referer.
     * @param string $userAgent User Agent to use. Browser user agent prefered.
     */
    function fakeReferer($targetUrl, $fakeReferer, $userAgent) {
        $curl = curl_init();
        $header[0]  = "Accept: text/xml,application/xml,application/xhtml+xml,";
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $header[]   = "Cache-Control: max-age=0";
        $header[]   = "Connection: keep-alive";
        $header[]   = "Keep-Alive: 300";
        $header[]   = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[]   = "Accept-Language: en-us,en;q=0.5";
        $header[]   = "Pragma: "; // browsers keep this blank.

        curl_setopt($curl, CURLOPT_URL, $targetUrl);
        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_REFERER, $fakeReferer);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5000);

        // @todo: follow redirects
        // @todo: see http://stackoverflow.com/questions/3519939/make-curl-follow-redirects
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $html = curl_exec($curl);
        curl_close($curl);

        // returns the content provided by the site
        echo $html;
    }
}

?>
