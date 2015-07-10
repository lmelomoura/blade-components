##BladeComponents - Pacote para criação de componentes não acoplados no Laravel 5.1 utilizando diretivas do Blade

![BladeComponents Logo](https://raw.githubusercontent.com/lmelomoura/blade-components/master/src/Assets/logo.png)

Esse pacote permite criar de forma fácil e simplifica componentes com funções diversas para serem utilizados e reutilizados de maneira simples.
Com a disponibilização de uma nova funcionalidade no Artisan, é possível com um único comando gerar toda a estrutura de funcionamento de seus novos componentes, separando todos os seus arquivos de forma organizada (PHP, CSS, HTML e Javascript).

[![GitHub issues](https://img.shields.io/github/issues/lmelomoura/blade-components.svg)](https://github.com/lmelomoura/blade-components/issues)
[![GitHub forks](https://img.shields.io/github/forks/lmelomoura/blade-components.svg)](https://github.com/lmelomoura/blade-components/network)
[![GitHub stars](https://img.shields.io/github/stars/lmelomoura/blade-components.svg)](https://github.com/lmelomoura/blade-components/stargazers)

##Instalação
O componente deve ser instalado (requerido) dentro do diretório padrão de sua aplicação através Composer, com o comando:

    composer require moura/bladecomponents

##Configuração
Após a instalação algumas configurações precisam ser feitas para que o BladeComponents funcione e para que o Blade consiga renderizar de maneira correta seus componentes.

####Configurando o service provider
O `provider` do BladeComponents precisa ser adicionado ao vetor de `providers` dentro do arquivo `config/app.php`

    Moura\BladeComponents\BladeComponentsProvider::class
  
>*Se o provider não for devidamente adicionado ao vetor de providers do Laravel, nenhum componente criado pelo BladeComponents irá funcionar*

####Gerando o arquivo de configuração
O BladeComponents, precisa de um arquivo de configuração. Para começar, precisamos criar esse arquivo de configuração em `config/bladecomponents.php`
Execute o comando:

    php artisan vendor:publish
  
Este comando irá criar o arquivo de configuração para sua aplicação. Você pode modificar esse arquivo para definir a sua própria configuração. 

>*Certifique-se de verificar se há alterações no arquivo de configuração original deste pacote entre os lançamentos de novas versões*

##Utilização do BladeComponents
A utilização do BladeComponents é bastante simples.
Para criar um novo componente, execute o comando:

    php artisan make:bladecomponent Name
  
Esse comando fará com que o BladeComponents gere toda a estrutura necessária para o funcionamento do novo componente.
Por padrão, o componente é criado utilizando o nome passado e concatenado ao final a plavra `componente` 
Ao final seguinte estrutura de diretórios e arquivos será criada em sua aplicação:
	
    <app>\
    	<BladeComponentes>\
    		<Components>\
    			NameComponent.php
    		<Scripts>\
    			<css>\
    				NameComponent.css
    			<js>\
    				NameComponent.js
    		<Views>\
    			NameComponent.blade.php

Agora que o componente foi criado, ele precisa ser registrado no vetor de configuração de componentes que está localizado dentro do arquivo `config/bladecomponents.php`
Após editar o arquivo `config/bladecomponents.php` e registrar o novo componente, ele estará pronto para ser implementado.

>*É possível gerar um componente sem arquivos CSS e JS exclusivos. Bastar informar o parâmetro `--plain` para o comando do BladeComponents desta maneira:*
  
    php artisan make:bladecomponent Name --plain

##Implementação do component
A implementação do componente deve ser feita alterando os arquivo gerados de acordo com a necessidade do seu aplicativo.
>*O BladeComponents permite que se use _diretivas do Blade_ dentro de todos os seus arquivos. É possível por exemplo utilizar diretivas de controle e laços de repetição _dentro dos arquivos JS e CSS_ de forma transparente*

###A classe BladeComponent
Todo componente criado pelo BladeComponents (exemplo `app/BladeComponents/Components/NameComponent.php`) herda uma classe abstrata própria. 
A estrutura padrão de classe de um componente é a seguinte:
```php
class NameComponent extends BladeComponent{
  function inputs()
  {
    return [
    
    ];
  }
  
  function buttons()
  {
    return [
    
    ];
  }
  
  function data()
  {
    return [
    
    ];
  }
}
```
Essa classe abstrata herdada pelos components é a `BladeComponent` que implementa 3 métodos abstratos. São eles:

##### inputs()

    asbtract function inputs();
  
>*Essa função retorna ao BladeComponents um array associativo contendo o código HTML de todos os `inputs`, `labels`, `selects`, `radio buttons`, `checkboxes` e `textareas` que serão utilizados pelos arquivos de view, css e js através do vetor de inputs `$inputs['identificador']`*

Exemplo de implementação da `asbtract function inputs()` com um `label` e um `input` text:

```php
function inputs()
{
  return [
    'foolabel' => Form::label(
        'fooSelect',
        'Text for label',
        [
            'class' => 'foo-class'
        ]
    ),
    'fooSelect' => Form::select(
        'fooSelect',
        [' '],
        null,
        [
            'multiple' => null,
            'class'    => 'foo-class'
        ]
    )
  ];
}
```
>*É extremamente recomendado utilizar a Form facade para gerar o HTML de todos os inputs e assim seguir o padrão Laravel para garantir o correto funcionamento do componente*

##### buttons()

    asbtract function buttons();

>*Essa função retorna ao BladeComponents um array associativo contendo o código HTML de todos os `buttons` que serão utilizados pelos arquivos de view, css e js através do vetor de buttons `$buttons['identificador']`*

Exemplo de implementação da `asbtract function buttons()` com um submit `button` e um reset `button`:
```php
function buttons()
{
  return [
    'submitButton' => Form::submit('Click Me!'),
    'resetButton' => Form::reset('Clear form')
  ];
}
```
>*É extremamente recomendado utilizar a Form facade para gerar o HTML de todos os `buttons` e assim seguir o padrão Laravel para garantir o correto funcionamento do componente*  

##### data()

    asbtract function data();

>*Essa função retorna ao BladeComponents um array associativo contendo objetos de dados que poderão ser utilizados para a construção dos `inputs` e `buttons` através da função `$this->getData('identificador')` ou pelos arquivos de view, css e js através do vetor de dados `$data['identificador']`*

Exemplo de implementação da `asbtract function data()` com `objeto` de dados qualquer:

```php
function data()
{
  return [
    'fooObject' => app(FooNameSpace\FooClass)
  ];
}
```


###CSS de estilo do componente
Quando o compoente é criado pelo BladeComponents sem que o parâmetro `--plain` seja passado ao comando, por padrão um arquivo CSS é criado para o novo componente.

    app/BladeComponents/Scripts/css/ComponentName.css

Utlize esse arquivo para criar os estilos prórpios de seu componente.

#### Utilizando diretivas blade dentro de arquivos CSS
O BladeComponents permite ao desenvolvedor que utilize diretivas blade dentro do arquivo CSS de stilo padrão do componente.
Exemplo de arquivo CSS contendo diretivas Blade:
```css
.componentName-class div{
  @if(array_key_exists('fooLabel',$inputs))
    border-color : red;
  @else
    border-color : blue;
  @endif;
}
```
>*É possível utlizar todos os inputs, buttons e data objects que forão defidos. Todas as diretivas do blade estão disponíveis para utlização, permitindo que se crie um arquivo CSS `dinâmico`*

###Javascript de controle do componente

Quando o compoente é criado pelo BladeComponents sem que o parâmetro `--plain` seja passado ao comando, por padrão um arquivo JS é criado para o novo componente.

    app/BladeComponents/Scripts/js/ComponentName.js

Utlize esse arquivo para criar todo o javascript de controle de seu componente

#### Utilizando diretivas blade dentro de arquivos JS
O BladeComponents permite ao desenvolvedor que utilize diretivas blade dentro do arquivo JS de controle padrão do componente.
Exemplo de arquivo JS contendo diretivas Blade utilizando jQuery framework:
```js
$(document).ready(function() {
  @if(array_key_exists('fooLabel',$inputs))
    console.log("ready!");
  @else
    console.log("Not ready!");
  @endif;
});
```
>*É possível utlizar todos os inputs, buttons e data objects que forão defidos. Todas as diretivas do blade estão disponíveis para utlização, permitindo que se crie um arquivo JS `dinâmico`*

## Utilização dos componentes dentro da aplicação
Todo componente criado e registrado no arquivo de configuração do BladeComponents, é também transformado em uma diretiva do Blade e sua utilização é muito simples.
Dentro de um arquivo de view qualquer, faça o seguinte:
```html
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
  @NameComponent('Name');
</body>
</html>
```

>*É possível que ao chamar o componente, seja passado como segundo parâmtro um vetor com variáveis para serem utilizadas na view padrão do componente*

```html
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
  @NameComponent('Name',['foo' => 'bar','other' => ObjectClass]);
</body>
</html>
```
