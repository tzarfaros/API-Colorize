<?php

namespace App\Entity;

use App\Repository\ThemesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ThemesRepository::class)
 */
class Themes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"api_palettes_browse","api_palettes_browseWithThemes","api_themes_browse","api_themes_readWithPalettes","api_themes_browseWithPalettes"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     * @Groups({"api_palettes_browse","api_palettes_browseWithThemes","api_themes_browse","api_themes_readWithPalettes","api_themes_browseWithPalettes"})
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Palettes::class, inversedBy="themes")
     * @Groups("api_themes_readWithPalettes","api_themes_browseWithPalettes")
     * 
     */
    private $palettes;

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
}
