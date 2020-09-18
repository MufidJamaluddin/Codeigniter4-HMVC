<?php namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Update an HMVC routes
 *
 * @package App\Commands
 * @author Mufid Jamaluddin <https://github.com/MufidJamaluddin/Codeigniter4-HMVC>
 */
class RouteUpdate extends BaseCommand
{
    /**
	 * The group the command is lumped under
	 * when listing commands.
	 *
	 * @var string
	 */
    protected $group       = 'Development';

    /**
	 * The Command's name
	 *
	 * @var string
	 */
    protected $name        = 'route:update';

	/**
	 * the Command's short description
	 *
	 * @var string
	 */
    protected $description = 'Update CodeIgniter HMVC routes for app/Modules folder';

	/**
	 * the Command's usage
	 *
	 * @var string
	 */
	protected $usage = 'route:update [Options]';

	/**
	 * the Command's Arguments
	 *
	 * @var array
	 */
	protected $arguments = [];

	/**
	 * the Command's Options
	 *
	 * @var array
	 */
	protected $options = [
        '-n' => 'Set module namespace (default App\Modules)',
        '-i' => 'Set route with /index path without parameter (true/false, default true)',
        '-m' => 'Set route one module name to be create/update (app/Modules/YourModuleName)',
        '-f' => 'Set module folder inside app path (default Modules)',
	];

    /**
     * Namespace Name of Route
     */
    protected $namespace_name; 

    /**
     * Module folder (default /Modules)
     */
    protected $module_folder;

    /**
     * Create route with /index path (without parameter) or no (default no)
     */
    protected $with_index;

    /**
     * Run route:update CLI
     */
    public function run(array $params)
    {
        helper('inflector');
        
        $namespace_name        = $params['-n'] ?? CLI::getOption('n');
        $this->namespace_name  = $namespace_name ?? 'App\Modules';

        $withIndex             = $params['-i'] ?? CLI::getOption('i');
        $this->with_index      = $withIndex == 'false' ? false : true;

        $module                = $params['-m'] ?? CLI::getOption('m');
        
        $module_folder         = $params['-f'] ?? CLI::getOption('f');
        $this->module_folder   = $module_folder ?? 'Modules';
        
        try
        {
            if($module)
            {
                $this->make_route_file($module);
            }
            else
            {
                $module_folders = array_filter(glob(APPPATH . $this->module_folder . '/*', GLOB_BRACE), 'is_dir');

                foreach($module_folders as $module)
                {
                    $module = basename($module);

                    $this->make_route_file($module);
                }
            }

            $module = $module ?? '';
            CLI::write("\nModule $module routes has successfully been created.\n");
        }
        catch(Exception $e)
        {
            CLI::error($e);
        }
    }

    /**
     * Make route file of specific module
     */
    protected function make_route_file($module) 
    {
        $module = basename($module);

        CLI::write("\nCreate route for $module");
        
        $group_name = strtolower($module);

        $path = APPPATH . "$this->module_folder/$module/Config/dashboard.php";

        $module_route_config = fopen($path, "w") or die("Unable to create routes file for $module module!");

        $controllers = glob(APPPATH . "$this->module_folder/$module/Controllers/*.php", GLOB_BRACE);

        $configuration_template = "<?php

if(!isset(\$routes))
{ 
    \$routes = \Config\Services::routes(true);
}

\$routes->group('$group_name', ['namespace' => '$this->namespace_name\\$module\Controllers'], function(\$subroutes){
";
        foreach($controllers as $controller)
        {
            $controller = pathinfo($controller, PATHINFO_FILENAME);

            if($controller != 'BaseController')
            {
                $class_name = "$this->namespace_name\\$module\Controllers\\$controller";

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
}