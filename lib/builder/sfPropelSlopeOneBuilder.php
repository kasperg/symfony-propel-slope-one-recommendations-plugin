<?php

abstract class sfPropelSlopeOneBuilder
{

	protected $sqlParser;	
	
	public function __construct(sfPropelSlopeOneSqlParser $sqlParser) {
		$this->sqlParser = $sqlParser;
	}
	
	protected function reset()
	{
		sfSlopeOnePeer::doDeleteAll();
	}
	
	public abstract function build();
	
}