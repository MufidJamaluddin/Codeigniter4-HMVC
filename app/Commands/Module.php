<?php
/**
 * CodeIgniter
 *
 * Copyright (c) 2014-2019 British Columbia Institute of Technology
 * Copyright (c) 2019-2022 CodeIgniter Foundation
 *
 * @author     CodeIgniter Dev Team, Mufid Jamaluddin
 * @copyright  2019-2022 CodeIgniter Foundation
 * @license    https://opensource.org/licenses/MIT	MIT License
 * @link       https://codeigniter.com
 * @since      Version 4.2.0
 * @filesource
 */

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Creates a controller & models inside a module folder
 *
 * @package CodeIgniter\Commands
 */
class Module extends BaseCommand
{

	/**
	 * The group the command is lumped under
	 * when listing commands.
	 *
	 * @var string
	 */
	protected $group = 'Custom CLI';

	/**
	 * The Command's name
	 *
	 * @var string
	 */
	protected $name = 'module';

	/**
	 * the Command's short description
	 *
	 * @var string
	 */
	protected $description = 'Create a new module.';

	/**
	 * the Command's usage
	 *
	 * @var string
	 */
	protected $usage = 'module [module_name]';

	/**
	 * the Command's Arguments
	 *
	 * @var array
	 */
	protected $arguments = [
		'module_name' => 'The module name',
	];


	/**
	 * Creates a new module.
	 *
	 * @param array $params
	 */
	public function run(array $params = [])
	{
        $module = array_shift($params);
        $controller = array_shift($params);
        $model = array_shift($params);

        if (empty($module))
        {
            $module = CLI::prompt('Module name');
        }

        if (empty($controller))
        {
            $controller = CLI::prompt('Controller name');
        }

        if (empty($model))
        {
            $model = CLI::prompt('Model name');
        }

        if (empty($module))
        {
            CLI::error('You must provide a module name');
            return;
        }

        if (empty($controller))
        {
            CLI::error('You must provide a controller name');
            return;
        }

        if (empty($model))
        {
            CLI::error('You must provide a model name');
            return;
        }

        helper('inflector');
        $module = pascalize($module);
        $controller = pascalize($controller);
        $model = pascalize($model);

        $moduleExist = false;
        if ( is_dir(APPPATH . '/Modules/' . $module) ) {
            $moduleExist = true;
        } else {
            mkdir(APPPATH . '/Modules/' . $module);
        }

        if ( !is_dir(APPPATH . '/Modules/' . $module . '/Controllers/') ) {
            mkdir(APPPATH . '/Modules/' . $module . '/Controllers/');
        }

        if ( !is_dir(APPPATH . '/Modules/' . $module . '/Models/') ) {
            mkdir(APPPATH . '/Modules/' . $module . '/Models/');
        }

        if ( !is_dir(APPPATH . '/Modules/' . $module . '/Config/') ) {
            mkdir(APPPATH . '/Modules/' . $module . '/Config/');
        }
        
        if ( !file_exists( APPPATH . '/Modules/' . $module . '/Config/Routes.php' ) ) {
            $this->createConfig($module, $controller);
        }

        if ( file_exists(APPPATH . '/Modules/' . $module . '/Controllers/' . $controller . '.php') ) {
            CLI::error(
                'Can\'t create new controller because ' . $module . '/'. $controller .'.php exist!'
            );
            return;
        } else {
            $this->createController($module, $controller, $model);
        }

        if ( file_exists(APPPATH . '/Modules/' . $module . '/Models/' . $model . 'Model.php') ) {
            CLI::error(
                'The ' . $module . '/'. $model .'Model.php exist',
                'yellow'
            );
        } else {
            $this->createModel($module, $model);
        }

        if ($moduleExist) {
            $this->updateConfig($module);
        }
	}

