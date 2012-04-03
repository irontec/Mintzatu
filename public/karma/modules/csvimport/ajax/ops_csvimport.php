<?php
require_once('../class.CsvImportBackend.php');

$csvImport = new CsvImportBackend(isset($_GET['upload']));

