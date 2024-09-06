<?php
    session_start();
    include('../includes/header.php');
    include('../includes/navbar.php');
    require_once '../config/api.php';
    require_once '../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../config');
    $dotenv->load();
    function generateGamertag() {
        $url = 'https://xbl.io/api/v2/generate/gamertag';
        $api_key = $_ENV['OPENXBL_API_KEY'];
        $data = [
            "algorithm" => 1,
            "count" => 3,
            "seed" => "",
            "locale" => "pt-br"
        ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-authorization: ' . $api_key,
            'Content-Type: application/json',
            'accept: */*'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
        }
        curl_close($ch);
        if (isset($error_msg)) {
            return ['error' => $error_msg];
        }
        return json_decode($response, true);
    }
    $response = generateGamertag();
    if (isset($response['Gamertags'])) {
        $generatedGamertags = $response['Gamertags'];
    } else {
        $errorMessage = $response['error'] ?? 'Erro ao gerar Gamertag';
    }
?>
<div class="container mx-auto mt-10">
    <h1 class="text-3xl font-bold mb-6">GGamertags Geradas</h1>
    <?php if (isset($generatedGamertags)): ?>
        <div class="bg-gray-800 p-4 rounded-lg shadow-md">
            <ul>
                <?php foreach ($generatedGamertags as $gamertag): ?>
                    <li><?php echo $gamertag; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else: ?>
        <p><?php echo $errorMessage; ?></p>
    <?php endif; ?>
</div>
<?php include('../includes/footer.php'); ?>