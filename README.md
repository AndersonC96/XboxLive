## 🎮 Xbox Live Gamepass Dashboard

Painel interativo desenvolvido com PHP, Tailwind CSS e a API do Xbox Live para visualizar jogos, conquistas e informações detalhadas do perfil Xbox Live em uma interface elegante e fácil de usar.

## 🛠️ Funcionalidades

- **🔍 Busca de Jogos**: Pesquisa direta de jogos no catálogo do Gamepass.
- **📋 Listagem de Jogos**: Exibe todos os jogos do Gamepass com informações como preço, descrição, desenvolvedor, publisher, franquia e categoria.
- **🏆 Conquistas**: Exibe conquistas dos jogos, progresso e plataformas.
- **🧑 Perfil do Usuário**: Mostra informações detalhadas do perfil Xbox, como gamerscore, reputação e bio.
- **🖥️ Plataformas**: Exibe graficamente as plataformas suportadas por cada jogo (Xbox One, Xbox Series X|S, PC).
- **📊 Paginação e Filtro**: Lista até 12 jogos por página, com opções de filtragem por Nome, Gamerscore, Data de Jogo, e Plataforma.

## 🚀 Tecnologias Utilizadas

- **PHP** - Linguagem principal para o backend.
- **Tailwind CSS** - Framework para estilização rápida e responsiva.
- **API do Xbox Live** - Integração para capturar dados em tempo real.
- **MySQL** - Banco de dados para armazenamento de informações de jogos e perfil.

## 💻 Pré-requisitos

- **PHP 7.4+**
- **MySQL**
- **Composer (para gerenciamento de dependências)**
- **API Key do Xbox Live**

### Erro comum: `Class "mysqli" not found`

Se você visualizar este erro ao carregar a aplicação, o PHP não está com a extensão de acesso ao MySQL habilitada.

- **No XAMPP/Windows**: abra o arquivo `php.ini`, descomente a linha `extension=mysqli` (remova o `;` do início) e reinicie o Apache.
- **No Linux (PHP instalado via pacote)**: instale ou habilite o pacote `php-mysql` (ou `php8.x-mysql`) e reinicie o servidor web.
- Após habilitar a extensão, reinicie o servidor e recarregue a página.

## 📝 Instalação

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/xbox-live-dashboard.git
cd Steam_games
```

### 2. Instale as dependências com o Composer

```bash
composer install
```

### 3. Configure o banco de dados

- Crie o banco de dados MySQL.
- Importe o arquivo `database.sql` para criar as tabelas necessárias.
- Configure o arquivo `.env`:

```bash
DB_HOST=localhost
DB_NAME=nome_do_banco
DB_USER=usuario_do_banco
DB_PASSWORD=senha_do_banco
XBOX_API_KEY=sua_chave_da_api
```

## ⚙️ Uso

### 1. Inicie o servidor PHP ou hospede em um servidor local/online.

```bash
php -S localhost:8000
```

### 2. Acesse a aplicação no navegador:

- Acesse a URL: `http://localhost:8000` (ou a URL da sua hospedagem).

### 3. Explore as funcionalidades:

- Veja seu perfil Xbox, conquistas e detalhes dos jogos.
- Use a barra de busca para encontrar jogos específicos e utilize filtros para organizar as listagens.