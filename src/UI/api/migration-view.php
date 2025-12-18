<?php

$data = json_decode(file_get_contents('php://input'), true);
$file = __DIR__ . "/../../../../storage/migrations/drafts/{$data['name']}.json";

echo file_exists($file)
    ? file_get_contents($file)
    : json_encode([]);
