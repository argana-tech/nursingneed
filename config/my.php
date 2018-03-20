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

];
