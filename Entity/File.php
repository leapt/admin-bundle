<?php

namespace Leapt\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Leapt\CoreBundle\Doctrine\Mapping as LeaptCore;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Leapt\AdminBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="leapt_admin_file")
 */
class File {
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    protected $path;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="tags", type="string", length=255, nullable=true)
     */
    protected $tags;

    /**
     * @var UploadedFile
     *
     * @Assert\File(maxSize="6000000")
     * @Assert\NotBlank()
     * @LeaptCore\File(path="uploads/images", mappedBy="path", filename="name")
     */
    public $file;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}