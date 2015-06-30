<?php
require_once 'core/PaizaTester.php';
$code = trim(fgets(STDIN));
new PaizaTester($code);