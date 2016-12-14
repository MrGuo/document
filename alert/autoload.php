<?php
function seaslog_autoloader($class) {

    if (!class_exists($class)) {
        $filename = BASE_PATH . '/' . str_replace('\\', '/', $class) . '.php';
        include_once "$filename";
    }
}

spl_autoload_register('seaslog_autoloader');
