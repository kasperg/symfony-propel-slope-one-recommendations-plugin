<?php
/**
 * @package    symfony
 * @subpackage plugin
 * @author     Kasper Garnæs
 */
class sfPropelActAsSlopeOneRaterBehavior
{

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
