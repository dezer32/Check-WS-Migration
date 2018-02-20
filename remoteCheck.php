<?php
require_once __DIR__ . '/CheckMigrations.php';
$checkMigrations = new CheckMigrations('http://chetv.local');
$checkMigrations->getRemoteDiff();