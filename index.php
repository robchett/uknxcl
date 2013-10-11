<?php
try {
    include $_SERVER['DOCUMENT_ROOT'] . '/.core/config.php';
} catch (Exception $e) {
    echo $e->getMessage();
}
