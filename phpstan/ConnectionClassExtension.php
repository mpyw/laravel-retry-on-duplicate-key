<?php

namespace Mpyw\LaravelRetryOnDuplicateKey\PHPStan;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;

final class ConnectionClassExtension implements MethodsClassReflectionExtension
{
    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        return $methodName === 'retryOnDuplicateKey'
            && (
                \is_a($classReflection->getName(), ConnectionInterface::class, true)
                || \is_a($classReflection->getName(), DB::class, true)
            );
    }

    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        return new RetryOnDuplicateKeyMethod(
            $classReflection,
            \is_a($classReflection->getName(), DB::class, true),
        );
    }
}
