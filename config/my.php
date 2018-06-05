<?php

return [
    'master' => [
        'default_file_path' => env(
            'MY_MASTER_DEFAULT_FILE_PATH',
            'default/master'
        ),
        'upload_file_path' => env(
            'MY_MASTER_UPLOAD_FILE_PATH',
            'uploads/master'
        ),
    ],
    'dpc' => [
        'upload_file_path' => env(
            'MY_DPC_UPLOAD_FILE_PATH',
            'uploads/dpc'
        ),
        'h_filename' => env(
            'MY_DPC_H_FILE_NAME',
            'h_file.tsv'
        ),
        'ef_filename' => env(
            'MY_DPC_EF_FILE_NAME',
            'ef_file.tsv'
        ),
        'import' => [
            'encode' => env(
                'MY_DPC_IMPORT_ENCODE',
                'SJIS-win'
                //'UTF-8'
            ),
            'delimiter' => env(
                'MY_DPC_IMPORT_DELIMITER',
                "\t"
            ),
        ]
    ],
    'h_file' => [
        'payload_names' => [
            'a' => [
                1 => '創傷処置',
                2 => '呼吸ケア',
                3 => '点滴ライン同時３本以上の管理',
                4 => '心電図モニターの管理',
                5 => 'シリンジポンプの管理',
                6 => '輸血や血液製剤の管理',
                7 => '専門的な治療・処置',
                8 => '救急搬送後の入院',
            ],
            'c' => [
                1 => '開頭の手術',
                2 => '開胸の手術',
                3 => '開腹の手術',
                4 => '骨の手術',
                5 => '胸腔鏡・腹腔鏡手術',
                6 => '全身麻酔・脊椎麻酔の手術',
                7 => '救命等に係る内科的治療',
            ],

        ]
    ],
];
