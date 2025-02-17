<?php

use Payline\App\Interface\Entity\BasicEntityInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

function autoloading(
    ContainerBuilder $container,
    array $interfaceToImplementationMap,
    array $argumentMapping,
    string $namespace,
    string $directory
)
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

                $isPublic = (str_contains($className, '\Controller')
                    || str_contains($className, '\Service')
                    || str_contains($className, '\Manager')
                );

                $definition = $container
                    ->register($className, $className)
                    ->setAutowired(true)
                    ->setAutoconfigured(true)
                    ->setPublic($isPublic);

                // Interfece mapping
                foreach ($interfaceToImplementationMap as $interface => $implementation) {
                    if ($className === $implementation) {
                        $container->setAlias($interface, $implementation)->setPublic($isPublic);
                    }
                }

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