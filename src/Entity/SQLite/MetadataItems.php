<?php

namespace App\Entity\SQLite;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SQLite\MetadataItemsRepository")
 * @ORM\Table(name="metadata_items")
 */
class MetadataItems
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="parent_id", type="integer")
     */
    private $parentId;

    /**
     * @var int
     *
     * @ORM\Column(name="metadata_type", type="integer", nullable=true)
     */
    private $metadataType;

    /**
     * @var string
     *
     * @ORM\Column(name="guid", type="string", length=255)
     */
    private $guid;


    /**
     * @var int
     *
     * @ORM\Column(name="[index]", type="integer")
     */
    private $index;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var float
     *
     * @ORM\Column(name="rating", type="float", nullable=true)
     */
    private $rating;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="text", nullable=true)
     */
    private $summary;

    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="integer", nullable=true)
     */
    private $duration;

    /**
     * @var string
     *
     * @ORM\Column(name="tags_genre", type="string", length=255, nullable=true)
     */
    private $tagsGenre;

    /**
     * @var string
     *
     * @ORM\Column(name="tags_director", type="string", length=255, nullable=true)
     */
    private $tagsDirector;

    /**
     * @var string
     *
     * @ORM\Column(name="tags_writer", type="string", length=255, nullable=true)
     */
    private $tagsWriter;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="originally_available_at", type="datetime", nullable=true)
     */
    private $originallyAvailableAt;

    /**
     * @var MediaItems
     *
     * @ORM\OneToOne(targetEntity="App\Entity\SQLite\MediaItems", mappedBy="metadataItem", cascade={"persist", "remove"})
     */
    private $mediaItems;

    /**
     * @var MetadataItems
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\SQLite\MetadataItems", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    private $parent;

    /**
     * @var MetadataItems[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\SQLite\MetadataItems", mappedBy="parent", fetch="EXTRA_LAZY")
     */
    private $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @param int $index
     */
    public function setIndex(int $index): void
    {
        $this->index = $index;
    }

    /**
     * @param $parentId
     */
    public function setParentId($parentId): void
    {
        $this->parentId = $parentId;
    }

    /**
     * @return int|null
     */
    public function getMetadataType(): ?int
    {
        return $this->metadataType;
    }

    /**
     * @param int|null $metadataType
     *
     * @return MetadataItems
     */
    public function setMetadataType(?int $metadataType): self
    {
        $this->metadataType = $metadataType;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGuid(): ?string
    {
        return $this->guid;
    }

    /**
     * @param string $guid
     *
     * @return MetadataItems
     */
    public function setGuid(string $guid): self
    {
        $this->guid = $guid;

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
     * @param string|null $title
     *
     * @return MetadataItems
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

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
     * @return MetadataItems
     */
    public function setRating(?float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSummary(): ?string
    {
        return $this->summary;
    }

    /**
     * @param string|null $summary
     *
     * @return MetadataItems
     */
    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

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
     * @return MetadataItems
     */
    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTagsGenre(): ?string
    {
        return $this->tagsGenre;
    }

    /**
     * @param string|null $tagsGenre
     *
     * @return MetadataItems
     */
    public function setTagsGenre(?string $tagsGenre): self
    {
        $this->tagsGenre = $tagsGenre;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTagsDirector(): ?string
    {
        return $this->tagsDirector;
    }

    /**
     * @param string|null $tagsDirector
     *
     * @return MetadataItems
     */
    public function setTagsDirector(?string $tagsDirector): self
    {
        $this->tagsDirector = $tagsDirector;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTagsWriter(): ?string
    {
        return $this->tagsWriter;
    }

    /**
     * @param string|null $tagsWriter
     *
     * @return MetadataItems
     */
    public function setTagsWriter(?string $tagsWriter): self
    {
        $this->tagsWriter = $tagsWriter;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getOriginallyAvailableAt(): ?\DateTimeInterface
    {
        return $this->originallyAvailableAt;
    }

    /**
     * @param \DateTimeInterface|null $originallyAvailableAt
     *
     * @return MetadataItems
     */
    public function setOriginallyAvailableAt(?\DateTimeInterface $originallyAvailableAt): self
    {
        $this->originallyAvailableAt = $originallyAvailableAt;

        return $this;
    }

    /**
     * @return MediaItems|null
     */
    public function getMediaItems(): ?MediaItems
    {
        return $this->mediaItems;
    }

    /**
     * @param MediaItems|null $mediaItems
     *
     * @return MetadataItems
     */
    public function setMediaItems(?MediaItems $mediaItems): self
    {
        $this->mediaItems = $mediaItems;

        // set (or unset) the owning side of the relation if necessary
        $newMetadata_item = $mediaItems === null ? null : $this;
        if ($newMetadata_item !== $mediaItems->getMetadataItem()) {
            $mediaItems->setMetadataItem($newMetadata_item);
        }

        return $this;
    }

    /**
     * @return MetadataItems|null
     */
    public function getParent(): ?self
    {
        return $this->parent;
    }

    /**
     * @param MetadataItems|null $parent
     *
     * @return MetadataItems
     */
    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @param MetadataItems[] $child
     *
     * @return MetadataItems
     */
    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    /**
     * @param MetadataItems[] $child
     *
     * @return MetadataItems
     */
    public function removeChild(self $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }
}
