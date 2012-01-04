<?php
require_once '../include/php/config.inc';
require_once '../include/php/DataImport.php';

$file = $argv[1];
$table = $argv[2];

DataImport::importFile($file, $table);
