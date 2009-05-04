# sfPropelSlopeOneRecommendation plugin

sfPropelSlopeOneRecommendation is a symfony plugin that enables collaborative filtering based on the [Slope One algorithms](http://en.wikipedia.org/wiki/Slope_One). The plugin supports two scenarios:

* Recommending new items to a user based on his/her previous ratings 
* Recommending other items with similar ratings to a specific item 

sfPropelSlopeOneRecommendation consists of Propel behaviors for retrieving recommendations and tasks for maintaining the underlying datastructure for the recommendations.

This plugin supports but does not require [sfGuardPlugin](http://www.symfony-project.org/plugins/sfGuardPlugin) for user management and [sfPropelActAsRatableBehaviorPlugin](http://www.symfony-project.org/plugins/sfPropelActAsRatableBehaviorPlugin) for ratings through flexible configuration options. 


## Installation

* Install the plugin
		
		> php symfony plugin-install http://plugins.symfony-project.com/sfPropelSlopeOneRecommendationPlugin

* If not already done, enabled behaviors in ``config/propel.ini``:

		> propel.builder.addBehaviors = true

* Rebuild the model

 		> php symfony propel-build-model
		> php symfony propel-build-sql

* Update your database tables by starting from scratch (it will delete all the existing tables, then re-create them):

		> php symfony propel-insert-sql

or you can create the new table by using the generated SQL statements in ``data/sql/plugins.sfPropelSlopeOneRecommendationPlugin.lib.model.schema.sql``

* Clear the cache

		> php symfony cc


## Configuration

If you use the default configuration of [sfPropelActAsRatableBehaviorPlugin](http://www.symfony-project.org/plugins/sfPropelActAsRatableBehaviorPlugin) then sfPropelSlopeOneRecommendationPlugin does not require further configuration.

If you use another method for handling user ratings then copy the content from the sample ``app.yml`` provided with the plugin to your application ``app.yml``:  

* The table which holds user item ratings

		rating_table:	sf_ratings

* The column which holds ids of users

		rater_id_column: user_id

* The column which holds the class name of rated objects

		rateable_model_column: ratable_model

* The column which holds the id of rated objects

		rateable_id_column:	ratable_id

* The column which holds value of item ratings

		rating_column: rating

## Usage

### Recommendations

sfPropelSlopeOneRecommendationPlugin supports two types of recommendations:

* Recommendations of new items to a user based on his/her previous ratings 
* Recommendations of other items with similar ratings to a specific item 


#### User recommendations

To retrieve recommendations of new items to a user based on his/her previous ratings the user class must have the ``sfPropelActAsSlopeOneRaterBehavior``.

This can be added in two ways:

* In ``schema.yml`` (since symfony 1.1)
		
		propel:
			user:
				id:
				[...]
				_behaviors:
					sfPropelActAsSlopeOneRaterBehavior: { } 	

* Programmatically

		[php]
		sfPropelBehavior::add('sfGuardUser', array('sfPropelActAsSlopeOneRaterBehavior'));

Note that if you do not use [sfGuardPlugin](http://www.symfony-project.org/plugins/sfGuardPlugin) for user management your user class must implement a ``getId()`` method which returns a unique id.

When the behavior has been added you can now call ``getRecommendations($itemClass, $numRecommendations)``:

	[php]
	//get 10 recommended Item objects for the user
	$recommendations = $user->getRecommentations('Item', 10);

The recommendations are always ordered with the best recommendation first. Recommendations never contain objects which the user has already rated so if the user has rated all objects then ``getRecommendations()`` will return an empty array.


#### Item recommendations

To retrieve recommendations of other items with similar ratings to a specific item the item class must have the ``sfPropelActAsSlopeOneRateableBehavior``.

This can be added in two ways:

* In ``schema.yml`` (since symfony 1.1)
		
		propel:
			item:
				id:
				[...]
				_behaviors:
					sfPropelActAsSlopeOneRateableBehavior: { } 	

* Programmatically

		[php]
		sfPropelBehavior::add('Item', array('sfPropelActAsSlopeOneRateableBehavior'));

Note that your item class must implement a ``getId()`` method which returns a unique id.

When the behavior has been added you can now call ``getRecommendations($numRecommendations)``:

	[php]
	//get 10 recommended Item objects based on the rating of the current item
	$recommendations = $item->getRecommentations(10);

The recommendations are always ordered with the best recommendation first.


#### Recommendation objects

The objects returned by the two ``getRecommendations()`` will actually be of type ``sfSlopeOneRecommendation``. This class implements two methods:

* ``getRecommendationRating()`` which returns the expected rating of the returned object
* ``getRecommendationObject()`` which returns the actual recommended object

In all other concerns ``sfSlopeOneRecommendation`` acts as a transparent wrapper class passing calls to all other methods to the recommended object.

An array of ``sfSlopeOneRecommendation`` objects returned by ``getRecommendations()`` will be sorted by descending value returned by ``getRecommendationRating()``.


### Maintenance

The Slope One implementation used in this plugin uses a database table containing precalculated rating relations between items. Data in this table needs to be updated on a regular basis to for recommendations to reflect the latest ratings.

#### Task-based maintenance

To maintain this you can invoke either of the provided tasks:

* The MySQL task creates a ``slope_one`` stored procedure for maintaining the table and executes this. This will usually be faster than running the PHP task. This is recommended for production use if you use MySQL 5.1 verson which supports stored procedures.

		> php symfony slopeone:build-mysql
	
* The PHP task uses a combination of PHP and standard SQL queries for maintaining the data.

		> php symfony slopeone:build-php

Depending on the number of items and ratings in your application maintenance can be time consuming and it suitable for a nightly cron job.

#### Programmatic maintenance

It is also possible to invoke the maintenance programmatically:

* The MySQL approach:

		[php]
		$builder = new sfPropelSlopeOneMySqlBuilder(new sfPropelSlopeOneSqlParser());
		$builder->build();
	
* The PHP approach:

		[php]
		$builder = new sfPropelSlopeOnePhpBuilder(new sfPropelSlopeOneSqlParser());
		$builder->build();


##Credits

The Slope One algorithmns were introduced in [*Slope One Predictors for Online Rating-Based Collaborative Filtering*](http://www.daniel-lemire.com/fr/abstracts/SDM2005.html) by Daniel Lemire and Anna Maclachlan. 

sfPropelSlopeOneRecommendationPlugin is heavily inspired by the [OpenSlopeOne project](http://code.google.com/p/openslopeone/) by Chaoqun Fu.

    
## TODO / Ideas
 
 
## Changelog

### 2009-04-23 | 0.1.0 Alpha

* Initial release

