# Xbox Live Dashboard - Engenharia de Software & Integração de Dados

Este projeto é uma plataforma de gerenciamento e visualização de dados da Xbox Live, construída para demonstrar a implementação de arquiteturas modernas (MVC), padrões de projeto sênior e consumo de APIs complexas em PHP.

O sistema transforma dados brutos da infraestrutura Xbox em uma experiência de usuário (UX) premium, focada em performance e segurança.

## 🛠️ Stack Técnica e Padrões

*   **Linguagem:** PHP 8.2+ (Tipagem estrita e DTOs)
*   **Frontend:** Tailwind CSS + Design System Xbox Carbon (Glassmorphism)
*   **Integração:** Guzzle HTTP (PSR-7) para consumo da API OpenXBL
*   **Banco de Dados:** MySQL (Padrão Singleton e Repository Pattern)
*   **Segurança:** Proteção CSRF em formulários críticos, Headers de Segurança HTTP (nosniff, XSS-Protection, Frame-Options) e Middleware de autenticação no roteador.
*   **Gestão de Sessão:** Regeneração de ID em login e expiração robusta em logout para mitigar Session Fixation.
*   **Arquitetura:** MVC Artesanal com Front Controller, Service Layer e Data Transfer Objects (DTO).

## 🚀 O que o sistema resolve?

Integrar com a infraestrutura da Microsoft/Xbox exige resiliência devido à latência e limites de taxa (Rate Limits). Este dashboard resolve isso através de:
*   **Camada Híbrida de Dados:** Sincroniza IDs de jogos para o MySQL local e realiza a hidratação de detalhes via API sob demanda.
*   **Resiliência de API:** Tratamento granular de erros HTTP (401, 429, 5xx) com feedback amigável via Flash Messages.
*   **Performance via Cache:** Sistema de cache de objetos em disco que reduz o overhead de rede em requisições repetitivas.

## 🕹️ Funcionalidades Implementadas

### 🎮 Centro de Jogos (Game Pass & Store)
*   **Catálogo Completo:** Visualização em grid com capas oficiais e metadados reais.
*   **Filtros Especializados:** EA Play, PC Game Pass, "Coming Soon" e Cloud Gaming (Touch).
*   **Sincronização:** Gatilhos para atualização de metadados locais via API Marketplace.

### 👥 Social & Presença
*   **Gamer Profile:** Card dinâmico com Gamerscore, Reputação e Bio.
*   **Status Online:** Sincronização do estado de presença e atividade atual.
*   **Rede Social:** Listagem de amigos, seguidores e histórico de jogadores recentes.

### 🏆 Carreira e Mídia
*   **Achievement Tracker:** Progresso detalhado de conquistas por título.
*   **Media Gallery:** Visualizador de Capturas de Tela e Clipes de Jogo.

## ⚙️ Configuração do Ambiente

1.  Instale as dependências: `composer install`
2.  Configure o arquivo `.env` (use o `.env.example` como base).
3.  Importe o `database.sql`.
4.  Certifique-se de que a pasta `storage/cache` tenha permissão de escrita.

---
Desenvolvido por **Anderson Cavalcante Barbosa** como demonstração técnica de engenharia PHP moderna.
