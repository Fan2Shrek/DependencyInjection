<?php

namespace Sruuua\DependencyInjection;

use ReflectionClass;

class Service
{
    private string $name;

    private ?object $object = null;

    private string $class;

    private array $args = [];

    /**
     * @var ReflectionType[]
     */
    private array $dependencies = [];

    private bool $isInstancied;

    private static Container $container;

    public function __construct(string $name, string $class, ?array $args = null, ?object $object = null, ?array $dependencies = null)
    {
        $this->name = $name;
        $this->class = $class;
        $this->args = $args ?? [];
        $this->object = $object ?? null;
        $this->dependencies = $dependencies ?? $this->getAllDependencies($class);
        $this->isInstancied = (is_object($object));
    }

    /**
     * Instance service
     * 
     * @return void
     */
    public function instance(): void
    {
        $dependencies = array_map(
            fn ($dependency) => self::$container->get($dependency),
            $this->dependencies
        );
        $class = $this->class;

        if (!empty($this->dependencies)) {
            $this->object = new $class(...$dependencies);
        } else {
            $this->object = new $class(...$this->args);
        }



        $this->isInstancied = true;
    }

    /**
     * Return all dependencies from a class
     * 
     * @param string $class Class Namespace
     * 
     * @return ReflectionNamedType[]
     */
    public function getAllDependencies(string $class): array
    {
        $types = [];
        $refClass = new ReflectionClass($class);

        if ($refClass->hasMethod('__construct')) {
            $construct = $refClass->getMethod('__construct');

            $types = [];
            foreach ($construct->getParameters() as $param) {
                $types[] = $param->getType();
            }
        }

        return $types;
    }

    /**
     * Return if the the service is instancied or not
     * 
     * @return bool
     */
    public function isInstancied(): bool
    {
        return $this->isInstancied;
    }

    /**
     * Get service (instance)
     * 
     * @return object
     */
    public function getService(): object
    {
        if (!$this->isInstancied()) {
            $this->instance();
        }
        return $this->object;
    }

    /**
     * Return value of class
     * 
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * Set container value
     * 
     * @param Container
     * 
     * @return void
     */
    public static function setContainer(Container $container): void
    {
        self::$container = $container;
    }
}
