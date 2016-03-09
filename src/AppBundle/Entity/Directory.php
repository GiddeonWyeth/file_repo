<?php
namespace AppBundle\Entity;

use AppBundle\Repository\DirectoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use UserBundle\Entity\User;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DirectoryRepository")
 * @ORM\Table(name="directory")
 * @UniqueEntity("path")
 * @ORM\HasLifecycleCallbacks()
 */
class Directory /*implements ORMBehaviors\Tree\NodeInterface, \ArrayAccess*/
{

    //use ORMBehaviors\Tree\Node;


    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $encodedName;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="directories")
     */
    private $user;


    private $userName;


    /**
     * @ORM\Column(type="boolean")
     */
    private $isPrivate;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     */
    private $path;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Directory", inversedBy="directories")
     */
    private $directory;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Directory", mappedBy="directory")
     */
    private $directories;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\File", mappedBy="directory")
     */
    private $files;


    public function __construct()
    {
        $this->directories = new ArrayCollection();
        $this->files = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get encodedName
     *
     * @return string
     */
    public function getEncodedName()
    {
        return $this->encodedName;
    }

    /**
     * Set encodedName
     *
     * @return Directory
     *
     * @ORM\PrePersist()
     */
    public function setEncodedName()
    {
        $this->encodedName = sha1(uniqid(mt_rand(), true));
        return $this;
    }

    /**
     * Get isPrivate
     *
     * @return boolean
     */
    public function getIsPrivate()
    {
        return $this->isPrivate;
    }

    /**
     * Set isPrivate
     *
     * @param boolean $isPrivate
     *
     * @return Directory
     */
    public function setIsPrivate($isPrivate)
    {
        $this->isPrivate = $isPrivate;
        return $this;
    }

    /**
     * Set userId
     *
     * @param \UserBundle\Entity\User $userId
     *
     * @return Directory
     */
    public function setUserId(\UserBundle\Entity\User $userId = null)
    {
        $this->user = $userId;
        return $this;
    }

    /**
     * Get userId
     *
     * @return \UserBundle\Entity\User
     */
    public function getUserId()
    {
        return $this->user;
    }

    /**
     * Get directoryId
     *
     * @return \AppBundle\Entity\Directory
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Set directoryId
     *
     * @param \AppBundle\Entity\Directory $directory
     *
     * @return Directory
     */
    public function setDirectory(\AppBundle\Entity\Directory $directory = null)
    {
        $this->directory = $directory;
        return $this;
    }

    /**
     * Add directory
     *
     * @param \AppBundle\Entity\Directory $directory
     *
     * @return Directory
     */
    public function addDirectory(\AppBundle\Entity\Directory $directory)
    {
        $this->directories[] = $directory;
        return $this;
    }

    /**
     * Remove directory
     *
     * @param \AppBundle\Entity\Directory $directory
     */
    public function removeDirectory(\AppBundle\Entity\Directory $directory)
    {
        $this->directories->removeElement($directory);
    }

    /**
     * Get directories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDirectories()
    {
        return $this->directories;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Directory
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

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

    /**
     * @return bool
     */
    public function createDir()
    {
        $path = $this->getUploadRootDir($this->getPath());
        if (!is_dir($path)) {
            mkdir($path);
            return true;
        } else {
            echo 'This directory exists';
            return false;
        }
    }

    /**
     * @param $path
     * @return string
     */
    protected function getUploadRootDir($path)
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../web/' . $this->getUploadDir($path);
    }

    /**
     * @param null $path
     * @return string
     */
    protected function getUploadDir($path = null)
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'files/' . $this->getUserName() . $path;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param mixed $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return Directory
     */
    public function setPath($path = null)
    {
        $this->path = $path;
        return $this;
    }

}
