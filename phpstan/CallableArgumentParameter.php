<?php

declare(strict_types=1);

namespace Mpyw\LaravelRetryOnDuplicateKey\PHPStan;

use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\PassedByReference;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;

final class CallableArgumentParameter implements ParameterReflection
{
    public function getName(): string
    {
        return 'args';
    }

    public function isOptional(): bool
    {
        return false;
    }

    public function getType(): Type
    {
        return new MixedType();
    }

    public function passedByReference(): PassedByReference
    {
        return PassedByReference::createNo();
    }

    public function isVariadic(): bool
    {
        return false;
    }

    public function getDefaultValue(): ?Type
    {
        return null;
    }
}
