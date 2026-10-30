<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;


    #[ORM\Column(length: 180)]
    private ?string $name = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, AnimeAVoir>
     */
    #[ORM\OneToMany(targetEntity: AnimeAVoir::class, mappedBy: 'user')]
    private Collection $animeAVoirs;

    /**
     * @var Collection<int, AnimeVu>
     */
    #[ORM\OneToMany(targetEntity: AnimeVu::class, mappedBy: 'user')]
    private Collection $animeVus;

    public function __construct()
    {
        $this->animeAVoirs = new ArrayCollection();
        $this->animeVus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    /**
     * @return Collection<int, AnimeAVoir>
     */
    public function getAnimeAVoir(): Collection
    {
        return $this->animeAVoirs;
    }

    public function addAnimeAVoir(AnimeAVoir $animeAVoir): static
    {
        if (!$this->animeAVoirs->contains($animeAVoir)) {
            $this->animeAVoirs->add($animeAVoir);
            $animeAVoir->setUser($this);
        }

        return $this;
    }

    public function removeAnimeAVoir(AnimeAVoir $animeAVoir): static
    {
        if ($this->animeAVoirs->removeElement($animeAVoir)) {
            // set the owning side to null (unless already changed)
            if ($animeAVoir->getUser() === $this) {
                $animeAVoir->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AnimeVu>
     */
    public function getAnimeVus(): Collection
    {
        return $this->animeVus;
    }

    public function addAnimeVu(AnimeVu $animeVu): static
    {
        if (!$this->animeVus->contains($animeVu)) {
            $this->animeVus->add($animeVu);
            $animeVu->setUser($this);
        }

        return $this;
    }

    public function removeAnimeVu(AnimeVu $animeVu): static
    {
        if ($this->animeVus->removeElement($animeVu)) {
            // set the owning side to null (unless already changed)
            if ($animeVu->getUser() === $this) {
                $animeVu->setUser(null);
            }
        }

        return $this;
    }
}
