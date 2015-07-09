<?php namespace Moura\BladeComponents;


use Illuminate\Support\ServiceProvider;
use \View;
use Moura\BladeComponents\Classes\BladeComponent;

class BladeComponentsProvider extends ServiceProvider{

    /**
     * Define artisan console command
     * @var array
     */
    protected $commands = [
        'Moura\BladeComponents\Console\Commands\BladeCommand'
    ];

    /**
     * Store all registered components objects
     * @var array
     */
    public static $registeredComponents = array();

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /* Instanciando os componentes */
        $this->startComponents();

    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/../config/bladecomponents.php');
        $this->publishes([$source => config_path('bladecomponents.php')]);
        $this->mergeConfigFrom($source, 'bladecomponents');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        /* Registering BladeCommand */
        $this->commands($this->commands);

        /* Providing the provider settings */
        $this->setupConfig();

        /* Adding the path of views and creating a namespace for them */
        View::addLocation(app_path('BladeComponents'.DIRECTORY_SEPARATOR.'Views'));
        View::addNamespace('BladeComponents', app_path('BladeComponents'.DIRECTORY_SEPARATOR.'Views'));
    }

    /**
     * Register all configured components
     * @throws \Exception
     */
    public function startComponents(){
        $bladeComponents = $this->app['config']['bladecomponents']['components'];
        foreach ($bladeComponents as $componentClass){
            /* Component instance */
            $instance = app($componentClass);
            /* Validate component */
            $this->validate($instance);
            /* Set blade directive command */
            $componentName = $instance->commandName();
            /* Checks if the component is not already registered */
            if (!array_key_exists($componentName,self::$registeredComponents)) {
                self::$registeredComponents[$componentName]['instance'] = $instance;
                self::$registeredComponents[$componentName]['instanceName'] = array();
            }
            else
                throw new \Exception("Component '$componentName' is already registered");
        }
    }

    /**
     * Validating the component registration process
     * @param BladeComponent $component
     * @throws \Exception
     */
    public function validate(BladeComponent &$component){
        /* Check if BladeComponents directory exists */
        if (!is_dir(app_path('BladeComponents'))) throw new \Exception("BladeComponents directory not found in '".app_path()."'");
        /* Check if Components directory exists */
        if (!is_dir(app_path('BladeComponents'.DIRECTORY_SEPARATOR.'Components'))) throw new \Exception("Components directory not found in '".app_path('BladeComponents')."'");
        /* Check if Views directory exists */
        if (!is_dir(app_path('BladeComponents'.DIRECTORY_SEPARATOR.'Views'))) throw new \Exception("Views directory not found in '".app_path('BladeComponents')."'");
        /* Check if the view file exists */
        if (!file_exists(app_path('BladeComponents'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.$component->view().'.blade.php'))) throw new \Exception("Views '".DIRECTORY_SEPARATOR.$component->view().'.blade.php'."' not found");

    }

}