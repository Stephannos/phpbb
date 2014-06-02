<?php
/**
*
* @package phpBB3
* @copyright (c) 2014 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace phpbb\console\command\update;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class check extends \phpbb\console\command\command
{
	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\config\config */
	protected $config;

	public function __construct(\phpbb\user $user, \phpbb\config\config $config)
	{
		$this->user = $user;
		$this->config = $config;
		$this->user->add_lang(array('acp/common'));
		parent::__construct();
	}

	protected function configure()
	{
		$this
			->setName('update:check')
			->setDescription($this->user->lang('CLI_UPDATE_CHECK'))
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		global $config, $user, $request;
		global $phpbb_root_path, $phpEx, $phpbb_container;

		$user->add_lang('install');

		$version_helper = $phpbb_container->get('version_helper');
		try
		{
			$recheck = $request->variable('versioncheck_force', false);
			$updates_available = $version_helper->get_suggested_updates($recheck);
		}
		catch (\RuntimeException $e)
		{
			$output->writeln('S_VERSIONCHECK_FAIL');

			$updates_available = array();
		}

		if (!empty($updates_available))
		{
			$output->writeln('UPDATE_NEEDED');
			$output->writeln('UPDATES_AVAILABLE');
			foreach ($updates_available as $branch => $version_data)
			{
				$output->writeln($version_data);
			}
		}
		else
		{
			$output->writeln('UPDATE_NOT_NEEDED');
		}
	}
}
