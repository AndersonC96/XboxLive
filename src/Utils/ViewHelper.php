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
     * @param string $paramName Nome do parâmetro de página na URL (default: page)
     * @return string HTML da paginação
     */
    public static function renderPagination($currentPage, $totalPages, $baseUrl = '?', $paramName = 'page')
    {
        if ($totalPages <= 1) return '';

        // Garantir que o baseUrl termine corretamente para concatenar ?param= ou &param=
        $separator = (strpos($baseUrl, '?') === false) ? '?' : '&';
        
        // Remover o parâmetro específico para evitar duplicidade
        $baseUrl = preg_replace('/([?&])' . preg_quote($paramName) . '=[^&]*(&|$)/', '$1', $baseUrl);
        $baseUrl = rtrim($baseUrl, '?&');
        $urlWithParam = $baseUrl . (strpos($baseUrl, '?') === false ? '?' : '&');

        $range = 2; // Quantos números mostrar ao redor da página atual
        $html = '<div class="mt-8 flex flex-wrap items-center justify-center gap-2 animate-fade-in">';

        // Botão Anterior
        $prevDisabled = $currentPage <= 1;
        $prevUrl = $urlWithParam . $paramName . '=' . ($currentPage - 1);
        $html .= sprintf(
            '<a href="%s" class="w-10 h-10 flex items-center justify-center rounded-xl font-bold text-sm transition-all %s shadow-lg shadow-black/20"><i class="fas fa-chevron-left"></i></a>',
            $prevDisabled ? 'javascript:void(0)' : $prevUrl,
            $prevDisabled ? 'bg-white/5 text-gray-800 cursor-not-allowed opacity-50' : 'bg-white/5 text-gray-500 hover:bg-white/10 hover:text-white hover:border-xbox-green/30 border border-transparent'
        );

        for ($i = 1; $i <= $totalPages; $i++) {
            if ($i == 1 || $i == $totalPages || ($i >= $currentPage - $range && $i <= $currentPage + $range)) {
                $isActive = ($i == $currentPage);
                $activeClass = $isActive 
                    ? 'bg-xbox-green text-white shadow-[0_0_15px_rgba(16,124,16,0.4)] border-xbox-green' 
                    : 'bg-white/5 text-gray-500 hover:bg-white/10 hover:text-white border-transparent hover:border-xbox-green/30';
                
                $html .= sprintf(
                    '<a href="%s%s=%d" class="w-10 h-10 flex items-center justify-center rounded-xl font-bold text-sm transition-all border %s">%d</a>',
                    $urlWithParam,
                    $paramName,
                    $i,
                    $activeClass,
                    $i
                );
            } 
            elseif ($i == $currentPage - $range - 1 || $i == $currentPage + $range + 1) {
                $html .= '<span class="w-8 text-center text-gray-700 font-black tracking-widest">...</span>';
            }
        }

        // Botão Próximo
        $nextDisabled = $currentPage >= $totalPages;
        $nextUrl = $urlWithParam . $paramName . '=' . ($currentPage + 1);
        $html .= sprintf(
            '<a href="%s" class="w-10 h-10 flex items-center justify-center rounded-xl font-bold text-sm transition-all %s shadow-lg shadow-black/20"><i class="fas fa-chevron-right"></i></a>',
            $nextDisabled ? 'javascript:void(0)' : $nextUrl,
            $nextDisabled ? 'bg-white/5 text-gray-800 cursor-not-allowed opacity-50' : 'bg-white/5 text-gray-500 hover:bg-white/10 hover:text-white hover:border-xbox-green/30 border border-transparent'
        );

        $html .= '</div>';
        return $html;
    }
}
