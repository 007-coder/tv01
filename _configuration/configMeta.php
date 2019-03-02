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
      'metaProp1' => 'metaProp1Val',
      'metaProp2' => 'metaProp2Va1',
    ],    
    'thumbnailSize' => [
      'editable' => false,
      'visible' => false           
    ]
  ],

  'admin' => [
    'dashboard' => [        
        'closedAuctions' => [
          'editable' => false,
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
