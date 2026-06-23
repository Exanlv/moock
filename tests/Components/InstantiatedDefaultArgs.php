<?php

declare(strict_types=1);

namespace Tests\Components;

use Tests\Components\MixedConstructor;

class InstantiatedDefaultArgs
{
    public function methodDefaultEmpty(MixedConstructor $prop = new MixedConstructor())
    {
    }

    public function methodDefaultString(MixedConstructor $prop = new MixedConstructor('::my string::'))
    {
    }

    public function methodDefaultRecursiveEmpty(MixedConstructor $prop = new MixedConstructor(new MixedConstructor()))
    {
    }

    public function methodDefaultRecursiveWithString(MixedConstructor $prop = new MixedConstructor(new MixedConstructor('::nested string::')))
    {
    }

    public function methodDefaultPhpVariableSyntaxString(MixedConstructor $prop = new MixedConstructor('$variable'))
    {
    }

    public function methodDefaultPhpTagSyntaxString(MixedConstructor $prop = new MixedConstructor('<?php echo "test"; ?>'))
    {
    }

    public function methodDefaultNewKeywordInString(MixedConstructor $prop = new MixedConstructor('new ClassName()'))
    {
    }
}
