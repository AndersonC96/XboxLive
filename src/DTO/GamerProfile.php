<?php
declare(strict_types=1);

namespace Anderson\XboxLive\DTO;

class GamerProfile
{
    public function __construct(
        public readonly string $gamertag,
        public readonly string $gamerpic,
        public readonly string $gamerscore,
        public readonly string $tier,
        public readonly string $reputation,
        public readonly string $bio,
        public readonly string $location,
        public readonly ?string $xuid = null
    ) {}

    public static function fromXblArray(array $data, array $sessionUser): self
    {
        $settings = [];
        if (isset($data['settings'])) {
            foreach ($data['settings'] as $setting) {
                $settings[$setting['id']] = $setting['value'];
            }
        }

        return new self(
            gamertag: $settings['Gamertag'] ?? ($sessionUser['username'] ?? 'Usuário'),
            gamerpic: $settings['GameDisplayPicRaw'] ?? 'img/default_avatar.jpg',
            gamerscore: number_format((float)($settings['Gamerscore'] ?? 0), 0, ',', '.'),
            tier: $settings['AccountTier'] ?? 'Silver',
            reputation: $settings['XboxOneRep'] ?? 'Good',
            bio: $settings['Bio'] ?? 'Nenhuma bio disponível.',
            location: $settings['Location'] ?? 'Não informada',
            xuid: $data['id'] ?? ($data['hostId'] ?? null)
        );
    }
}
