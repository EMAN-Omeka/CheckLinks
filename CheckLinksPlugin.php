<?php
/**
 * Check Links
 * @copyright Copyright 2010-2014 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Check Links plugin.
 */
class CheckLinksPlugin extends Omeka_Plugin_AbstractPlugin
{
  protected $_hooks = array(
    'define_acl',
    'define_routes',
 		'admin_head',
 		'install',
 		'uninstall',
  );
  protected $_filters = array(
  	'admin_navigation_main',
  );

  function hookAdminHead()
  {
    queue_css_file('checklinks');
  }

  function hookDefineRoutes($args)
    {
  		$router = $args['router'];
   		$router->addRoute(
  				'cl_page',
  				new Zend_Controller_Router_Route(
  						'checklinks',
  						array(
  								'module' => 'check-links',
  								'controller'   => 'page',
  								'action'       => 'checklinks',
  						)
  				)
  		);
    }

  /**
   * Add the pages to the public main navigation options.
   *
   * @param array Navigation array.
   * @return array Filtered navigation array.
   */
  public function filterAdminNavigationMain($nav)
  {
    $nav[] = array(
                    'label' => __('Check Links'),
                    'uri' => url('checklinks'),
                    'recource' => 'CheckLinks_Page',
                  );
    return $nav;
  }


  function hookDefineAcl($args)
  {
  	$acl = $args['acl'];
  	$checkLinksAdmin = new Zend_Acl_Resource('CheckLinks_Page');
  	$acl->add($checkLinksAdmin);
  	$acl->deny(array('admin', 'contributor', 'editor'), array('CheckLinks_Page'));
    $acl->allow(array('super'), array('CheckLinks_Page'));
  }

  /**
   * Install the plugin.
   */
  public function hookInstall()
  {
	  $db = $this->_db;
	  $sql = "CREATE TABLE IF NOT EXISTS `$db->CheckLinks` (
	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `link` text NOT NULL,
	  `code` varchar(20),
	  `type` varchar(25),
	  `editlink` text COLLATE utf8_unicode_ci,
	  PRIMARY KEY (`id`)
	  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
	  $db->query($sql);
  }

   /**
   * Uninstall the plugin.
   */
  public function hookUnInstall()
  {
	  $db = $this->_db;
  	$sql = "DROP TABLE IF EXISTS `$db->CheckLinks`";
	  $db->query($sql);
	}

}