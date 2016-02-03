<?php
namespace UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;

/**
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Directory", mappedBy="user")
     */
    protected $directories;

    public function __construct()
    {
        parent::__construct();
        $this->directories = new ArrayCollection();
    }

    /**
     * Add directory
     *
     * @param \AppBundle\Entity\Directory $directory
     *
     * @return User
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
}
