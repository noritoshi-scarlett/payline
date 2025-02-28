<?php
declare(strict_types=1);

use Noritoshi\Payline\Infrastructure\Domain\BasicEntityInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

function autoloading(
    ContainerBuilder $container,
    array $interfaceToImplementationMap,
    array $classesToSkip,
    array $argumentMapping,
    string $namespace,
    string $directory
): void
{
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    foreach ($files as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $relativePath = str_replace([$directory . '/', '.php'], '', $file->getPathname());
            $className = $namespace . str_replace('/', '\\', $relativePath);

            if (class_exists($className)) {
                if (new ReflectionClass($className)->isAbstract()) {
                    continue;
                }
                if (new ReflectionClass($className)->implementsInterface(BasicEntityInterface::class)) {
                    continue;
                }

                $isAutowire = !str_contains($className, 'Noritoshi\Payline\Application\Manager');
                $isPublic = (str_contains($className, '\Controller')
                    || str_contains($className, '\Service')
                    || str_contains($className, '\Manager')
                );
                // Interface mapping
                foreach ($interfaceToImplementationMap as $interface => $implementation) {
                    if ($className === $implementation) {
                        $container->setAlias($interface, $implementation)->setPublic($isPublic);
                    }
                }
                //Skip Mapping
                if (in_array($className, $classesToSkip, true)) {
                    continue;
                }
                // Build definition
                $definition = $container
                    ->register($className, $className)
                    ->setAutowired($isAutowire)
                    ->setAutoconfigured(true)
                    ->setPublic($isPublic);

                // Table names mapping
                if (isset($argumentMapping[$className])) {
                    foreach ($argumentMapping[$className] as $argumentName => $argumentValue) {
                        $definition->setArgument($argumentName, $argumentValue);
                    }
                }
            }
        }
    }
}