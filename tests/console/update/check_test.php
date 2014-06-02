<?php
/**
 *
 * @package testing
 * @copyright (c) 2014 phpBB Group
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

require_once dirname(__FILE__) . '/tasks/simple.php';
require_once dirname(__FILE__) . '/tasks/simple_not_ready.php';
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use phpbb\console\command\update\check;

class phpbb_console_command_check_test extends phpbb_test_case
{
	/** @var \phpbb\user */
	protected $user;

	protected $command_name;

	/** @var \phpbb\config\config */
	protected $config;

	protected function setUp()
	{
		$this->user = $this->getMock('\phpbb\user');
		$this->user->method('lang')->will($this->returnArgument(0));
	}

	public function test_up_to_date()
	{

		$command_tester = $this->get_command_tester();
		$command_tester->execute(array('command' => $this->command_name, '--no-ansi' => true));
		$this->assertContains('UPDATE_NOT_NEEDED', preg_replace('/\s+/', '', $command_tester->getDisplay()));
	}

	public function test_not_up_to_date()
	{
		$command_tester = $this->get_command_tester();
		$command_tester->execute(array('command' => $this->command_name, '--no-ansi' => true));
		$this->assertContains('UPDATE_NEEDEDUPDATES_AVAILABLE', preg_replace('/\s+/', '', $command_tester->getDisplay()));
	}

	public function get_command_tester()
	{
		$application = new Application();
		$application->add(new check($this->command_name, $this->user));
		// ??????????????????????????????

		$command = $application->find('update:check');
		$this->command_name = $command->getName();
		return new CommandTester($command);
	}
}
