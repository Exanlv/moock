<?php

declare(strict_types=1);

use Exan\Pudocumenter\Documenter;
use Exan\Pudocumenter\StdOutMarkdownPrinter;
use Tests\Documentation\AssertingExpectationsTest;
use Tests\Documentation\BasicMockingTest;
use Tests\Documentation\FilteringMethodsTest;
use Tests\Documentation\PartialMocksTest;
use Tests\Documentation\ReplacingMethodTest;

require './vendor/autoload.php';

$documenter = new Documenter(
    BasicMockingTest::class,
    ReplacingMethodTest::class,
    AssertingExpectationsTest::class,
    FilteringMethodsTest::class,
    PartialMocksTest::class,
);

$documenter->document(new StdOutMarkdownPrinter());
