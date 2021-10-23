<?php
/*
 *  package: Custom Quick Icons
 *  copyright: Copyright (c) 2021. Jeroen Moolenschot | Joomill
 *  license: GNU General Public License version 3 or later
 *  link: https://www.joomill-extensions.com
 */

namespace Joomill\Module\Customquickicon\Administrator\Dispatcher;

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;

/**
 * Dispatcher class for mod_custom_quickicon
 *
 * @since  4.0.0
 */
class Dispatcher extends AbstractModuleDispatcher
{
	/**
	 * Returns the layout data.
	 *
	 * @return  array
	 *
	 * @since   4.0.0
	 */
	protected function getLayoutData()
	{
		$data = parent::getLayoutData();

		$helper          = $this->app->bootModule('mod_custom_quickicon', 'administrator')->getHelper('CustomQuickIconHelper');
		$data['buttons'] = $helper->getButtons($data['params'], $this->getApplication());
		return $data;
	}
}
