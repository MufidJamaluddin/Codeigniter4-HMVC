<?php namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Creates a new seed file.
 *
 * @package App\Commands
 * @author Mufid Jamaluddin <https://github.com/MufidJamaluddin/Codeigniter4-HMVC>
 */
class SeedMake extends BaseCommand
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
    protected $name        = 'seed:make';

	/**
	 * the Command's short description
	 *
	 * @var string
	 */
    protected $description = 'Make Seed Class File';

	/**
	 * the Command's usage
	 *
	 * @var string
	 */
	protected $usage       = 'seed:make [seed_name] [Options]';

	/**
	 * the Command's Arguments
	 *
	 * @var array
	 */
	protected $arguments   = [
		'seed_name' => 'The seed file name',
	];

	/**
	 * the Command's Options
	 *
	 * @var array
	 */
	protected $options     = [
        '-n' => 'Set seed namespace',
        '-table' => 'Set table name'
	];

	/**
	 * Creates a new seed file with the current timestamp.
	 *
	 * @param array $params
	 */
	public function run(array $params = [])
	{
		helper('inflector');
		$name = array_shift($params);

		if (empty($name))
		{
			$name = CLI::prompt(lang('Commands.nameSeed'));
		}

		if (empty($name))
		{
			CLI::error(lang('Commands.badCreateName'));
			return;
		}

		$ns       = $params['-n'] ?? CLI::getOption('n');
		$table    = $params['-table'] ?? CLI::getOption('table');
		$homepath = APPPATH;

		if (! empty($ns))
		{
			$config     = new Autoload();
			$namespaces = $config->psr4;

			foreach ($namespaces as $namespace => $path)
			{
				if ($namespace === $ns)
				{
					$homepath = realpath($path);
					break;
				}
			}
		}
		else
		{
			$ns = 'App';
		}

		$config   = config('Seeds');
		$fileName = $name;

		$path = $homepath . '/Database/Seeds/' . $fileName . '.php';

		$name = pascalize($name);

		$template = <<<EOD
<?php namespace $ns\Database\Seeds;

class {name} extends Seeder
{
	private \$table = '{table}';
	private \$seed_size = 33;

	public function run()
	{
        \$this->db->truncate(\$this->table);

        /*
        \$data = [
            'username' => 'superadmin',
            'password' => 'majubersama'
        ];
        \$this->db->insert(\$this->table, \$data);

        echo \"seeding \$seed_size user accounts\";

        for (\$i = 0; \$i < \$seed_size; \$i++) 
        {
            echo \".\";

            \$data = array(
                'username' => \$this->faker->unique()->userName,
                'password' => 'majubersama',
            );

            \$this->db->insert(\$this->table, \$data);
        }
        */
	}
}

EOD;
		$template = str_replace('{name}', $name, $template);
		$template = str_replace('{table}', $table ?? $name, $template);

		helper('filesystem');
		if (! write_file($path, $template))
		{
			CLI::error(lang('Commands.writeError'));
			return;
		}

		CLI::write('Created file: ' . CLI::color(str_replace($homepath, $ns, $path), 'green'));
	}

}
