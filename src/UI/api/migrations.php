<?php

$draftPath = __DIR__ . '/../../../../storage/migrations/drafts';

$files = glob($draftPath . '/*.json');
echo json_encode(array_map(fn($f) => basename($f, '.json'), $files));
