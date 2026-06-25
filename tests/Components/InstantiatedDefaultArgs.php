<?php

declare(strict_types=1);

namespace Tests\Components;

use Tests\Components\{MixedConstructor, MixedConstructor as AliasedMC, MixedConstructor as AliasedMCTwo};
use Tests\Components\MixedConstructor as IndividualAliasMC;
use Tests\{Components\MixedConstructor as SubNamespaceMC};

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

    public function methodAliasedEmpty(AliasedMC $prop = new AliasedMC()): bool
    {
        return false;
    }

    public function methodAliasedWithString(AliasedMC $prop = new AliasedMC('::aliased string::')): bool
    {
        return false;
    }

    public function methodAliasedRecursive(AliasedMC $prop = new AliasedMC(new AliasedMCTwo())): bool
    {
        return false;
    }

    public function methodAliasedRecursiveWithString(AliasedMC $prop = new AliasedMC(new AliasedMCTwo('::aliased nested string::'))): bool
    {
        return false;
    }

    public function methodIndividualAliasEmpty(IndividualAliasMC $prop = new IndividualAliasMC()): bool
    {
        return false;
    }

    public function methodIndividualAliasWithString(IndividualAliasMC $prop = new IndividualAliasMC('::individual alias string::')): bool
    {
        return false;
    }

    public function methodSubNamespaceGroupUseEmpty(SubNamespaceMC $prop = new SubNamespaceMC()): bool
    {
        return false;
    }

    public function methodSubNamespaceGroupUseWithString(SubNamespaceMC $prop = new SubNamespaceMC('::sub namespace string::')): bool
    {
        return false;
    }
}
