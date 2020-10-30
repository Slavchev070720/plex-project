<?php

namespace App\Entity\Main;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Main\SeriesRepository")
 *  @ORM\Table(name="series",indexes={
 *    @ORM\Index(name="series_title_idx", columns={"title"}),
 *     })
 */
class Series
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imdb_id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private $release_date;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $poster_url;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $rating;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    /**
     * @var Episode[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Main\Episode", mappedBy="series", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"season" = "ASC"})
     */
    private $episodes;

    public function __construct()
    {
        $this->episodes = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getImdbId(): ?string
    {
        return $this->imdb_id;
    }

    /**
     * @param string|null $imdb_id
     *
     * @return Series
     */
    public function setImdbId(?string $imdb_id): self
    {
        $this->imdb_id = $imdb_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Series
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return Series
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->release_date;
    }

    /**
     * @param \DateTimeInterface|null $release_date
     *
     * @return Series
     */
    public function setReleaseDate(?\DateTimeInterface $release_date): self
    {
        $this->release_date = $release_date;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPosterUrl(): ?string
    {
        return $this->poster_url;
    }

    /**
     * @param string|null $poster_url
     *
     * @return Series
     */
    public function setPosterUrl(?string $poster_url): self
    {
        $this->poster_url = $poster_url;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getRating(): ?float
    {
        return $this->rating;
    }

    /**
     * @param float|null $rating
     *
     * @return Series
     */
    public function setRating(?float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * @param int|null $duration
     *
     * @return Series
     */
    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return Collection|Episode[]
     */
    public function getEpisodes(): Collection
    {
        return $this->episodes;
    }

    /**
     * @param Episode
     *
     * @return Series
     */
    public function addEpisode(Episode $episode): self
    {
        if (!$this->episodes->contains($episode)) {
            $this->episodes[] = $episode;
            $episode->setSeries($this);
        }

        return $this;
    }

    /**
     * @param Episode $episode
     *
     * @return Series
     */
    public function removeEpisode(Episode $episode): self
    {
        if ($this->episodes->contains($episode)) {
            $this->episodes->removeElement($episode);
            // set the owning side to null (unless already changed)
            if ($episode->getSeries() === $this) {
                $episode->setSeries(null);
            }
        }

        return $this;
    }
}


