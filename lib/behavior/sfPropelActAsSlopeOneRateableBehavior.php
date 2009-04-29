<?php
/**
 * @package    symfony
 * @subpackage plugin
 * @author     Kasper Garnæs
 */
class sfPropelActAsSlopeOneRateableBehavior
{

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
