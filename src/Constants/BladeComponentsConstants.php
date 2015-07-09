<?php namespace Moura\BladeComponents\Constants;

abstract class BladeComponentsConstants{
    /* Default Directory constants */
    const DIRECTORY_CREATION_SUCCESS = 'Base directories successfully created!';
    const DIRECTORY_BASE_REPOSITORY = 'BladeComponents';
    const DIRECTORY_BASE_COMPONENTS = 'Components';
    const DIRECTORY_BASE_SCRIPTS = 'Scripts';
    const DIRECTORY_BASE_SCRIPTS_JS = 'js';
    const DIRECTORY_BASE_SCRIPTS_CSS = 'css';
    const DIRECTORY_BASE_VIEWS = 'Views';

    /* Default Component constants */
    const COMPONENT_EXISTS_ERROR = 'There is a component with the given name. Please check the given name';
    const COMPONENT_CREATION_SUCCESS = 'Done!!';
    const COMPONENT_CREATION_ERROR = 'Error in the component creation process: ';
    const COMPONENT_NAME_CONCAT = 'Component';
    const COMPONENT_STUB_NAME = 'BladeComponents.stub';

    /* Default View constants*/
    const VIEW_CREATION_ERROR = "Can't create the view file. Check if you have permission to create it.";
    const VIEW_CREATION_SUCCESS = 'View successfully created!';
    const VIEW_CREATION_COMMENT = "{{-- Default view for DummyClass --}}";

    /* Default Scripts constants */
    const SCRIPT_CREATION_SUCCESS = 'Scripts successfully created!';
    const SCRIPT_CREATION_JS_ERROR = "Can't create the javascript file. Check if you have permission to create it.";
    const SCRIPT_CREATION_JS_COMMENT = "/* Default javascript file for DummyClass */";
    const SCRIPT_CREATION_CSS_ERROR = "Can't create the CSS file. Check if you have permission to create it.";
    const SCRIPT_CREATION_CSS_COMMENT = "/* Default CSS file for DummyClass */";

    /* Default Class constants */
    const CLASS_CREATION_ERROR = "Can't create the class file. Check if you have permission to create it.";
    const CLASS_CREATION_SUCCESS = 'Class successfully created!';
    const CLASS_CREATION_COMMENT = "/* Default class file for component DummyClass */";
};


