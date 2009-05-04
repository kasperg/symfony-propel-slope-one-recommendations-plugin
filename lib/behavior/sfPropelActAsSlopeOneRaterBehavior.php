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
 * A Propel behavior for adding recommendation capabilities to user classes 
 * which perform ratings on model objects based on the Slope One algorithmns. 
 * 
 * @package    symfony
 * @subpackage plugin
 * @author     Kasper Garnæs <kasper.garnaes@gmail.com>
 */
class sfPropelActAsSlopeOneRaterBehavior
{

	/**
	 * Returns recommended objects which the user has not rated based on his/her 
	 * rating of other objects.
   * 
   * This implementation is based on the 
   * OpenSlopeOne project by Chaoqun Fu, http://code.google.com/p/openslopeone/.
	 * 
	 * @param BaseObject $object The user for which to return the recommendations
	 * @param string $model The name of the class for which to return recommendations
	 * @param int $limit The number of recommendation objects which should be returned. 
	 * Use NULL for returning all recommended objects
	 * @return array of sfRecommendationObject objects which wrap the recommended objects
	 */
  public function getRecommendations(BaseObject $object, $model, $limit = NULL)
  {
  	$parser = new sfPropelSlopeOneSqlParser();
  	
    $slopeQuery =  'SELECT 	sf_slope_one.item2_id AS id, 
    												SUM((%ratings%.%rating% * sf_slope_one.times) - sf_slope_one.rating)/
                            SUM(sf_slope_one.times) AS rating
                    FROM sf_slope_one, %ratings% 
                    WHERE %ratings%.%rater_id% = :rater_id AND
                          %ratings%.%rateable_model% = :item_model AND
                          %ratings%.%rateable_id% = sf_slope_one.item1_id AND
                          %ratings%.%rateable_model% = sf_slope_one.item1_model AND
                          sf_slope_one.item2_id NOT IN (SELECT %ratings%.%rateable_id% 
														                          	FROM %ratings% 
														                          	WHERE %ratings%.%rater_id% = :rater_id)
                    GROUP BY item2_id
                    ORDER BY rating DESC';
    $slopeQuery .= (isset($limit)) ? ' LIMIT '.$limit : '';

    $connection = Propel::getConnection();
    $statement = $connection->prepare($parser->parse($slopeQuery));
    $statement->execute(array('rater_id' => $object->getId(),
    													'item_model' => $model));
    
    $ratings = array();
    while ($result = $statement->fetch())
    {
    	$ratings[$result['id']] = $result['rating'];
    }
        
    $modelObject = new $model();
    $objects = call_user_func(array(get_class($modelObject->getPeer()), 'retrieveByPKs'), array_keys($ratings));
    foreach ($objects as &$object)
    {
    	$object = new sfSlopeOneRecommendation($object, $ratings[$object->getId()]);
    }
    return $objects;
  }

}
