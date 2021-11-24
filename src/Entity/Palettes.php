<?php

namespace App\Entity;

use App\Repository\PalettesRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PalettesRepository::class)
 */
class Palettes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"api_palettes_browse", "api_palettes_browseWithColors","api_palettes_readWithColors","api_palettes_readWithUser","api_palettes_browseWithUser",
     * "api_palettes_browseWithFiles","api_palettes_readWithFiles","api_palettes_browseWithThemes","api_themes_readWithPalettes","api_themes_browseWithPalettes", 
     * "api_user_readWithPalettesFavorites", "api_files_browseWithPalettes", "api_user_readWithPalettescreated","api_palettes_browseWithColorHex","api_user_readWithFiles",
     *  "api_palettes_readUserWithPalettes", "api_user_readWithPalettesLikes"})

     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Groups({"api_palettes_browse", "api_palettes_browseWithColors","api_palettes_readWithColors","api_palettes_readWithUser","api_palettes_browseWithUser",
     * "api_palettes_browseWithFiles","api_palettes_readWithFiles","api_palettes_browseWithThemes","api_themes_readWithPalettes","api_themes_browseWithPalettes", 
     * "api_user_readWithPalettesFavorites", "api_files_browseWithPalettes", "api_user_readWithPalettescreated","api_palettes_browseWithColorHex", "api_user_readWithFiles", 
     * "api_palettes_readUserWithPalettes", "api_user_readWithPalettesLikes"})

     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"api_palettes_browse", "api_palettes_browseWithColors","api_palettes_readWithColors","api_palettes_readWithUser","api_palettes_browseWithUser",
     * "api_palettes_browseWithFiles","api_palettes_readWithFiles","api_palettes_browseWithThemes","api_themes_readWithPalettes","api_themes_browseWithPalettes", 
     * "api_user_readWithPalettesFavorites", "api_files_browseWithPalettes", "api_user_readWithPalettescreated","api_palettes_browseWithColorHex", 
     * "api_user_readWithFiles", "api_palettes_readUserWithPalettes", "api_user_readWithPalettesLikes"})

     */
    private $nbrLikes;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"api_palettes_browse", "api_palettes_browseWithColors","api_palettes_readWithColors","api_palettes_readWithUser","api_palettes_browseWithUser",

     * "api_palettes_browseWithFiles","api_palettes_readWithFiles","api_palettes_browseWithThemes","api_themes_readWithPalettes","api_themes_browseWithPalettes", 
     * "api_user_readWithPalettesFavorites", "api_files_browseWithPalettes", "api_user_readWithPalettescreated","api_palettes_browseWithColorHex", 
     * "api_user_readWithFiles", "api_palettes_readUserWithPalettes", "api_user_readWithPalettesLikes"})

     */
    private $public;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"api_palettes_browse", "api_palettes_browseWithColors","api_palettes_readWithColors","api_palettes_readWithUser","api_palettes_browseWithUser",

     * "api_palettes_browseWithFiles","api_palettes_readWithFiles","api_palettes_browseWithThemes","api_themes_readWithPalettes","api_themes_browseWithPalettes", 
     * "api_user_readWithPalettesFavorites", "api_files_browseWithPalettes", "api_user_readWithPalettescreated","api_palettes_browseWithColorHex", 
     * "api_user_readWithFiles", "api_palettes_readUserWithPalettes", "api_user_readWithPalettesLikes"})

     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"api_palettes_browse", "api_palettes_browseWithColors","api_palettes_readWithColors","api_palettes_readWithUser","api_palettes_browseWithUser",
     * "api_palettes_browseWithFiles","api_palettes_readWithFiles","api_palettes_browseWithThemes","api_themes_readWithPalettes","api_themes_browseWithPalettes", 
     * "api_user_readWithPalettesFavorites", "api_files_browseWithPalettes", "api_user_readWithPalettescreated","api_palettes_browseWithColorHex", "api_user_readWithFiles", "api_palettes_readUserWithPalettes", "api_user_readWithPalettesLikes"})

     */
    private $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="palettesFavorites")
     *@Groups({"api_palettes_readWithUser","api_palettes_browseWithUser"})
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=Themes::class, mappedBy="palettes", cascade={"persist"})
     * @Groups({"api_palettes_browseWithThemes"})
     */
    private $themes;

    /**
     * @ORM\ManyToMany(targetEntity=Files::class, mappedBy="palettes")
     * @Groups({"api_palettes_browseWithFiles","api_palettes_readWithFiles", "api_palettes_browseWithColorHex"})
     */
    private $files;

    /**
     * @ORM\ManyToMany(targetEntity=Colors::class, inversedBy="palettes", cascade={"persist"})
     * @Groups({"api_palettes_browse", "api_palettes_browseWithColors","api_palettes_readWithColors","api_palettes_readWithUser","api_palettes_browseWithUser",
     * "api_palettes_browseWithFiles","api_palettes_readWithFiles","api_palettes_browseWithThemes","api_themes_readWithPalettes","api_themes_browseWithPalettes", "api_user_readWithPalettesFavorites", "api_files_browseWithPalettes", "api_user_readWithPalettescreated","api_palettes_browseWithColorHex", "api_user_readWithFiles", "api_palettes_readUserWithPalettes", "api_user_readWithPalettesLikes"})
     */
    private $colors;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="palettesCreated")
     * @Groups({"api_files_browse", "api_palettes_readWithUser","api_palettes_browseWithUser"})
     */
    private $owner;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $features;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="palettesLikes")
     */
    private $likes;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->themes = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->colors = new ArrayCollection();

        $this->createdAt = new DateTimeImmutable();
        $this->nbrLikes = 0;
        $this->public = false;
        $this->features = false;
        $this->likes = new ArrayCollection();
        
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

    public function getNbrLikes(): ?int
    {
        return $this->nbrLikes;
    }

    public function setNbrLikes(?int $nbrLikes): self
    {
        $this->nbrLikes = $nbrLikes;

        return $this;
    }

    public function getPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|user[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(user $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    public function removeUser(user $user): self
    {
        $this->user->removeElement($user);

        return $this;
    }

    /**
     * @return Collection|Themes[]
     */
    public function getThemes(): Collection
    {
        return $this->themes;
    }

    public function addTheme(Themes $theme): self
    {
        if (!$this->themes->contains($theme)) {
            $this->themes[] = $theme;
            $theme->addPalette($this);
        }

        return $this;
    }

    public function removeTheme(Themes $theme): self
    {
        if ($this->themes->removeElement($theme)) {
            $theme->removePalette($this);
        }

        return $this;
    }

    /**
     * @return Collection|Files[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(Files $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->addPalette($this);
        }

        return $this;
    }

    public function removeFile(Files $file): self
    {
        if ($this->files->removeElement($file)) {
            $file->removePalette($this);
        }

        return $this;
    }

    /**
     * @return Collection|colors[]
     */
    public function getColors(): Collection
    {
        return $this->colors;
    }

    public function addColor(colors $color): self
    {
        if (!$this->colors->contains($color)) {
            $this->colors[] = $color;
        }

        return $this;
    }

    public function removeColor(colors $color): self
    {
        $this->colors->removeElement($color);

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getFeatures(): ?bool
    {
        return $this->features;
    }

    public function setFeatures(?bool $features): self
    {
        $this->features = $features;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(User $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->addPalettesLike($this);
        }

        return $this;
    }

    public function removeLike(User $like): self
    {
        if ($this->likes->removeElement($like)) {
            $like->removePalettesLike($this);
        }

        return $this;
    }
}
