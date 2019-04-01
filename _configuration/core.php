<?php

// remove for real using
if (!defined('__PHP_ASSETS__')) {
    define('__PHP_ASSETS__', '/somePath');
}
if (!defined('__SUBDIRECTORY__')) {
    define('__SUBDIRECTORY__', '/someDir');
}
if (!defined('__LOGS__')) {
    define('__LOGS__', '/someLogsDir');
}
if (!defined('__DOCROOT__')) {
    define('__DOCROOT__', '/someRootDir');
}
if (!defined('__RESELLER_FILES__')) {
    define('__RESELLER_FILES__', '/someResselerFiles');
}
// remove for real using


use Sam\Observer\AbsenteeBidObserver;
use Sam\Observer\AccountObserver;
use Sam\Observer\AuctionObserver;
use Sam\Observer\AuctionBidderObserver;
use Sam\Observer\AuctionCustDataObserver;
use Sam\Observer\AuctionCustFieldObserver;
use Sam\Observer\AuctionLotItemObserver;
use Sam\Observer\AuctionLotItemCacheObserver;
use Sam\Observer\AuctionParametersObserver;
use Sam\Observer\BidTransactionObserver;
use Sam\Observer\InvoiceObserver;
use Sam\Observer\InvoiceItemObserver;
use Sam\Observer\LocationObserver;
use Sam\Observer\LotCategoryObserver;
use Sam\Observer\LotImageObserver;
use Sam\Observer\LotItemObserver;
use Sam\Observer\LotItemCustDataObserver;
use Sam\Observer\LotItemCustFieldObserver;
use Sam\Observer\TimedOnlineItemObserver;
use Sam\Observer\UserObserver;
use Sam\Observer\UserBillingObserver;
use Sam\Observer\UserCustDataObserver;
use Sam\Observer\UserInfoObserver;
use Sam\Observer\UserShippingObserver;
use Sam\Observer\UserWatchlistObserver;
use Sam\Observer\AuctionCacheObserver;



/**
 * Options are arranged in alphabetical order
 * SAM-3346: Implement new configuration way
 */
