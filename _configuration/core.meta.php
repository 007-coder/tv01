<?php

use Sam\Installation\Config\ConfigValidator;

// В этом файле устанавливаются настройки доступности
// полей на редактирования а так же мета-свойства полей
// По-умолчанию все поля в форме доступны для редактирования.
// Если вы хотите запретить редактирование поля - пропишите 
// 'editable' => false для данного поля (соблюдая структуру 
// из _configuration/core.php)

return [

    'account' => [
        'page' => [
            'editable' => true,
            'visible' => true,

            // data type
            'inputDataType' => ConfigValidator::T_BOOL,
            'validate' => [
                'required' => true,
                'validationRules' =>
                    'validate1:const=5,const2=dgdgdg|validate2|validate3',
            ],
        ],
        'thumbnailSize' => [
            'editable' => true,
            'validate' => [
                'required' => true,
            ],
        ],
    ],

    'admin' => [
        'dashboard' => [
            'closedAuctions' => [
                'editable' => true,
                'validate' => [
                    'validationRules' => 'isTest',
                ],
            ],
        ],

        'auction' => [
            'lastBids' => [
                'refreshTimeout' => [
                    'editable' => false,
                ],
            ],
            'lots' => [
                'syncTimeout' => [
                    'editable' => false,
                ],
                'quickEditLotLimit' => [
                    'editable' => false,
                ],
            ],
        ],

        'inventory' => [
            'fieldConfig' => [
                'LotStatus' => [
                    'title' => [
                        'editable' => false,
                    ],
                ],
                'ItemNumber' => [
                    'requirable' => [
                        'validate' => [
                            'validationRules' => 'isTest454',
                        ],
                    ],
                ],

            ],

        ],

        'user' => [
            'reseller' => [
                'auctionBidderCertUploadDir' => [
                    'editable' => false,
                ],
            ],
        ],

    ],

];
