<?php

declare(strict_types=1);

namespace Tests\Analyzer;

use Exan\Moock\Analyzer\Yanker;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class YankerTest extends AnalyzerTestCase
{
    #[Test]
    public function it_captures_namespace_and_imports()
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
        PHP);

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
    public function it_extracts_method_args_from_classes()
    {
        $yanked = Yanker::fetch(<<<PHP
            <?php

            class MyClass
            {
                public function myMethod(string \$arg = 'test'): void
                {
                    if (true) {
                        return;
                    }
                }
            }
        PHP, ['MyClass.myMethod']);

        dd($yanked);
    }
}
