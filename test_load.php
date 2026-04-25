<?php
require 'vendor/autoload.php';
try {
    $app = require_once 'bootstrap/app.php';
    echo "OK";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
    error_log($e->getTraceAsString());
}
