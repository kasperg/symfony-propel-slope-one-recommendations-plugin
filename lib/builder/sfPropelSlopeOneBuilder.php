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
 * Base class for implementations of algorithmns for maintaining the 
 * Slope One data table
 * 
 * @package    symfony
 * @subpackage plugin
 * @author     Kasper Garnæs <kasper.garnaes@gmail.com>
 */
abstract class sfPropelSlopeOneBuilder
{

	/**
	 * The SQL parser
	 *
	 * @var sfPropelSlopeOneSqlParser
	 */
	protected $sqlParser;	
	
	/**
	 * @param sfPropelSlopeOneSqlParser $sqlParser The SQL parser to use.
	 */
	public function __construct(sfPropelSlopeOneSqlParser $sqlParser) {
		$this->sqlParser = $sqlParser;
	}
	
	/**
	 * Deletes all current Slope One data.
	 */
	protected function reset()
	{
		sfSlopeOnePeer::doDeleteAll();
	}
	
	/**
	 * Perform maintenance of the Slope One data.
	 */
	public abstract function build();
	
}