<?php

namespace Mpyw\LaravelRetryOnDuplicateKey\PHPStan;

use Illuminate\Database\ConnectionInterface;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;

final class ConnectionClassExtension implements MethodsClassReflectionExtension
{
    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        return \is_subclass_of($classReflection->getName(), ConnectionInterface::class)
            && $methodName === 'retryOnDuplicateKey';
    }

    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        return new RetryOnDuplicateKeyMethod($classReflection);
    }
}
