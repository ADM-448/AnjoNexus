<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$keyAPI = env('OPEN_AI_KEY');
$response = Illuminate\Support\Facades\Http::get('https://generativelanguage.googleapis.com/v1beta/models?key=' . $keyAPI);
$array = $response->json();
print_r(array_map(fn($m) => $m['name'], $array['models'] ?? []));
