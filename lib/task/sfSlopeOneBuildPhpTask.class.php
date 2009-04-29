<?php

class sfPropelSlopeOneBuildPhpTask extends sfPropelBaseTask
{
  protected function configure()
  {
    $this->namespace = 'slopeone';
    $this->name = 'build-php';
    $this->briefDescription = 'Builds slope one tables using PHP (slow)';
		$this->addArgument('application', sfCommandArgument::REQUIRED, 'The application name');
  }
 
  protected function execute($arguments = array(), $options = array())
  {
  	$databaseManager = new sfDatabaseManager($this->configuration);
		$builder = new sfPropelSlopeOnePhpBuilder(new sfPropelSlopeOneSqlParser());
		$builder->build();
  }
}