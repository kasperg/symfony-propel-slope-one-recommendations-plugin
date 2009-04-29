<?php

class sfSlopeOneRecommendation
{
	
	private $object;
	private $rating;
	
	function __construct($object, $rating)
	{
		$this->object = $object;
		$this->rating = $rating;
	}
	
	public function getRecommendationRating()
	{
		return $this->rating;
	}
	
	public function getRecommendationObject()
	{
		return $this->object;
	}
	
	public function __call($name, $arguments)
	{
		return call_user_func_array(array($this->object, $name), $arguments);
	}
	
}
