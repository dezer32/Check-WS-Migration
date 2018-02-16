<?php
require_once __DIR__ . '/CheckMigrations.php';
$checkMigrations = new CheckMigrations();
$checkMigrations->getRemoteDiff();