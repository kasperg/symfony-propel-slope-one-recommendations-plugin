<?php

class sfPropelSlopeOneSqlParser
{
	
	protected $ratingTable;
	protected $raterIdColumn;
	protected $rateableIdColumn;
	protected $rateableModelColumn;
	protected $ratingColumn;
	
	public function __construct() {
		$this->ratingTable = sfConfig::get('app_sf_slope_one_recommendation_plugin_rating_table');
		$this->raterIdColumn = sfConfig::get('app_sf_slope_one_recommendation_plugin_rater_id_column');
		$this->rateableIdColumn = sfConfig::get('app_sf_slope_one_recommendation_plugin_rateable_id_column');
		$this->rateableModelColumn = sfConfig::get('app_sf_slope_one_recommendation_plugin_rateable_model_column');
		$this->ratingColumn = sfConfig::get('app_sf_slope_one_recommendation_plugin_rating_column');
	}
	
	public function parse($sql)
	{
		return str_replace(	array('%ratings%', '%rater_id%', '%rateable_id%', '%rateable_model%', '%rating%'),
												array($this->ratingTable, $this->raterIdColumn, $this->rateableIdColumn, $this->rateableModelColumn, $this->ratingColumn),
												$sql);	
	}
	
}