    /**
     * Make route file of specific module
     */
    protected function updateConfig($module) 
    {
        $module = basename($module);

        CLI::write("\nCreate route for $module");
        
        $group_name = strtolower($module);

        $path = APPPATH . "/Modules/$module/Config/Routes.php";

        $module_route_config = fopen($path, "w") or die("Unable to create routes file for $module module!");

        $controllers = glob(APPPATH . "Modules/$module/Controllers/*.php", GLOB_BRACE);

        $configuration_template = "<?php

if(!isset(\$routes))
{ 
    \$routes = \Config\Services::routes(true);
}

\$routes->group('$group_name', ['namespace' => 'App\Modules\\$module\Controllers'], function(\$subroutes){
";
        foreach($controllers as $controller)
        {
            $controller = pathinfo($controller, PATHINFO_FILENAME);

            if($controller != 'BaseController')
            {
                $class_name = "App\Modules\\$module\Controllers\\$controller";

                CLI::write("Configurate $class_name");

                $controller_path = strtolower($controller);

                $controller_info = new \ReflectionClass($class_name);

                $class_methods = $controller_info->getMethods(\ReflectionMethod::IS_PUBLIC);

                $configuration_template .= "\n\t/*** Route for $controller ***/\n";

                foreach($class_methods as $key => $method)
                {
                    if(strpos($method->name, '__') === false)
                    {
                        if($method->name == 'initController')
                            continue;

                        if($method->name == 'index' && $method->getNumberOfRequiredParameters() == 0)
                        {
                            $configuration_template .= "\t\$subroutes->add('$controller_path', '$controller::index');\n";
                            if(!$this->with_index && $method->getNumberOfParameters() == 0) continue;
                        }

                        $uri_addons = $method->name;
                        $param_addons = $method->name;
                        $method_parameters = $method->getParameters();
                        
                        foreach($method_parameters as $key => $item_parameter)
                        {
                            if($item_parameter->getType())
                            {
                                $arg_name = $item_parameter->getType()->getName();
                            }
                            else
                            {
                                $arg_name = 'string';
                            }

                            switch($arg_name)
                            {
                                case 'int': 
                                    $uri_addons .= '/(:num)';
                                break;

                                default:
                                    $uri_addons .= '/(:alphanum)';
                                break;
                            }

                            $param_addons .= '/$' . ($key + 1);
                        }
                        
                        $configuration_template .= "\t\$subroutes->add('$controller_path/$uri_addons', '$controller::$param_addons');\n";
                    }
                }
                
            }  
        }

        $configuration_template .= "
});";

        fwrite($module_route_config, $configuration_template);
        fclose($module_route_config);
    }


    private function createConfig($module, $controller) {
        helper('inflector');

        $template = <<<EOD
<?php

if(!isset(\$routes))
{ 
    \$routes = \Config\Services::routes(true);
}

\$routes->group('{module}', ['namespace' => 'App\Modules\{moduleName}\Controllers'], function(\$subroutes){

    /*** Route for {Controller} ***/
    \$subroutes->add('{subpath}', '{Controller}::index');

});

EOD;
        $controller = pascalize($controller);
        $subpath = strtolower($controller);
        $module = strtolower($module);
        $moduleName = pascalize($module);

        $template = str_replace('{module}', $module, $template);
        $template = str_replace('{moduleName}', $moduleName, $template);
        $template = str_replace('{Controller}', $controller, $template);
        $template = str_replace('{subpath}', $subpath, $template);

        $homepath = APPPATH;
        $path = $homepath . '/Modules/' . $moduleName . '/Config/Routes.php';

        helper('filesystem');
        if (! write_file($path, $template))
        {
            CLI::error("Error trying to create $moduleName/Config/Routes.php file, check if the directory is writable.");
            return;
        }

        CLI::write('Module config created: ' . CLI::color(str_replace($homepath, 'App', $path), 'green'));
    }

	private function createController($module, $controller, $model)
    {
        helper('inflector');

        $ns = 'App';

        $homepath = APPPATH;

        $fileName = ucwords($controller);

        // full path
        $path = $homepath . '/Modules/' . $module . '/Controllers/' . $fileName . '.php';

        // Class name should be pascal case now (camel case with upper first letter)
        $controller = pascalize($controller);

        $template = <<<EOD
<?php namespace App\Modules\{moduleName}\Controllers;

use CodeIgniter\Controller;
use App\Modules\{moduleName}\Models\{model}Model;

class {name} extends Controller
{
    public function index()
    {
        \$data = ['title' => '{name} Page', 'view' => 'land/data', 'data' => 'Hello World from {moduleName} Module -> {name}!'];
        return view('template/layout', \$data);
    }
}

EOD;
        $template = str_replace('{name}', $controller, $template);
        $template = str_replace('{model}', $model, $template);
        $template = str_replace('{moduleName}', $module, $template);

        helper('filesystem');
        if (! write_file($path, $template))
        {
            CLI::error("Error trying to create $controller file, check if the directory is writable.");
            return;
        }

        CLI::write('Controller class created: ' . CLI::color(str_replace($homepath, $ns, $path), 'green'));
    }

    private function createModel($module, $name)
    {
        helper('inflector');

        $ns = 'App';

        $homepath = APPPATH;

        $fileName = ucwords($name);

        // full path
        $path = $homepath . '/Modules/' . $module .'/Models/' . $fileName . 'Model.php';

        // Class name should be pascal case now (camel case with upper first letter)
        $name = pascalize($name);

        $template = '<?php namespace App\Modules\{moduleName}\Models;

use CodeIgniter\Model;

class {name}Model extends Model
{
    protected $table      = "";
    protected $primaryKey = "";

    protected $returnType     = array();
    protected $useSoftDeletes = true;

    protected $allowedFields = [];

    protected $useTimestamps = false;
    protected $createdField  = "created_at";
    protected $updatedField  = "updated_a";
    protected $deletedField  ="deleted_at";

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}';
        $template = str_replace('{name}', $name, $template);
        $template = str_replace('{moduleName}', $module, $template);

        helper('filesystem');
        if (! write_file($path, $template))
        {
            CLI::error("Error trying to create $name file, check if the directory is writable.");
            return;
        }

        CLI::write('Model class created: ' . CLI::color(str_replace($homepath, $ns, $path), 'green'));
    }
}
