<?php namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Create an Module in HMVC
 *
 * @package App\Commands
 * @author Mufid Jamaluddin <https://github.com/MufidJamaluddin/Codeigniter4-HMVC>
 */
class ModuleCreate extends BaseCommand
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
    protected $name        = 'module:create';

    /**
     * the Command's short description
     *
     * @var string
     */
    protected $description = 'Create CodeIgniter HMVC Modules in app/Modules folder';

    /**
     * the Command's usage
     *
     * @var string
     */
    protected $usage        = 'module:create [ModuleName] [Options]';

    /**
     * the Command's Arguments
     *
     * @var array
     */
    protected $arguments    = [ 'ModuleName' => 'Module name to be created' ];

    /**
     * the Command's Options
     *
     * @var array
     */
    protected $options      = [
        '-f' => 'Set module folder inside app path (default Modules)',
        '-v' => 'Set view folder inside app path (default Views/modules/)',
    ];

    /**
     * Module Name to be Created
     */
    protected $module_name;


    /**
     * Module folder (default /Modules)
     */
    protected $module_folder;


    /**
     * View folder (default /View)
     */
    protected $view_folder;


    /**
     * Run route:update CLI
     */
    public function run(array $params)
    {
        helper('inflector');

        $this->module_name = $params[0];

        if(!isset($this->module_name))
        {
            CLI::error("Module name must be set!");
            return;
        }

        $this->module_name = ucfirst($this->module_name);

        $module_folder         = $params['-f'] ?? CLI::getOption('f');
        $this->module_folder   = ucfirst($module_folder ?? 'Modules');

        $view_folder         = $params['-v'] ?? CLI::getOption('v');
        $this->view_folder   = $view_folder ?? 'Views';

        mkdir(APPPATH .  $this->module_folder . '/' . $this->module_name);

        try
        {
            $this->createConfig();
            $this->createController();
            $this->createModel();
            $this->createView();

            CLI::write('Module created!');
        }
        catch (\Exception $e)
        {
            CLI::error($e);
        }
    }

    /**
     * Create Config File
     */
    protected function createConfig()
    {
        $configPath = APPPATH .  $this->module_folder . '/' . $this->module_name . '/Config';

        mkdir($configPath);

        if (!file_exists($configPath . '/Routes.php'))
        {
            $routeName = strtolower($this->module_name);

            $template = "<?php

if(!isset(\$routes))
{ 
    \$routes = \Config\Services::routes(true);
}

\$routes->group('$routeName', ['namespace' => 'App\Modules\\$this->module_name\\Controllers'], function(\$subroutes){

	/*** Route for Dashboard ***/
	\$subroutes->add('', 'Dashboard::index');
	\$subroutes->add('dashboard', 'Dashboard::index');

});";

            file_put_contents($configPath . '/Routes.php', $template);
        }
        else
        {
            CLI::error("Can't Create Routes Config! Old File Exists!");
        }
    }

    /**
     * Create Controller File
     */
    protected function createController()
    {
        $controllerPath = APPPATH .  $this->module_folder . '/' . $this->module_name . '/Controllers';

        mkdir($controllerPath);

        if (!file_exists($controllerPath . '/Dashboard.php'))
        {
            $template = "<?php namespace App\Modules\\$this->module_name\\Controllers;

use App\Modules\\$this->module_name\\Models\UserModel;
use CodeIgniter\Controller;

class Dashboard extends Controller
{
    private \$userModel;

    /**
     * Constructor.
     */
    public function __construct()
    {
        \$this->userModel = new UserModel();
    }

    public function index()
	{
		\$data = [
		    'title' => 'Dashboard Page',
            'view' => '" . strtolower($this->module_name) . "/dashboard',
            'data' => \$this->userModel->getUsers(),
        ];

		return view('template/layout', \$data);
	}

}
";
            file_put_contents($controllerPath . '/Dashboard.php', $template);
        }
        else
        {
            CLI::error("Can't Create Controller! Old File Exists!");
        }
    }

    /**
     * Create Models File
     */
    protected function createModel()
    {
        $modelPath = APPPATH .  $this->module_folder . '/' . $this->module_name . '/Models';

        mkdir($modelPath);

        if (!file_exists($modelPath . '/UserEntity.php')) {
            $template = "<?php namespace App\Modules\\$this->module_name\\Models;

class UserEntity
{
    protected \$id;
    protected \$name;

    public function __construct()
    {

    }

    public static function of(\$uid, \$uname)
    {
        \$user = new UserEntity();
        \$user->setId(\$uid);
        \$user->setName(\$uname);

        return \$user;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return \$this->id;
    }

    /**
     * @param mixed \$id
     */
    public function setId(\$id): void
    {
        \$this->id = \$id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return \$this->name;
    }

    /**
     * @param mixed \$name
     */
    public function setName(\$name): void
    {
        \$this->name = \$name;
    }
}";

            file_put_contents($modelPath . '/UserEntity.php', $template);
        }
        else
        {
            CLI::error("Can't Create UserEntity! Old File Exists!");
        }

        if (!file_exists($modelPath . '/UserModel.php'))
        {

            $template = "<?php namespace App\Modules\\$this->module_name\\Models;

class UserModel
{
    public function getUsers()
    {
        return [
            UserEntity::of('PL0001', 'Mufid Jamaluddin'),
            UserEntity::of('PL0002', 'Andre Jhonson'),
            UserEntity::of('PL0003', 'Indira Wright'),
        ];
    }
}";
            file_put_contents($modelPath . '/UserModel.php', $template);
        }
        else
        {
            CLI::error("Can't Create UserModel! Old File Exists!");
        }
    }

    /**
     * Create View
     */
    protected function createView()
    {
        if($this->view_folder !== $this->module_folder)
            $view_path = APPPATH . $this->view_folder . '/' . strtolower($this->module_name);
        else
            $view_path = APPPATH . $this->module_folder . '/' . $this->module_name . '/Views';

        mkdir($view_path);

        if (!file_exists($view_path . '/dashboard.php'))
        {
            $template = '<section>

	<h1>Dashboard Page</h1>

    <table border=\"1px\">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach (\$data ?? [] as \$key => \$itemUser):?>
            <tr>
                <td><?=\$itemUser->getId() ?? "" ?></td>
                <td><?=\$itemUser->getName() ?? "" ?></td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>

	<p>If you would like to edit this page you will find it located at:</p>

	<pre><code>app/Views/'. strtolower($this->module_name) .'/dashboard.php</code></pre>

	<p>The corresponding controller for this page can be found at:</p>

	<pre><code>app/Modules/'. $this->module_name .'/Controllers/Dashboard.php</code></pre>

</section>';

            file_put_contents($view_path . '/dashboard.php', $template);
        }
        else
        {
            CLI::error("Can't Create View! Old File Exists!");
        }

    }

}