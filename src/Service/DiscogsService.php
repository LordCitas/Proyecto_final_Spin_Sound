<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class DiscogsService
{
    private const BASE_URL = 'https://api.discogs.com';

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $token,
    ) {}

    /**
     * Busca un release por artista + título y devuelve el primer resultado.
     * Si se pasa un $discogsId busca directamente ese release.
     */
    public function getReleaseData(string $artista, string $titulo, ?int $discogsId = null): ?array
    {
        try {
            if ($discogsId) {
                return $this->fetchRelease($discogsId);
            }

            // 1) Buscar en el catálogo
            $response = $this->client->request('GET', self::BASE_URL . '/database/search', [
                'headers' => $this->headers(),
                'query'   => [
                    'artist' => $artista,
                    'title'  => $titulo,
                    'type'   => 'release',
                    'per_page' => 1,
                ],
            ]);

            $data = $response->toArray();

            if (empty($data['results'])) {
                return null;
            }

            $releaseId = $data['results'][0]['id'];
            return $this->fetchRelease($releaseId);

        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Obtiene los detalles completos de un release por su ID de Discogs.
     */
    public function fetchRelease(int $id): ?array
    {
        try {
            $response = $this->client->request('GET', self::BASE_URL . '/releases/' . $id, [
                'headers' => $this->headers(),
            ]);

            return $response->toArray();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Busca lanzamientos por texto libre (para el buscador general).
     */
    public function search(string $query, int $perPage = 20, int $page = 1): array
    {
        try {
            $response = $this->client->request('GET', self::BASE_URL . '/database/search', [
                'headers' => $this->headers(),
                'query'   => [
                    'q'        => $query,
                    'type'     => 'release',
                    'per_page' => $perPage,
                    'page'     => $page,
                ],
            ]);

            return $response->toArray();
        } catch (\Throwable $e) {
            return ['results' => [], 'pagination' => []];
        }
    }

    private function headers(): array
    {
        $headers = [
            'User-Agent' => 'SpinSoundApp/1.0 +https://spinsound.com',
        ];

        if ($this->token) {
            $headers['Authorization'] = 'Discogs token=' . $this->token;
        }

        return $headers;
    }
}

