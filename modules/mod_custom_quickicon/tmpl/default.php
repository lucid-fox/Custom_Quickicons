<?php
/*
 *  package: Custom Quick Icons
 *  copyright: Copyright (c) 2021. Jeroen Moolenschot | Joomill
 *  license: GNU General Public License version 3 or later
 *  link: https://www.joomill-extensions.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $app->getDocument()->getWebAssetManager();
$wa->useScript('core');
$wa->registerAndUseScript('mod_quickicon', 'mod_quickicon/quickicon.min.js', ['relative' => true, 'version' => 'auto'], ['type' => 'module']);
$wa->registerAndUseScript('mod_quickicon-es5', 'mod_quickicon/quickicon-es5.min.js', ['relative' => true, 'version' => 'auto'], ['nomodule' => true, 'defer' => true]);

$html = HTMLHelper::_('icons.buttons', $buttons);
?>
<?php if (!empty($html)) : ?>
	<nav class="quick-icons px-3 pb-3" aria-label="<?php echo Text::_('MOD_CUSTOM_QUICKICON_NAV_LABEL') . ' ' . $module->title; ?>">
		<ul class="nav flex-wrap">
			<?php echo $html; ?>
		</ul>
	</nav>
<?php endif; ?>
