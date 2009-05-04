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
 * Class which performs maintainance of the Slope One data table using PHP and
 * standard SQL.
 * 
 * This implementation is based on the 
 * OpenSlopeOne project by Chaoqun Fu, http://code.google.com/p/openslopeone/.
 * 
 * @package    symfony
 * @subpackage plugin
 * @author     Kasper Garnæs <kasper.garnaes@gmail.com>
 */
class sfPropelSlopeOnePhpBuilder extends sfPropelSlopeOneBuilder
{
	
	/**
	 * Performs maintenance of the Slope One table using PHP and stanfard SQL.
	 */
	public function build()
	{
		$this->reset();
		
  	$sql = $this->sqlParser->parse('SELECT DISTINCT %rateable_id% AS rateable_id, 
  																					%rateable_model% AS rateable_model
  											 						FROM %ratings%');

  	$connection = Propel::getConnection();
    $result = $connection->query($sql);
    while ($values = $result->fetch())
    {
        $slopeOneSql = 'INSERT INTO sf_slope_one (item1_id, item1_model, item2_id, item2_model, times, rating)
													SELECT  a.%rateable_id% AS item1_id,
																	a.%rateable_model% AS item1_model,
																	b.%rateable_id% AS item2_id,
																	b.%rateable_model% AS item2_model,
																	COUNT(*) AS times, 
																 	SUM(a.%rating% - b.%rating%) AS rating 
													FROM %ratings% a, %ratings% b 
													WHERE a.%rateable_id% = :rateable_id AND
													 			a.%rateable_model% = :rateable_model AND
																a.%rateable_model% = b.%rateable_model% AND
																b.%rateable_id% != a.%rateable_id% AND
																a.%rater_id% = b.%rater_id%
																GROUP BY a.%rateable_id%, b.%rateable_id%';
				
   			$statement = $connection->prepare($this->sqlParser->parse($slopeOneSql));
				$statement->execute(array('rateable_id' => $values['rateable_id'], 
																	'rateable_model' => $values['rateable_model']));
    }
		
	}
	
}