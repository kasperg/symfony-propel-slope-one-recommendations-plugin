<?php

sfPropelBehavior::registerHooks('sfPropelActAsSlopeOneRateableBehavior', array());
sfPropelBehavior::registerHooks('sfPropelActAsSlopeOneRaterBehavior', array());

sfPropelBehavior::registerMethods('sfPropelActAsSlopeOneRateableBehavior', array (
  array (
    'sfPropelActAsSlopeOneRateableBehavior',
    'getRecommendations'
)));

sfPropelBehavior::registerMethods('sfPropelActAsSlopeOneRaterBehavior', array (
  array (
    'sfPropelActAsSlopeOneRaterBehavior',
    'getRecommendations'
)));