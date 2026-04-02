<?php

declare(strict_types=1);

use Exan\Pudocumenter\Documenter;
use Exan\Pudocumenter\StdOutMarkdownPrinter;
use Exan\Pudocumenter\StdOutMarkdownTOCPrinter;
use Tests\Documentation\AssertingExpectationsTest;
use Tests\Documentation\BasicMockingTest;
use Tests\Documentation\CompatibilityTest;
use Tests\Documentation\OrderExpectationTest;
use Tests\Documentation\FilteringMethodsTest;
use Tests\Documentation\PartialMocksTest;
use Tests\Documentation\ReplacingMethodTest;

require './vendor/autoload.php';

$documenter = new Documenter(
    BasicMockingTest::class,
    ReplacingMethodTest::class,
    AssertingExpectationsTest::class,
    OrderExpectationTest::class,
    FilteringMethodsTest::class,
    PartialMocksTest::class,
    CompatibilityTest::class,
);

echo '# Table of contents', PHP_EOL, PHP_EOL;

$documenter->document(new StdOutMarkdownTOCPrinter());

echo PHP_EOL;

$documenter->document(new StdOutMarkdownPrinter());
