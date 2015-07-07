<?php namespace Moura\BladeComponents\Classes;
use Illuminate\Support\Facades\File;
use Moura\BladeComponents\BladeComponentsProvider;


/**
 * Class BladeComponent
 *
 * @package Moura\BladeComponents\Classes
 */

abstract class BladeComponent {
    /**
     * View que será carregada ao chamar o component
     * @var string;
     */
    private $view;

    /**
     * Commando do blade que deve ser executado
     * @var string;
     */
    private $commandName;

    /**
     * Inputs com seus nomes, ids e for corrigidos
     * @var array
     */
    private $inputs;

    /**
     * Buttons com seus nomes e ids corrigidos
     * @var array
     */
    private $buttons;

    /**
     * Array de dados para ser utilizado na construção do componente
     * @var array
     */
    private $data;

    /**
     * Retorna um array com o código html de todos os buttons que serão utilizados no formulário
     * @return array
     */
    abstract function buttons();

    /**
     * * Retorna um array com o código html de todos os inputs que serão utilizados no formulário
     * @return array
     */
    abstract function inputs();

    /**
     * * Retorna um array com dados para ser utilizado nos inputs
     * @return array
     */
    abstract function data();

    /**
     * Contrutor da classe.
     * Checa se a view existe.
     */
    public function __construct(){
        /* Setando o nome do comando e da view para o componente */
        $this->commandName = explode('\\',get_class($this));
        $this->commandName = end($this->commandName);
        $this->view = $this->commandName;

        /* Recuperando os dados da aplicação */
        $this->data = $this->data();

        /* Ajustando a indentificação dos buttons e inputs */
        $this->setupHtml();

        /* Renderizando o component */
        $this->composer();
    }

    /**
     * Retorna o nome do commando a ser criado
     * @return string
     */
    public function commandName(){
        return $this->commandName;
    }

    /**
     * Retorna a view do compoente
     * @return string
     */
    public function view(){
        return $this->view;
    }

    /**
     * Define o path absoluto da view
     * @param string $path
     */
    public function setView($path){
        $this->view = $path;
    }

    /**
     * Retorna uma posição específica do vetor de dados
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
     * Retorna o array de dados completo
     * @return array
     */
    public function getAllData(){
        return $this->data;
    }

    /**
     * Configura o html que sera injetado na view do component
     */
    public function setupHtml(){
        $this->inputs = $this->inputs();
        $this->buttons = $this->buttons();
    }

    /**
     * Renderiza a view na tela
     * @param $name
     * @param array $data
     * @return string
     * @throws \Exception
     */
    public function compile($name, $data = array()){
        /* Verificando se o nome passado para a instancia do componente já não vou registrado */
        if(!array_key_exists($name,BladeComponentsProvider::$registeredComponents[$this->commandName]['instanceName'])){
            /* Registrando o nome da instância do componente */
            BladeComponentsProvider::$registeredComponents[$this->commandName]['instanceName'][$name] = [];
            /* Criando os inputs de controle para serem utilizados nas requests */
            $this->inputs[$this->commandName]['instanceName'] = \Form::hidden('instanceName',$name);
            $this->inputs[$this->commandName]['component'] = \Form::hidden('component',self::class);
            /* Ajustando o vetor de dados que será passado a view */
            $data['name'] = $name;
            $data['buttons'] = $this->buttons;
            $data['inputs'] = $this->inputs;
            /* Ajustando o html renderizado */
            $render = $this->inputs[$this->commandName]['instanceName'];
            $render .= $this->inputs[$this->commandName]['component'];
            $render .= view('BladeComponents::'.$this->view,$data);
            return $render;
        }else
            throw new \Exception("The '$name' instance already exists for the component '$this->commandName'");

    }

    /**
     * Chama função que vai renderizar a view
     */
    public function composer(){
        $commandName = $this->commandName;
        app('blade.compiler')->directive($commandName, function($data)use($commandName){
            return '<?php echo '.BladeComponentsProvider::class.'::$registeredComponents[\''.$commandName.'\'][\'instance\']->compile'.$data.' ?>';
        });
    }
}

