<?php
    session_start();
    include('../includes/header.php');
    include('../includes/navbar.php');
    require_once '../config/api.php';
    $endpoint = "account";
    $response = openXBLRequest($endpoint);
    $profileUsers = $response['profileUsers'][0] ?? null;
    $gamertag = null;
    if ($profileUsers) {
        foreach ($profileUsers['settings'] as $setting) {
            if ($setting['id'] === 'Gamertag') {
                $gamertag = $setting['value'];
                break;
            }
        }
    }
?>
<div class="container mx-auto mt-10">
    <h1 class="text-3xl font-bold">Bem-vindo, <?php echo htmlspecialchars($gamertag); ?>!</h1>
    <p class="text-gray-500">Aqui estão os detalhes do seu perfil Xbox Live.</p>
    <ul class="bg-gray-800 p-4 rounded-lg shadow-md">
        <li><strong>Gamerscore:</strong>
            <?php
                foreach ($profileUsers['settings'] as $setting) {
                    if ($setting['id'] === 'Gamerscore') {
                        echo htmlspecialchars($setting['value']);
                        break;
                    }
                }
            ?>
        </li>
        <li><strong>Conta:</strong>
            <?php
                foreach ($profileUsers['settings'] as $setting) {
                    if ($setting['id'] === 'AccountTier') {
                        echo htmlspecialchars($setting['value']);
                        break;
                    }
                }
            ?>
        </li>
        <li><strong>Reputação:</strong>
            <?php
                foreach ($profileUsers['settings'] as $setting) {
                    if ($setting['id'] === 'XboxOneRep') {
                        echo htmlspecialchars($setting['value']);
                        break;
                    }
                }
            ?>
        </li>
        <li><strong>Bio:</strong>
            <?php
                foreach ($profileUsers['settings'] as $setting) {
                    if ($setting['id'] === 'Bio') {
                        echo htmlspecialchars($setting['value']);
                        break;
                    }
                }
            ?>
        </li>
    </ul>
</div>
<?php include('../includes/footer.php'); ?>