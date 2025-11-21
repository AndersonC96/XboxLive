<?php
    session_start();
    include('../includes/header.php');
    include('../includes/navbar.php');
    require_once '../config/api.php';
    $endpoint = "account";
    $response = openXBLRequest($endpoint);
    $profileUsers = (is_array($response) && isset($response['profileUsers'][0])) ? $response['profileUsers'][0] : null;
    $gamertag = null;
    if ($profileUsers && isset($profileUsers['settings']) && is_array($profileUsers['settings'])) {
        foreach ($profileUsers['settings'] as $setting) {
            if (isset($setting['id']) && $setting['id'] === 'Gamertag') {
                $gamertag = $setting['value'] ?? null;
                break;
            }
        }
    }
?>
<div class="container mx-auto mt-10 flex justify-center">
    <div class="bg-white/30 backdrop-blur-lg p-8 rounded-lg shadow-lg border border-white/20 max-w-lg text-center">
        <h1 class="text-4xl font-bold mb-4 text-black">Bem-vindo, <span class="text-green-500"><?php echo htmlspecialchars($gamertag); ?></span>!</h1>
        <ul class="text-left text-black space-y-3">
            <li><span class="text-green-500 font-bold">Gamerscore:</span>
                <?php
                $settings = $profileUsers['settings'] ?? [];
                if (is_array($settings)) {
                    foreach ($settings as $setting) {
                        if (isset($setting['id']) && $setting['id'] === 'Gamerscore') {
                            $gamerscore = $setting['value'] ?? null;
                            if (is_numeric($gamerscore)) {
                                if ($gamerscore >= 1000) {
                                    echo number_format($gamerscore, 0, '', '.') . ' ';
                                } else {
                                    echo $gamerscore . ' ';
                                }
                                echo '<img src="../img/gs.png" alt="Gamerscore Icon" class="inline w-5 h-5">';
                            } else {
                                echo '-';
                            }
                            break;
                        }
                    }
                }
                ?>
            </li>
            <li><span class="text-green-500 font-bold">Conta:</span>
                <?php
                $settings = $profileUsers['settings'] ?? [];
                if (is_array($settings)) {
                    foreach ($settings as $setting) {
                        if (isset($setting['id']) && $setting['id'] === 'AccountTier') {
                            echo htmlspecialchars($setting['value'] ?? '-');
                            break;
                        }
                    }
                }
                ?>
            </li>
            <li><span class="text-green-500 font-bold">Reputação:</span>
                <?php
                $settings = $profileUsers['settings'] ?? [];
                if (is_array($settings)) {
                    foreach ($settings as $setting) {
                        if (isset($setting['id']) && $setting['id'] === 'XboxOneRep') {
                            echo htmlspecialchars($setting['value'] ?? '-');
                            break;
                        }
                    }
                }
                ?>
            </li>
            <li><span class="text-green-500 font-bold">Bio:</span>
                <?php
                $settings = $profileUsers['settings'] ?? [];
                if (is_array($settings)) {
                    foreach ($settings as $setting) {
                        if (isset($setting['id']) && $setting['id'] === 'Bio') {
                            echo htmlspecialchars($setting['value'] ?? '-');
                            break;
                        }
                    }
                }
                ?>
            </li>
        </ul>
    </div>
</div>
<br>
<?php include('../includes/footer.php'); ?>