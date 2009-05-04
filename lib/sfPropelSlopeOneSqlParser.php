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
 * Parses SQL queries containing references to the rating model and replaces
 * these with values from the configuration.
 * 
 * If configuration is obmitted the plugin assumes that 
 * sfPropelActAsRatableBehaviorPlugin is used.
 * 
 * @package    symfony
 * @subpackage plugin
 * @author     Kasper Garnæs <kasper.garnaes@gmail.com>
 */
class sfPropelSlopeOneSqlParser
{
	
	protected $ratingTable;
	protected $raterIdColumn;
	protected $rateableIdColumn;
	protected $rateableModelColumn;
	protected $ratingColumn;
	
	public function __construct() {
		$this->ratingTable = sfConfig::get('app_sf_slope_one_recommendation_plugin_rating_table', 'sf_ratings');
		$this->raterIdColumn = sfConfig::get('app_sf_slope_one_recommendation_plugin_rater_id_column', 'user_id');
		$this->rateableIdColumn = sfConfig::get('app_sf_slope_one_recommendation_plugin_rateable_id_column', 'ratable_id');
		$this->rateableModelColumn = sfConfig::get('app_sf_slope_one_recommendation_plugin_rateable_model_column', 'ratable_model');
		$this->ratingColumn = sfConfig::get('app_sf_slope_one_recommendation_plugin_rating_column', 'rating');
	}
	
	/**
	 * Parses an SQL query.
	 *
	 * @param string $sql SQL query containing references
	 * @return string SQL query with references replaced by configuration values.
	 */
	public function parse($sql)
	{
		return str_replace(	array('%ratings%', '%rater_id%', '%rateable_id%', '%rateable_model%', '%rating%'),
												array($this->ratingTable, $this->raterIdColumn, $this->rateableIdColumn, $this->rateableModelColumn, $this->ratingColumn),
												$sql);	
	}
	
}