return [
    'account' => [
        'page' => false,
        'pageExcludeMainAccount' => true,
        'pageHideWithoutAuction' => true,
        'thumbnailSize' => 6,
    ],

    /**
     * For Admin side
     */
    'admin' => [
        'dashboard' => [
            /**
             * Maximum count of closed auctions shown at admin dashboard, SAM-1652
             */
            'closedAuctions' => 10,
        ],

        'auction' => [
            'lastBids' => [
                /**
                 * Last Bids Report refresh interval in seconds
                 */
                'refreshTimeout' => 30, // seconds
            ],

            'lots' => [
                /**
                 * Data auto sync timeout
                 */
                'syncTimeout' => 60,
                /**
                 * Count of lots on the auction lots page, when "Quick edit" feature is on (SAM-1547)
                 */
                'quickEditLotLimit' => 25,
            ],
        ],

        'inventory' => [
            /**
             * SAM-1503: Lot/ inventory edit page configuration
             * Array of lot field config defaults. This field order is used as default order.
             * Index max size 30 chars.
             * Default values:
             * 'requirable' => false, // define, that field could be defined as required/non-required
             * 'alwaysRequired' => false, // always mandatory field
             * 'required' => false,        // required by default
             * 'relDir' => after,
             * 'relIndex' => [index of previous element or NULL (for the first)]
             */
            'fieldConfig' => [
                'LotStatus' => [
                    'title' => 'Status',
                ],
                'ItemNumber' => [
                    'title' => 'Item #',
                    'requirable' => true,
                ],
                'ItemSync' => [
                    'title' => 'Item sync IDs',
                ],
                'Category' => [
                    'title' => 'Category',
                    'requirable' => true,
                ],
                'AuctionType' => [
                    'title' => 'Auction type, Start/End date/time, timezone',
                ],
                'LotNumber' => [
                    'title' => 'Lot Number (consisting of prefix, lot number and extension)',
                    'requirable' => true,
                ],
                'SeoUrl' => [
                    'title' => 'Lot URL',
                ],
                'Parcel' => [
                    'title' => 'Parcel Id',
                    'requirable' => true,
                ],
                'Quantity' => [
                    'title' => 'Quantity (including "quantity x money" checkbox)',
                    'requirable' => true,
                ],
                'LotName' => [
                    'title' => 'Lot Name',
                    'alwaysRequired' => true,
                ],
                'LotDescription' => [
                    'title' => 'Lot Description',
                    'requirable' => true,
                ],
                'Changes' => [
                    'title' => 'Changes',
                    'requirable' => true,
                ],
                'Warranty' => [
                    'title' => 'Warranty',
                    'requirable' => true,
                ],
                'SpecialTerms' => [
                    'title' => 'Special Terms and Conditions',
                ],
                'LotImage' => [
                    'title' => 'Images (Uploads)',
                ],
                'ListingOnly' => [
                    'title' => 'Listing Only',
                ],
                'BuyNow' => [
                    'title' => 'Buy now, offer, no bidding',
                ],
                'Estimates' => [
                    'title' => 'Estimates (low & high)',
                    'requirable' => true,
                ],
                'StartingBid' => [
                    'title' => 'Starting Bid',
                    'alwaysRequired' => true,
                ],
                'Increments' => [
                    'title' => 'Increments',
                ],
                'Cost' => [
                    'title' => 'Cost',
                    'requirable' => true,
                ],
                'ReplacementPrice' => [
                    'title' => 'Replacement Price',
                    'requirable' => true,
                ],
                'ReservePrice' => [
                    'title' => 'Reserve Price',
                    'requirable' => true,
                ],
                'Consignor' => [
                    'title' => 'Consignor',
                    'requirable' => true,
                ],
                'Commission' => [
                    'title' => 'Consignment Commission',
                    'requirable' => true,
                ],
                'ConsignorFee' => [
                    'title' => 'Consignor Fee',
                    'requirable' => true,
                ],
                'WinningBidder' => [
                    'title' => 'Winning Bidder (including auction, date and hammer price and "internet bid" checkbox)',
                ],
                'OnlyTaxBp' => [
                    'title' => 'Only Tax BP',
                ],
                'Tax' => [
                    'title' => 'Sales Tax',
                    'requirable' => true,
                ],
                'BpSetting' => [
                    'title' => 'Bp Setting',
                ],
                'BpRangeCalculation' => [
                    'title' => 'Bp Range Calculation',
                ],
                'AdditionalBpInternet' => [
                    'title' => 'Additional Bp Internet',
                ],
                'NoTaxOutside' => [
                    'title' => 'No Sales tax out of state',
                ],
                'Returned' => [
                    'title' => 'Returned',
                ],
                'Featured' => [
                    'title' => 'Featured lot',
                ],
                'ClerkNote' => [
                    'title' => 'Note to auction clerk',
                    'requirable' => true,
                ],
                'GeneralNote' => [
                    'title' => 'General note',
                    'requirable' => true,
                ],
                'Offers' => [
                    'title' => 'Offers',
                ],
                'ResetLot' => [
                    'title' => 'Reset lot',
                ],
                'ItemBillingCountry' => [
                    'title' => 'Item Billing Country',
                ],
                'BulkGroup' => [
                    'title' => 'Bulk Group',
                ],
                'Location' => [
                    'title' => 'Location',
                ],
                'SeoOptions' => [
                    'title' => 'SEO Meta Title, SEO Meta Keywords, SEO Meta Description',
                ],
                'BuyersPremiumId' => [
                    'title' => 'Use BP rule',
                ],
                'FbOgTitle' => [
                    'title' => 'Facebook OG Title',
                ],
                'FbOgDescription' => [
                    'title' => 'Facebook OG Description',
                ],
                'FbOgImageUrl' => [
                    'title' => 'Facebook OG Image Url',
                ],
                'CustomFields' => [
                    'title' => 'Lot item custom fields',
                ],
            ],
        ],

        'report' => [
            'customLots' => [
                /**
                 * Default report fields and order
                 */
                'fields' => ['ItemNumber', 'Category', 'LotNumber', 'Quantity', 'LotName', 'HammerPrice',
                    'AuctionNumber', 'DateSold', 'WinningBidder', 'InternetBid'],
                'order' => [
                    'field' => 'ItemNumber',
                    'direction' => 'ascending', // 'ascending' or 'descending'
                ],
            ],
        ],
    ],

    'app' => [
        'errorFriendlyPage' => [
            'ajaxMessage' => 'Oops!  An error has occurred.\r\n\r\nThe error was logged, and we will take a look into this right away.',
            'email' => 'support@swb-consulting.com',
            'feedback' => false,
            'path' => __PHP_ASSETS__ . '/friendly_error_page.php',
            'pathFeedback' => __SUBDIRECTORY__ . '/error_report.php',
        ],

        'header' => [
            /**
             * Value for X-Frame-Options HTTP response header
             */
            'xFrameOption' => 'SAMEORIGIN', // 'DENY', 'ALLOW-FROM uri
        ],

        'headerFooterRemoteUrlCachingTimeout' => 360,

        'helpUrl' => 'http://www.auctionserver.net/wiki-2',

        /**
         * Modify Page_Url to ignore the SERVER_PORT when this option HTTP_HOST_IGNORE_SERVER_PORT is true
         */
        'httpHostIgnoreServerPort' => false,

        'inlineHelp' => [
            /**
             * Prefix inline help with (<section> - <key>)
             */
            'helpMode' => false,
        ],

        /**
         * Remove whitespace characters from result html
         */
        'removeWhitespace' => true,

        'rolloutName' => '',

        /**
         * Enable SEO friendly URL, SAM-2869
         */
        'seoFriendlyUrlEnabled' => true,

        'supportUrl' => 'http://support.auctionserver.net',

        'timezone' => [
            'sortPriority' => 'US'  // 'US', 'Africa', 'America', 'Asia', 'Atlantic', 'Australia', 'EU', 'Pacific'
        ],
    ],

    'auction' => [
        /**
         * Auction closer script configuration
         */
        'closer' => [
            'executionTime' => 55,
        ],

        'days' => [
            'maxLimit' => 3650, // 10 years
        ],

        /**
         * Lifetime for record in `auction_cache` table
         */
        'dbCacheTimeout' => 86400,  // Seconds: 86400 = 1day, 300 = 5min

        /**
         * SAM-2647 Max execution time for script, that updates auction_cache
         */
        'dbCacheUpdateMaxExecutionTime' => 5, // Seconds

        /**
         * SAM-1945: Extend from current time
         */
        'extendFromCurrentTime' => [
            'enabled' => false,
        ],

        /**
         * SAM-3091: Hybrid Auctions Feature
         */
        'hybrid' => [
            'enabled' => false,
            /**
             * Delay after "Extend Time" interval
             */
            'closingDelay' => 3,
            /**
             * Minimal allowed "Extend Time" interval in seconds
             */
            'extendTimeMin' => 10,
        ],

        'list' => [
            /**
             * Data auto sync timeout
             */
            'syncTimeout' => 60,  // seconds

            /**
             * Image size in auction list
             */
            'thumbnailSize' => 6,
        ],

        'seoUrl' => [
            'lengthLimit' => 128,
        ],

        'saleNo' => [
            /**
             * Concatenated or selected sale# input, SAM-3023
             */
            'concatenated' => true,
            'extensionSeparator' => '',
        ],

        'saleNoConcatenated' => true,  // SAM-3023

        /**
         * Test auction. SAM-4105, SAM-840
         */
        'test' => [
            'prefix' => 'Test - ',
        ],
    ],

    'bidding' => [
        /**
         * Timeout in seconds for confirm place bid
         */
        'inlineConfirmTimeout' => 10,

        /**
         * SAM-3502: Accidental high bid warning
         */
        'highBidWarningMultiplier' => 10,

        'orBid' => [
            'enabled' => false,
        ],
    ],

    'billing' => [
        'gate' => [
            'authorizeNet' => [
                'isDev' => true,
                'version' => '3.1',
                'delimiterChar' => '|',
                'delimiterData' => 'TRUE',
                'url' => 'FALSE',
                'type' => 'AUTH_CAPTURE',
                'method' => 'CC',
                'relayResponse' => 'FALSE',
            ],
            'beanStream' => [
                /**
                 * Transaction types abbreviations: P - Purchase, PA - Pre-Authorization, PAC - Pre-Authorization Completion, VR - Void return, R - Return, VP - Void Purchase, V - Void
                 */
                'transactionType' => 'P',
                'requestType' => 'BACKEND',
            ],
            'payPal' => [
                /**
                 * Number of retries when ipn call returns empty
                 */
                'ipnRetries' => 2,

                /**
                 * Time pause before the retry
                 * using microsecond
                 * 500000 microseconds equivalent to 500 milliseconds
                 */
                'pause' => 500000,
            ],
            'sage' => [
                /**
                 * Transaction Processing Codes: 01 = Sale, 02 = AuthOnly, 03 = Force/PriorAuthSale, 04 = Void, 06 = Credit, 11 = PriorAuthSale by Reference
                 */
                'typeCode' => '01',
            ],
        ],
        'userAuthorization' => [
            /** @var float */
            'amount' => 0.0,
            /**
             * Number of days until this authorization/ capture expires
             * -1: never expires. 0: expires immediately
             * @var int
             */
            'expiration' => -1,
        ],
        'skipCcValidation' => false,
    ],

    'cache' => [
        'filesystem' => [
            'path' => '/tmp/cache',
            'ttl' => 86400, //seconds, 24 hour
        ],
        'memory' => [
            'enabled' => true,
            'adapter' => [
                'name' => 'memory',
                'options' => [
                    // 'memory_limit' => '15M', // doesn't work because of bug at https://github.com/zendframework/zend-cache/blob/master/src/Storage/Adapter/MemoryOptions.php#L87
                    'ttl' => 5,     // this value should be enough for web and it won't delay cache expiration in rtbd
                    'writable' => true, // enables write in cache
                ],
            ],
        ],
    ],

    'captcha' => [
        /**
         * Alternative captcha options
         */
        'alternative' => [
            'minReq' => 2,      // SIGNUP_CAPTCHA_ALTERNATIVE_MIN_REQ
            'time' => 5,        // SIGNUP_CAPTCHA_ALTERNATIVE_TIME
        ],

        'wordLen' => '6',
        'width' => '250',
        'height' => '100',
        'dotNoiseLevel' => '40',
        'lineNoiseLevel' => '4',

        'secretText' => false,  // CAPTCHA_AUTO_TEST

        /**
         * - false: use neither captcha nor alternative
         * - "simple": use general captcha
         * - "alternative": use the captcha alternative
         */
        'type' => 'simple',     // SIGNUP_CAPTCHA
    ],

    'coupon' => [
        'code' => [
            'lengthLimit' => 50,
        ],
        'title' => [
            'lengthLimit' => 50,
        ],
    ],

    'csv' => [
        'clearValue' => '~~CLEAR~~', // SAM-4396
        'defaultValue' => '~~DEFAULT~~', // SAM-4396
        'lot' => [
            'autoCreateCategory' => true,
            'autoCreateConsignor' => true,
        ],
    ],

    'custom' => [
        /**
         * The way to search customized class: 'array' - in CUSTOM_CLASS_REGISTRY, 'file' - search in file system (SAM-1921)
         */
        'classSearchWay' => 'file',
        /**
         * List of class names, that have customized class (SAM-1921)
         */
        'registry' => [],
    ],

    'db' => [
        'adapter' => 'MySqli5',
        'server' => 'localhost',
        'port' => null,
        'database' => '',
        'username' => '',
        'password' => '',
        'encoding' => 'latin1', // 'utf8',
        'profiling' => false,
        'readonly' => [
            'enabled' => false,
            'adapter' => 'MySqli5',
            'server' => 'localhost',
            'port' => null,
            'database' => null,
            'username' => null,
            'password' => null,
            'encoding' => 'latin1',
            'profiling' => false,
        ],

        'mysqlMaxInt' => 2147483647,
    ],

    'email' => [
        /**
         * SAM-2944: Domains for links in email templates
         * Options, can be combined in order of priority separated by comma
         * - SERVER_NAME use $_SERVER['SERVER_NAME']
         * - HTTP_HOST use value from define('HTTP_HOST',...)
         * - ACCOUNT_HOST, for core->portal->enabled only, will use the respective object's account sub domain or url_domain, depending on setting
         */
        'domainLinkRendering' => 'SERVER_NAME,HTTP_HOST,ACCOUNT_HOST',
    ],

    'feed' => [
        'checkEncoding' => false,
        'itemsPerPage' => 100,
        'name' => [
            'lengthLimit' => 100,
        ],
        'slug' => [
            'lengthLimit' => 100,
        ],
        'customReplacements' => [
            'order' => [
                'lengthLimit' => 13, // column `order` in db is decimal(10,2)
            ],
        ],
    ],

    'filesystem' => [
        'managerClass' => 'File_Local',
        'remote' => [
            'masterHost' => '',   // HTTP_HOST should be used
            'ipAllow' => ['127.0.0.1/32', '::1/128'],  // Localhost only
            'ipDeny' => [],
            'folderAllow' => [
                '/uploads/admin_language/',
                '/uploads/auction/',
                '/uploads/auction_files/',
                '/uploads/lot_item/',
                '/uploads/language/master/customfields.csv',    // must be defined before general path '/uploads/language/'
                '/uploads/language/master/usercustomfields.csv',
                '/uploads/language/',
                '/uploads/item_files/',
                '/uploads/settings/',
                '/uploads/lot_category/',
                '/uploads/lot_image_bucket/',
                '/uploads/reseller/',
                '/uploads/user_files/',
                '/wwwroot/reseller_data/',
                '/wwwroot/images/',
                '/wwwroot/lot-info/',
                '/wwwroot/general/',
                '/wwwroot/sitemap/cache/',
            ],
            'regexDeny' => [
                '/\/tmp\//',    // no /tmp/* directories
                '/\.\./', // no directory backtracking
                '/\/\.svn/i', // no svn directories
                '/\/uploads\/language\/master\//i', // no files in uploads/language/master, TODO: we need to allow the custom fields file
                '/\.php$/i', // no .php files
            ],
        ],
    ],

    'general' => [
        /**
         * Logging levels: 0: always, 1: error, 2: warning, 3: info, 4: debug, 5: trace (for example when looping through arrays)
         */
        'debugLevel' => 3,
        /**
         * When we want to output debug info at rendered page
         * @var bool
         */
        'debugInWeb' => false,

        /**
         * Compare floating points with limited precision
         */
        'floatPrecisionCompare' => 4,
    ],

    'image' => [
        'cacheLimit' => 100,
        'cacheLifetime' => 9800,//1800
        'cacheRetries' => -3,
        'jpegQuality' => 75,
        'linkPrefix' => ['default' => ''],
        'maxHeight' => 1000,
        'maxWidth' => 1000,
        'maxWidthHeight' => 6000000, // 3000 * 2000
        'remoteImageTimeout' => 1,
        'uploadMaxSize' => 3072, // 3Mb
        'thumbnail' => [
            'size0' => ['width' => 1000, 'height' => 1000],  // largest image
            'size1' => ['width' => 93, 'height' => 93],     // featured lots on auction info page
            'size2' => ['width' => 500, 'height' => 500],   // medium size image on detail page & bidding client
            'size3' => ['width' => 300, 'height' => 300],
            'size4' => ['width' => 100, 'height' => 100],     // thumbnail (catalog, details, other lots)
            'size5' => ['width' => 160, 'height' => 160],   // clerking console
            'size6' => ['width' => 300, 'height' => 300],   // search results
            'size7' => ['width' => 760, 'height' => 100],   // header logo
            'size8' => ['width' => 400, 'height' => 400],   // Large image on projector
        ],
    ],

    'location' => [
        'name' => [
            'lengthLimit' => 50,
        ],
    ],

    'lot' => [
        'category' => [
            /**
             * Category image dimensions
             */
            'imageSize' => ['width' => 200, 'height' => 200],
            /**
             * Lot category max possible level starting from 0
             */
            'maxLevel' => 3,

            'fullTreeCacheLifetime' => 1440,

            'auctionTreeCacheLifetime' => 5,

            'name' => [
                'lengthLimit' => 50,
            ],
        ],

        'customField' => [
            'postalCode' => [
                'searchRadius' => [5, 10, 20, 50, 100, 250],    // miles
            ],
        ],

        'image' => [
            /**
             * Max quantity of files, that could be uploaded in the bucket
             */
            'inBucketLimit' => 500,

            /**
             * Max file size for images uploaded through the bucket
             */
            'inBucketMaxSize' => 1048576,   // 1Mb

            /**
             * Max quantity of files, that could be assigned to a lot
             */
            'perLotLimit' => 100,
        ],

        'itemNo' => [
            'extensionSeparator' => '',
            /**
             * Concatenated or separated item# input, SAM-3023
             */
            'concatenated' => true,
        ],

        'lotNo' => [
            /**
             * Concatenated or separated lot# input, SAM-3023
             */
            'concatenated' => true,
            'extensionSeparator' => '',
            'prefixSeparator' => '',
        ],

        'seoUrl' => [
            /**
             * SAM-3588 SEO URL and meta information improvements
             */
            'dbCache' => [
                'updateMaxExecutionTime' => 5, // Seconds
            ],
            'lengthLimit' => 128,
        ],

        'name' => [
            'lengthLimit' => 500,       // Max size of lot_item.name
        ],

        /**
         * Lot description rendering options, when pointer hovers on lot (SAM-3816)
         */
        'descriptionOnHover' => [
            /** @var bool */
            'enabled' => false, // turn rendering on/off
            /** @var int */
            'length' => 200, // length limit of description
        ],

        /**
         * Stop words, which should be ignored when ordering by custom field, SAM-1523
         */
        'orderIgnoreWords' => ['a ', 'an ', 'the ', '"', '(', "'", '['],

        'video' => [
            // Add here id of the ticket, when Ed will repost this ticket under SAM 2.0
            'enabled' => false,
        ],
    ],

    /**
     * For Lot Details page
     */
    'lotDetails' => [
        'otherLots' => [
            'cacheControl' => [
                /**
                 * SAM-3506: Other lots on responsive lot detail page needs to be refactored
                 * 0 - means do not cache
                 */
                'maxAge' => 1800,
            ],
            'countForLegacyUi' => 5,
            'countForResponsiveUi' => 2,
        ],
        /**
         * Data auto sync timeout
         */
        'syncTimeout' => [
            'live' => 60,   // seconds
            'timed' => 60,  // seconds
        ],
    ],

    'mapp' => [
        // secret key posted via HTTP_X_BIDPATH_API_KEY to trigger switches
        // like hiding the captcha on signup, forgot password etc
        // when the page is requested from the mobile app
        'bidpathApiKey' => 'da39a3ee5e6b4b0d3255bfef95601890afd80709',
    ],

    /**
     * Registered observer classes
     */
    'observers' => [
        'AbsenteeBid' => [AbsenteeBidObserver::class],
        'Account' => [AccountObserver::class],
        'Auction' => [AuctionObserver::class],
        'AuctionBidder' => [AuctionBidderObserver::class],
        'AuctionCustData' => [AuctionCustDataObserver::class],
        'AuctionCustField' => [AuctionCustFieldObserver::class],
        'AuctionLotItem' => [AuctionLotItemObserver::class],
        'AuctionLotItemCache' => [AuctionLotItemCacheObserver::class],
        'AuctionParameters' => [AuctionParametersObserver::class],
        'BidTransaction' => [BidTransactionObserver::class],
        'Invoice' => [InvoiceObserver::class],
        'InvoiceItem' => [InvoiceItemObserver::class],
        'Location' => [LocationObserver::class],
        'LotCategory' => [LotCategoryObserver::class],
        'LotImage' => [LotImageObserver::class],
        'LotItem' => [LotItemObserver::class],
        'LotItemCustData' => [LotItemCustDataObserver::class],
        'LotItemCustField' => [LotItemCustFieldObserver::class],
        'TimedOnlineItem' => [TimedOnlineItemObserver::class],
        'User' => [UserObserver::class],
        'UserBilling' => [UserBillingObserver::class],
        'UserCustData' => [UserCustDataObserver::class],
        'UserInfo' => [UserInfoObserver::class],
        'UserShipping' => [UserShippingObserver::class],
        'UserWatchlist' => [UserWatchlistObserver::class],
        'AuctionCache' => [AuctionCacheObserver::class],
    ],

    /**
     * SAM Portal related settings
     */
    'portal' => [
        /**
         * Account visibility (SAM-3051)
         * Possible values : separate, directlink, transparent
         * @var bool Default value : separate
         */
        'domainAuctionVisibility' => 'separate',

        /**
         * SAM-1153: SAM Portal Features
         * @var bool Should be disabled by default
         */
        'enabled' => false,

        /**
         * Allow to create new account on new user registration (SAM-3655)
         * @var bool Default value : false
         */
        'enableAccountAdminSignup' => false,

        /**
         * Id of main account - account.id (SAM-4010)
         * @var int
         */
        'mainAccountId' => 1,

        /**
         * Whether to use subdomains (default) or maindomain for sam portal url handling
         * @var string default to 'subdomains'
         */
        'urlHandling' => 'subdomains',
    ],

    /**
     * Reminders are run by cron
     */
    'reminder' => [
        'payment' => [
            'lastRunFile' => __LOGS__ . '/invoice_payment_reminder_ts.txt',
        ],
        'pickup' => [
            'lastRunFile' => __LOGS__ . '/invoice_pickup_reminder_ts.txt',
        ],
        'registration' => [
            'lastRunFile' => __LOGS__ . '/auction_reminder_ts.txt',
            'interval' => 24, // once per day
        ],
    ],

    'rtb' => [
        'autoRefreshTimeout' => 20, // seconds

        'biddingInterest' => [
            'enabled' => true,
            'delayMs' => 500,       // 0.5 seconds
            'dropDelayMs' => 500,   // 0.5 seconds
            'dropTimeout' => 600,    // 10 min
            'gcTimeout' => 60,      // 1 min
        ],

        'bidsBelowCurrent' => 7,    // max 20

        'catalog' => [
            /**
             * Show "Estimate" column
             */
            'columnEstimate' => false,
            /**
             * Count of pages simultaneously loaded
             */
            'loadedPages' => 2,
            /**
             * Default length for rtb catalog page (lots in the page)
             */
            'pageLength' => 10000,
        ],

        'client' => [
            'ipAllow' => ['0.0.0.0/0', '::/0'],
            'ipDeny' => [],
            'password' => false,
        ],

        /**
         * Black window at consoles with request / response commands and data
         */
        'commandConsole' => [
            'enabled' => false,  // RTB_BENCHMARK
        ],

        'connectionRetryCount' => 3,

        /**
         * F: use flash bridge
         * W: use websocket // default, since Adobe Flash will be discontinued soon
         * A: auto detect ( if browser support websocket it will connect using websocket else will use flash bridge )
         */
        'connectionType' => 'W',

        /**
         * Enable/disable context menu (right mouse click) at admin clerk console
         */
        'contextMenuEnabled' => false,

        'feedbackEmail' => 'rtbfeedback@swb-consulting.com',

        'jsDeveloperConsole' => [
            // SAM-4319: Restrict displaying of rtb request/response commands in js developer console
            'displayCommands' => false,
        ],

        'memoryCache' => [
            // Enables default memory caching mechanism in rtbd process
            'enabled' => false,
        ],

        'messageCenter' => [
            /**
             * Allowed count of user messages in the Message Center
             * See, /admin/manage-settings/system-parameters/option/live_auction
             */
            'customMessageLimit' => 30,
            /**
             * Count of rendered messages in chat screen
             */
            'renderedMessageCount' => 50,
        ],

        'projector' => [
            /**
             * Define round precision of some numbers at round project (Current bid, Estimates)
             * -1 - to round to whole numbers (83 => 80)
             */
            'numberRoundPrecision' => 2,
            'amountCachedImages' => 2,
        ],

        'quantizeAskingBid' => true,

        /**
         * SAM-3924: RTBD scaling by providing a "repeater/ broadcasting" service for viewers
         */
        'rtbdViewerRepeater' => [
            'enabled' => false,
        ],

        'server' => [
            'bindHost' => null,         // RTB_SERVER_BIND
            'bindPort' => null,         // RTB_PORT
            'publicHost' => null,       // RTB_SERVER
            'publicPort' => 10100,      // RTB_PORT
            'publicPath' => '',         // RTB_PUBLIC_PATH (SAM-4122)
            'keepAlive' => true,        // RTB_KEEP_ALIVE
            'maxTotalTime' => 172800,   // RTB_MAX_TOTAL_TIME
            'maxIdleTime' => 172800,    // RTB_MAX_IDLE_TIME
            'shouldAuth' => false,      // RTB_AUTH
            /**
             * Rtbd WebSocket connection schema (SAM-3889)
             * false - ws://, true - wss://
             */
            'wss' => false,
        ],

        /**
         * Sound filenames for actions in rtb
         */
        'sound' => [
            'placeBid' => 'place_bid.mp3',
            'bidAccepted' => 'bid_accepted.mp3',
            'outBid' => 'out_bid.mp3',
            'soldNotWon' => 'sold_not_won.mp3',
            'soldWon' => 'sold_won.mp3',
            'passed' => 'passed.mp3',
            'fairWarning' => 'fair_warning.mp3',
            'onlineBidIncomingOnAdmin' => '40725^DING1.mp3',
            'enablePlay' => 'enable_play_sound.mp3',
            'bid' => 'bid_sound.mp3',
        ],

        /**
         * Default volume for rtb sounds. This const used by sound manager lib
         */

        'soundVolume' => 50,
    ],

    'shipping' => [
        // AuctionInc Shipping Calculate config
        'auctionInc' => [
            'calcMethod' => 'C',
            'packMethod' => 'T',
        ],
    ],

    /**
     * For Search / Catalog pages
     */
    'search' => [
        /**
         * Catalog compact image format (icon, thumb)
         */
        'compactImageFormat' => 'icon',

        /**
         * Settings for search landing page(SAM-3620)
         * default - default behaviour, run the search query without params
         * no-query - do not execute a search query at all if no query parameters were passed
         */
        'emptySearchQuery' => 'no-query',

        /**
         * Search Panel options(SAM-3620)
         * closed - default value, search panel closed
         * open - search panel is expanded and added css class .search_toggle on the parent div
         * open-form - search panel is expanded and added css class .search_toggle and .open_search on the parent div, so it can be customized
         */
        'emptySearchQueryPanel' => 'open-form',

        /**
         * Default value for "Exclude closed lots" on search results (SAM-3390)
         */
        'excludeClosedLots' => true,

        /**
         * Search index settings
         */
        'index' => [
            /** @var int */
            'type' => 1,    // 1 - fulltext, 2 - trigram
            'lang' => '',
            'trigramMinRelevance' => 0.5,
        ],

        /**
         * Data auto sync timeout
         */
        'syncTimeout' => 60,  // seconds
    ],

    'siteWideMessage' => [
        'file' => __DOCROOT__ . '/general/data/sitewidemessage.txt',
        'updateInterval' => 90, // seconds
    ],

    'soap' => [
        'clearValue' => '~~CLEAR~~', // SAM-4396
        'defaultValue' => '~~DEFAULT~~', // SAM-4396
        'emptyStringBehavior' => 'clear', // available options: 'clear', 'default', '' (no action)
        'ipAllow' => ['0.0.0.0/0', '::/0'], // Any
        'ipDeny' => [],
        'wsdlCacheEnabled' => true,
        'lot' => [
            'autoCreateCategory' => true,
            'autoCreateConsignor' => true,
        ],
    ],

    'statsServer' => [
        'enabledForRtb' => true,
        'enabledForWeb' => true,
        'host' => 'stats.auctionserver.net',
        'port' => 9001,
        'timeout' => 0.2,
    ],

    'user' => [
        'credentials' => [
            'passwordReset' => [
                'lifeTime' => 24, // hours
            ],
        ],

        'bidderNumber' => [
            'padLength' => 4,
            'padString' => '0',
        ],

        /**
         * SAM-2560: Bidonfusion - Track buyer interests
         */
        'buyerInterestPeriod' => 6, // Month

        /**
         * Settings for logging profile changes (SAM-1444)
         */
        'logProfile' => [
            /**
             * Log mode for user profile change
             * 0 - do not log changes in profile fields
             * 1 - log only user's own changes
             * 2 - log any changes (user's own, admin and system)
             */
            'mode' => 1,

            /**
             * Log message maximal allowed length
             */
            'messageMaxLength' => 1000,
        ],

        /**
         * Phone number format (structured,simple)
         */
        'phoneNumberFormat' => 'structured',

        'reseller' => [
            'auctionBidderCertUploadDir' => __RESELLER_FILES__ . '/aucbid_cert',
            'userCertUploadDir' => __RESELLER_FILES__ . '/user_cert',
        ],

        /**
         * Username of system user
         */
        'systemUsername' => 'system',
    ],

    'vendor' => [
        'artistResaleRights' => [   // ARR
            'currency' => "EUR",    // Currency for ARR
            'price' => 1000,        // Minimum hammer price for ARR
            'tax' => 4,             // Default ARR tax percent(%) on hammer price
        ],

        'bamboo' => [
            'enabled' => true,  // enable invoicing through bamboo
            'location' => 'fusion.samauctionsoftware.com',
            'apiUsername' => 'billing@swb-consulting.com',
            'apiPassword' => 'swbcons',
            'perAuctionFeeLive' => 0,   // float
            'perAuctionFeeTimed' => 0,
            'perAuctionFeeListing' => 0,
            'livePercentage' => 0,
            'timedPercentage' => 0,
            'audioFee' => 0,
            'audioVideoFee' => 0,
            'clientId' => 0,
            'liveCreationInvoiceNote' => '',
            'timedCreationInvoiceNote' => '',
            'livePercentageInvoiceNote' => '',
            'timedPercentageInvoiceNote' => '',
            'listingCreationInvoiceNote' => '',
        ],

        'bidpathStreaming' => [
            /**
             * BidPath streaming integration configuration.
             * To enable define a serialized array in the local configuration file core.local.php
             */
            'enabled' => false,
            'clientId' => 'THE CLIENT ID',
            'encryptionKey' => 'THE ENCRYPTION KEY',
            'encryptionVector' => 'THE ENCRYPTION VECTOR',
            'encoderUrl' => 'https://stream.bidpathhq.com/Streaming/BidPathStreamer.aspx',
            'playerUrl' => 'https://stream.bidpathhq.com/Streaming/BidPathStreamPlayer.aspx',
            'playerWidth' => '320',
            'playerHeight' => '273',
        ],

        'flowPlayer' => [
            'js' => '/assets/js/vendor/flowplayer/flowplayer-3.2.4.min.js',
            'swf' => '/assets/js/vendor/flowplayer/flowplayer-3.2.6-dev.swf',
        ],

        'google' => [
            'auth' => [
                'credentials' => '', //path to file with service account credentials
            ],
            'calendar' => [
                'enabled' => false,
                'calendarId' => 'primary',
                'updateTime' => 10 //minutes
            ],
            //googleAnalytics
            'analytics' => [
                /**
                 * SAM Google Analytics feature use tracking
                 * to use set 'UA-48963025-1'
                 */
                'webPropertyId' => false,
                /**
                 * SAM use Google Analytics event tracking code for some form buttons(Signup,Login)
                 */
                'trackingCode' => false,
            ],
        ],

        'jquery' => [
            'legacy' => [
                'src' => '/assets/js/vendor/jquery/jquery-1.11.1.min.js',
                'uiSrc' => '/assets/js/vendor/jquery/jquery-ui-1.11.1.custom.min.js',
                'migrateToolSrc' => '/assets/js/vendor/jquery/jquery-migrate-1.2.1.min.js',
            ],
            //            'responsive' => [
            //                'src' => '/assets/js/vendor/jquery/jquery-1.10.2.min.js',
            //                'uiSrc' => '/assets/js/vendor/jquery/jquery-ui-1.10.3.custom.min.js',
            //                'migrateToolSrc' => '/assets/js/vendor/jquery/jquery-migrate-1.2.1.js',
            //            ],
        ],

        /**
         * Setting for Magic Zoom Plus (SAM-3887)
         */
        'magicZoomPlus' => [

            /**
             * How to zoom image
             * Possible values : zoom, magnifier, preview, off
             * Default value : zoom
             */
            'zoomMode' => 'zoom',

            /**
             * When activate zoom
             * Possible values : hover,click
             * Default value : hover
             */
            'zoomOn' => 'hover',

            /**
             * Position of zoom window
             * Possible values : left, right, top, bottom, inner
             * Default value : right
             */
            'zoomPosition' => 'right',

            /**
             * Width of zoom window
             * Possible values : <percentage>, <pixels>, auto
             * Default value : auto
             */
            'zoomWidth' => 'auto',

            /**
             * Height of zoom window
             * Possible values : <percentage>, <pixels>, auto
             * Default value : auto
             */
            'zoomHeight' => 'auto',

            /**
             * Distance from small image to zoom window
             * Possible values : <pixels>
             * Default value : 15
             */
            'zoomDistance' => 15,

            /**
             * Position of caption on zoomed image
             * Possible values : top, bottom, off
             * Default value : off
             */
            'zoomCaption' => 'off',

            /**
             * How to show hint
             * Possible values : once, always,  off
             * Default value : once
             */
            'hint' => 'once',

            /**
             * Whether to allow changing zoom ratio with mouse wheel
             * Possible values : true, false
             * Default value : true
             */
            'variableZoom' => true,

            /**
             * Whether to load large image on demand (on first activation)
             * Possible values : true, false
             * Default value : false
             */
            'lazyZoom' => false,

            /**
             * Enable/disable smooth zoom movement
             * Possible values : true, false
             * Default value : true
             */
            'smoothing' => true,

            /**
             * Whether to allow context menu on right click
             * Possible values : true, false
             * Default value : false
             */
            'rightClick' => false,

            /**
             * Whether to scale up the large image if its original size is not enough for a zoom effect
             * Possible values : true, false
             * Default value : true
             */
            'upscale' => true,

            /**
             * Mouse event used to switch between multiple images
             * Possible values : click, hover
             * Default value : click
             */
            'selectorTrigger' => 'click',

            /**
             * Whether to enable dissolve effect when switching between images
             * Possible values : true, false
             * Default value : true
             */
            'transitionEffect' => true,

            /**
             * Whether to start Zoom on image automatically on page load or manually
             * Possible values : true, false
             * Default value : true
             */
            'autostart' => true,

            /**
             * Extra CSS class(es) to apply to zoom instance
             */
            'cssClass' => '',
        ],

        'qcodoJs' => '/assets/js/vendor/_core/_qc_minified.js',

        'samSharedService' => [
            /** Fetch from third-party service coordinates by postal code */
            'postalCode' => [
                'url' => 'http://api.auctionserver.net/index.php/rest/postalCode/getByCountryAndCode',
            ],
            /** Fetch from third-party service tax data by country and postal code */
            'tax' => [
                'loginToken' => '',     // API_LOGIN_TOKEN
                'url' => 'http://api.auctionserver.net/index.php/rest/taxData/getByCountryAndCode',
            ],
        ],

    ],
];
