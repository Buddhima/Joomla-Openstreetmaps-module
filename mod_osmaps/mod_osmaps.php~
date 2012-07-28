<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_osmaps
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

// Get javascript
$result=OsModMapsHelper::main($params,$module->id);

//Display javascript
require(JModuleHelper::getLayoutPath('mod_osmaps'));
?>
