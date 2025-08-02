<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $price = null;

    /**
     * @var Collection<int, SubCategory>
     */
    #[ORM\ManyToMany(targetEntity: SubCategory::class, inversedBy: 'products')]
    private Collection $sub_category;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column]
    private ?int $stock = null;

    /**
     * @var Collection<int, ProductHistory>
     */
    #[ORM\OneToMany(targetEntity: ProductHistory::class, mappedBy: 'product')]
    private Collection $productHistories;

    public function __construct()
    {
        $this->sub_category = new ArrayCollection();
        $this->productHistories = new ArrayCollection();
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

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, SubCategory>
     */
    public function getSubCategory(): Collection
    {
        return $this->sub_category;
    }

    public function addSubCategory(SubCategory $subCategory): static
    {
        if (!$this->sub_category->contains($subCategory)) {
            $this->sub_category->add($subCategory);
        }

        return $this;
    }

    public function removeSubCategory(SubCategory $subCategory): static
    {
        $this->sub_category->removeElement($subCategory);

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * @return Collection<int, ProductHistory>
     */
    public function getProductHistories(): Collection
    {
        return $this->productHistories;
    }

    public function addProductHistory(ProductHistory $productHistory): static
    {
        if (!$this->productHistories->contains($productHistory)) {
            $this->productHistories->add($productHistory);
            $productHistory->setProduct($this);
        }

        return $this;
    }

    public function removeProductHistory(ProductHistory $productHistory): static
    {
        if ($this->productHistories->removeElement($productHistory)) {
            // set the owning side to null (unless already changed)
            if ($productHistory->getProduct() === $this) {
                $productHistory->setProduct(null);
            }
        }

        return $this;
    }
}