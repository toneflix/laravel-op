<?php

$files = glob(__DIR__ . '/../app/Constants/*.php');

foreach ($files as $file) {
    require_once $file;
}
