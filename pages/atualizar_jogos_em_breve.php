<?php
require_once __DIR__ . '/../vendor/autoload.php';
\Anderson\XboxLive\Core\Bootstrap::run();
require '../config/db.php';
require_once '../config/api.php';

/**
 * Atualiza o banco de dados com os próximos lançamentos do Game Pass.
 * Baseado no padrão oficial de sincronização do Dashboard.
 */
function adicionarJogosEmBreve() {
    global $pdo;

    // Tenta primeiro o endpoint de Game Pass (mais comum para listas de IDs)
    $response = openXBLRequest('gamepass/coming-soon');

    // Fallback para marketplace se o primeiro falhar
    if (!$response) {
        $response = openXBLRequest('marketplace/coming-soon');
    }

    if (isset($response) && is_array($response)) {
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM coming_soon WHERE game_id = :game_id");
        $insertStmt = $pdo->prepare("INSERT INTO coming_soon (game_id) VALUES (:game_id)");
        
        $countSuccess = 0;
        $countExists = 0;

        foreach ($response as $game) {
            // A API pode retornar um array de objetos com 'id' ou apenas os IDs em alguns casos
            $gameId = $game['id'] ?? ($game['ProductId'] ?? (is_string($game) ? $game : null));

            if ($gameId) {
                $checkStmt->execute(['game_id' => $gameId]);
                $exists = $checkStmt->fetchColumn();

                if (!$exists) {
                    $insertStmt->execute(['game_id' => $gameId]);
                    echo "<div style='color: #107c10; font-family: sans-serif; font-size: 14px;'>[NOVO] Jogo $gameId adicionado ao calendário.</div>";
                    $countSuccess++;
                } else {
                    echo "<div style='color: #666; font-family: sans-serif; font-size: 14px;'>[OK] Jogo $gameId já monitorado.</div>";
                    $countExists++;
                }
            }
        }

        echo "<hr><div style='font-weight: bold; font-family: sans-serif;'>Sincronização Concluída: $countSuccess novos, $countExists já existentes.</div>";
    } else {
        echo "<div style='color: #f44336; font-weight: bold; font-family: sans-serif;'>Nenhum lançamento futuro encontrado na API no momento.</div>";
    }
}

adicionarJogosEmBreve();
