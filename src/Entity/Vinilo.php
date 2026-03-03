<?php

namespace App\Entity;

use App\Repository\ViniloRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ViniloRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Vinilo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titulo = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $fecha_lanzamiento = null;

    #[ORM\Column]
    private ?float $precio = null;

    #[ORM\Column]
    private ?int $stock = null;

    #[ORM\Column(nullable: true)]
    private ?int $discogs_id = null;

    /**
     * @var Collection<int, DetallePedido>
     */
    #[ORM\OneToMany(targetEntity: DetallePedido::class, mappedBy: 'vinilo')]
    private Collection $detallePedidos;

    /**
     * @var Collection<int, Artista>
     */
    #[ORM\ManyToMany(targetEntity: Artista::class, mappedBy: 'vinilos')]
    private Collection $artistas;

    /**
     * @var Collection<int, Genero>
     */
    #[ORM\ManyToMany(targetEntity: Genero::class, mappedBy: 'genero_vinilo')]
    private Collection $generos;

    /**
     * @var Collection<int, DetalleCarrito>
     */
    #[ORM\OneToMany(targetEntity: DetalleCarrito::class, mappedBy: 'vinilo')]
    private Collection $detalleCarritos;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagen = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $esNovedad = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $modifiedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    public function __construct()
    {
        $this->detallePedidos = new ArrayCollection();
        $this->artistas = new ArrayCollection();
        $this->generos = new ArrayCollection();
        $this->detalleCarritos = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->modifiedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getFechaLanzamiento(): ?\DateTime
    {
        return $this->fecha_lanzamiento;
    }

    public function setFechaLanzamiento(?\DateTime $fecha_lanzamiento): static
    {
        $this->fecha_lanzamiento = $fecha_lanzamiento;

        return $this;
    }

    public function getPrecio(): ?float
    {
        return $this->precio;
    }

    public function setPrecio(float $precio): static
    {
        $this->precio = $precio;

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

    public function getDiscogsId(): ?int
    {
        return $this->discogs_id;
    }

    public function setDiscogsId(?int $discogs_id): static
    {
        $this->discogs_id = $discogs_id;

        return $this;
    }

    /**
     * @return Collection<int, DetallePedido>
     */
    public function getDetallePedidos(): Collection
    {
        return $this->detallePedidos;
    }

    public function addDetallePedido(DetallePedido $detallePedido): static
    {
        if (!$this->detallePedidos->contains($detallePedido)) {
            $this->detallePedidos->add($detallePedido);
            $detallePedido->setPedido($this);
        }

        return $this;
    }

    public function removeDetallePedido(DetallePedido $detallePedido): static
    {
        if ($this->detallePedidos->removeElement($detallePedido)) {
            // set the owning side to null (unless already changed)
            if ($detallePedido->getPedido() === $this) {
                $detallePedido->setPedido(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Artista>
     */
    public function getArtistas(): Collection
    {
        return $this->artistas;
    }

    public function addArtista(Artista $artista): static
    {
        if (!$this->artistas->contains($artista)) {
            $this->artistas->add($artista);
            $artista->addVinilo($this);
        }

        return $this;
    }

    public function removeArtista(Artista $artista): static
    {
        if ($this->artistas->removeElement($artista)) {
            $artista->removeVinilo($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Genero>
     */
    public function getGeneros(): Collection
    {
        return $this->generos;
    }

    public function addGenero(Genero $genero): static
    {
        if (!$this->generos->contains($genero)) {
            $this->generos->add($genero);
            $genero->addGeneroVinilo($this);
        }

        return $this;
    }

    public function removeGenero(Genero $genero): static
    {
        if ($this->generos->removeElement($genero)) {
            $genero->removeGeneroVinilo($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, DetalleCarrito>
     */
    public function getDetalleCarritos(): Collection
    {
        return $this->detalleCarritos;
    }

    public function addDetalleCarrito(DetalleCarrito $detalleCarrito): static
    {
        if (!$this->detalleCarritos->contains($detalleCarrito)) {
            $this->detalleCarritos->add($detalleCarrito);
            $detalleCarrito->setVinilo($this);
        }

        return $this;
    }

    public function removeDetalleCarrito(DetalleCarrito $detalleCarrito): static
    {
        if ($this->detalleCarritos->removeElement($detalleCarrito)) {
            // set the owning side to null (unless already changed)
            if ($detalleCarrito->getVinilo() === $this) {
                $detalleCarrito->setVinilo(null);
            }
        }

        return $this;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): static
    {
        $this->imagen = $imagen;

        return $this;
    }

    public function isEsNovedad(): bool
    {
        return $this->esNovedad;
    }

    public function setEsNovedad(bool $esNovedad): static
    {
        $this->esNovedad = $esNovedad;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeImmutable
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(?\DateTimeImmutable $modifiedAt): static
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
