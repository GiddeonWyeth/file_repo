<?php
namespace AppBundle\Entity;

use AppBundle\Repository\DirectoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DirectoryRepository")
 * @ORM\Table(name="directory")
 * @ORM\HasLifecycleCallbacks()
 */
class Directory
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $encoded_name;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="directories")
     */
    private $user;


    private $user_name;


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


    public function __construct()
    {
        $this->directories = new ArrayCollection();
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
        return $this->encoded_name;
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
        $this->encoded_name = sha1(uniqid(mt_rand(), true));
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
     * Set directoryId
     *
     * @param \AppBundle\Entity\Directory $directoryId
     *
     * @return Directory
     */
    public function setDirectoryId(\AppBundle\Entity\Directory $directoryId = null)
    {
        $this->directory = $directoryId;
        return $this;
    }

    /**
     * Get directoryId
     *
     * @return \AppBundle\Entity\Directory
     */
    public function getDirectoryId()
    {
        return $this->directory;
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

    public function createDir()
    {
        //var_dump($this->getUploadRootDir($this->getPath()).$this->getName());
        return mkdir($this->getUploadRootDir($this->getPath()) . $this->getName());

    }

    protected function getUploadRootDir($path)
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../web/' . $this->getUploadDir($path);
    }

    protected function getUploadDir($path = null)
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'files/' . $this->getUserName() . '/' . $path;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->user_name;
    }

    /**
     * @param mixed $user_name
     */
    public function setUserName($user_name)
    {
        $this->user_name = $user_name;
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

}
