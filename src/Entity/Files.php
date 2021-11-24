<?php

namespace App\Entity;

use App\Repository\FilesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=FilesRepository::class)
 */
class Files
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"api_user_readWithFiles", "api_files_browse", "api_files_browseWithPalettes", "api_user_readWithFile", "api_palettes_browse",
     * "api_palettes_browseWithFiles","api_palettes_readWithFiles"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     * @Groups({"api_user_readWithFiles", "api_files_browse", "api_files_browseWithPalettes", "api_user_readWithFile", "api_palettes_browse",
     * "api_palettes_browseWithFiles","api_palettes_readWithFiles"})
     */
    private $name;

    

    /**
     * @ORM\ManyToMany(targetEntity=Palettes::class, inversedBy="files")
     * @Groups({"api_files_browseWithPalettes", "api_user_readWithFiles"})
     * 
     */
    private $palettes;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="filesPersonnel")
     * @Groups({"api_files_browse"})
     *
     */
    private $user;

    public function __construct()
    {
        $this->palettes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|palettes[]
     */
    public function getPalettes(): Collection
    {
        return $this->palettes;
    }

    public function addPalette(palettes $palette): self
    {
        if (!$this->palettes->contains($palette)) {
            $this->palettes[] = $palette;
        }

        return $this;
    }

    public function removePalette(palettes $palette): self
    {
        $this->palettes->removeElement($palette);

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
