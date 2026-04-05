<?php

namespace Anderson\XboxLive\Utils;

class ViewHelper
{
    /**
     * Renderiza o HTML da paginação com lógica de sliding window (elipses).
     * 
     * @param int $currentPage Página atual
     * @param int $totalPages Total de páginas
     * @param string $baseUrl URL base (com parâmetros existentes)
     * @return string HTML da paginação
     */
    public static function renderPagination($currentPage, $totalPages, $baseUrl = '?')
    {
        if ($totalPages <= 1) return '';

        // Garantir que o baseUrl termine corretamente para concatenar ?page= ou &page=
        $separator = (strpos($baseUrl, '?') === false) ? '?' : '&';
        // Remover parâmetro 'page' se ele já existir no baseUrl para evitar duplicidade
        $baseUrl = preg_replace('/([?&])page=[^&]*(&|$)/', '$1', $baseUrl);
        $baseUrl = rtrim($baseUrl, '?&');
        $urlWithParam = $baseUrl . (strpos($baseUrl, '?') === false ? '?' : '&');

        $range = 2; // Quantos números mostrar ao redor da página atual
        $html = '<div class="mt-16 flex flex-wrap items-center justify-center gap-2 animate-fade-in">';

        // Botão Anterior
        $prevDisabled = $currentPage <= 1;
        $prevUrl = $urlWithParam . 'page=' . ($currentPage - 1);
        $html .= sprintf(
            '<a href="%s" class="w-10 h-10 flex items-center justify-center rounded-xl font-bold text-sm transition-all %s shadow-lg shadow-black/20"><i class="fas fa-chevron-left"></i></a>',
            $prevDisabled ? 'javascript:void(0)' : $prevUrl,
            $prevDisabled ? 'bg-white/5 text-gray-800 cursor-not-allowed opacity-50' : 'bg-white/5 text-gray-500 hover:bg-white/10 hover:text-white hover:border-xbox-green/30 border border-transparent'
        );

        for ($i = 1; $i <= $totalPages; $i++) {
            // Lógica para mostrar: Primeira, Última e o Range ao redor da atual
            if ($i == 1 || $i == $totalPages || ($i >= $currentPage - $range && $i <= $currentPage + $range)) {
                $isActive = ($i == $currentPage);
                $activeClass = $isActive 
                    ? 'bg-xbox-green text-white shadow-[0_0_15px_rgba(16,124,16,0.4)] border-xbox-green' 
                    : 'bg-white/5 text-gray-500 hover:bg-white/10 hover:text-white border-transparent hover:border-xbox-green/30';
                
                $html .= sprintf(
                    '<a href="%spage=%d" class="w-10 h-10 flex items-center justify-center rounded-xl font-bold text-sm transition-all border %s">%d</a>',
                    $urlWithParam,
                    $i,
                    $activeClass,
                    $i
                );
            } 
            // Lógica para Elipses
            elseif ($i == $currentPage - $range - 1 || $i == $currentPage + $range + 1) {
                $html .= '<span class="w-8 text-center text-gray-700 font-black tracking-widest">...</span>';
            }
        }

        // Botão Próximo
        $nextDisabled = $currentPage >= $totalPages;
        $nextUrl = $urlWithParam . 'page=' . ($currentPage + 1);
        $html .= sprintf(
            '<a href="%s" class="w-10 h-10 flex items-center justify-center rounded-xl font-bold text-sm transition-all %s shadow-lg shadow-black/20"><i class="fas fa-chevron-right"></i></a>',
            $nextDisabled ? 'javascript:void(0)' : $nextUrl,
            $nextDisabled ? 'bg-white/5 text-gray-800 cursor-not-allowed opacity-50' : 'bg-white/5 text-gray-500 hover:bg-white/10 hover:text-white hover:border-xbox-green/30 border border-transparent'
        );

        $html .= '</div>';
        return $html;
    }
}
