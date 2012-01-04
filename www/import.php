<?php
require_once '../include/php/DataImport.php';

echo date('Y-m-d H:i:s')."<br>\n";
DataImport::importLeaderUnitFile('/var/www/unpc.benreece.org/data/AllAdults.csv');
echo date('Y-m-d H:i:s')."<br>\n";
DataImport::importLeaderTrainingFile('/var/www/unpc.benreece.org/data/TrainingCourseExport.csv');
echo date('Y-m-d H:i:s')."<br>\n";
