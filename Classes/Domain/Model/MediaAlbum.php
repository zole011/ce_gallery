<?php
namespace Goran\CeGallery\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Media album
 */
class MediaAlbum extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * fileCollectionRepository
     *
     * @var \TYPO3\CMS\Core\Resource\FileCollectionRepository
     * @inject
     */
    protected $fileCollectionRepository;

    /**
     * @var array
     */
    /* protected */public $assetCache;

    /**
     * @var array
     */
    protected $allowedMimeTypes = [];

    /**
     * @var string
     */
    protected $assetsOrderBy = '';

    /**
     * @var string
     */
    protected $assetsOrderDirection = 'asc';

    /**
     * @var bool
     */
    protected $excludeEmptyAlbums = false;

    /**
     * Assets
     * An array of File or FileReference
     *
     * @var array
     */
    protected $assets;

    /**
     * @var integer
     */
    protected $assetsCount;

    /**
     * @var bool
     */
    protected $hidden;

    /**
     * Title
     *
     * @var \string
     */
    protected $title;

    // /**
    //  * Tstamp
    //  *
    //  * @var \DateTime
    //  */
    // protected $tstamp;

    /**
     * Description visible online
     *
     * @var \string
     */
    protected $webdescription;

    /**
     * Main asset
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    protected $mainAsset;

    /**
     * Child albums
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Goran\CeGallery\Domain\Model\MediaAlbum>
     * @lazy
     */
    protected $albumCache;

    /**
     * Set allowedMimeTypes
     *
     * @param array $allowedMimeTypes
     */
    public function setAllowedMimeTypes($allowedMimeTypes)
    {
        $this->allowedMimeTypes = $allowedMimeTypes;
    }

    /**
     * Get allowedMimeTypes
     *
     * @return array $allowedMimeTypes
     */
    public function getAllowedMimeTypes()
    {
        return $this->allowedMimeTypes;
    }

    /**
     * Get assetsOrderBy
     *
     * @return string
     */
    public function getAssetsOrderBy()
    {
        return $this->assetsOrderBy;
    }

    /**
     * Set assetsOrderBy
     *
     * @param string $assetsOrderBy
     */
    public function setAssetsOrderBy($assetsOrderBy)
    {
        $this->assetsOrderBy = $assetsOrderBy;
    }

    /**
     * Get assetsOrderDirection
     *
     * @return string
     */
    public function getAssetsOrderDirection()
    {
        return $this->assetsOrderDirection;
    }

    /**
     * Set assetsOrderDirection
     *
     * @param string $assetsOrderDirection
     */
    public function setAssetsOrderDirection($assetsOrderDirection)
    {
        $this->assetsOrderDirection = strtolower($assetsOrderDirection);
    }

    /**
     * Get excludeEmptyAlbums
     *
     * @return bool
     */
    public function getExcludeEmptyAlbums()
    {
        return $this->excludeEmptyAlbums;
    }

    /**
     * Set excludeEmptyAlbums
     *
     * @param bool $excludeEmptyAlbums
     */
    public function setExcludeEmptyAlbums($excludeEmptyAlbums)
    {
        $this->excludeEmptyAlbums = (bool)$excludeEmptyAlbums;
    }

    /**
     * Set hidden
     *
     * @param boolean $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * Get hidden
     *
     * @return boolean
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Returns the title
     *
     * @return \string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns the tstamp
     *
     * @return \DateTime
     */
    public function getTstamp()
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_file_collection');
        $statement = $queryBuilder
          ->select('crdate')
          ->from('sys_file_collection')
          ->where(
            $queryBuilder->expr()->eq('uid', $this->getUid())
          )
          ->execute();
        $out = $statement->fetch();
        $date = new \DateTime();
        $date->setTimestamp($out['crdate']);
        return $date;
    }

    /**
     * Sets the title
     *
     * @param \string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the webdescription
     *
     * @return \string $webdescription
     */
    public function getWebdescription()
    {
        return $this->webdescription;
    }

    /**
     * Sets the webdescription
     *
     * @param \string $webdescription
     * @return void
     */
    public function setWebdescription($webdescription)
    {
        $this->webdescription = $webdescription;
    }

    /**
     * @return File[]|FileReference[]
     */
    public function getAssets()
    {
        if ($this->assetCache === null) {
            try {
                /** @var $fileCollection \TYPO3\CMS\Core\Resource\Collection\AbstractFileCollection */
                $fileCollection = $this->fileCollectionRepository->findByUid($this->getUid());
                // print_r($fileCollection); exit;
                $fileCollection->loadContents();
                $files = $fileCollection->getItems();
                // check if file has right mimeType
                if (count($this->allowedMimeTypes) > 0) {
                  foreach ($files as $key => $fileObject) {
                        /** @var $fileObject File|FileReference */
                        if (!in_array($fileObject->getMimeType(), $this->allowedMimeTypes)) {
                            unset($files[$key]);
                        }
                        $fileObject->_getMetaData();
                    }
                    // reset keys
                    $files = array_values($files);
                } else {
                  foreach ($files as $key => $fileObject) {
                    /** @var $fileObject File|FileReference */
                    $fileObject->_getMetaData();
                  }
                }
                if (trim($this->assetsOrderBy) !== '') {
                    $files = $this->orderAssets($files, $this->assetsOrderBy, $this->assetsOrderDirection);
                }
                $this->assetCache = $files;
            } catch (\Exception $exception) {
                // failing albums get disabled
                $this->setHidden(true);
                // $this->mediaAlbumRepository->update($this);
                $this->assetCache = [];
            }
        }
        return $this->assetCache;
    }

    /**
     * Get asset by uid
     *
     * @param int $assetUid
     * @return File|FileReference|NULL
     */
    public function getAssetByUid($assetUid)
    {
        foreach ($assets = $this->getAssets() as $asset) {
            /** @var $asset File|FileReference */
            if ((int)$assetUid === (int)$asset->getUid()) {
                return $asset;
            }
        }
        return null;
    }

    /**
     * Get pevious, current and next asset by assetUid
     *
     * @param $assetUid
     * @return FileInterface[]
     */
    public function getPreviousCurrentAndNext($assetUid)
    {
        $previous = $last = $current = $next = null;

        foreach ($assets = $this->getAssets() as $asset) {
            if ($current !== null) {
                $next = $asset;
                break;
            }
            $previous = $last;
            $last = $asset;
            /** @var $asset File|FileReference */
            if ((int)$assetUid === (int)$asset->getUid()) {
                $current = $asset;
            }
        }

        return [$previous, $current, $next];
    }

    /**
     * Get assetsCount
     *
     * @return integer
     */
    public function getAssetsCount()
    {
        if ($this->assetCache === null) {
            return count($this->getAssets());
        }
        return count($this->assetCache);
    }

    /**
     * @return File|FileReference
     */
    public function getMainAsset()
    {
        $mainAsset = null;
        if ($this->mainAsset) {
            $mainAsset = $this->mainAsset->getOriginalResource();
        } else {
            $assets = $this->getAssets();
            $mainAsset = $assets !== [] ? $assets[0] : null;
        }
        return $mainAsset;
    }

    /**
     * @return File|FileReference
     */
    public function getRandomAsset()
    {
        $assets = $this->getAssets();

        // if there is an asset, return it
        if (count($assets)) {
            return $assets[rand(1, count($assets)) - 1];
        } else {
            // try to fetch it from child album
            $randomAlbum = $this->getRandomAlbum();
            if ($randomAlbum) {
                return $randomAlbum->getRandomAsset();
            }
            // album and child album are empty
            return null;
        }
    }

    /**
     * @param FileInterface[] $files
     * @param string $orderBy
     * @param string $direction
     * @return FileInterface[]
     */
    protected function orderAssets($files, $orderBy, $direction)
    {
        usort($files, function ($a, $b) use ($orderBy, $direction) {
            if ($orderBy === 'crdate') {
                $compare = $a->getCreationTime() > $b->getCreationTime();
            } elseif (in_array($orderBy, ['content_creation_date', 'content_modification_date'], true)) {
                $compare = $a->getProperty($orderBy) > $b->getProperty($orderBy);
            } else {
                $compare = strnatcasecmp($a->getProperty($orderBy), $b->getProperty($orderBy));
            }
            return $direction === 'desc' ? -1 * $compare : $compare;
        });

        return $files;
    }
}
