# Xbox Live Dashboard Case (V2 - Premium Refactor)

Este projeto é uma refatoração profissional de um sistema legado de Dashboard Xbox Live. Ele foi transformado de uma aplicação procedural simples para uma arquitetura moderna orientada a serviços (SOA), com foco em segurança, escalabilidade e design premium.

## ✨ O que há de novo (Refatoração 2.0)

### 🏗️ Arquitetura Técnica
*   **PSR-4 Autoloading**: Organização completa do código fonte na pasta `src/` usando Namespaces (`Anderson\XboxLive`).
*   **Camada de Serviços (Service Layer)**: 
    *   `AuthService`: Centraliza login, registro e persistência de sessão.
    *   `OpenXBLService`: Cliente de API desacoplado com tratamento de erros e normalização de dados.
    *   `Database`: Singleton PDO para conexões seguras e eficientes.
*   **Bootstrap Centralizado**: Ponto único de entrada para inicialização de variáveis de ambiente, sessões e configurações.
*   **Segurança**: Senhas criptografadas com `password_hash`, proteção de rotas via middleware de serviço e uso estrito de Prepared Statements.

### 🎨 Design & UX Premium
*   **Design System Xbox Carbon**: Nova identidade visual baseada em tons de cinza profundos, verde Xbox vibrante e tipografia tecnológica (`Outfit`).
*   **Glassmorphism**: Efeitos de transparência avançados e profundidade visual em todos os componentes.
*   **Responsividade**: Totalmente adaptado para dispositivos móveis usando grids modernos e flexbox.
*   **Funcionalidades Plus**:
    *   Busca em tempo real e paginação nas Conquistas e Amigos.
    *   Sistema de vínculo de Gamertag dinâmico (Manual ou Automático).
    *   Feedback visual imediato para ações do usuário.

## 🚀 Como Executar

### 1. Pré-requisitos
*   PHP 8.0+
*   Composer
*   XAMPP / MySQL

### 2. Instalação
1. Clone o repositório.
2. Na raiz do projeto, instale as dependências:
   ```bash
   composer install
   ```
3. Renomeie o arquivo `.env.example` para `.env` (ou crie um novo) e configure suas credenciais:
   ```env
   DB_HOST=localhost
   DB_NAME=xboxlive_dashboard
   DB_USER=root
   DB_PASSWORD=
   XBOX_API_KEY=sua_chave_aqui
   ```

### 3. Banco de Dados
Importe o arquivo `database.sql` no seu PHPMyAdmin para criar as tabelas e o usuário administrativo inicial:
*   **Usuário**: `admin`
*   **Senha**: `senha123`

---

## 🛠️ Tecnologias Utilizadas
*   **PHP** (Vanilla com PSR-4)
*   **MySQL** (MariaDB)
*   **Tailwind CSS** (Custom Config)
*   **OpenXBL API** (Integração Social)
*   **Composer** (Gerenciamento de dependências e autoload)

Este projeto serve como uma demonstração sólida de habilidades em **Engenharia de Software PHP**, **Arquitetura de Sistemas** e **Desenvolvimento Frontend Refinado**.
