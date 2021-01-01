<?php
/** @noinspection PhpIncludeInspection */
require_once '../../../../vendor/autoload.php';

use CarloNicora\Minimalism\Minimise\Models\Minimise;
use CarloNicora\Minimalism\Minimalism;

$minimalism = new Minimalism();
echo $minimalism->render(Minimise::class);