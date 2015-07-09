<?php namespace Moura\BladeComponents\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Moura\BladeComponents\Constants\BladeComponentsConstants;

class BladeCommand extends  GeneratorCommand {
    /*
     * Trait used to obtain the app namespace
     */
    use \Illuminate\Console\AppNamespaceDetectorTrait;

    /**
     * Component name
     * @var string
     */
    protected $componentName = '';

    /**
     * Class folder path
     * @var string
     */
    protected $baseComponentClassFolder = '';

    /**
     * View folder path
     * @var string
     */
    protected $baseComponentViewFolder = '';

    /**
     * Script folder path
     * @var string
     */
    protected $baseComponentScriptFolder = '';

    /**
     * Javascript folder path
     * @var string
     */
    protected $baseComponentScriptJsFolder = '';

    /**
     * CSS script folder path
     * @var string
     */
    protected $baseComponentScriptCssFolder = '';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:bladecomponent
        {name : Component class name. ex: Emails (The word \'Component\' will always be concatenated at the final of the given name, producing something like \'EmailComponent\'.)}
        {--plain : This option sets the script file will be created or not}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create inside the app folder, the BladeComponents repository folder for component class and component view.';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/Stubs/'.BladeComponentsConstants::COMPONENT_STUB_NAME;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
       try {
           /* Define base folders path */
           $this->baseComponentClassFolder = app_path(BladeComponentsConstants::DIRECTORY_BASE_REPOSITORY.DIRECTORY_SEPARATOR.BladeComponentsConstants::DIRECTORY_BASE_COMPONENTS);
           $this->baseComponentViewFolder = app_path(BladeComponentsConstants::DIRECTORY_BASE_REPOSITORY.DIRECTORY_SEPARATOR.BladeComponentsConstants::DIRECTORY_BASE_VIEWS);
           $this->baseComponentScriptFolder = app_path(BladeComponentsConstants::DIRECTORY_BASE_REPOSITORY.DIRECTORY_SEPARATOR.BladeComponentsConstants::DIRECTORY_BASE_SCRIPTS);
           $this->baseComponentScriptJsFolder = app_path(BladeComponentsConstants::DIRECTORY_BASE_REPOSITORY.DIRECTORY_SEPARATOR.BladeComponentsConstants::DIRECTORY_BASE_SCRIPTS.DIRECTORY_SEPARATOR.BladeComponentsConstants::DIRECTORY_BASE_SCRIPTS_JS);
           $this->baseComponentScriptCssFolder = app_path(BladeComponentsConstants::DIRECTORY_BASE_REPOSITORY.DIRECTORY_SEPARATOR.BladeComponentsConstants::DIRECTORY_BASE_SCRIPTS.DIRECTORY_SEPARATOR.BladeComponentsConstants::DIRECTORY_BASE_SCRIPTS_CSS);

           /* Define component name */
           $this->componentName = $this->argument('name').BladeComponentsConstants::COMPONENT_NAME_CONCAT;

           /* Validating the component creation */
           $this->validate();

           /* Creating folder structure */
           $this->createFolders();

           /* Creating class */
           $this->createClass();

           /* Creating view */
           $this->createView();

           /* Creating script */
           if(!$this->option('plain'))
               $this->createScripts();

           return $this->info(BladeComponentsConstants::COMPONENT_CREATION_SUCCESS);
       }catch (\Exception $e){
           return $this->error(BladeComponentsConstants::COMPONENT_CREATION_ERROR.'\''.$e->getMessage().'\'');
       }
    }

    /**
     * Create the folder structure of the component repository
     * @return string
     */
    protected function createFolders(){
        /* Base folder: Components */
        $this->files->makeDirectory($this->baseComponentClassFolder, 0777, true, true);

        /* Base folder: Views */
        $this->files->makeDirectory($this->baseComponentViewFolder, 0777, true, true);

        /* Base folder: Scripts */
        $this->files->makeDirectory($this->baseComponentScriptJsFolder, 0777, true, true);
        $this->files->makeDirectory($this->baseComponentScriptCssFolder, 0777, true, true);

        return $this->info(BladeComponentsConstants::DIRECTORY_CREATION_SUCCESS);
    }

    /**
     * Create the class component file
     * @throws \Exception
     * @return string
     */
    protected function createClass(){
        $file = fopen($this->baseComponentClassFolder.DIRECTORY_SEPARATOR.$this->componentName.'.php', 'w+');
        if(!fputs($file,$this->renderClass()))
            throw new \Exception(BladeComponentsConstants::CLASS_CREATION_ERROR);
        return $this->info(BladeComponentsConstants::CLASS_CREATION_SUCCESS);
    }

    /**
     * Apply the appropriate string replacement for the component class file
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function renderClass(){
        $stubContent = $this->files->get($this->getStub());
        $namespace = $this->getAppNamespace().BladeComponentsConstants::DIRECTORY_BASE_REPOSITORY.'\\'.BladeComponentsConstants::DIRECTORY_BASE_COMPONENTS;
        $stubContent = str_replace('DummyNamespace', $namespace, $stubContent);
        $stubContent = str_replace('DummyClass', $this->componentName, $stubContent);
        return '<?php '.BladeComponentsConstants::CLASS_CREATION_COMMENT.PHP_EOL.$stubContent;
    }

    /**
     * Create the view component file
     * @throws \Exception
     * @return string
     */
    protected function createView(){
        $file = fopen($this->baseComponentViewFolder.DIRECTORY_SEPARATOR.$this->componentName.'.blade.php', 'w+');
        if(!fputs($file,$this->renderView()))
            throw new \Exception(BladeComponentsConstants::VIEW_CREATION_ERROR);
        return $this->info(BladeComponentsConstants::VIEW_CREATION_SUCCESS);
    }

    /**
     * Apply the appropriate string replacement for the component view file
     * @return string
     */
    protected function renderView(){
        return str_replace('DummyClass', $this->componentName, BladeComponentsConstants::VIEW_CREATION_COMMENT);
    }

    /**
     * Create the javascript component file
     * @throws \Exception
     */
    protected function createScriptJs(){
        $file = fopen($this->baseComponentScriptJsFolder.DIRECTORY_SEPARATOR.$this->componentName.'.js', 'w+');
        if(!fputs($file,$this->renderScriptJs()))
            throw new \Exception(BladeComponentsConstants::SCRIPT_CREATION_ERROR);
    }

    /**
     * Apply the appropriate string replacement for the javascript file
     * @return string
     */
    protected function renderScriptJs(){
        return str_replace('DummyClass', $this->componentName, BladeComponentsConstants::SCRIPT_CREATION_JS_COMMENT);
    }

    /**
     * Create the CSS component file
     * @throws \Exception
     */
    protected function createScriptCss(){
        $file = fopen($this->baseComponentScriptCssFolder.DIRECTORY_SEPARATOR.$this->componentName.'.css', 'w+');
        if(!fputs($file,$this->renderScriptCss()))
            throw new \Exception(BladeComponentsConstants::SCRIPT_CREATION_ERROR);
    }

    /**
     * Apply the appropriate string replacement for the CSS file
     * @throws \Exception
     * @return string
     */
    protected function renderScriptCss(){
        return str_replace('DummyClass', $this->componentName, BladeComponentsConstants::SCRIPT_CREATION_CSS_COMMENT);
    }

    /**
     * Call all script creation functions
     * @throws \Exception
     * @return string
     */
    protected function createScripts(){
        $this->createScriptJs();
        $this->createScriptCss();
        return $this->info(BladeComponentsConstants::SCRIPT_CREATION_SUCCESS);
    }

    /**
     * Apply all validations before create the component files
     * @throws \Exception
     */
    protected function validate(){
        /* Check if the component doesn't exists */
        if(file_exists($this->baseComponentClassFolder.DIRECTORY_SEPARATOR.$this->componentName.'.php'))
            throw new \Exception(BladeComponentsConstants::COMPONENT_EXISTS_ERROR);
    }

}

