<?php

namespace App\Entity;

use App\Repository\AnimeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnimeRepository::class)]
class Anime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    private ?string $author = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    /**
     * @var Collection<int, AnimeAVoir>
     */
    #[ORM\OneToMany(targetEntity: AnimeAVoir::class, mappedBy: 'anime')]
    private Collection $animeAVoir;

    /**
     * @var Collection<int, AnimeVu>
     */
    #[ORM\OneToMany(targetEntity: AnimeVu::class, mappedBy: 'anime')]
    private Collection $animeVus;

    public function __construct()
    {
        $this->animeAVoir = new ArrayCollection();
        $this->animeVus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection<int, AnimeAVoir>
     */
    public function getanimeAVoir(): Collection
    {
        return $this->animeAVoir;
    }

    public function addanimeAVoir(AnimeAVoir $animeAVoir): static
    {
        if (!$this->animeAVoir->contains($animeAVoir)) {
            $this->animeAVoir->add($animeAVoir);
            $animeAVoir->setAnime($this);
        }

        return $this;
    }

    public function removeanimeAVoir(AnimeAVoir $animeAVoir): static
    {
        if ($this->animeAVoir->removeElement($animeAVoir)) {
            // set the owning side to null (unless already changed)
            if ($animeAVoir->getAnime() === $this) {
                $animeAVoir->setAnime(null);
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
            $animeVu->setAnime($this);
        }

        return $this;
    }

    public function removeAnimeVu(AnimeVu $animeVu): static
    {
        if ($this->animeVus->removeElement($animeVu)) {
            // set the owning side to null (unless already changed)
            if ($animeVu->getAnime() === $this) {
                $animeVu->setAnime(null);
            }
        }

        return $this;
    }
}
