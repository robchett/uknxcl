<?php
require $_SERVER['DOCUMENT_ROOT'] . '/.core/config.php';
ini_set('phar.readonly', 0);
$phar = new Phar('application.phar');
$phar->startBuffering();
$phar['index.php'] = 'index.php';

$files = \classes\get::recursive_glob(root . '/inc', '*.*');
foreach ($files as $file) {
    $phar[str_replace(root, '', $file)] = file_get_contents($file);
}

$files = \classes\get::recursive_glob(root . '/js', '*.*');
foreach ($files as $file) {
    $phar[str_replace(root, '', $file)] = file_get_contents($file);
}

$files = \classes\get::recursive_glob(root . '/css', '*.*');
foreach ($files as $file) {
    $phar[str_replace(root, '', $file)] = file_get_contents($file);
}

$phar['index.php'] = '<?php require $_SERVER[\'DOCUMENT_ROOT\'] . \'/.core/config.php\';';
$phar->stopBuffering();
$phar->createDefaultStub('index.php');