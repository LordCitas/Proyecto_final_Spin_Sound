<?php

namespace App\DataFixtures;

use App\Entity\Artista;
use App\Entity\Genero;
use App\Entity\Vinilo;
use App\Service\DiscogsService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ViniloFixtures extends Fixture
{
    public function __construct(
        private readonly DiscogsService $discogsService
    ) {}

    private const VINILOS = [
        // ── ROCK (5) ──────────────────────────────────────────────
        [
            'titulo'      => 'Abbey Road',
            'artista'     => 'The Beatles',
            'nacionalidad'=> 'Reino Unido',
            'genero'      => 'Rock',
            'año'         => 1969,
            'precio'      => 34.99,
            'stock'       => 10,
            'discogsId'   => 24047,
            'type'        => 'masters',
        ],
        [
            'titulo'      => 'The Dark Side Of The Moon',
            'artista'     => 'Pink Floyd',
            'nacionalidad'=> 'Reino Unido',
            'genero'      => 'Rock',
            'año'         => 1973,
            'precio'      => 39.99,
            'stock'       => 8,
            'discogsId'   => 10362,
            'type'        => 'masters',
        ],
        [
            'titulo'      => 'Nevermind',
            'artista'     => 'Nirvana',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Rock',
            'año'         => 1991,
            'precio'      => 27.99,
            'stock'       => 20,
            'discogsId'   => 13814,
            'type'        => 'masters',
        ],
        [
            'titulo'      => 'Back In Black',
            'artista'     => 'AC/DC',
            'nacionalidad'=> 'Australia',
            'genero'      => 'Rock',
            'año'         => 1980,
            'precio'      => 29.99,
            'stock'       => 14,
            'discogsId'   => 8471,
            'type'        => 'masters',
        ],
        [
            'titulo'      => 'Led Zeppelin IV',
            'artista'     => 'Led Zeppelin',
            'nacionalidad'=> 'Reino Unido',
            'genero'      => 'Rock',
            'año'         => 1971,
            'precio'      => 32.99,
            'stock'       => 9,
            'discogsId'   => 1015465,
            'type'        => 'masters',
        ],

        // ── POP (5) ───────────────────────────────────────────────
        [
            'titulo'      => 'KPop Demon Hunters (Soundtrack From The Netflix Film)',
            'artista'     => 'Various Artists',
            'nacionalidad'=> 'Internacional',
            'genero'      => 'Pop',
            'año'         => 2025,
            'precio'      => 29.99,
            'stock'       => 15,
            'discogsId'   => 3925902,
            'type'        => 'masters',
        ],
        [
            'titulo'      => 'Bad',
            'artista'     => 'Michael Jackson',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Pop',
            'año'         => 1987,
            'precio'      => 26.99,
            'stock'       => 12,
            'discogsId'   => 8517,
            'type'        => 'masters',
        ],
        [
            'titulo'      => '1989',
            'artista'     => 'Taylor Swift',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Pop',
            'año'         => 2014,
            'precio'      => 31.99,
            'stock'       => 18,
            'discogsId'   => 750386,
            'type'        => 'masters',
        ],
        [
            'titulo'      => 'Lemonade',
            'artista'     => 'Beyonce',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Pop',
            'año'         => 2016,
            'precio'      => 34.99,
            'stock'       => 10,
            'discogsId'   => 992029,
            'type'        => 'masters',
        ],
        [
            'titulo'      => 'Future Nostalgia',
            'artista'     => 'Dua Lipa',
            'nacionalidad'=> 'Reino Unido',
            'genero'      => 'Pop',
            'año'         => 2020,
            'precio'      => 28.99,
            'stock'       => 22,
            'discogsId'   => 1705638,
            'type'        => 'masters',
        ],

        // ── JAZZ (5) ──────────────────────────────────────────────
        [
            'titulo'      => 'Kind Of Blue',
            'artista'     => 'Miles Davis',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Jazz',
            'año'         => 1959,
            'precio'      => 36.99,
            'stock'       => 7,
            'discogsId'   => 5460,
            'type'        => 'masters',
        ],
        [
            'titulo'      => 'A Love Supreme',
            'artista'     => 'John Coltrane',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Jazz',
            'año'         => 1965,
            'precio'      => 33.99,
            'stock'       => 6,
            'discogsId'   => 32287,
            'type'        => 'masters',
        ],
        [
            'titulo'      => 'Time Out',
            'artista'     => 'The Dave Brubeck Quartet',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Jazz',
            'año'         => 1959,
            'precio'      => 29.99,
            'stock'       => 9,
            'discogsId'   => 34081,
            'type'        => 'masters',
        ],
        [
            'titulo'      => 'Getz / Gilberto',
            'artista'     => 'Stan Getz and Joao Gilberto',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Jazz',
            'año'         => 1964,
            'precio'      => 27.99,
            'stock'       => 11,
            'discogsId'   => 85178,
            'type'        => 'masters',
        ],
        [
            'titulo'      => 'Mingus Ah Um',
            'artista'     => 'Charles Mingus',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Jazz',
            'año'         => 1959,
            'precio'      => 31.99,
            'stock'       => 8,
            'discogsId'   => 65014,
            'type'        => 'masters',
        ],

        // ── CLÁSICA (5) ───────────────────────────────────────────
        [
            'titulo'      => 'Beethoven: Symphony No. 9',
            'artista'     => 'Herbert von Karajan',
            'nacionalidad'=> 'Austria',
            'genero'      => 'Clasica',
            'año'         => 1963,
            'precio'      => 38.99,
            'stock'       => 5,
            'discogsId'   => 276504,
            'type'        => 'masters',
        ],
        [
            'titulo'      => 'Bach: Goldberg Variations',
            'artista'     => 'Glenn Gould',
            'nacionalidad'=> 'Canada',
            'genero'      => 'Clasica',
            'año'         => 1981,
            'precio'      => 35.99,
            'stock'       => 6,
            'discogsId'   => 261784,
            'type'        => 'masters',
        ],
        [
            'titulo'      => 'Mozart: Requiem',
            'artista'     => 'Karl Bohm',
            'nacionalidad'=> 'Austria',
            'genero'      => 'Clasica',
            'año'         => 1971,
            'precio'      => 32.99,
            'stock'       => 7,
            'discogsId'   => 233186,
            'type'        => 'masters',
        ],
        [
            'titulo'      => 'Vivaldi: The Four Seasons',
            'artista'     => 'Nigel Kennedy',
            'nacionalidad'=> 'Reino Unido',
            'genero'      => 'Clasica',
            'año'         => 1989,
            'precio'      => 29.99,
            'stock'       => 10,
            'discogsId'   => 1141041,
            'type'        => 'masters',
        ],
        [
            'titulo'      => 'Debussy: Clair de Lune & Other Piano Works',
            'artista'     => 'Walter Gieseking',
            'nacionalidad'=> 'Alemania',
            'genero'      => 'Clasica',
            'año'         => 1954,
            'precio'      => 27.99,
            'stock'       => 8,
            'discogsId'   => 3392098,
            'type'        => 'masters',
        ],

        // ── FUNK / SOUL (5) ───────────────────────────────────────
        [
            'titulo'      => 'Thriller',
            'artista'     => 'Michael Jackson',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Funk / Soul',
            'año'         => 1982,
            'precio'      => 24.99,
            'stock'       => 12,
            'discogsId'   => 8883,
            'type'        => 'masters',
        ],
        [
            'titulo'      => "What's Going On",
            'artista'     => 'Marvin Gaye',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Funk / Soul',
            'año'         => 1971,
            'precio'      => 30.99,
            'stock'       => 9,
            'discogsId'   => 66631,
            'type'        => 'masters',
        ],
        [
            'titulo'      => 'Off The Wall',
            'artista'     => 'Michael Jackson',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Funk / Soul',
            'año'         => 1979,
            'precio'      => 22.99,
            'stock'       => 14,
            'discogsId'   => 435524,
            'type'        => 'masters',
        ],
        [
            'titulo'      => 'I Never Loved A Man The Way I Love You',
            'artista'     => 'Aretha Franklin',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Funk / Soul',
            'año'         => 1967,
            'precio'      => 28.99,
            'stock'       => 7,
            'discogsId'   => 122933,
            'type'        => 'masters',
        ],
        [
            'titulo'      => 'Innervisions',
            'artista'     => 'Stevie Wonder',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Funk / Soul',
            'año'         => 1973,
            'precio'      => 26.99,
            'stock'       => 11,
            'discogsId'   => 86466,
            'type'        => 'masters',
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
                $manager->flush();
            }

            // --- Artista (reutilizar si ya existe) ---
            $artista = $manager->getRepository(Artista::class)->findOneBy(['nombre' => $data['artista']]);
            if (!$artista) {
                $artista = new Artista();
                $artista->setNombre($data['artista']);
                $artista->setNacionalidad($data['nacionalidad']);
                $manager->persist($artista);
                $manager->flush();
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

            // Obtener imagen desde Discogs
            if ($data['discogsId']) {
                $releaseData = $this->discogsService->fetchRelease($data['discogsId']);
                if (!$releaseData) {
                    echo "⚠️  Discogs no devolvió datos para releaseId {$data['discogsId']} ({$data['titulo']}).\n";
                } else {
                    $imageUrl = $this->discogsService->getImageUrl($releaseData);

                    if ($imageUrl) {
                        $vinilo->setImagen($imageUrl);
                        echo "🖼️  Imagen obtenida para: {$data['titulo']} ({$imageUrl})\n";
                    } else {
                        echo "⚠️  No se encontró imagen en los datos de Discogs para releaseId {$data['discogsId']} ({$data['titulo']}).\n";
                    }
                }

                // Pausa corta para evitar alcanzar límites de la API
                usleep(150000); // 150ms
            }

            $artista->addVinilo($vinilo);
            $genero->addGeneroVinilo($vinilo);

            $manager->persist($vinilo);
            echo "✅  Añadido: {$data['titulo']} ({$data['año']})\n";
        }

        $manager->flush();
        echo "🎵  Fixtures cargados.\n";
    }
}
