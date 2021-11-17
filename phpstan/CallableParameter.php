<?php

namespace Mpyw\LaravelRetryOnDuplicateKey\PHPStan;

use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\PassedByReference;
use PHPStan\Type\CallableType;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;

final class CallableParameter implements ParameterReflection
{
    /**
     * @var CallableArgumentParameter[]
     */
    private array $argumentParameters;
    private Type $returnType;

    public function __construct(array $argumentParameters, ?Type $returnType = null)
    {
        $this->argumentParameters = $argumentParameters;
        $this->returnType = $returnType ?? new MixedType();
    }

    public function getName(): string
    {
        return 'callback';
    }

    public function isOptional(): bool
    {
        return false;
    }

    public function getType(): Type
    {
        return new CallableType($this->argumentParameters, $this->returnType);
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
