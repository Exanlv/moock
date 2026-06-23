<?php

declare(strict_types=1);

namespace Tests\Components;

use Tests\Components\MixedConstructor;

class InstantiatedDefaultArgs
{
    public function methodDefaultEmpty(MixedConstructor $prop = new MixedConstructor()): bool
    {
        return false;
    }

    public function methodDefaultString(MixedConstructor $prop = new MixedConstructor('::my string::')): bool
    {
        return false;
    }

    public function methodDefaultRecursiveEmpty(MixedConstructor $prop = new MixedConstructor(new MixedConstructor())): bool
    {
        return false;
    }

    public function methodDefaultRecursiveWithString(MixedConstructor $prop = new MixedConstructor(new MixedConstructor('::nested string::'))): bool
    {
        return false;
    }

    public function methodDefaultPhpVariableSyntaxString(MixedConstructor $prop = new MixedConstructor('$variable')): bool
    {
        return false;
    }

    public function methodDefaultPhpTagSyntaxString(MixedConstructor $prop = new MixedConstructor('<?php echo "test"; ?>')): bool
    {
        return false;
    }

    public function methodDefaultNewKeywordInString(MixedConstructor $prop = new MixedConstructor('new ClassName()')): bool
    {
        return false;
    }
}
