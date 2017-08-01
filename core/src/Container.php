<?php

namespace Core;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Core\Validator\RepositoryPresenceVerifier;
use Core\Contracts\Container as ContainerContract;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Container\Container as IlluminateContainer;

class Container extends IlluminateContainer implements ContainerContract
{
    /**
     * The available container bindings and their respective load methods.
     *
     * @var array
     */
    public $availableBindings = [
        'container' => 'registerContainerBindings',
        'Core\Contracts\Container' => 'registerContainerBindings',
        'Illuminate\Contracts\Container' => 'registerContainerBindings',
        'Illuminate\Container\Container' => 'registerContainerBindings',

        'validator' => 'registerValidatorBindings',
        'Illuminate\Contracts\Validation\Factory' => 'registerValidatorBindings',

        'translator' => 'registerTranslationBindings',

        'files' => 'registerFilesBindings',

        'config' => 'registerConfigBindings',
    ];

    /**
     * The service binding methods that have been executed.
     *
     * @var array
     */
    protected $ranServiceBinders = [];

    /**
     * The loaded service providers.
     *
     * @var array
     */
    protected $loadedProviders = [];

    /**
     * All of the loaded configuration files.
     *
     * @var array
     */
    protected $loadedConfigurations = [];

    /**
     * Resolve the given type from the container.
     *
     * @param string $abstract
     *
     * @return mixed
     */
    public function make($abstract)
    {
        $abstract = $this->getAlias($abstract);

        if (array_key_exists($abstract, $this->availableBindings) &&
            !array_key_exists($this->availableBindings[$abstract], $this->ranServiceBinders)) {
            $this->{$method = $this->availableBindings[$abstract]}();

            $this->ranServiceBinders[$method] = true;
        }

        return parent::make($abstract);
    }

    /**
     * Register a service provider with the application.
     *
     * @param \Illuminate\Support\ServiceProvider|string $provider
     *
     * @return \Illuminate\Support\ServiceProvider
     */
    public function register($provider)
    {
        if (!$provider instanceof ServiceProvider) {
            $provider = new $provider($this);
        }

        if (array_key_exists($providerName = get_class($provider), $this->loadedProviders)) {
            return;
        }

        $this->loadedProviders[$providerName] = true;

        if (method_exists($provider, 'register')) {
            $provider->register();
        }

        if (method_exists($provider, 'boot')) {
            return $this->call([$provider, 'boot']);
        }
    }

    /**
     * Load a configuration file into the application.
     *
     * @param string $name
     */
    public function configure($name)
    {
        if (isset($this->loadedConfigurations[$name])) {
            return;
        }

        $this->loadedConfigurations[$name] = true;

        $path = $this->getConfigurationPath($name);

        if ($path) {
            $this->make('config')->set($name, require $path);
        }
    }

    /**
     * Register container bindings for the application.
     */
    protected function registerValidatorBindings()
    {
        $this->singleton('validator', function () {
            $this->register('Illuminate\Validation\ValidationServiceProvider');

            $validator = $this->make('validator');

            $validator->setPresenceVerifier(new RepositoryPresenceVerifier($this));

            return $validator;
        });

        $this->alias('validator', 'Illuminate\Contracts\Validation\Factory');
    }

    /**
     * Register container bindings for the application.
     */
    protected function registerContainerBindings()
    {
        $this->instance('container', $this);
        $this->alias('Core\Contracts\Container', 'container');
        $this->alias('Illuminate\Container\Container', 'container');
        $this->alias('Illuminate\Contracts\Container', 'container');
    }

    /**
     * Register container bindings for the application.
     */
    protected function registerTranslationBindings()
    {
        $this->singleton('translator', function () {
            $this->configure('app');

            if (!$this->bound('path.lang')) {
                $this->instance('path.lang', $this->getLanguagePath());
            }

            $this->register('Illuminate\Translation\TranslationServiceProvider');

            return $this->make('translator');
        });
    }

    /**
     * Register container bindings for the application.
     */
    protected function registerFilesBindings()
    {
        $this->singleton('files', function () {
            return new Filesystem();
        });
    }

    /**
     * Register container bindings for the application.
     */
    protected function registerConfigBindings()
    {
        $this->singleton('config', function () {
            return new ConfigRepository();
        });
    }

    /**
     * Get the path to the application's language files.
     *
     * @return string
     */
    protected function getLanguagePath()
    {
        if (is_dir($langPath = $this->basePath().'/resources/lang')) {
            return $langPath;
        } else {
            return __DIR__.'/../resources/lang';
        }
    }

    /**
     * Get the path to the given configuration file.
     *
     * If no name is provided, then we'll return the path to the config folder.
     *
     * @param string|null $name
     *
     * @return string
     */
    protected function getConfigurationPath($name = null)
    {
        if (!$name) {
            $appConfigDir = $this->basePath('config').'/';

            if (file_exists($appConfigDir)) {
                return $appConfigDir;
            } elseif (file_exists($path = __DIR__.'/../config/')) {
                return $path;
            }
        } else {
            $appConfigPath = $this->basePath('config').'/'.$name.'.php';

            if (file_exists($appConfigPath)) {
                return $appConfigPath;
            } elseif (file_exists($path = __DIR__.'/../config/'.$name.'.php')) {
                return $path;
            }
        }
    }

    /**
     * Get the base path for the application.
     *
     * @param string|null $path
     *
     * @return string
     */
    protected function basePath($path = null)
    {
        return __DIR__.'/../../'.($path ? '/'.$path : $path);
    }
}
