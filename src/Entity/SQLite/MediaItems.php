<?php

namespace App\Entity\SQLite;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SQLite\MediaItemsRepository")
 * @ORM\Table(name="media_items")
 */
class MediaItems
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
     * @var MetadataItems
     *
     * @ORM\OneToOne(targetEntity="App\Entity\SQLite\MetadataItems", inversedBy="mediaItems", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="metadata_item_id", referencedColumnName="id")
     */
    private $metadataItem;

    /**
     * @var MediaStreams[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\SQLite\MediaStreams", mappedBy="mediaItem")
     * @ORM\JoinColumn(name="id", referencedColumnName="media_item_id")
     */
    private $mediaStreams;

    public function __construct()
    {
        $this->mediaStreams = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return MetadataItems|null
     */
    public function getMetadataItem(): ?MetadataItems
    {
        return $this->metadataItem;
    }

    /**
     * @param MetadataItems|null $metadataItem
     *
     * @return MediaItems
     */
    public function setMetadataItem(?MetadataItems $metadataItem): self
    {
        $this->metadataItem = $metadataItem;

        return $this;
    }

    /**
     * @return Collection|MediaStreams[]
     */
    public function getMediaStreams(): Collection
    {
        return $this->mediaStreams;
    }

    /**
     * @param MediaStreams $mediaStream
     *
     * @return MediaItems
     */
    public function addMediaStream(MediaStreams $mediaStream): self
    {
        if (!$this->mediaStreams->contains($mediaStream)) {
            $this->mediaStreams[] = $mediaStream;
            $mediaStream->setMediaItem($this);
        }

        return $this;
    }

    /**
     * @param MediaStreams $mediaStream
     *
     * @return MediaItems
     */
    public function removeMediaStream(MediaStreams $mediaStream): self
    {
        if ($this->mediaStreams->contains($mediaStream)) {
            $this->mediaStreams->removeElement($mediaStream);
            // set the owning side to null (unless already changed)
            if ($mediaStream->getMediaItem() === $this) {
                $mediaStream->setMediaItem(null);
            }
        }

        return $this;
    }
}
