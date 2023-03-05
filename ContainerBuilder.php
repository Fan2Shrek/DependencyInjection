<?php

namespace Sruuua\DependencyInjection;

use Symfony\Component\Yaml\Yaml;

class ContainerBuilder
{
    /**
     * @var Container
     */
    private Container $container;

    /**
     * @var string[]
     */
    private array $excludes;

    public function __construct($ctx)
    {

        $this->container = new Container();
        $this->container->set($ctx::class, $ctx);
        $this->initializeContainer();
    }

    /**
     * Initialiaze the container with dependency
     * 
     */
    public function initializeContainer()
    {
        $this->buildWithYaml();
        $this->buildAppServices();
    }

    /**
     * Register all class in src folder except excluded files
     * 
     */
    public function buildAppServices()
    {
        $this->registerFolder('../src');
    }

    /**
     * Register all class in the folder
     * 
     * @var string $folder the folder to register
     * 
     * @return void
     */
    public function registerFolder(string $path): void
    {
        $files = array_diff(scandir($path), array('.', '..'));
        foreach ($files as $fileName) {
            $file = $path . '/' . $fileName;
            if (!in_array($file, $this->excludes)) {
                if (!is_dir($file)) {
                    $this->register($file);
                } else {
                    $this->registerFolder($file);
                }
            }
        }
    }

    /**
     * Inject dependency needed in class
     * 
     * @param string $class Class Namespace
     * 
     * @return void
     */
    public function instance(string $class): void
    {
        $this->container->register($class, $class);
    }

    /**
     * Adapt and register a namespace from filename
     * 
     * @var string $fileName the file to register
     */
    public function register(string $fileName)
    {
        $namespace = str_replace('.php', '', $fileName);
        $namespace = str_replace('../src', 'App', $namespace);
        $namespace = str_replace('/', '\\', $namespace);

        if (class_exists($namespace)) $this->instance($namespace);
    }

    /**
     * Parse and build yaml file
     * 
     * @return Container
     */
    public function buildWithYaml()
    {
        $yaml = Yaml::parseFile('../config/services.yml');
        $this->excludes = $yaml['excludes'];

        $this->buildInitialServices($yaml['services']);
    }

    /**
     * Build the container with all dependency
     * 
     * @var array[] $yaml the yaml's services
     * 
     * @return void
     */
    public function buildInitialServices(array $yaml): void
    {
        foreach ($yaml as $name => $build) {

            if (isset($build['arg'])) {
                $this->container->register($name, $build['class'], $build['arg']);
            } else {
                $this->container->register($name, $build['class']);
            }
        }
    }

    /**
     * Get the container
     * 
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }
}
