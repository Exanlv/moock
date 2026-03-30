<?php

declare(strict_types=1);

use Exan\Moock\MoockAssert;

$ecm = [
    '\PHPUnit\Framework\Assert' => 'assertEquals',
    '\Tester\Assert' => 'same',
];

foreach ($ecm as $class => $method) {
    if (class_exists($class)) {
        MoockAssert::useAssert($class::$method(...));
        return;
    }
}
