<?php
defined('TYPO3_MODE') || die();

$boot = function ($_EXTKEY) {

    /* ===========================================================================
        Register an "image gallery" layout
    =========================================================================== */
    $GLOBALS['TYPO3_CONF_VARS']['EXT']['file_list']['templateLayouts'][] = [
        'Image Gallery', // You may use a LLL:EXT: label reference here of course!
        'CeGallery',
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools::class]['flexParsing'][]
       = \Goran\CeGallery\Hooks\FlexFormHook::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_extfilefunc.php']['processData']['ce_gallery'] = 'Goran\\CeGallery\\Hooks\\ProcessHook';
};

$boot($_EXTKEY);
unset($boot);