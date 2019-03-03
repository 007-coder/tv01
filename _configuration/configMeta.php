<?php 
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
      'visible' => false,
      'required' => true,
      'type'=>'boolean',
      'validate' =>[
        'requider'=>true,        
        'maxlength'=>30
      ]
      /*'metaProp1' => 'metaProp1Val' */
    ],    
    'thumbnailSize' => [
      'editable' => false,
      'required' => true,                    
    ]
  ],

  'admin' => [
    'dashboard' => [        
        'closedAuctions' => [
          'editable' => false,
          'required' => true,
          'metaProp2' => 'metaProp2Va1'
        ],
    ],

    'auction' => [ 
        'lastBids' => [
          'refreshTimeout' => [
            'editable' => false            
          ]
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
      'fieldConfig' =>[
        'LotStatus' => [
          'title' => [
            'editable' => false            
          ]
        ]
      ]

    ],

    'user' => [
      'reseller' => [
        'auctionBidderCertUploadDir' => [
          'editable' => false
        ],
      ]
    ]


  ]



];
