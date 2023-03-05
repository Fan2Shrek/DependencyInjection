<?php

namespace Sruuua\DependencyInjection;

class Container
{
    /**
     * @var Service[] $elements;
     */
    private array $elements;

    /**
     * Init the elements
     */
    public function __construct()
    {
        $this->elements = array();
        Service::setContainer($this);
        $this->set('container', $this);
    }

    /**
     * Register an class on the container
     * 
     * @param string $name the name of the dependency
     * @param string $class the class namespace to init
     * @param ?array $arguments default null 
     * 
     *  @return void
     */
    public function register(string $name, string $class, ?array $arguments = null): void
    {
        $denpendencies = null;

        if (isset($arguments)) {
            $denpendencies = [];

            foreach ($arguments as $argument) {
                if (str_starts_with($argument, '@')) {
                    $denpendencies[] = $argument;
                }
            }
        }

        $this->elements['@' . $name] = new Service($name, $class, $arguments, dependencies: $denpendencies);
    }

    /**
     * Return the instance of asked dependency
     * 
     * @param string $name the name of the depency
     * 
     * @return ?object the instance asked
     */
    public function get(string $name): ?object
    {
        if (str_starts_with($name, '@')) $name = substr($name, 1);

        if ($this::class === $name) return $this->get('container');

        if (isset($this->elements['@' . $name])) {
            $service = $this->elements['@' . $name];
            if (!$service->isInstancied()) {
                $service->instance();
            }

            return $service->getService();
        } else {
            return null;
        }
    }

    /**
     * Register the class in containe
     * 
     * @var string $name the inner name
     * @var object $object the instancied object
     * 
     * @return void
     */
    public function set(string $name, object $obj): void
    {
        if ($obj instanceof Service) {
            $this->elements['@' . $name] = $obj;

            return;
        } else {
            $this->elements['@' . $name] = new Service($name, $obj::class, object: $obj);
        }
    }

    /**
     * Return all instance by type
     * 
     * @var string $type the type to seek
     * 
     * @return object[]
     */
    public function getAllByType(string $type): array
    {
        $services = [];

        if (interface_exists($type)) {
            foreach ($this->elements as $service) {
                $class = $service->getClass();
                if (in_array($type, class_implements($class))) {
                    $services[] = $service->getService();
                }
            }
        } else {
            foreach ($this->elements as $service) {
                if ($service->getService() instanceof $type) {
                    $services[] = $service->getService();
                }
            }
        }

        return $services;
    }

    /**
     * get all the instance
     * 
     * @return Service[] all the registred services
     */
    public function getElements(): array
    {
        return $this->elements;
    }
}
