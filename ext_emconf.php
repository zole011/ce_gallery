<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "ce_background"
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
        'title' => 'Gallery plugin',
        'description' => 'Extends file list with gallery layout',
        'category' => 'plugin',
        'author' => 'Goran Medakovic',
        'author_email' => 'goran.it@gmail.com',
        'state' => 'stable',
        'internal' => '',
        'uploadfolder' => '0',
        'createDirs' => '',
        'clearCacheOnLoad' => 0,
        'version' => '1.0.0',
        'constraints' => array(
                'depends' => array(
                        'typo3' => '9.0.0-9.99.99',
                        'file_list' => '2.3.1-2.3.99',
                ),
                'conflicts' => array(
                ),
                'suggests' => array(
                ),
        ),
);

