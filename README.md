block-referer-php
=================

A PHP lib for blocking referer

Blank Referer Usage:

```php
require_once('BlockReferer.class.php');

BlockReferer::allBlankMethods();
```

Fake Referer Usage:

```php
require_once('../BlockReferer.class.php');
$referer = 'http://www.example.com';
$userAgent = $_SERVER['HTTP_USER_AGENT'];
BlockReferer::fakeReferer('', $referer, $userAgent);
```

Example:

[http://example.com/test/example.php?http://targeturl.com]

