<?php return array (
  'account' => 
  array (
    'page' => true,
    'pageExcludeMainAccount' => false,
    'pageHideWithoutAuction' => false,
    'thumbnailSize' => 9,
  ),
  'admin' => 
  array (
    'dashboard' => 
    array (
      'closedAuctions' => 12,
    ),
    'auction' => 
    array (
      'lastBids' => 
      array (
        'refreshTimeout' => 32,
      ),
    ),
    'inventory' => 
    array (
      'fieldConfig' => 
      array (
        'ItemNumber' => 
        array (
          'requirable' => false,
        ),
        'Category' => 
        array (
          'requirable' => false,
        ),
        'Warranty' => 
        array (
          'requirable' => false,
        ),
        'Estimates' => 
        array (
          'requirable' => false,
        ),
      ),
    ),
  ),
  'bidding' => 
  array (
    'highBidWarningMultiplier' => 20,
  ),
  'captcha' => 
  array (
    'secretText' => true,
  ),
  'db' => 
  array (
    'readonly' => 
    array (
      'enabled' => true,
    ),
  ),
);