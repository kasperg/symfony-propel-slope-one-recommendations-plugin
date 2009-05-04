<?php
/*
 * This file is part of the sfPropelActAsRatableBehavior package.
 *
 * (c) 2009 Kasper Garnæs <kasper.garnaes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * symfony task for performing maintenance of the Slope One tables using
 * MySQL stored procedure from the command line.
 * 
 * @package    symfony
 * @subpackage plugin
 * @author     Kasper Garnæs <kasper.garnaes@gmail.com>
 */
class sfPropelSlopeOneBuildMysqlTask extends sfPropelBaseTask
{
  protected function configure()
  {
    $this->namespace = 'slopeone';
    $this->name = 'build-mysql';
    $this->briefDescription = 'Builds slope one tables using MySQL producedure (fast)';
		$this->addArgument('application', sfCommandArgument::REQUIRED, 'The application name');
  }
 
  /**
   * Executes the maintenance task.
   *
   * @param array $arguments The arguments
   * @param array $options The options
   */
  protected function execute($arguments = array(), $options = array())
  {
  	$databaseManager = new sfDatabaseManager($this->configuration);
		$builder = new sfPropelSlopeOneMySqlBuilder(new sfPropelSlopeOneSqlParser());
		$builder->build();
  }
}