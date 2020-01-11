<?php
/**
 *
 * Display Last Post extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2013 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace aurelienazerty\styleta\event;

/**
 * Event listener
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	
	/** @var \phpbb\template\template */
	protected $template;

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
	 * @return \aurelienazerty\styleta\event\listener
	 * @access public
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->user = $user;
		$this->template = $template;
		$this->db = $db;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.page_footer'		=> 'ajout_publicite',
			'core.user_setup' 		=> 'load_language_on_setup',
		);
	}
	
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'aurelienazerty/styleta',
			'lang_set' => 'langues',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}
	
	public function ajout_publicite($event)
	{
		$sql_array = array(
			'SELECT'	=> 'u.valeur',
				'FROM'		=> array(
					'user_pref'	=> 'u',
					'user_pref_type' => 'p', 
				),
				'WHERE' => 
					'u.user_id = ' . (int) $this->user->data["user_id"] . 
					' AND u.user_pref_type_id = p.user_pref_type_id' .
					' AND input_name = "publicite"',
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$sql_fetchrow = $this->db->sql_fetchrow($result);
		$pub = $sql_fetchrow['valeur'];
		$this->template->assign_var('DISPLAY_PUB', ($pub == 'y'));
	}
}
