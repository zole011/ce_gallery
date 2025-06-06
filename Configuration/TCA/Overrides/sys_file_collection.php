<?php
defined('TYPO3_MODE') || die();

$additionalColumns = [
    'webdescription' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:ce_gallery/Resources/Private/Language/frontend.xlf:webdescription',
        'config' => [
            'type' => 'text',
            'cols' => 40,
            'rows' => 5,
            'eval' => 'trim',
            'enableRichtext' => true,
            'fieldControl' => [
                'fullScreenRichtext' => [
                    'disabled' => false,
                ],
            ],
        ],
        'defaultExtras' => 'richtext[]',
    ],
    'main_asset' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:ce_gallery/Resources/Private/Language/frontend.xlf:main_asset',
        'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
            'images',
            [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:ce_gallery/Resources/Private/Language/frontend.xlf:main_asset.add'
                ],
                'maxitems' => 1,
            ],
            $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
        )
    ],
    // 'tstamp' => [
    //   'label' => 'tstamp',
    //   'config' => [
    //     'type' => 'passthrough',
    //   ]
    // ],
    // 'tstamp' => [
    //   'exclude' => true,
    //   'label' => 'Date edited',
    //   'config' => [
    //       'type' => 'input',
    //       'renderType' => 'inputDateTime',
    //       'eval' => 'date,int',
    //       'default' => 0,
    //       'default' => new DateTime()->getTimestamp(),
    //   ]
    // ],
    'crdate' => [
      'exclude' => true,
      'label' => 'Date created',
      'config' => [
          'type' => 'input',
          'renderType' => 'inputDateTime',
          'eval' => 'date,int',
          // 'default' => 0,
          'default' => time(),
      ]
    ],
];

foreach ($GLOBALS['TCA']['sys_file_collection']['types'] as $type => $tmp) {
    $GLOBALS['TCA']['sys_file_collection']['types'][$type]['showitem'] .= ',--div--;LLL:EXT:ce_gallery/Resources/Private/Language/frontend.xlf:mediaalbum';
    $GLOBALS['TCA']['sys_file_collection']['types'][$type]['showitem'] .= ',main_asset,webdescription,crdate';
    // $GLOBALS['TCA']['sys_file_collection']['types'][$type]['showitem'] .= ',main_asset,webdescription';
    // $GLOBALS['TCA']['sys_file_collection']['types'][$type]['showitem'] .= ',main_asset,webdescription,tstamp';
}

// enable main asset preview in list module
$GLOBALS['TCA']['sys_file_collection']['ctrl']['thumbnail'] = 'main_asset';


\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule(
    $GLOBALS['TCA']['sys_file_collection']['columns'],
    $additionalColumns
);
