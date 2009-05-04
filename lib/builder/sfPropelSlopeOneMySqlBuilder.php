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
 * Class which performs maintainance of the Slope One data table using a MySQL
 * stored procedure.
 * 
 * This implementation is based on the 
 * OpenSlopeOne project by Chaoqun Fu, http://code.google.com/p/openslopeone/.
 * 
 * @package    symfony
 * @subpackage plugin
 * @author     Kasper Garnæs <kasper.garnaes@gmail.com>
 */
class sfPropelSlopeOneMySqlBuilder extends sfPropelSlopeOneBuilder
{

	/**
	 * Performs maintenance of the Slope One table using MySQL procedure.
	 * 
	 * Fast and recommended for production use.
	 */
	public function build()
	{
    if (!$this->hasProcedure())
    {
        $this->createProcedure();
    }

		$this->reset();
    
    $connection = Propel::getConnection();
		$connection->query('call slope_one');
	}
	
	/**
	 * Determines whether the Slope One stored procedure has been created.
	 *
	 * @return bool Whether the Slope One stored procedure has been created
	 */
	private function hasProcedure()
	{
    $sql = 'SHOW PROCEDURE STATUS
						WHERE Db = "' . $this->getDatabaseName() . '" AND name = "slope_one"';
    
		$connection = Propel::getConnection();
    $result = $connection->query($sql);
    return (bool) $result->fetch();		
	}

	/**
	 * Creates the Slope One stored procedure.
	 */
  private function createProcedure()
  {
		$sql = 'CREATE PROCEDURE `slope_one`()
              BEGIN                    
                  DECLARE tmp_rateable_id INT;
                  DECLARE tmp_rateable_model VARCHAR(255);
                  DECLARE done INT DEFAULT 0;                    
                  DECLARE rateable_cursor CURSOR FOR SELECT DISTINCT %rateable_id%, %rateable_model% FROM %ratings%;
                  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done=1;
                  OPEN rateable_cursor;
                  WHILE (!done) DO
                      FETCH rateable_cursor INTO tmp_rateable_id, tmp_rateable_model;
                      IF (!done) THEN
                          INSERT INTO sf_slope_one (item1_id, item1_model, item2_id, item2_model, times, rating)
													SELECT  a.%rateable_id% AS item1_id,
																	a.%rateable_model% AS item1_model,
																	b.%rateable_id% AS item2_id,
																	b.%rateable_model% AS item2_model,
																	COUNT(*) AS times, 
																 	SUM(a.%rating% - b.%rating%) AS rating 
													FROM %ratings% a, %ratings% b 
													WHERE a.%rateable_id% = tmp_rateable_id AND
													 			a.%rateable_model% = tmp_rateable_model AND
																a.%rateable_model% = b.%rateable_model% AND
																b.%rateable_id% != a.%rateable_id% AND
																a.%rater_id% = b.%rater_id%
																GROUP BY a.%rateable_id%, b.%rateable_id%;
                      END IF;
                  END WHILE;
                  CLOSE rateable_cursor;
              END';
      
		$connection = Propel::getConnection();
    $connection->query($this->sqlParser->parse($sql));
  }
  
  /**
   * Removes the Slope One stored procedure.
   */
  private function deleteProcedure()
  {
  	$connection = Propel::getConnection();
    $connection->query('DROP PROCEDURE IF EXISTS `slope_one`');
  }
  
  /**
   * Returns the name of the database which is used by Propel
   *
   * @return string the name of the database which is used by Propel
   */
  private function getDatabaseName()
  {
  	$config = Propel::getConfiguration();
		$dsnString = $config['datasources'][Propel::getDefaultDB()]['connection']['dsn'];
		$dbNameStart = stripos($dsnString, 'dbname=') + strlen('dbname=');
		return substr($dsnString, $dbNameStart, strpos($dsnString, ';', $dbNameStart) - $dbNameStart);
  }
	
}