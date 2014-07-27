block-referer-php
=================

A PHP lib for masking referer. Two strategies are in use: blanking it with javascript and redirects, and faking it with curl.

Blank Referer Usage:

```php
<?php
  require_once('BlockReferer.class.php');
  
  BlockReferer::allBlankMethods();
?>
```

Fake Referer Usage:

```php
<?php
  require_once('../BlockReferer.class.php');
  
  $referer = 'http://www.example.com';
  $userAgent = $_SERVER['HTTP_USER_AGENT'];
  
  BlockReferer::fakeReferer('', $referer, $userAgent);
?>
```

Example:

[http://example.com/test/example.php?http://targeturl.com](http://example.com/test/example.php?http://targeturl.com)

