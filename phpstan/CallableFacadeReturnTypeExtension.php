<?php

declare(strict_types=1);

namespace Mpyw\LaravelRetryOnDuplicateKey\PHPStan;

use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptor;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\MixedType;

final class CallableFacadeReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return DB::class;
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'retryOnDuplicateKey';
    }

    public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): \PHPStan\Type\Type
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
