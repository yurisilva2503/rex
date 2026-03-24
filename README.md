<p align="center">
  <img src="public/assets/img/logo.png" width="300" alt="Laravel Logo" />
</p>

<h1 align="center">Rex</h1>

---

## Sobre o Projeto

O **Rex** é uma aplicação web focada na gestão de indicadores de desempenho.

## Tecnologias Principais

O projeto emprega as seguintes linguagens e ferramentas core:

- [PHP 8.2+](https://www.php.net/)
- [Laravel 12](https://laravel.com/)
- [MySQL](https://www.mysql.com/)

## Pré-requisitos

Certifique-se de ter as seguintes dependências instaladas no seu ambiente de desenvolvimento:

- PHP `^8.2`
- [Composer](https://getcomposer.org/)
- Servidor Banco de Dados rodando (MySQL)

## Primeiros Passos

Para rodar a aplicação localmente, siga o fluxo de setup abaixo:

1. **Faça o clone do repositório ou navegue até o diretório root do projeto:**
   ```bash
   cd rex
   ```

2. **Instale as dependências PHP com Composer:**
   ```bash
   composer install
   ```

3. **Instale as dependências Frontend com NPM:**
   ```bash
   npm install
   ```

4. **Configuração do `.env`:**
   Copie a estrutura do `.env.example` para um novo `.env` (se ainda não existir) e configure a conexão com o banco de dados:
   ```bash
   cp .env.example .env
   ```
   *Edite `DB_DATABASE`, `DB_USERNAME` e `DB_PASSWORD` conforme as configurações da sua máquina.*

5. **Gerar a Chave do APP:**
   ```bash
   php artisan key:generate
   ```

6. **Rodar as Migrações:**
   ```bash
   php artisan migrate
   ```

7. **Iniciar o Servidor de Desenvolvimento:**
   Para rodar de forma simultânea e reativa o servidor do Laravel e a construção de assets do Vite, apenas rode:
   ```bash
   npm run dev
   ```

Seu ambiente deverá ficar ativo e respondendo na porta definida, normalmente acessível em `http://localhost:8000`.
