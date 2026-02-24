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
        // ── ROCK (5) ──────────────────────────────────────────────
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
        [
            'titulo'      => 'Back In Black',
            'artista'     => 'AC/DC',
            'nacionalidad'=> 'Australia',
            'genero'      => 'Rock',
            'año'         => 1980,
            'precio'      => 29.99,
            'stock'       => 14,
            'discogsId'   => 3771485,
        ],
        [
            'titulo'      => 'Led Zeppelin IV',
            'artista'     => 'Led Zeppelin',
            'nacionalidad'=> 'Reino Unido',
            'genero'      => 'Rock',
            'año'         => 1971,
            'precio'      => 32.99,
            'stock'       => 9,
            'discogsId'   => 2247887,
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
            'discogsId'   => 35255740,
        ],
        [
            'titulo'      => 'Bad',
            'artista'     => 'Michael Jackson',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Pop',
            'año'         => 1987,
            'precio'      => 26.99,
            'stock'       => 12,
            'discogsId'   => 2506673,
        ],
        [
            'titulo'      => '1989',
            'artista'     => 'Taylor Swift',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Pop',
            'año'         => 2014,
            'precio'      => 31.99,
            'stock'       => 18,
            'discogsId'   => 6275798,
        ],
        [
            'titulo'      => 'Lemonade',
            'artista'     => 'Beyonce',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Pop',
            'año'         => 2016,
            'precio'      => 34.99,
            'stock'       => 10,
            'discogsId'   => 8559537,
        ],
        [
            'titulo'      => 'Future Nostalgia',
            'artista'     => 'Dua Lipa',
            'nacionalidad'=> 'Reino Unido',
            'genero'      => 'Pop',
            'año'         => 2020,
            'precio'      => 28.99,
            'stock'       => 22,
            'discogsId'   => 16015688,
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
            'discogsId'   => 1672525,
        ],
        [
            'titulo'      => 'A Love Supreme',
            'artista'     => 'John Coltrane',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Jazz',
            'año'         => 1965,
            'precio'      => 33.99,
            'stock'       => 6,
            'discogsId'   => 608814,
        ],
        [
            'titulo'      => 'Time Out',
            'artista'     => 'The Dave Brubeck Quartet',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Jazz',
            'año'         => 1959,
            'precio'      => 29.99,
            'stock'       => 9,
            'discogsId'   => 725493,
        ],
        [
            'titulo'      => 'Getz / Gilberto',
            'artista'     => 'Stan Getz and Joao Gilberto',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Jazz',
            'año'         => 1964,
            'precio'      => 27.99,
            'stock'       => 11,
            'discogsId'   => 1285696,
        ],
        [
            'titulo'      => 'Mingus Ah Um',
            'artista'     => 'Charles Mingus',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Jazz',
            'año'         => 1959,
            'precio'      => 31.99,
            'stock'       => 8,
            'discogsId'   => 823820,
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
            'discogsId'   => 1259357,
        ],
        [
            'titulo'      => 'Bach: Goldberg Variations',
            'artista'     => 'Glenn Gould',
            'nacionalidad'=> 'Canada',
            'genero'      => 'Clasica',
            'año'         => 1981,
            'precio'      => 35.99,
            'stock'       => 6,
            'discogsId'   => 2011834,
        ],
        [
            'titulo'      => 'Mozart: Requiem',
            'artista'     => 'Karl Bohm',
            'nacionalidad'=> 'Austria',
            'genero'      => 'Clasica',
            'año'         => 1971,
            'precio'      => 32.99,
            'stock'       => 7,
            'discogsId'   => 1545202,
        ],
        [
            'titulo'      => 'Vivaldi: The Four Seasons',
            'artista'     => 'Nigel Kennedy',
            'nacionalidad'=> 'Reino Unido',
            'genero'      => 'Clasica',
            'año'         => 1989,
            'precio'      => 29.99,
            'stock'       => 10,
            'discogsId'   => 2218509,
        ],
        [
            'titulo'      => 'Debussy: Clair de Lune & Other Piano Works',
            'artista'     => 'Walter Gieseking',
            'nacionalidad'=> 'Alemania',
            'genero'      => 'Clasica',
            'año'         => 1954,
            'precio'      => 27.99,
            'stock'       => 8,
            'discogsId'   => 3421876,
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
            'discogsId'   => 2911293,
        ],
        [
            'titulo'      => "What's Going On",
            'artista'     => 'Marvin Gaye',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Funk / Soul',
            'año'         => 1971,
            'precio'      => 30.99,
            'stock'       => 9,
            'discogsId'   => 580554,
        ],
        [
            'titulo'      => 'Off The Wall',
            'artista'     => 'Michael Jackson',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Funk / Soul',
            'año'         => 1979,
            'precio'      => 22.99,
            'stock'       => 14,
            'discogsId'   => 1506510,
        ],
        [
            'titulo'      => 'I Never Loved A Man The Way I Love You',
            'artista'     => 'Aretha Franklin',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Funk / Soul',
            'año'         => 1967,
            'precio'      => 28.99,
            'stock'       => 7,
            'discogsId'   => 917862,
        ],
        [
            'titulo'      => 'Innervisions',
            'artista'     => 'Stevie Wonder',
            'nacionalidad'=> 'Estados Unidos',
            'genero'      => 'Funk / Soul',
            'año'         => 1973,
            'precio'      => 26.99,
            'stock'       => 11,
            'discogsId'   => 382766,
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

            $artista->addVinilo($vinilo);
            $genero->addGeneroVinilo($vinilo);

            $manager->persist($vinilo);
            echo "✅  Añadido: {$data['titulo']} ({$data['año']})\n";
        }

        $manager->flush();
        echo "🎵  Fixtures cargados.\n";
    }
}
