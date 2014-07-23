<!DOCTYPE html>
<html>
<head>
    <title>BlockReferer Test</title>
</head>

<body>
    <pre>
<?php
    if (isset($_SERVER['HTTP_REFERER'])) {
        echo('Referer: '.$_SERVER['HTTP_REFERER']);
    } else {
        echo ('Yes! No Referer!');
    }

?>
    </pre>
</body>
</html>
