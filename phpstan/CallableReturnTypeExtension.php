<?php

namespace Mpyw\LaravelRetryOnDuplicateKey\PHPStan;

use Illuminate\Database\ConnectionInterface;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptor;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;

final class CallableReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return ConnectionInterface::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'retryOnDuplicateKey';
    }

    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
    {
        if (\count($methodCall->getArgs()) > 0) {
            $type = $scope->getType($methodCall->getArgs()[0]->value);

            if ($type instanceof ParametersAcceptor) {
                return $type->getReturnType();
            }
        }

        return new MixedType();
    }
}
