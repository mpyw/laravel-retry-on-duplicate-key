<?php

declare(strict_types=1);

namespace Mpyw\LaravelRetryOnDuplicateKey\PHPStan;

use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\PassedByReference;
use PHPStan\Type\CallableType;
use PHPStan\Type\Type;

final class CallableParameter implements ParameterReflection
{
    /**
     * @var CallableArgumentParameter[]
     */
    private array $argumentParameters;

    /**
     * @param CallableArgumentParameter[] $argumentParameters
     */
    public function __construct(array $argumentParameters)
    {
        $this->argumentParameters = $argumentParameters;
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
        return new CallableType($this->argumentParameters);
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
