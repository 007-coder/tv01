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
            'editable' => false,
            'visible' => true,
            'description' => 'Option some description,,,,text text',

            // data type
            'inputDataType' => ConfigValidator::T_BOOL,

            //validation rules
            'validate' => [
                'validationRules' =>''
                    /*'validate1:const=5,const2=dgdgdg|validate2|validate3'*/,
            ],
        ],
        'thumbnailSize' => [
            'description' => 'thumbnailSize: Option some description,,,,text text',
            'editable' => true,
            'validate' => [
                'validationRules' =>''
            ],
        ],
    ],

    'admin' => [
        'dashboard' => [
            'closedAuctions' => [
                'editable' => true,
                'validate' => [
                    'validationRules' => '',
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
                            'validationRules' => '',

                        ],
                    ],
                ],

            ],

        ],

        'report' =>[
            'customLots' => [
                'fields' => [
                    // data type
                    'inputDataType' => ConfigValidator::T_ARRAY,
                    'inputType' => 'textarea',
                    'description' => 'Option some description,,,,text text',
                    //validation rules
                    'validate' => [
                        'validationRules' =>''
                            /*'validate1:const=5,const2=dgdgdg|validate2|validate3'*/,
                    ],
                ]
            ]
        ],

        'user' => [
            'reseller' => [
                'auctionBidderCertUploadDir' => [
                    'editable' => false,
                ],
            ],
        ],

    ],

    'filesystem' => [
        'remote' => [
            'ipAllow' => [
                'inputDataType' => ConfigValidator::T_ARRAY,
                'description' => 'Description for <b>ipAllow</b> option',
                'valuesDelimiter' => ','
            ],
            'ipDeny' => [
                'inputDataType' => ConfigValidator::T_ARRAY,
                'description' => 'Description for <b>ipDeny</b> option',
                'valuesDelimiter' => ','
            ],
            'folderAllow' => [
                'inputDataType' => ConfigValidator::T_ARRAY,
                'inputType' => 'textarea',
                'description' => 'Description for <b>folderAllow</b> option',
                'valuesDelimiter' => ','
            ],
            'regexDeny' => [
                'inputDataType' => ConfigValidator::T_ARRAY,
                'inputType' => 'textarea',
                'description' => 'Description for <b>regexDeny</b> option',
                'valuesDelimiter' => ','
            ],

        ]
    ],

    'lot' => [
        'customField' => [
            'postalCode' => [
                'searchRadius' => [
                    // data type
                    'inputDataType' => ConfigValidator::T_ARRAY,
                    'description' => 'Option some description,,,,text text',
                    //validation rules
                    'validate' => [
                        'validationRules' =>''
                    ],
                ]
            ]
        ],

        'orderIgnoreWords' => [
            // data type
            'inputDataType' => ConfigValidator::T_ARRAY,
            'inputType' => 'textarea',
            'description' => 'Option some description,,,,text text',
            //validation rules
            'validate' => [
                'validationRules' =>''
            ],
        ]
    ],

    'rtb' => [
        'client' => [
            'ipAllow' => [
                // data type
                'inputDataType' => ConfigValidator::T_ARRAY,
                'description' => 'Option some description,,,,text text',
                //validation rules
                'validate' => [
                    'validationRules' =>''
                ],
            ],
            'ipDeny' => [
                // data type
                'inputDataType' => ConfigValidator::T_ARRAY,
                'description' => 'Option some description,,,,text text',
                //validation rules
                'validate' => [
                    'validationRules' =>''
                ],
            ]
        ]
    ],


];
