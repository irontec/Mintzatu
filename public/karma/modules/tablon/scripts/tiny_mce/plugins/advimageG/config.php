<?php

$legacyPath = '../../../../../../../tinyCache/';

if (is_dir($legacyPath)) {
    $tinycachePath = $legacyPath;
} else {
    $tinycachePath = '../../../../../../../cache/tinyCache/';
}

define('CACHE', $tinycachePath);




?>
