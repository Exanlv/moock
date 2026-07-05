<?php

declare(strict_types=1);

namespace Tests\Analyzer;

use Exan\Moock\Analyzer\Yanker;
use PHPUnit\Framework\Attributes\Test;

class YankerTest extends AnalyzerTestCase
{
    #[Test]
    public function it_captures_namespace_and_imports(): void
    {
        $yanked = Yanker::fetch(<<<PHP
                <?php

                declare(strict_types=1);

                namespace Some\Cool\Namespace;

                use Some\Namespaced\Class;
                use Some\OtherClass\Too as /* Why would you put a comment here? */ SomeAlias;
                use A\Third\Class\{
                    // There's even a comment here
                    With\Funky\Syntax as WayToMakeYouCry,
                    AndMore,
                };

                class SomeClass {
                    use ShouldntBeCaptured;
                }

                use Some\Import\In\Wrong\But\Valid\Place;
            PHP, ['SomeClass', 'someMethod', '$arg']);

        $this->assertContainsTokenAnywhere('Some\Cool\Namespace', $yanked->namespace);

        $this->assertContainsTokenAnywhere('Some\Namespaced\Class', $yanked->uses);

        $this->assertContainsTokenAnywhere('Some\OtherClass\Too', $yanked->uses);
        $this->assertContainsTokenAnywhere('SomeAlias', $yanked->uses);

        $this->assertContainsTokenAnywhere('A\Third\Class', $yanked->uses);
        $this->assertContainsTokenAnywhere('With\Funky\Syntax', $yanked->uses);
        $this->assertContainsTokenAnywhere('WayToMakeYouCry', $yanked->uses);
        $this->assertContainsTokenAnywhere('AndMore', $yanked->uses);

        $this->assertDoesntContainTokenAnywhere('ShouldntBeCaptured', $yanked->uses);

        $this->assertContainsTokenAnywhere('Some\Import\In\Wrong\But\Valid\Place', $yanked->uses);

        $this->assertDoesntContainTokenAnywhere('/* Why would you put a comment here? */', $yanked->uses);
        $this->assertDoesntContainTokenAnywhere('// There\'s even a comment here', $yanked->uses);

        foreach ($yanked->uses as $import) {
            $this->assertEquals(T_USE, $import[0][0]);
            $this->assertEquals(';', $import[count($import) - 1]);
        }
    }

    #[Test]
    public function it_extracts_method_args_from_classes(): void
    {
        $yanked = Yanker::fetch(<<<PHP
                <?php

                class MyClass
                {
                    public function myMethod(string \$arg /* Some comment */ = /* More comment */ 'test'): void
                    {
                        if (true) {
                            return;
                        }
                    }
                }

                class OtherClass
                {
                    public function myMethod(string \$arg = 'not-test'): void
                    {
                        if (true) {
                            return;
                        }
                    }
                }
            PHP, ['MyClass', 'myMethod', '$arg']);

        $this->assertEquals([[
            T_CONSTANT_ENCAPSED_STRING,
            "'test'",
            5,
        ]], $yanked->args);
    }

    #[Test]
    public function it_extracts_args_not_at_the_start_of_method(): void
    {
        $yanked = Yanker::fetch(<<<PHP
                <?php

                class MyClass
                {
                    public function myMethod(string \$other = 'something', string \$arg = new Something(new OtherThing())): void
                    {
                        if (true) {
                            return;
                        }
                    }
                }
            PHP, ['MyClass', 'myMethod', '$arg']);

        $this->assertEquals('new Something(new OtherThing())', $this->implode($yanked->args));
    }

    #[Test]
    public function it_extracts_args_not_at_the_start_or_end(): void
    {
        $yanked = Yanker::fetch(<<<PHP
                <?php

                class MyClass
                {
                    public function myMethod(string \$other = 'something', string \$arg = new Something('class MyClass'), string \$somethingElse = 'dummy'): void
                    {
                        if (true) {
                            return;
                        }
                    }
                }
            PHP, ['MyClass', 'myMethod', '$arg']);

        $this->assertEquals('new Something(\'class MyClass\')', $this->implode($yanked->args));
    }

    #[Test]
    public function it_extracts_args_from_anonymous_classes(): void
    {
        $yanked = Yanker::fetch(<<<PHP
                <?php

                \$instance = new class () {
                    public function myMethod(string \$other = 'something', string \$arg = new Something('class MyClass'), string \$somethingElse = 'dummy'): void
                    {
                        if (true) {
                            return;
                        }
                    }
                };
            PHP, ['3$0', 'myMethod', '$arg']);

        $this->assertEquals('new Something(\'class MyClass\')', $this->implode($yanked->args));
    }

    #[Test]
    public function it_extracts_args_from_the_correct_anonymous_class_on_single_line(): void
    {
        $code = '<?php '
            . '$instance = new class () {'
            . 'public function myMethod(string $arg = "test") { }'
            . '};' // End of first anonymous class
            . '$other = new class () {'
            . 'public function myMethod(string $arg = "expected") { }'
            . '};' // End of second anonymous class
            . '$yetAnother = new class () {'
            . 'public function myMethod(string $arg = "test") { }'
            . '};' // End of third anonymous class
        ;

        $yanked = Yanker::fetch($code, ['1$1', 'myMethod', '$arg']);

        $this->assertEquals([[
            T_CONSTANT_ENCAPSED_STRING,
            '"expected"',
            1,
        ]], $yanked->args);
    }

    #[Test]
    public function it_extracts_args_from_the_correct_anonymous_class_on_separated_lines(): void
    {
        $yanked = Yanker::fetch(<<<PHP
                <?php

                \$instance = new class () {
                    public function myMethod(string \$arg = 'something'): void
                    {
                        if (true) {
                            return;
                        }
                    }
                };

                \$other = new class () {
                    public function myMethod(string \$arg = 'expected'): void
                    {
                        if (true) {
                            return;
                        }
                    }
                };
            PHP, ['12$0', 'myMethod', '$arg']);

        $this->assertEquals([[
            T_CONSTANT_ENCAPSED_STRING,
            "'expected'",
            13,
        ]], $yanked->args);
    }
}
