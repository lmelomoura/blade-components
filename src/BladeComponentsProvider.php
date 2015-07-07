<?php namespace Moura\BladeComponents;


use Illuminate\Support\ServiceProvider;
use \View;
use Moura\BladeComponents\Classes\BladeComponent;

class BladeComponentsProvider extends ServiceProvider{

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
        /* Disponibilizando a configuração do provider */
        $this->setupConfig();

        /* Adicionando o path das views */
        View::addLocation(app_path('BladeComponents'.DIRECTORY_SEPARATOR.'Views'));
        View::addNamespace('BladeComponents', app_path('BladeComponents'.DIRECTORY_SEPARATOR.'Views'));
    }

    /**
     * Inicializa todos os components passados no arquivo de configuração
     * @throws \Exception
     */
    public function startComponents(){
        $bladeComponents = $this->app['config']['bladecomponents']['components'];
        foreach ($bladeComponents as $componentClass){
            /* Instanciando o novo componente */
            $instance = app($componentClass);
            /* Validando os básicos de funcionamento do service provider e do componente*/
            $this->validate($instance);
            /* Recuperando o commando que será criado para esse componente */
            $componentName = $instance->commandName();

            if (!array_key_exists($componentName,self::$registeredComponents)) {
                self::$registeredComponents[$componentName]['instance'] = $instance;
                self::$registeredComponents[$componentName]['instanceName'] = array();
            }
            else
                throw new \Exception("Component '$componentName' is already registered");
        }
    }

    public function validate(BladeComponent &$component){
        /* Checa se o diretório BladeComponents existe */
        if (!is_dir(app_path('BladeComponents'))) throw new \Exception("BladeComponents directory not found in '".app_path()."'");
        /* Checa se o diretório Components existe */
        if (!is_dir(app_path('BladeComponents'.DIRECTORY_SEPARATOR.'Components'))) throw new \Exception("Components directory not found in '".app_path('BladeComponents')."'");
        /* Checa se o diretório Views existe */
        if (!is_dir(app_path('BladeComponents'.DIRECTORY_SEPARATOR.'Views'))) throw new \Exception("Views directory not found in '".app_path('BladeComponents')."'");
        /* Checa se a view do componente existe*/
        if (!is_dir(app_path('BladeComponents/Views'))) throw new \Exception("Views directory not found in '".app_path('BladeComponents')."'");
        /* Checa se o arquivo de view existe */
        if (!file_exists(app_path('BladeComponents'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.$component->view().'.blade.php'))) throw new \Exception("Views '".DIRECTORY_SEPARATOR.$component->view().'.blade.php'."' not found");

    }

}