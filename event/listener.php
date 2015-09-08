<?php
/**
 *
 * Display Last Post extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2013 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace Aurelienazerty\styleTA\event;

/**
 * Event listener
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\user $user */
	protected $user;
	
	/** @var int */
	private $last_post_id;

	/**
	 * Constructor
	 *
	 * @param \phpbb\db\driver\driver_interface    $db               DBAL object
	 * @param \phpbb\config\config	$config	Config object
	 * @param \phpbb\user	$user	user object
	 * @return \Aurelienazerty\styleTA\event\listener
	 * @access public
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\user $user)
	{
		$this->user = $user;
		$this->config = $config;
		$this->db = $db;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.page_footer'		=> 'ajout_publicite',
		);
	}
	
	public function ajout_publicite($event)
	{
		$sql_array = array(
			'SELECT'	=> 'u.publicite',
				'FROM'		=> array(
					'user_site_pref'	=> 'u',
				),
				'WHERE' => 'u.user_id = ' . (int) $user['user_id'],
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, 1, 1);
		$pub = $this->db->sql_fetchrow($result)['publicite'];
		$this->template->assign_var('DISPLAY_PUB', $pub == 'y');
	}
}
