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

	/** @var Symfony\Component\DependencyInjection\ContainerBuilder */
	protected $phpbb_container;

	public function __construct(\phpbb\user $user, \phpbb\config\config $config, \Symfony\Component\DependencyInjection\ContainerBuilder $phpbb_container)
	{
		$this->user = $user;
		$this->config = $config;
		$this->phpbb_container = $phpbb_container;
		$this->user->add_lang(array('acp/common'));
		parent::__construct();
	}

	protected function configure()
	{
		$this
			->setName('update:check')
			->setDescription($this->user->lang('CLI_DESCRIPTION_UPDATE_CHECK'))
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$version_helper = $this->phpbb_container->get('version_helper');
		try
		{
			$recheck = true;
			$updates_available = $version_helper->get_suggested_updates($recheck);
		}
		catch (\RuntimeException $e)
		{
			$output->writeln($this->user->lang('S_VERSIONCHECK_FAIL'));

			$updates_available = array();
		}

		if (!empty($updates_available))
		{
			$output->writeln($this->user->lang('UPDATE_NEEDED'));
			$output->writeln($this->user->lang('UPDATES_AVAILABLE'));
			foreach ($updates_available as $branch => $version_data)
			{
				$output->writeln($version_data);
			}
		}
		else
		{
			$output->writeln($this->user->lang('UPDATE_NOT_NEEDED'));
		}
	}
}
