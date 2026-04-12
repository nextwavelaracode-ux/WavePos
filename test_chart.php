<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$f = new App\Http\Controllers\FinanzasController();
$r = new Illuminate\Http\Request();
$res = $f->index($r);
file_put_contents('test_chart.json', json_encode($res->gatherData()['graficaPeriodos']));
echo "Done";
