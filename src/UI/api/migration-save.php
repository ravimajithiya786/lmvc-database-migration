<?php

$data = json_decode(file_get_contents('php://input'), true);
$file = __DIR__ . "/../../../../storage/migrations/drafts/{$data['name']}.json";

file_put_contents($file, json_encode($data['schema'], JSON_PRETTY_PRINT));
