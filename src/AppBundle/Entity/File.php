<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use UserBundle\Entity\User;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FileRepository")
 * @ORM\Table(name="file")
 * @ORM\HasLifecycleCallbacks()
 */
class File
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Directory", inversedBy="id")
     */
    private $directory;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $encodedName;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @param mixed $directory
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function uploadFile(UploadedFile $file, User $user, Directory $directory = null)
    {
        $directoryPath = $directory ? $directory->getPath() : null;
        $file->move(self::getUploadRootDir('files/' . $user->getUsername() . $directoryPath), $this->getEncodedName() . '.' . $file->getClientOriginalExtension());
    }

    protected function getUploadRootDir($path)
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../web/' . $path;
    }

    /**
     * @return mixed
     */
    public function getEncodedName()
    {
        return $this->encodedName;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setEncodedName()
    {
        $this->encodedName = sha1(uniqid(mt_rand(), true));
        return $this;
    }

}
