<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014-2019 British Columbia Institute of Technology
 * Copyright (c) 2019-2020 CodeIgniter Foundation
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package    CodeIgniter
 * @author     CodeIgniter Dev Team
 * @copyright  2019-2020 CodeIgniter Foundation
 * @license    https://opensource.org/licenses/MIT	MIT License
 * @link       https://codeigniter.com
 * @since      Version 4.0.0
 * @filesource
 */

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Creates a controller class.
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
        $name = array_shift($params);

        if (empty($name))
        {
            $name = CLI::prompt('Name the module');
        }

        if (empty($name))
        {
            CLI::error('You must provide a module name');
            return;
        }

		//First we have to create a controller class
        $this->createController($name);
        
        //Then we create a model class
		$this->createModel($name);
	}

	private function createController($name)
    {
        helper('inflector');

        $ns = 'App';

        $homepath = APPPATH;

        $fileName = ucwords($name);

        // full path
        $path = $homepath . '/Controllers/' . $fileName . '.php';

        // Class name should be pascal case now (camel case with upper first letter)
        $name = pascalize($name);

        $template = <<<EOD
<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\{name}Model;

class {name} extends Controller
{
    public function index()
    {
        echo 'Hello World!';
    }
}

EOD;
        $template = str_replace('{name}', $name, $template);

        helper('filesystem');
        if (! write_file($path, $template))
        {
            CLI::error("Error trying to create $name file, check if the directory is writable.");
            return;
        }

        CLI::write('Controller class created: ' . CLI::color(str_replace($homepath, $ns, $path), 'green'));
    }

    private function createModel($name)
    {
        helper('inflector');

        $ns = 'App';

        $homepath = APPPATH;

        $fileName = ucwords($name);

        // full path
        $path = $homepath . '/Models/' . $fileName . 'Model.php';

        // Class name should be pascal case now (camel case with upper first letter)
        $name = pascalize($name);

        $template = '
<?php namespace App\Models;

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

        helper('filesystem');
        if (! write_file($path, $template))
        {
            CLI::error("Error trying to create $name file, check if the directory is writable.");
            return;
        }

        CLI::write('Model class created: ' . CLI::color(str_replace($homepath, $ns, $path), 'green'));
    }
}
