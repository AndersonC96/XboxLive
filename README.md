# 🎮 Xbox Live Gamepass Dashboard

Dashboard interativo em PHP que integra a API do OpenXBL para exibir perfil, presença, conquistas e catálogos do Game Pass em uma interface inspirada no ecossistema Xbox.

## ✨ Principais funcionalidades
- **Autenticação local**: páginas dedicadas para cadastro e login com hash seguro de senha e gerenciamento de sessão.
- **Visão geral do jogador**: painel principal com Gamertag, gamerscore, bio, reputação, localização, estado de presença e últimos jogos acessados a partir dos endpoints `account`, `player/summary` e `player/titleHistory`.
- **Feed da comunidade**: timeline de atividades recente com busca por Gamertag, consumindo `activity/feed`.
- **Conquistas**: lista paginada de jogos com conquistas, imagens (BoxArt e Hero), progresso e ícones das plataformas suportadas.
- **Catálogo Game Pass**:
  - Listagem completa com paginação local (12 itens), busca instantânea e metadados como descrição, publisher, franquia e preço.
  - Páginas segmentadas para lançamentos, mais jogados, promoções, gratuitos/pagos, EA Play, Game Pass para PC e títulos em nuvem.
  - Rotas de atualização (`atualizar_jogos*.php`) que populam tabelas específicas (`gamepass_games`, `cloud_gamepass`, `ea_gamepass`, `pc_gamepass`) usadas nas consultas ao endpoint `marketplace/details`.
- **Exploração social**: páginas para amigos, solicitações, clubes, presença, alertas, histórico, recomendações e geração de Gamertag que consomem os respectivos endpoints da API.
- **Pesquisa rápida**: barra dedicada para buscar jogos pelo catálogo (`search.php`) e filtros adicionais nas páginas de listagem.

## 🗂️ Estrutura do projeto
- `index.php` – redireciona usuários autenticados para o dashboard ou para a tela de login.
- `pages/` – todas as telas da aplicação (dashboard, feed, conquistas, catálogos, social, autenticação e utilitários de sincronização de jogos).
- `actions/` – handlers de formulário (`login_action.php`, `register_action.php`) que usam PDO e `password_hash`/`password_verify`.
- `config/` – integrações com OpenXBL (`api.php`) e banco de dados MySQL (`db.php`) via Dotenv.
- `includes/` – cabeçalho, rodapé e barra de navegação reutilizáveis.
- `database.sql` – script para criar o schema, tabelas de jogos e um usuário padrão para acesso inicial.

## 🧰 Tecnologias
- **PHP 7.4+** – backend e renderização das páginas.
- **OpenXBL API** – fonte de dados para conta, presença, feed, conquistas e detalhes dos jogos.
- **MySQL** – persistência de usuários e listas de títulos do Game Pass.
- **Composer** – autoload e gerenciamento do Dotenv.
- **Tailwind CSS + Font Awesome** – estilização e ícones.

## ⚙️ Preparação do ambiente
1. Instale as dependências do PHP e do Composer.
2. Crie o arquivo `.env` na raiz do projeto com as variáveis:
   ```bash
   DB_HOST=localhost
   DB_NAME=xboxlive_dashboard
   DB_USER=seu_usuario
   DB_PASSWORD=sua_senha
   OPENXBL_API_KEY=sua_chave_openxbl
   ```
3. Configure a extensão MySQLi/PDO_mysql no PHP (a aplicação usa PDO).

### Banco de dados
1. Importe o `database.sql` no MySQL para criar o schema e tabelas de catálogo.
2. Utilize o usuário seedado (`admin`/`senha123`) ou crie novos registros na tela de cadastro.

## 🚀 Como executar
1. Instale as dependências do Composer:
   ```bash
   composer install
   ```
2. Inicie o servidor de desenvolvimento do PHP na raiz do projeto:
   ```bash
   php -S localhost:8000
   ```
3. Acesse `http://localhost:8000/pages/login.php`, autentique-se e navegue pelas seções do dashboard.

## 🔍 Dicas de uso
- Sempre mantenha sua `OPENXBL_API_KEY` válida; chamadas sem chave retornam respostas vazias e impedem o carregamento dos cards.
- Use as páginas de atualização de catálogo após criar o banco para preencher as tabelas de IDs antes de navegar pelas listagens.
- Caso veja o erro `Class "mysqli" not found`, habilite a extensão MySQL do PHP e reinicie o servidor.

## 📜 Licença
Este projeto é fornecido no estado em que se encontra.
