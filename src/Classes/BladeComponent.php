<?php namespace Moura\BladeComponents\Classes;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Moura\BladeComponents\BladeComponentsProvider;
use Moura\BladeComponents\Constants\BladeComponentsConstants;


/**
 * Class BladeComponent
 *
 * @package Moura\BladeComponents\Classes
 */
abstract class BladeComponent {
    /**
     * Default view component file.
     * @var string;
     */
    private $view;

    /**
     * Blade command that will be created.
     * @var string;
     */
    private $commandName;

    /**
     * Inputs and labels to be used in the view.
     * @var array
     */
    private $inputs;

    /**
     * Buttons to be used in the view.
     * @var array
     */
    private $buttons;

    /**
     * Data objects to be used inside the inputs constructor.
     * @var array
     */
    private $data;

    /**
     * Object to handle the file system.
     * @var Filesystem
     */
    private $files;

    /**
     * Path for javascript files.
     * @var string
     */
    private $jsPath;

    /**
     * Path for CSS files.
     * @var string
     */
    private $cssPath;

    /**
     * Returns an associative array with the HTML code of all the buttons that will be available in view.
     * @return array
     */
    abstract function buttons();

    /**
     * Returns an associative array with the HTML code of all inputs, labels, selects, radio buttons, checkboxes and textareas, which will be available in view.
     * @return array
     */
    abstract function inputs();

    /**
     * Returns an associative array of data objects to be used as values passed to the inputs and buttons.
     * @return array
     */
    abstract function data();

    /**
     * Component class constructor
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem){
        /*  Creating the object to handle the file system*/
        $this->files = $filesystem;
        /* Set path for JS e CSS files*/
        $this->jsPath = app_path(BladeComponentsConstants::DIRECTORY_BASE_REPOSITORY.DIRECTORY_SEPARATOR.BladeComponentsConstants::DIRECTORY_BASE_SCRIPTS.DIRECTORY_SEPARATOR.BladeComponentsConstants::DIRECTORY_BASE_SCRIPTS_JS);
        $this->cssPath = app_path(BladeComponentsConstants::DIRECTORY_BASE_REPOSITORY.DIRECTORY_SEPARATOR.BladeComponentsConstants::DIRECTORY_BASE_SCRIPTS.DIRECTORY_SEPARATOR.BladeComponentsConstants::DIRECTORY_BASE_SCRIPTS_CSS);
        /* Set blade command name and view name for component */
        $this->commandName = explode('\\',get_class($this));
        $this->commandName = end($this->commandName);
        $this->view = $this->commandName;
        /* Retrieving application data array */
        $this->data = $this->data();
        /* Retrieving all inputs and buttons */
        $this->setupHtml();
        /* Rendering the component */
        $this->composer();
    }

    /**
     * Return the blade commando name.
     * @return string
     */
    public function commandName(){
        return $this->commandName;
    }

    /**
     * Return the view for component.
     * @return string
     */
    public function view(){
        return $this->view;
    }

    /**
     * Return a specific data object.
     * @param $dataName
     * @return mixed
     */
    public function getData($dataName){
        $return = null;
        if (array_key_exists($dataName, $this->data))
            $return = $this->data[$dataName];
        return $return;
    }

    /**
     * Return data object array.
     * @return array
     */
    public function getAllData(){
        return $this->data;
    }

    /**
     * Modifies the HTML for all inputs and buttons.
     */
    public function setupHtml(){
        $this->inputs = $this->inputs();
        $this->buttons = $this->buttons();
    }

    /**
     * Renders and compile view.
     * @param $name
     * @param array $data
     * @return string
     * @throws \Exception
     */
    public function compile($name, $data = array()){
        $render = '';
        /* Check if the given name for the component instance is not already registered */
        if(!array_key_exists($name,BladeComponentsProvider::$registeredComponents[$this->commandName]['instanceName'])){
            /* Registering the component instance name */
            BladeComponentsProvider::$registeredComponents[$this->commandName]['instanceName'][$name] = [];
            /* Creating the control inputs to be utilized in the requests */
            $this->inputs[$this->commandName]['instanceName'] = \Form::hidden('instanceName',$name);
            $this->inputs[$this->commandName]['component'] = \Form::hidden('component',self::class);
            /* Adjusting the data vector that will be passed to view */
            $data['name'] = $name;
            $data['buttons'] = $this->buttons;
            $data['inputs'] = $this->inputs;
            $data['data'] = $this->data;
            /* Checking if there is a default JavaScript file to be rendered */
            if ($this->files->exists($this->jsPath.DIRECTORY_SEPARATOR.$this->commandName.'.js')) {
                $javaScript = $this->files->get($this->jsPath.DIRECTORY_SEPARATOR.$this->commandName.'.js');
                $render.= '<script type="text/javascript">'.$this->parseBladeCode($javaScript,$data).'</script>';
            }
            /* Setting the html rendered */
            $render .= $this->inputs[$this->commandName]['instanceName'];
            $render .= $this->inputs[$this->commandName]['component'];
            $render .= view('BladeComponents::'.$this->view,$data);
            /* Checking if there is a default CSS file to be rendered */
            if ($this->files->exists($this->cssPath.DIRECTORY_SEPARATOR.$this->commandName.'.css')) {
                $javaScript = $this->files->get($this->cssPath.DIRECTORY_SEPARATOR.$this->commandName.'.css');
                $render.= '<style type="text/css">'.$this->parseBladeCode($javaScript,$data).'</style>';
            }
            return $render;
        }else
            throw new \Exception("The '$name' instance already exists for the component '$this->commandName'");

    }

    /**
     * Prepare the component view to be compiled and rendered.
     */
    public function composer(){
        $commandName = $this->commandName;
        app('blade.compiler')->directive($commandName, function($data)use($commandName){
            return '<?php echo '.BladeComponentsProvider::class.'::$registeredComponents[\''.$commandName.'\'][\'instance\']->compile'.$data.' ?>';
        });
    }

    /**
     * Parse the string to get the PHP version of the code
     * @param $string
     * @param array $args
     * @return string
     * @throws \Exception
     */
    public static function parseBladeCode($string,array $args=array()){
        $generated = Blade::compileString($string);
        ob_start(); extract($args,EXTR_SKIP);
        try
        {
            eval('?>'.$generated);
        }
        catch (\Exception $e)
        {
            ob_get_clean(); throw $e;
        }
        $content = ob_get_clean();
        return $content;
    }
}

