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
        '-i' => 'Set route with index method (true/false, default true)',
	];


    public function run(array $params)
    {
        helper('inflector');
        
        $namespace_name  = $params['-n'] ?? CLI::getOption('n');
        $namespace_name  = $namespace_name ?? 'App\Modules';

        $withIndex       = $params['-i'] ?? CLI::getOption('i');
        $withIndex       = $withIndex == 'false' ? false : true;
        
        try
        {
            $this->make_route_file($namespace_name, $withIndex);
        }
        catch(Exception $e)
        {
            CLI::error($e);
        }
    }

    protected function make_route_file($namespace_name, $withIndex) 
    {
        $module_folders = array_filter(glob(APPPATH . 'modules/*', GLOB_BRACE), 'is_dir');

        foreach($module_folders as $module)
        {
            $module = basename($module);

            CLI::write("\nCreate route for $module");
            
            $group_name = strtolower($module);

            $path = APPPATH . "modules/$module/config/Routes.php";

            $module_route_config = fopen($path, "w") or die("Unable to create routes file for $module module!");

            $controllers = glob(APPPATH . "modules/$module/controllers/*.php", GLOB_BRACE);

            $configuration_template = "<?php

\$routes->group('$group_name', ['namespace' => '$namespace_name\\$module\Controllers'], function(\$subroutes){
";
            foreach($controllers as $controller)
            {
                $controller = pathinfo($controller, PATHINFO_FILENAME);

                if($controller != 'BaseController')
                {
                    $class_name = "$namespace_name\\$module\Controllers\\$controller";

                    CLI::write("Configurate $class_name");

                    $class_methods = get_class_methods($class_name);
                    $controller_path = strtolower($controller);

                    if($class_methods)
                    {
                        $configuration_template .= "\n\t/*** Route for $controller ***/\n";

                        foreach($class_methods as $key => $method)
                        {
                            if(strpos($method, '__') === false)
                            {
                                if($method == 'initController')
                                    continue;
                                    
                                if($method == 'index')
                                {
                                    $configuration_template .= "\t\$subroutes->add('$controller_path', '$controller::index');\n";
                                    if(!$withIndex) continue;
                                }
                                
                                $configuration_template .= "\t\$subroutes->add('$controller_path\\$method', '$controller::$method');\n";
                            }
                        }
                    }
                }  
            }

            $configuration_template .= "
});";

            fwrite($module_route_config, $configuration_template);
            fclose($module_route_config);
        }

        CLI::write("\nModule routes has successfully been created.\n");
    }
}