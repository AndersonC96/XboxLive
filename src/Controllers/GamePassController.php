<?php
declare(strict_types=1);

namespace Anderson\XboxLive\Controllers;

use Anderson\XboxLive\Services\OpenXBLService;
use Anderson\XboxLive\Repositories\GameRepository;

class GamePassController extends BaseController
{
    private OpenXBLService $api;
    private GameRepository $repository;

    public function __construct()
    {
        $this->api = new OpenXBLService();
        $this->repository = new GameRepository();
    }

    private function getCatalog(string $tableName, string $title, string $view = 'todos_os_jogos'): void
    {
        $userProfile = $this->api->getProfileDTO();
        $page = (int)($_GET['page'] ?? 1);
        
        // Paginação REAL no Banco de Dados via Repository
        $result = $this->repository->getPagedIds($tableName, $page, 12);
        
        // Hidratação dos dados (DTOs) para os IDs da página atual
        $games = !empty($result['ids']) ? $this->api->getProductsDTO($result['ids']) : [];

        $this->render($view, [
            'title' => $title,
            'showNavbar' => true,
            'userProfile' => (array)$userProfile,
            'games' => $games,
            'currentPage' => $page,
            'totalPages' => $result['totalPages'],
            'syncUrl' => "/$view/sync"
        ], 'main');
    }

    public function allGames(): void { $this->getCatalog('gamepass_games', 'Xbox Game Pass'); }
    public function eaPlay(): void { $this->getCatalog('ea_gamepass', 'EA Play', 'ea_play'); }
    public function pcGamePass(): void { $this->getCatalog('pc_gamepass', 'PC Game Pass', 'gamepass_pc'); }
    public function noController(): void { $this->getCatalog('jogos_sem_controle', 'Sem Controle', 'jogos_sem_controle'); }
    public function addedRecently(): void { $this->getCatalog('gamepass_novos', 'Novos no Game Pass', 'gamepass_novos'); }
    public function comingSoon(): void { $this->getCatalog('gamepass_em_breve', 'Em Breve', 'gamepass_em_breve'); }
    public function leavingSoon(): void { $this->getCatalog('gamepass_saindo', 'Saindo em Breve', 'gamepass_saindo'); }

    // --- Sincronização em Lote ---
    public function sync(): void { $this->doSync('gamepass_games', $this->api->getGamePassAll(), '/todos_os_jogos'); }
    public function syncEAPlay(): void { $this->doSync('ea_gamepass', $this->api->getEAPlayAll(), '/ea_play'); }
    public function syncPCGamePass(): void { $this->doSync('pc_gamepass', $this->api->getPCGamePassAll(), '/gamepass_pc'); }
    public function syncNoController(): void { $this->doSync('jogos_sem_controle', $this->api->getNoControllerGames(), '/jogos_sem_controle'); }
    public function syncAddedRecently(): void { $this->doSync('gamepass_novos', $this->api->getNewGamePass(), '/adicionados_recentemente'); }
    public function syncComingSoon(): void { $this->doSync('gamepass_em_breve', $this->api->getComingSoon(), '/em_breve'); }
    public function syncLeavingSoon(): void { $this->doSync('gamepass_saindo', $this->api->getLeavingSoon(), '/saindo_em_breve'); }

    private function doSync(string $table, array $apiResponse, string $redirect): void
    {
        $ids = array_filter(array_column($apiResponse, 'id'));
        $added = $this->repository->syncIds($table, $ids);
        $this->redirect($redirect . '?synced=' . $added);
    }

    public function gameDetails(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) $this->redirect('/todos_os_jogos');

        $userProfile = $this->api->getProfileDTO();
        $product = $this->api->getProductDTO($id);

        $this->render('jogo', [
            'title' => ($product->title ?? 'Detalhes') . ' - Xbox Live',
            'showNavbar' => true,
            'userProfile' => (array)$userProfile,
            'product' => $product,
            'productId' => $id
        ], 'main');
    }
}
