<?php
/*
 *  package: Custom Quick Icons
 *  copyright: Copyright (c) 2021. Jeroen Moolenschot | Joomill
 *  license: GNU General Public License version 3 or later
 *  link: https://www.joomill-extensions.com
 */

namespace Joomill\Module\Customquickicon\Administrator\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use Joomill\Module\Customquickicon\Administrator\Event\CustomQuickIconsEvent;

/**
 * Helper for mod_custom_quickicon
 *
 * @since  1.6
 */
class CustomQuickIconHelper
{
	/**
	 * Stack to hold buttons
	 *
	 * @var     array[]
	 * @since   1.6
	 */
	protected $buttons = array();

	/**
	 * Helper method to return button list.
	 *
	 * This method returns the array by reference so it can be
	 * used to add custom buttons or remove default ones.
	 *
	 * @param   Registry        $params       The module parameters
	 * @param   CMSApplication  $application  The application
	 *
	 * @return  array  An array of buttons
	 *
	 * @since   1.6
	 */
	public function getButtons(Registry $params, CMSApplication $application = null)
	{
		if ($application == null)
		{
			$application = Factory::getApplication();
		}

		$key     = (string) $params;
		$context = (string) $params->get('context', 'mod_custom_quickicon');

		if (!isset($this->buttons[$key]))
		{
			// Load mod_custom_quickicon language file in case this method is called before rendering the module
			$application->getLanguage()->load('mod_custom_quickicon');

			$this->buttons[$key] = [];

			// JOOMLA DEFAULT QUICKICONS
            if ($params->get('show_users'))
            {
                $quickicon = [
                    'image'   => 'icon-users',
                    'link'    => Route::_('index.php?option=com_users&view=users'),
                    'linkadd' => Route::_('index.php?option=com_users&task=user.add'),
                    'name'    => 'MOD_QUICKICON_USER_MANAGER',
                    'access'  => array('core.manage', 'com_users', 'core.create', 'com_users'),
                    'group'   => $context,
                ];

                if ($params->get('show_users') == 2)
                {
                    $quickicon['amount'] = 'index.php?option=com_users&amp;task=users.getQuickiconContent&amp;format=json';
                }

                $this->buttons[$key][] = $quickicon;
            }

            if ($params->get('show_menuitems'))
            {
                $quickicon = [
                    'image'   => 'icon-list',
                    'link'    => Route::_('index.php?option=com_menus&view=items&menutype='),
                    'linkadd' => Route::_('index.php?option=com_menus&task=item.add'),
                    'name'    => 'MOD_QUICKICON_MENUITEMS_MANAGER',
                    'access'  => array('core.manage', 'com_menus', 'core.create', 'com_menus'),
                    'group'   => $context,
                ];

                if ($params->get('show_menuitems') == 2)
                {
                    $quickicon['ajaxurl'] = 'index.php?option=com_menus&amp;task=items.getQuickiconContent&amp;format=json';
                }

                $this->buttons[$key][] = $quickicon;
            }

            if ($params->get('show_articles'))
            {
                $quickicon = [
                    'image'   => 'icon-file-alt',
                    'link'    => Route::_('index.php?option=com_content&view=articles'),
                    'linkadd' => Route::_('index.php?option=com_content&task=article.add'),
                    'name'    => 'MOD_QUICKICON_ARTICLE_MANAGER',
                    'access'  => array('core.manage', 'com_content', 'core.create', 'com_content'),
                    'group'   => $context,
                ];

                if ($params->get('show_articles') == 2)
                {
                    $quickicon['ajaxurl'] = 'index.php?option=com_content&amp;task=articles.getQuickiconContent&amp;format=json';
                }

                $this->buttons[$key][] = $quickicon;
            }

            // ARTICLE QUICKICONS
            $items = $params->get('article_items', []);
            $items = (array) $items;

            foreach ($items as $item)
            {
                $db = Factory::getDbo();
                $db->setQuery('SELECT title FROM #__content WHERE id='.$item->item_id);
                $title = $db->loadResult();

                $quickicon = [
                    'image'   => $item->item_icon,
                    'link'    => Route::_("index.php?option=com_content&view=article&task=article.edit&id=$item->item_id"),
                    'name'    => $title,
                    'group'   => $context,
                ];

                $this->buttons[$key][] = $quickicon;
            }

            // CATEGORY QUICKICONS
            $items = $params->get('category_items', []);
            $items = (array) $items;

            foreach ($items as $item)
            {
                $db = Factory::getDbo();
                $db->setQuery('SELECT title FROM #__categories WHERE id='.$item->article_category);
                $name = $db->loadResult();
                if ($item->article_language != "*") {
                    $title =  $name .  ' (' . $item->article_language .')';
                } else {
                    $title =  $name;
                }

                $quickicon = [
                    'image'   => $item->item_icon,
                    'link'    => Route::_("index.php?option=com_content&filter[category_id]=$item->article_category&filter[language]=$item->article_language&filter[published]=1&filter[level]=1"),
                    'linkadd'    => Route::_("index.php?option=com_content&view=article&layout=edit&catid=$item->article_category&language=$item->article_language"),
                    'name'    => $title,
                    'group'   => $context,
                ];

                $this->buttons[$key][] = $quickicon;
            }

            if ($params->get('show_categories'))
            {
                $quickicon = [
                    'image'   => 'icon-folder-open',
                    'link'    => Route::_('index.php?option=com_categories&view=categories&extension=com_content'),
                    'linkadd' => Route::_('index.php?option=com_categories&task=category.add'),
                    'name'    => 'MOD_QUICKICON_CATEGORY_MANAGER',
                    'access'  => array('core.manage', 'com_categories', 'core.create', 'com_categories'),
                    'group'   => $context,
                ];

                if ($params->get('show_categories') == 2)
                {
                    $quickicon['ajaxurl'] = 'index.php?option=com_categories&amp;task=categories.getQuickiconContent&amp;format=json';
                }

                $this->buttons[$key][] = $quickicon;
            }

            if ($params->get('show_media'))
            {
                $this->buttons[$key][] = [
                    'image'  => 'icon-images',
                    'link'   => Route::_('index.php?option=com_media'),
                    'name'   => 'MOD_QUICKICON_MEDIA_MANAGER',
                    'access' => array('core.manage', 'com_media'),
                    'group'  => $context,
                ];
            }

            if ($params->get('show_modules'))
            {
                $quickicon = [
                    'image'   => 'icon-cube',
                    'link'    => Route::_('index.php?option=com_modules&view=modules&client_id=0'),
                    'linkadd' => Route::_('index.php?option=com_modules&view=select&client_id=0'),
                    'name'    => 'MOD_QUICKICON_MODULE_MANAGER',
                    'access'  => array('core.manage', 'com_modules'),
                    'group'   => $context
                ];

                if ($params->get('show_modules') == 2)
                {
                    $quickicon['ajaxurl'] = 'index.php?option=com_modules&amp;task=modules.getQuickiconContent&amp;format=json';
                }

                $this->buttons[$key][] = $quickicon;
            }
            // MODULE QUICKICONS
            $items = $params->get('module_items', []);
            $items = (array) $items;

            foreach ($items as $item)
            {
                $db = Factory::getDbo();
                $db->setQuery('SELECT title FROM #__modules WHERE id='.$item->item_id);
                $title = $db->loadResult();

                $quickicon = [
                    'image'   => $item->item_icon,
                    'link'    => Route::_("index.php?option=com_modules&view=module&task=module.edit&id=$item->item_id"),
                    'name'    => $title,
                    'group'   => $context,
                ];

                $this->buttons[$key][] = $quickicon;
            }

            if ($params->get('show_plugins'))
            {
                $quickicon = [
                    'image'  => 'icon-plug',
                    'link'   => Route::_('index.php?option=com_plugins'),
                    'name'   => 'MOD_QUICKICON_PLUGIN_MANAGER',
                    'access' => array('core.manage', 'com_plugins'),
                    'group'  => $context
                ];

                if ($params->get('show_plugins') == 2)
                {
                    $quickicon['ajaxurl'] = 'index.php?option=com_plugins&amp;task=plugins.getQuickiconContent&amp;format=json';
                }

                $this->buttons[$key][] = $quickicon;
            }

            if ($params->get('show_template_styles'))
            {
                $this->buttons[$key][] = [
                    'image'  => 'icon-paint-brush',
                    'link'   => Route::_('index.php?option=com_templates&view=styles&client_id=0'),
                    'name'   => 'MOD_QUICKICON_TEMPLATE_STYLES',
                    'access' => array('core.admin', 'com_templates'),
                    'group'  => $context
                ];
            }

            if ($params->get('show_template_code'))
            {
                $this->buttons[$key][] = [
                    'image'  => 'icon-code',
                    'link'   => Route::_('index.php?option=com_templates&view=templates&client_id=0'),
                    'name'   => 'MOD_QUICKICON_TEMPLATE_CODE',
                    'access' => array('core.admin', 'com_templates'),
                    'group'  => $context
                ];
            }

            if ($params->get('show_checkin'))
            {
                $quickicon = [
                    'image'   => 'icon-unlock-alt',
                    'link'    => Route::_('index.php?option=com_checkin'),
                    'name'    => 'MOD_QUICKICON_CHECKINS',
                    'access'  => array('core.admin', 'com_checkin'),
                    'group'   => $context
                ];

                if ($params->get('show_checkin') == 2)
                {
                    $quickicon['ajaxurl'] = 'index.php?option=com_checkin&amp;task=getQuickiconContent&amp;format=json';
                }

                $this->buttons[$key][] = $quickicon;
            }

            if ($params->get('show_cache'))
            {
                $quickicon = [
                    'image'   => 'icon-cloud',
                    'link'    => Route::_('index.php?option=com_cache'),
                    'name'    => 'MOD_QUICKICON_CACHE',
                    'access'  => array('core.admin', 'com_cache'),
                    'group'   => $context
                ];

                if ($params->get('show_cache') == 2)
                {
                    $quickicon['ajaxurl'] = 'index.php?option=com_cache&amp;task=display.getQuickiconContent&amp;format=json';
                }

                $this->buttons[$key][] = $quickicon;
            }

            if ($params->get('show_global'))
            {
                $this->buttons[$key][] = [
                    'image'  => 'icon-cog',
                    'link'   => Route::_('index.php?option=com_config'),
                    'name'   => 'MOD_QUICKICON_GLOBAL_CONFIGURATION',
                    'access' => array('core.manage', 'com_config', 'core.admin', 'com_config'),
                    'group'  => $context,
                ];
            }

            // HIKASHOP DEFAULT QUICKICONS
            if ($params->get('show_hikashop_dashboard'))
            {
                $this->buttons[$key][] = [
                    'image'  => 'icon-cart',
                    'link'   => Route::_('index.php?option=com_hikashop'),
                    'name'   => 'MOD_QUICKICON_HIKASHOP',
                    'group'  => $context,
                ];
            }

            if ($params->get('show_hikashop_products'))
            {
                $this->buttons[$key][] = [
                    'image'  => 'icon-cubes',
                    'link'   => Route::_('index.php?option=com_hikashop&ctrl=product'),
                    'linkadd' => Route::_('index.php?option=com_hikashop&ctrl=product&task=add'),
                    'name'   => 'MOD_QUICKICON_PRODUCTS',
                    'group'  => $context,
                ];
            }

            if ($params->get('show_hikashop_categories'))
            {
                $this->buttons[$key][] = [
                    'image'  => 'icon-folder',
                    'link'   => Route::_('index.php?option=com_hikashop&ctrl=category'),
                    'linkadd' => Route::_('index.php?option=com_hikashop&ctrl=category&task=add'),
                    'name'   => 'MOD_QUICKICON_CATEGORIES',
                    'group'  => $context,
                ];
            }

            if ($params->get('show_hikashop_users'))
            {
                $this->buttons[$key][] = [
                    'image'  => 'icon-user',
                    'link'   => Route::_('index.php?option=com_hikashop&ctrl=user&filter_partner=0'),
                    'name'   => 'MOD_QUICKICON_USERS',
                    'group'  => $context,
                ];
            }

            if ($params->get('show_hikashop_orders'))
            {
                $this->buttons[$key][] = [
                    'image'  => 'icon-credit',
                    'link'   => Route::_('index.php?option=com_hikashop&ctrl=order'),
                    'linkadd' => Route::_('index.php?option=com_hikashop&ctrl=order&task=neworder'),
                    'name'   => 'MOD_QUICKICON_ORDERS',
                    'group'  => $context,
                ];
            }

            if ($params->get('show_hikashop_discounts'))
            {
                $this->buttons[$key][] = [
                    'image'  => 'icon-tag',
                    'link'   => Route::_('index.php?option=com_hikashop&ctrl=discount&filter_type=discount'),
                    'linkadd' => Route::_('index.php?option=com_hikashop&ctrl=discount&discount_type=discount&task=add'),
                    'name'   => 'MOD_QUICKICON_DISCOUNTS',
                    'group'  => $context,
                ];
            }

            if ($params->get('show_hikashop_coupons'))
            {
            $this->buttons[$key][] = [
                'image'  => 'icon-tags-2',
                'link'   => Route::_('index.php?option=com_hikashop&ctrl=discount&filter_type=coupon'),
                'linkadd' => Route::_('index.php?option=com_hikashop&ctrl=discount&discount_type=coupon&task=add'),
                'name'   => 'MOD_QUICKICON_COUPONS',
                'group'  => $context,
            ];
        }

            if ($params->get('show_hikashop_carts'))
            {
                $this->buttons[$key][] = [
                    'image'  => 'icon-basket',
                    'link'   => Route::_('index.php?option=com_hikashop&ctrl=cart&cart_type=cart'),
                    'linkadd' => Route::_('index.php?option=com_hikashop&ctrl=cart&cart_type=cart&task=add'),
                    'name'   => 'MOD_QUICKICON_CARTS',
                    'group'  => $context,
                ];
            }

            if ($params->get('show_hikashop_wishlist'))
            {
                $this->buttons[$key][] = [
                    'image'  => 'icon-heart-2',
                    'link'   => Route::_('index.php?option=com_hikashop&ctrl=cart&cart_type=wishlist'),
                    'linkadd' => Route::_('index.php?option=com_hikashop&ctrl=cart&cart_type=wishlist&task=add'),
                    'name'   => 'MOD_QUICKICON_WISHLIST',
                    'group'  => $context,
                ];
            }

            if ($params->get('show_hikashop_waitlist'))
            {
                $this->buttons[$key][] = [
                    'image'  => 'icon-clock',
                    'link'   => Route::_('index.php?option=com_hikashop&ctrl=waitlist'),
                    'linkadd' => Route::_('index.php?option=com_hikashop&ctrl=waitlist&task=add'),
                    'name'   => 'MOD_QUICKICON_WAITLIST',
                    'group'  => $context,
                ];
            }

            if ($params->get('show_hikashop_emailhistory'))
            {
                $this->buttons[$key][] = [
                    'image'  => 'icon-envelope-opened',
                    'link'   => Route::_('index.php?option=com_hikashop&ctrl=email_history'),
                    'name'   => 'MOD_QUICKICON_EMAILHISTORY',
                    'group'  => $context,
                ];
            }

            if ($params->get('show_hikashop_config'))
            {
                $this->buttons[$key][] = [
                    'image'  => 'icon-wrench',
                    'link'   => Route::_('index.php?option=com_hikashop&ctrl=config'),
                    'name'   => 'MOD_QUICKICON_CONFIGURATION',
                    'group'  => $context,
                ];
            }

            // CUSTOM QUICKICONS
            $items = $params->get('custom_items', []);
            $items = (array) $items;

            foreach ($items as $item)
			{
                $quickicon = [
					'image'   => $item->item_icon,
					'link'    => Route::_($item->item_link),
					'name'    => $item->item_name,
                    'group'   => $context,
				];
                if ($item->item_link_add)
                {
                    $quickicon['linkadd'] = Route::_($item->item_link_add);
                }

				$this->buttons[$key][] = $quickicon;
			}

		}

		return $this->buttons[$key];
	}
}
