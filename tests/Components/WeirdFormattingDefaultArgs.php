<?php

declare(strict_types=1);

namespace Tests\Components;

use Tests\Components\MixedConstructor;

// phpcs:ignoreFile
class WeirdFormattingDefaultArgs
{
    public function methodExtraSpacesAroundEquals(MixedConstructor $prop   =   new MixedConstructor()): bool
    {
        return false;
    }

    public function methodMultilineSignature(
        MixedConstructor $prop = new MixedConstructor()
    ): bool {
        return false;
    }

    public function methodMultilineSignatureWithString(
        MixedConstructor $prop = new MixedConstructor('::multiline string::')
    ): bool {
        return false;
    }

    public function methodSpacesInsideConstructorArgs(MixedConstructor $prop = new MixedConstructor(  '::spaced args string::'  )): bool
    {
        return false;
    }

    public function methodSpaceBetweenClassAndParens(MixedConstructor $prop = new MixedConstructor ('::spaced parens::')): bool
    {
        return false;
    }

    public function methodEverythingOnItsOwnLine(
        MixedConstructor
            $prop
            =
            new MixedConstructor(
                '::everything separate::'
            )
    ): bool {
        return false;
    }

    public function methodTabsAsIndentation(
		MixedConstructor $prop = new MixedConstructor(
			'::tabs indented::'
		)
    ): bool {
        return false;
    }

    public function methodTrailingCommaInConstructor(MixedConstructor $prop = new MixedConstructor('::trailing comma::',)): bool
    {
        return false;
    }

    public function methodCommentInsideArgs(MixedConstructor $prop = new MixedConstructor(/* before */ '::commented args::' /* after */)): bool
    {
        return false;
    }

    public function methodMultilineNested(
        MixedConstructor $prop = new MixedConstructor(
            new MixedConstructor(
                '::multiline nested::'
            )
        )
    ): bool {
        return false;
    }

    public function methodCommentBetweenNewAndClass(MixedConstructor $prop = new /* comment */ MixedConstructor('::new comment::')): bool
    {
        return false;
    }

    public function methodNamedArg(MixedConstructor $prop = new MixedConstructor(property: '::named arg::')): bool
    {
        return false;
    }

    public function /* comment */ methodFunctionKeywordCommented(MixedConstructor $prop = new MixedConstructor('::function commented::')): bool
    {
        return false;
    }
}
