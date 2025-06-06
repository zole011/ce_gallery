<?php

namespace Goran\CeGallery\Hooks;

class FlexFormHook
{
   /**
   * @param array $dataStructure
   * @param array $identifier
   * @return array
   */
   public function parseDataStructureByIdentifierPostProcess(array $dataStructure, array $identifier): array
   {
     if ($identifier['type'] === 'tca' && $identifier['tableName'] === 'tt_content' && $identifier['dataStructureKey'] === 'filelist_filelist,list') {
         $dataStructure['sheets']['sDEF']['ROOT']['el']['settings.includeSubfolders']['TCEforms']['onChange'] = 'reload';
         $file = PATH_site . 'typo3conf/ext/ce_gallery/Configuration/FlexForm/Additional.xml';
         $content = file_get_contents($file);
         if ($content) {
             $dataStructure['sheets']['extraEntry'] = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($content);
         }
     }
     return $dataStructure;
   }
}