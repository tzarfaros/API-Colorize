<?php

namespace App\Entity;

use App\Repository\ColorsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ColorsRepository::class)
 */
class Colors
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     *  @Groups({"api_palettes_browse", "api_palettes_browseWithColors","api_palettes_readWithColors","api_palettes_browseWithColorHex", "api_colors_browse",
     *  "api_palettes_browseWithFiles","api_palettes_readWithFiles","api_palettes_browseWithThemes","api_themes_readWithPalettes","api_themes_browseWithPalettes",
     *  "api_user_readWithPalettesFavorites", "api_files_browseWithPalettes", "api_user_readWithPalettescreated", "api_user_readWithFiles",
     * "api_palettes_findSaveByPagesByFilters", "api_user_readWithPalettesLikes"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     * 
     * @Groups({"api_palettes_browse" ,"api_palettes_browseWithColors","api_palettes_readWithColors","api_palettes_readWithUser","api_palettes_findSaveByPagesByFilters",
     * "api_palettes_browseWithUser","api_palettes_browseWithFiles","api_palettes_readWithFiles","api_palettes_browseWithThemes","api_palettes_readUserWithPalettes",
     * "api_themes_readWithPalettes","api_themes_browseWithPalettes", "api_user_readWithPalettesFavorites", "api_files_browseWithPalettes", "api_user_readWithPalettescreated",
     * "api_palettes_browseWithColorHex", "api_colors_browse", "api_user_readWithFiles", "api_user_readWithPalettesLikes"})

     */
    private $name;

    /**
     * @ORM\Column(type="string", length=128)
     *
     *  @Groups({"api_palettes_browse", "api_palettes_browseWithColors","api_palettes_readWithColors","api_palettes_findSaveByPagesByFilters",
     * "api_palettes_readWithUser","api_palettes_browseWithUser","api_palettes_browseWithFiles","api_palettes_readWithFiles","api_palettes_browseWithThemes",
     * "api_themes_readWithPalettes","api_themes_browseWithPalettes", "api_user_readWithPalettesFavorites", "api_files_browseWithPalettes", 
     * "api_user_readWithPalettescreated","api_palettes_browseWithColorHex", "api_colors_browse","api_user_readWithFiles", "api_palettes_readUserWithPalettes", "api_user_readWithPalettesLikes"})

     */
    private $hex;

    /**
     * @ORM\ManyToMany(targetEntity=Palettes::class, mappedBy="colors")
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

    public function getHex(): ?string
    {
        return $this->hex;
    }

    public function setHex(string $hex): self
    {
        $this->hex = $hex;

        return $this;
    }

    /**
     * @return Collection|Palettes[]
     */
    public function getPalettes(): Collection
    {
        return $this->palettes;
    }

    public function addPalette(Palettes $palette): self
    {
        if (!$this->palettes->contains($palette)) {
            $this->palettes[] = $palette;
            $palette->addColor($this);
        }

        return $this;
    }

    public function removePalette(Palettes $palette): self
    {
        if ($this->palettes->removeElement($palette)) {
            $palette->removeColor($this);
        }

        return $this;
    }
}