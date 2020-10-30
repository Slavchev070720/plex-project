<?php

namespace App\Entity\SQLite;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SQLite\MediaStreamsRepository")
 * @ORM\Table(name="media_streams")
 */
class MediaStreams
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
     * @var MediaItems
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\SQLite\MediaItems", inversedBy="mediaStreams")
     * @ORM\JoinColumn(name="media_item_id", referencedColumnName="id")
     */
    private $mediaItem;

    /**
     * @var int
     *
     * @ORM\Column(name="stream_type_id", type="integer")
     */
    private $streamTypeId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $language;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return MediaItems|null
     */
    public function getMediaItem(): ?MediaItems
    {
        return $this->mediaItem;
    }

    /**
     * @param MediaItems|null $mediaItem
     *
     * @return MediaStreams
     */
    public function setMediaItem(?MediaItems $mediaItem): self
    {
        $this->mediaItem = $mediaItem;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStreamTypeId(): ?int
    {
        return $this->streamTypeId;
    }

    /**
     * @param int $streamTypeId
     *
     * @return MediaStreams
     */
    public function setStreamTypeId(int $streamTypeId): self
    {
        $this->streamTypeId = $streamTypeId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * @param string|null $language
     *
     * @return MediaStreams
     */
    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }
}
