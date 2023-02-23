<?php

namespace Sruuua\DependencyInjection;

use Symfony\Component\Yaml\Yaml;

class ContainerBuilder
{
    /**
     * @var Container
     */
    private Container $container;

    public function __construct()
    {
        $this->container = new Container();
    }

    /**
     * Build the container with all dependency
     * 
     * @return Container
     */
    public function build(): Container
    {
        $yaml = Yaml::parseFile('./build.yml');

        foreach ($yaml as $name => $build) {

            if (isset($build['arg'])) {
                $this->container->register($name, $build['class'], $build['arg']);
            } else {
                $this->container->register($name, $build['class']);
            }

            if (isset($build['func'])) {
                foreach ($build['func'] as $func) {

                    foreach ($func as $funcName => $argsList) {
                        
                        foreach ($argsList as $args) {
                            $all_args = [];

                            if (!is_array($args)) {
                                $temp = $this->container->get($args);
                                if (null !== $temp) {
                                    $all_args[] = $temp;
                                } else {
                                    $all_args[] = $args;
                                }
                            } else {
                                foreach ($args as $arg) {
                                    $temp = $this->container->get($arg);
                                    if (null !== $temp) {
                                        $all_args[] = $temp;
                                    } else {
                                        $all_args[] = $arg;
                                    }
                                }
                            }

                            $this->container->get($name)->$funcName(...$all_args);
                        }
                    }
                }
            }
        }

        return $this->container;
    }
}
