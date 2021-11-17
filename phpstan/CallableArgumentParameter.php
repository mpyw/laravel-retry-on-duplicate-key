<?php

namespace Mpyw\LaravelRetryOnDuplicateKey\PHPStan;

use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\PassedByReference;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;

final class CallableArgumentParameter implements ParameterReflection
{
    private Type $type;

    public function __construct(?Type $type = null)
    {
        $this->type = $type ?? new MixedType();
    }

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
        return $this->type;
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
