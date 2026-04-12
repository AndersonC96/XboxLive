<?php
declare(strict_types=1);

namespace Anderson\XboxLive\DTO;

class GameProduct
{
    public function __construct(
        public readonly string $productId,
        public readonly string $title,
        public readonly string $description,
        public readonly string $developer,
        public readonly string $publisher,
        public readonly string $boxArt,
        public readonly string $heroImage,
        public readonly array $screenshots = [],
        public readonly string $category = 'Jogo'
    ) {}

    public static function fromXblArray(array $data): self
    {
        $props = $data['LocalizedProperties'][0] ?? [];
        $images = $props['Images'] ?? [];
        
        $boxArt = 'img/default_game.jpg';
        $heroImage = '';
        $screenshots = [];

        foreach ($images as $img) {
            $uri = str_starts_with($img['Uri'], '//') ? 'https:' . $img['Uri'] : $img['Uri'];
            
            if ($img['ImagePurpose'] === 'BoxArt') $boxArt = $uri;
            if (in_array($img['ImagePurpose'], ['BrandedKeyArt', 'SuperHeroArt', 'Poster'])) $heroImage = $uri;
            if ($img['ImagePurpose'] === 'Screenshot') $screenshots[] = $uri;
        }

        return new self(
            productId: $data['ProductId'] ?? '',
            title: $props['ProductTitle'] ?? 'Sem Título',
            description: $props['ProductDescription'] ?? 'Sem descrição.',
            developer: $props['DeveloperName'] ?? 'Xbox Studio',
            publisher: $props['PublisherName'] ?? 'Xbox',
            boxArt: $boxArt,
            heroImage: $heroImage ?: $boxArt,
            screenshots: $screenshots,
            category: $data['Properties']['Category'] ?? 'Digital'
        );
    }
}
