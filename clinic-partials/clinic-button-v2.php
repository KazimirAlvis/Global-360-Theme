<?php
$assessment_site_id = function_exists('cpt360_get_assessment_id') ? cpt360_get_assessment_id() : '';
$assessment_label = 'Take Risk Assessment Now';
require __DIR__ . '/assessment-questionnaire.php';
