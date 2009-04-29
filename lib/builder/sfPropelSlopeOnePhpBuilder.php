<?php

class sfPropelSlopeOnePhpBuilder extends sfPropelSlopeOneBuilder
{
	
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