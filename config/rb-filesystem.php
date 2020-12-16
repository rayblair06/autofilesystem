<?php

return [
    /**
     * The files we want to exclude while using the MoveToDisk command
     */
    'exclude_files' => [
        '.DS_Store',
        '.gitignore',
        '.xero-token.txt',
    ],

    /**
     * Our Filesystem decorator class
     */
    'extend_filesystem_class' => 'Rayblair\Filesystem\ExtendFilesystem'
];
