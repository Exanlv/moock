<?php

declare(strict_types=1);

use Exan\Pudocumenter\Documenter;
use Exan\Pudocumenter\StdOutMarkdownPrinter;
use Exan\Pudocumenter\StdOutMarkdownTOCPrinter;
use Tests\Documentation\HelperGenerators\ArrayTest;
use Tests\Documentation\HelperGenerators\DateTest;
use Tests\Documentation\HelperGenerators\NumberTest;
use Tests\Documentation\HelperGenerators\StrTest;
use Tests\Documentation\HelperGenerators\TypeTest;

require './vendor/autoload.php';

$documenter = new Documenter(
    StrTest::class,
    NumberTest::class,
    DateTest::class,
    TypeTest::class,
    ArrayTest::class,
);

echo '# Table of contents', PHP_EOL, PHP_EOL;

$documenter->document(new StdOutMarkdownTOCPrinter());

echo PHP_EOL;

$documenter->document(new StdOutMarkdownPrinter());
