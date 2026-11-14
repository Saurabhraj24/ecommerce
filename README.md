# Ecommerce

A Laravel-based eCommerce fashion application.

## Requirements

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL / PostgreSQL / SQLite

## Setup Instructions

1. **Clone the repository:**
   ```bash
   git clone https://github.com/Saurabhraj24/ecommerce.git
   cd ecommerce
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies:**
   ```bash
   npm install
   npm run build
   ```

4. **Environment Setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Run Migrations:**
   ```bash
   php artisan migrate
   ```

6. **Start the Development Server:**
   ```bash
   php artisan serve
   ```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
