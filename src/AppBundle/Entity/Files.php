<?php
/**
 * Created by PhpStorm.
 * User: anton.vityazev
 * Date: 12.02.2016
 * Time: 11:49
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Files
{
    /**
     * @var ArrayCollection
     */
    private $files;

    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param mixed $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }
}