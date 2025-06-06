<?php
defined('TYPO3_MODE') || die();

$boot = function ($_EXTKEY) {

    /* ===========================================================================
        Register default TypoScript
    =========================================================================== */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        $_EXTKEY,
        'Configuration/TypoScript/',
        'Ce Gallery'
    );

};

$boot($_EXTKEY);
unset($boot);
