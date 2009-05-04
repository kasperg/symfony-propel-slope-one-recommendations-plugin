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
 * A Propel behavior for adding recommendation capabilities to model classes which are
 * rated by users based on the Slope One algorithmns. 
 * 
 * @package    symfony
 * @subpackage plugin
 * @author     Kasper Garnæs <kasper.garnaes@gmail.com>
 */
class sfPropelActAsSlopeOneRateableBehavior
{

	/**
	 * Returns objects of the same class and with similar ratings as the current rateable object.
   * 
   * This implementation is based on the 
   * OpenSlopeOne project by Chaoqun Fu, http://code.google.com/p/openslopeone/.
	 *
	 * @param BaseObject $object The rateable object for which to return other recommended object
	 * @param int $limit The number of recommendation objects which should be returned. Use NULL for returning all recommended objects
	 * @return array of sfRecommendationObject objects which wrap the recommended objects
	 */
  public function getRecommendations(BaseObject $object, $limit = NULL)
  {
		$parser = new sfPropelSlopeOneSqlParser();
  	
    $slopeQuery =  'SELECT 	item2_id AS id,
    												SUM(rating/times) AS rating
                    FROM sf_slope_one 
                    WHERE item1_id = :item_id AND 
													item1_model = :item_model AND
                          item1_model = item2_model
                    GROUP BY item2_id
                    ORDER BY rating DESC';
    $slopeQuery .= (isset($limit)) ? ' LIMIT '.$limit : '';

    $connection = Propel::getConnection();
    $statement = $connection->prepare($parser->parse($slopeQuery));
    $statement->execute(array('item_id' => $object->getId(),
    													'item_model' => get_class($object)));
    
    $ratings = array();
    while ($result = $statement->fetch())
    {
    	$ratings[$result['id']] = $result['rating'];
    }
        
    $objects = call_user_func(array(get_class($object->getPeer()), 'retrieveByPKs'), array_keys($ratings));
    foreach ($objects as &$object)
    {
    	$object = new sfSlopeOneRecommendation($object, $ratings[$object->getId()]);
    }
    return $objects;
  }

}
