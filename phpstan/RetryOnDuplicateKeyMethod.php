<?php

namespace Mpyw\LaravelRetryOnDuplicateKey\PHPStan;

use Illuminate\Database\QueryException;
use PHPStan\Reflection\ClassMemberReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionVariant;
use PHPStan\Reflection\MethodReflection;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Generic\TemplateTypeMap;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

final class RetryOnDuplicateKeyMethod implements MethodReflection
{
    private ClassReflection $class;
    private bool $static;

    public function __construct(ClassReflection $classReflection, bool $static)
    {
        $this->class = $classReflection;
        $this->static = $static;
    }

    public function getDeclaringClass(): ClassReflection
    {
        return $this->class;
    }

    public function isStatic(): bool
    {
        return $this->static;
    }

    public function isPrivate(): bool
    {
        return false;
    }

    public function isPublic(): bool
    {
        return true;
    }

    public function getDocComment(): ?string
    {
        return null;
    }

    public function getName(): string
    {
        return 'retryOnDuplicateKey';
    }

    public function getPrototype(): ClassMemberReflection
    {
        return $this;
    }

    public function getVariants(): array
    {
        $variants = [];

        for ($i = 0; $i < 10; ++$i) {
            $argumentParameters = [];
            for ($j = 0; $j < $i; ++$j) {
                $argumentParameters[] = new CallableArgumentParameter();
            }

            $variants[] = new FunctionVariant(
                TemplateTypeMap::createEmpty(),
                null,
                [
                    new CallableParameter($argumentParameters),
                    ...$argumentParameters,
                ],
                false,
                new MixedType(),
            );
        }

        return $variants;
    }

    public function isDeprecated(): TrinaryLogic
    {
        return TrinaryLogic::createNo();
    }

    public function getDeprecatedDescription(): ?string
    {
        return null;
    }

    public function isFinal(): TrinaryLogic
    {
        return TrinaryLogic::createNo();
    }

    public function isInternal(): TrinaryLogic
    {
        return TrinaryLogic::createNo();
    }

    public function getThrowType(): ?Type
    {
        return new ObjectType(QueryException::class);
    }

    public function hasSideEffects(): TrinaryLogic
    {
        return TrinaryLogic::createMaybe();
    }
}
