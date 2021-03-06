<?php
$quote = [
    [
        'isCurbing'=> '最近一次更新是否為瞬間價格穩定措施',
        'isCurbingRise'=> '最近一次更新是否為暫緩撮合且瞬間趨漲',
        'isCurbingFall'=> '最近一次更新是否為暫緩撮合且瞬間趨跌',
        'isTrial'=> '最近一次更新是否為試算',
        'isOpenDelayed'=> '當日是否曾發生延後開盤',
        'isCloseDelayed'=> '當日是否曾發生延後收盤',
        'isHalting'=> '最近一次更新是否為暫停交易',
        'isClosed'=> '當日是否為已收盤',
    ],
    [
        [
            'priceOpen'=> '當日之開盤價',
            'price'=> '總成交價',
            'at'=> '最新一筆成交時間',
        ]
    ],
    [
        [
            'priceHigh'=> '當日之最高價',
            'price'=> '總成交價',
            'at'=> '最新一筆成交時間',
        ]
    ],
    [   
        [
            'priceLow'=> '當日之最低價',
            'price'=> '總成交價',
            'at'=> '最新一筆成交時間',
        ]
    ],
    [
        [    
            'total'=> '總和',
            'at'=> '最新一筆成交時間',
            'unit'=> '總成交張數',
            'volume'=> '總成交量',
        ]
    ],
    [
        [
            'trade'=> '貿易',
            'price'=> '最新一筆成交價格',
            'unit'=> '最新一筆成交張數',
            'volume'=> '最新一筆成交之成交量',
            'serial'=> '最新一筆成交之序號',
            'at'=> '最新一筆成交時間',
        ]
    ],   
    [   
        [
            'trial'=> '試撮',
            'price'=> '最新一筆試撮價格',
            'unit'=> '最新一筆試撮張數',
            'volume'=> '最新一筆試撮成交量',
            'at'=> '最新一筆試撮時間',
        ]
    ],
    [ 
        'order'=> '最佳五檔',
        [
            'at'=> '最新一筆更新時間',  
        ],
        [
            'bestBids'=> '最佳出價',
            [
                'price'=> '價格',
                'unit'=> '張數',
                'volume'=> '量',
            ],
            [
                'price'=> '價格',
                'unit'=> '張數',
                'volume'=> '量',
            ],
            [
                'price'=> '價格',
                'unit'=> '張數',
                'volume'=> '量',
            ],
            [
                'price'=> '價格',
                'unit'=> '張數',
                'volume'=> '量',
            ],
            [
                'price'=> '價格',
                'unit'=> '張數',
                'volume'=> '量',
            ],
        ],
        [
            'bestAsks'=> '最好的',
            [
                'price'=> '價格',
                'unit'=> '張數',
                'volume'=> '量',
            ],
            [
                'price'=> '價格',
                'unit'=> '張數',
                'volume'=> '量',
            ],
            [
                'price'=> '價格',
                'unit'=> '張數',
                'volume'=> '量',
            ],
            [
                'price'=> '價格',
                'unit'=> '張數',
                'volume'=> '量',
            ],
            [
                'price'=> '價格',
                'unit'=> '張數',
                'volume'=> '量',
            ],
        ],
    ]
];

return $quote;
