<?php
/*
 * This file is part of the sfPropelActAsRatableBehavior package.
 *
 * (c) 2009 Kasper Garnæs <kasper.garnaes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$_test_dir = realpath(dirname(__FILE__).'/..');

require_once(dirname(__FILE__).'/../../../../config/ProjectConfiguration.class.php');
$configuration = new ProjectConfiguration(realpath($_test_dir.'/..'));
$appConfiguration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
sfContext::createInstance($appConfiguration);
new sfDatabaseManager($appConfiguration);
$loader = new sfPropelData();
$loader->loadData(dirname(__FILE__).'/fixtures.yml');

include($configuration->getSymfonyLibDir().'/vendor/lime/lime.php');
