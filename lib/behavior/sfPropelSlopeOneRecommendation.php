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
 * A wrapper class for recommended objects which also provides access to 
 * the calculated expected rating.
 * 
 * @package    symfony
 * @subpackage plugin
 * @author     Kasper Garnæs <kasper.garnaes@gmail.com>
 */
class sfSlopeOneRecommendation
{
	
	private $object;
	private $rating;
	
	function __construct($object, $rating)
	{
		$this->object = $object;
		$this->rating = $rating;
	}
	
	/**
	 * Returns the calculated expected rating of the object
	 *
	 * @return int calculated expected rating of the object
	 */
	public function getRecommendationRating()
	{
		return $this->rating;
	}
	
	/**
	 * Returns the actual recommended object
	 *
	 * @return object The recommended object
	 */	
	public function getRecommendationObject()
	{
		return $this->object;
	}
	
	/**
	 * Pass all other method calls to the actual recommneded object
	 *
	 * @param string $name The name of the method to call
	 * @param array $arguments The arguments to pass to the method
	 * @return unknown The result of the method call
	 */
	public function __call($name, $arguments)
	{
		return call_user_func_array(array($this->object, $name), $arguments);
	}
	
}
