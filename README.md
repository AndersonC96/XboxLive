# Xbox Live Dashboard - Engenharia de Software & Integração de Dados

Este projeto é uma plataforma de gerenciamento e visualização de dados da Xbox Live, construída para demonstrar a implementação de arquiteturas modernas (MVC), padrões de projeto sênior e consumo de APIs complexas em PHP.

O sistema transforma dados brutos da infraestrutura Xbox em uma experiência de usuário (UX) premium, focada em performance e segurança.

## 🚀 O que o sistema resolve?

Integrar com a infraestrutura da Microsoft/Xbox pode ser lento e complexo devido ao volume de dados. Este dashboard resolve isso através de:
*   **Camada Híbrida de Dados:** Sincroniza IDs de jogos em massa para o MySQL local e realiza a "hidratação" (busca de detalhes como capas e preços) sob demanda via API.
*   **Arquitetura desacoplada:** Separação total entre a lógica de persistência (Repositories), regras de negócio (Services) e fluxo de navegação (Controllers).
*   **Performance via Cache:** Sistema de cache de objetos em disco que reduz em até 90% o tempo de carregamento em acessos repetidos.

## 🛠️ Stack Técnica e Padrões

*   **Linguagem:** PHP 8.2+ (Tipagem estrita e DTOs)
*   **Frontend:** Tailwind CSS + Design System Xbox Carbon (Glassmorphism)
*   **Integração:** Guzzle HTTP (PSR-7) para consumo da API OpenXBL
*   **Banco de Dados:** MySQL (Padrão Singleton e Repository Pattern)
*   **Segurança:** Proteção CSRF em formulários, Headers de Segurança HTTP e Middleware de autenticação no roteador.
*   **Padrões de Projeto:** MVC, Singleton, DTO (Data Transfer Objects), Repository e Front Controller.

## 🕹️ Funcionalidades Implementadas

### 🎮 Centro de Jogos (Game Pass & Store)
*   **Catálogo Completo:** Visualização em grid com capas oficiais e metadados reais.
*   **Filtros Especializados:** Seções para EA Play, PC Game Pass, Títulos "Coming Soon" e jogos otimizados para toque (Cloud).
*   **Sincronização em Tempo Real:** Botões de sync que atualizam o banco de dados local com as últimas movimentações da Microsoft Store.
*   **Deep Details:** Página individual por jogo com galeria oficial, descrição técnica e integração direta com a loja.

### 👥 Social & Presença
*   **Gamer Profile:** Card dinâmico com Gamerscore, Reputação e Bio.
*   **Status Online:** Sincronização em tempo real do estado de presença (Online/Offline) e atividade atual.
*   **Social Network:** Listagem completa de amigos, seguidores e jogadores recentes das últimas sessões online.
*   **Activity Feed:** Timeline global com conquistas e mídias compartilhadas pela rede do jogador.

### 🏆 Carreira e Mídia
*   **Achievement Tracker:** Histórico completo de títulos jogados com barras de progresso e estatísticas de gamerscore.
*   **Media Gallery:** Visualizador centralizado de Capturas de Tela (Screenshots) e Clipes de Jogo gravados pelo console.

## ⚙️ Configuração do Ambiente

1.  Clone o repositório no seu servidor Apache (XAMPP/WAMP).
2.  Instale as dependências via Composer:
    ```bash
    composer install
    ```
3.  Configure o arquivo `.env` com suas credenciais:
    ```env
    DB_HOST=localhost
    DB_NAME=xboxlive
    DB_USER=root
    DB_PASS=
    XBOX_API_KEY=Sua_Chave_Aqui
    ```
4.  Importe o esquema do banco de dados contido em `database.sql`.

## 📈 Decisões de Arquitetura (Tech Insights)

*   **Por que DTOs?** Em vez de trafegar arrays associativos perigosos entre as camadas, usamos objetos tipados (`GameProduct`, `GamerProfile`). Isso garante que o desenvolvedor saiba exatamente quais dados estão disponíveis, reduzindo bugs de produção.
*   **Paginação em SQL:** Diferente de abordagens procedurais, a paginação ocorre direto na query SQL (LIMIT/OFFSET), garantindo que o sistema seja escalável mesmo com milhares de jogos no banco.
*   **Clean Router:** O roteador customizado gerencia URLs amigáveis e atua como um Middleware, validando a sessão do usuário antes mesmo do Controller ser instanciado.

---
Desenvolvido por **Anderson Cavalcante Barbosa** como demonstração técnica de arquitetura PHP moderna.
