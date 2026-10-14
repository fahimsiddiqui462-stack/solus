<?php
require_once __DIR__ . '/../config/config.php';
header('Cache-Control: no-store');
json_response(['ok' => true, 'csrf' => csrf_token()]);
