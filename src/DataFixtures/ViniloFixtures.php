<?php

namespace App\DataFixtures;

use App\Entity\Artista;
use App\Entity\Genero;
use App\Entity\Vinilo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ViniloFixtures extends Fixture
{
    private const VINILOS = [
        [
            'titulo'      => 'KPop Demon Hunters (Soundtrack From The Netflix Film)',
            'artista'     => 'Various Artists',
            'nacionalidad'=> 'Internacional',
            'genero'      => 'Pop',
            'año'         => 2025,
            'precio'      => 29.99,
            'stock'       => 15,
            'discogsId'   => 35255740,
        ],
        [
            'titulo'      => 'Abbey Road',
            'artista'     => 'The Beatles',
            'nacionalidad'=> 'Reino Unido',
            'genero'      => 'Rock',
            'año'         => 1969,
            'precio'      => 34.99,
            'stock'       => 10,
            'discogsId'   => 666397,
        ],
        [
            'titulo'      => 'Thriller',
            'artista'     => 'Michael Jackson',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Funk / Soul',
            'año'         => 1982,
            'precio'      => 24.99,
            'stock'       => 12,
            'discogsId'   => 2911293,
        ],
        [
            'titulo'      => 'The Dark Side Of The Moon',
            'artista'     => 'Pink Floyd',
            'nacionalidad'=> 'Reino Unido',
            'genero'      => 'Rock',
            'año'         => 1973,
            'precio'      => 39.99,
            'stock'       => 8,
            'discogsId'   => 9287809,
        ],
        [
            'titulo'      => 'Nevermind',
            'artista'     => 'Nirvana',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Rock',
            'año'         => 1991,
            'precio'      => 27.99,
            'stock'       => 20,
            'discogsId'   => 7097051,
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::VINILOS as $data) {
            // --- Género (reutilizar si ya existe) ---
            $genero = $manager->getRepository(Genero::class)->findOneBy(['nombre' => $data['genero']]);
            if (!$genero) {
                $genero = new Genero();
                $genero->setNombre($data['genero']);
                $manager->persist($genero);
                $manager->flush(); // flush para poder reusar en siguientes iteraciones
            }

            // --- Artista (reutilizar si ya existe) ---
            $artista = $manager->getRepository(Artista::class)->findOneBy(['nombre' => $data['artista']]);
            if (!$artista) {
                $artista = new Artista();
                $artista->setNombre($data['artista']);
                $artista->setNacionalidad($data['nacionalidad']);
                $manager->persist($artista);
            }

            // --- Vinilo (evitar duplicados) ---
            $existente = $manager->getRepository(Vinilo::class)->findOneBy(['titulo' => $data['titulo']]);
            if ($existente) {
                echo "⏭️  Ya existe: {$data['titulo']}, saltando...\n";
                continue;
            }

            $vinilo = new Vinilo();
            $vinilo->setTitulo($data['titulo']);
            $vinilo->setFechaLanzamiento(new \DateTime("{$data['año']}-01-01"));
            $vinilo->setPrecio($data['precio']);
            $vinilo->setStock($data['stock']);
            $vinilo->setDiscogsId($data['discogsId']);

            $artista->addVinilo($vinilo);
            $genero->addGeneroVinilo($vinilo);

            $manager->persist($vinilo);
            echo "✅  Añadido: {$data['titulo']} ({$data['año']})\n";
        }

        $manager->flush();
        echo "🎵  Fixtures cargados.\n";
    }
}
