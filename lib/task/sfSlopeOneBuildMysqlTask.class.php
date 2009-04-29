<?php 

class sfPropelSlopeOneBuildMysqlTask extends sfPropelBaseTask
{
  protected function configure()
  {
    $this->namespace = 'slopeone';
    $this->name = 'build-mysql';
    $this->briefDescription = 'Builds slope one tables using MySQL producedure (fast)';
		$this->addArgument('application', sfCommandArgument::REQUIRED, 'The application name');
  }
 
  protected function execute($arguments = array(), $options = array())
  {
  	$databaseManager = new sfDatabaseManager($this->configuration);
		$builder = new sfPropelSlopeOneMySqlBuilder(new sfPropelSlopeOneSqlParser());
		$builder->build();
  }
}