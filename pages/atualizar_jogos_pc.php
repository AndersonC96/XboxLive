<?php
    require '../config/db.php';
    require_once '../config/api.php';
    function adicionarJogosNoBanco() {
        global $pdo;
        $response = openXBLRequest('gamepass/pc');
        if (isset($response) && is_array($response)) {
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM pc_gamepass WHERE game_id = :game_id");
            $insertStmt = $pdo->prepare("INSERT INTO pc_gamepass (game_id) VALUES (:game_id)");
            foreach ($response as $game) {
                if (isset($game['id']) && !empty($game['id'])) {
                    $gameId = $game['id'];
                    $checkStmt->execute(['game_id' => $gameId]);
                    $exists = $checkStmt->fetchColumn();
                    if (!$exists) {
                        $insertStmt->execute(['game_id' => $gameId]);
                        echo "Jogo com ID $gameId adicionado ao banco de dados.<br>";
                    } else {
                        echo "Jogo com ID $gameId já está cadastrado no banco de dados.<br>";
                    }
                } else {
                    echo "ID não encontrado para o jogo.<br>";
                }
            }
        } else {
            echo "Nenhum jogo encontrado no Game Pass.";
        }
    }
    adicionarJogosNoBanco();