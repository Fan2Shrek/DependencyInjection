<?php

namespace Sruuua\DependencyInjection;

class Container
{
    /**
     * @var array $elements;
     */
    private array $elements;

    /**
     * Init the elements
     */
    public function __construct()
    {
        $this->elements = array();
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
        if (isset($arguments)) {
            $all_args = array();
            foreach ($arguments as $argument) {
                if (str_starts_with($argument, '@')) {
                    $all_args[] = $this->elements[$argument];
                } else {
                    $all_args[] = $argument;
                }
            }
            $this->elements['@' . $name] = array('class' => $class, 'arguments' => $all_args);
        } else {
            $this->elements['@' . $name] = array('class' => $class, 'arguments' => array());
        }

        return;
    }

    /**
     * Instancy an class
     * 
     * @param array $class the array 
     * 
     * @return object the instance
     */
    public function instance(array $class): object
    {
        $all_args = [];
        foreach ($class['arguments'] as $argument) {
            if (is_array($argument)) {
                $all_args[] = $this->instance($argument);
            } else {
                $all_args[] = $argument;
            }
        }

        return new $class['class'](...$all_args);
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
        if (isset($this->elements['@' . $name])) {
            if (is_array($this->elements['@' . $name])) {
                $this->elements['@' . $name] = $this->instance($this->elements['@' . $name]);
            }

            return $this->elements['@' . $name];
        } else {
            return null;
        }
    }

    /**
     * get all the instance
     * 
     * @return array all the registred instance
     */
    public function getElements(): array
    {
        return $this->elements;
    }
}
