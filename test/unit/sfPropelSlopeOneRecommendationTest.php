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
 * The unit test assumes that sfGuardPlugin is installed and that the model 
 * class Item is defined. 
 */

include(dirname(__FILE__).'/../bootstrap/unit.php');

sfPropelBehavior::add('sfGuardUser', array('sfPropelActAsSlopeOneRaterBehavior'));

//Setup values
sfRatingPeer::doDeleteAll();
sfGuardUserPeer::doDeleteAll();

$users = array();
for ($i = 0; $i < 3; $i++)
{
	$users[$i] = new sfGuardUser();
	$users[$i]->setUsername(rand());
	$users[$i]->save();
}

$items = ItemPeer::doSelect(new Criteria());
$items[0]->setRating(5, $users[0]->getId());
$items[1]->setRating(3, $users[0]->getId());
$items[2]->setRating(2, $users[0]->getId());
$items[0]->setRating(3, $users[1]->getId());
$items[1]->setRating(4, $users[1]->getId());
$items[1]->setRating(2, $users[2]->getId());
$items[2]->setRating(5, $users[2]->getId());

$t = new lime_test(10, new lime_output_color());

//Build slope one tables
foreach (array('sfPropelSlopeOnePhpBuilder', 'sfPropelSlopeOneMySqlBuilder') as $builder)
{
	$builder = new $builder(new sfPropelSlopeOneSqlParser());
	$builder->build();
	$t->is(sfSlopeOnePeer::doCount(new Criteria()), sizeof($items) * (sizeof($items)-1), 'Correct number of slope one values generated using '.get_class($builder));
}

$t->is(sizeof($items[0]->getRecommendations()), sizeof($items)-1, 'Correct number of recommendations retrieved for item');
$t->is(sizeof($items[0]->getRecommendations(1)), 1, 'Correct number of recommendations retrieved with limit');

$t->is(sizeof($users[0]->getRecommendations('Item')), 0, 'Users should only be recommended items which they have not already rated');

$recommendations = $users[2]->getRecommendations('Item');
$t->is(sizeof($recommendations), 1, 'Users should only be recommended items which they have not already rated');
$t->isa_ok($recommendations[0], 'sfSlopeOneRecommendation', 'Behaviors should return recommendation objects');
$t->is($recommendations[0]->getTitle(), $items[0]->getTitle(), 'Recommendation object should match original object');
$t->is($recommendations[0]->getRecommendationRating(), 4.33333333, 'Recommendation score matches expected value'); //Matches example from http://en.wikipedia.org/wiki/Slope_One

$t->is(sizeof($users[2]->getRecommendations('Item', 0)), 0, 'Correct number of recommendations retrieved with limit');