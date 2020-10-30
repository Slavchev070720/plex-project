<?php

namespace App\Entity\Main;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\Main\MovieRepository")
 * @ORM\Table(name="movie",indexes={
 *     @ORM\Index(name="movie_title_idx", columns={"title"}),
 *      })
 */
class Movie
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
     * @ORM\Column(type="string", length=9, nullable=true)
     * @Assert\Length(
     *      min = 9,
     *      max = 9,
     *      minMessage = "Imdb Id must be exatly 9 characters long",
     *      maxMessage = "Imdb Id must be exatly 9 characters long"
     * )
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
    private $subtitle_lang;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $audio_lang;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $writer;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $director;

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
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $error_audio;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $error_subtitle;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

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
     * @return Movie
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
     * @return Movie
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
     * @return Movie
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
     * @return Movie
     */
    public function setReleaseDate(?\DateTimeInterface $release_date): self
    {
        $this->release_date = $release_date;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSubtitleLang(): ?string
    {
        return $this->subtitle_lang;
    }

    /**
     * @param string|null $subtitle_lang
     *
     * @return Movie
     */
    public function setSubtitleLang(?string $subtitle_lang): self
    {
        $this->subtitle_lang = $subtitle_lang;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAudioLang(): ?string
    {
        return $this->audio_lang;
    }

    /**
     * @param string|null $audio_lang
     *
     * @return Movie
     */
    public function setAudioLang(?string $audio_lang): self
    {
        $this->audio_lang = $audio_lang;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getWriter(): ?string
    {
        return $this->writer;
    }

    /**
     * @param string|null $writer
     *
     * @return Movie
     */
    public function setWriter(?string $writer): self
    {
        $this->writer = $writer;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDirector(): ?string
    {
        return $this->director;
    }

    /**
     * @param string|null $director
     *
     * @return Movie
     */
    public function setDirector(?string $director): self
    {
        $this->director = $director;

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
     * @return Movie
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
     * @return Movie
     */
    public function setRating(?float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return Movie
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getErrorAudio(): ?bool
    {
        return $this->error_audio;
    }

    /**
     * @param bool $error_audio
     *
     * @return Movie
     */
    public function setErrorAudio(bool $error_audio): self
    {
        $this->error_audio = $error_audio;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getErrorSubtitle(): ?bool
    {
        return $this->error_subtitle;
    }

    /**
     * @param bool $error_subtitle
     *
     * @return Movie
     */
    public function setErrorSubtitle(bool $error_subtitle): self
    {
        $this->error_subtitle = $error_subtitle;

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
     * @return Movie
     */
    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }
}
