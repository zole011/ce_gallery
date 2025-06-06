<?php
namespace Goran\CeGallery\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class ProcessHook implements \TYPO3\CMS\Core\Utility\File\ExtendedFileUtilityProcessDataHookInterface
{
  /**
   * Post-process a file action.
   *
   * @param string $action The action
   * @param array $cmdArr The parameter sent to the action handler
   * @param array $result The results of all calls to the action handler
   * @param \TYPO3\CMS\Core\Utility\File\ExtendedFileUtility $parentObject Parent object
   * @return void
   */
  public function processData_postProcessAction($action, array $cmdArr, array $result, \TYPO3\CMS\Core\Utility\File\ExtendedFileUtility $parentObject)
  {
    // Create gallery.info files only inside /fileadmin/gallery folder
    // used to store gallery meta-data in backend
    if ($action == 'newfolder' && !empty($result) && $cmdArr['target'] == '1:/galerija/') {
      $folder = $result[count($result) - 1];
      $folder->getStorage()->createFile('gallery.info', $folder);
    }
  }
}
