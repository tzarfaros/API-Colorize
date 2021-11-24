<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"api_user_browse", "api_user_readWithPalettesFavorites", "api_user_readWithFiles", "api_user_readWithFile", "api_user_readWithPalettescreated", "api_files_browse", "api_palettes_readWithUser","api_palettes_browseWithUser", "api_palettes_readUserWithPalettes", "api_user_readWithPalettesLikes"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     * @Groups({"api_user_browse", "api_user_readWithPalettesFavorites", "api_user_readWithFiles", "api_user_readWithFile", "api_user_readWithPalettescreated", "api_files_browse", "api_palettes_readWithUser","api_palettes_browseWithUser", "api_palettes_readUserWithPalettes", "api_user_readWithPalettesLikes"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"api_user_browse", "api_user_readWithPalettesFavorites", "api_user_readWithFiles", "api_user_readWithFile", "api_user_readWithPalettescreated", "api_files_browse", "api_palettes_readWithUser","api_palettes_browseWithUser", "api_user_readWithPalettesLikes"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"api_user_browse"})
     * 
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"api_user_browse"})
     * 
     */
    private $password;

    /**
     * @ORM\ManyToMany(targetEntity=Palettes::class, mappedBy="user")
     * @Groups({"api_user_readWithPalettesFavorites"})
     * 
     */
    private $palettesFavorites;

    /**
     * @ORM\OneToMany(targetEntity=Files::class, mappedBy="user")
     * @Groups({"api_user_readWithFiles"})
     * 
     */
    private $filesPersonnel;

    /**
     * @ORM\OneToMany(targetEntity=Palettes::class, mappedBy="owner")
     * @Groups({"api_user_readWithPalettescreated", "api_palettes_readUserWithPalettes"})
     * 
     */
    private $palettesCreated;

    /**
     * @ORM\ManyToMany(targetEntity=Palettes::class, inversedBy="likes")
     * @Groups({"api_user_readWithPalettesLikes"})
     */
    private $palettesLikes;


    public function __construct()
    {
        $this->palettesfavorites = new ArrayCollection();
        $this->filesPersonnel = new ArrayCollection();
        $this->owner = new ArrayCollection();
        $this->palettesLikes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|palettes[]
     */
    public function getPalettesFavorites(): Collection
    {
        return $this->palettesFavorites;
    }

    public function addPalettesFavorites(palettes $palettesFavorites): self
    {
        if (!$this->palettesFavorites->contains($palettesFavorites)) {
            $this->palettesFavorites[] = $palettesFavorites;
        }

        return $this;
    }

    public function removePalettesFavoriteS(palettes $palettesFavorites): self
    {
        $this->palettesFavorites->removeElement($palettesFavorites);

        return $this;
    }

    /**
     * @return Collection|files[]
     */
    public function getFilesPersonnel(): Collection
    {
        return $this->filesPersonnel;
    }

    public function addFilesPersonnel(files $filesPersonnel): self
    {
        if (!$this->filesPersonnel->contains($filesPersonnel)) {
            $this->filesPersonnel[] = $filesPersonnel;
            $filesPersonnel->setUser($this);
        }

        return $this;
    }

    public function removeFilesPersonnel(files $filesPersonnel): self
    {
        if ($this->filesPersonnel->removeElement($filesPersonnel)) {
            // set the owning side to null (unless already changed)
            if ($filesPersonnel->getUser() === $this) {
                $filesPersonnel->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|files[]
     */
    public function getPalettesCreated(): Collection
    {
        return $this->palettesCreated;
    }

    public function addPalettesCreated(palettes $palettesCreated): self
    {
        if (!$this->palettesCreated->contains($palettesCreated)) {
            $this->palettesCreated[] = $palettesCreated;
            $palettesCreated->setOwner($this);
        }

        return $this;
    }

    public function removePalettesCreated(palettes $palettesCreated): self
    {
        if ($this->palettesCreated->removeElement($palettesCreated)) {
            // set the owning side to null (unless already changed)
            if ($palettesCreated->getOwner() === $this) {
                $palettesCreated->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of username
     */ 
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of username
     *
     * @return  self
     */ 
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection|palettes[]
     */
    public function getPalettesLikes(): Collection
    {
        return $this->palettesLikes;
    }

    public function addPalettesLike(palettes $palettesLike): self
    {
        if (!$this->palettesLikes->contains($palettesLike)) {
            $this->palettesLikes[] = $palettesLike;
        }

        return $this;
    }

    public function removePalettesLike(palettes $palettesLike): self
    {
        $this->palettesLikes->removeElement($palettesLike);

        return $this;
    }
}
