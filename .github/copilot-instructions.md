# Copilot Instructions for Paden Backend

## Project Overview
This project is a Laravel-based web application. Laravel is a PHP framework designed for building robust and scalable web applications. The codebase includes features such as routing, middleware, database migrations, and real-time event broadcasting.

### Key Components
- **Controllers**: Located in `app/Http/Controllers/`, these handle HTTP requests and responses.
- **Models**: Found in `app/Models/`, these represent the data structure and interact with the database.
- **Routes**: Defined in `routes/` (e.g., `api.php`, `web.php`), these map URLs to controllers.
- **Views**: Stored in `resources/views/`, these are Blade templates for rendering HTML.
- **Migrations**: Located in `database/migrations/`, these define the database schema.

### External Dependencies
- **Composer**: Used for managing PHP dependencies. The `composer.json` file lists all required packages.
- **Node.js**: Used for managing frontend assets via `npm` or `yarn`. The `package.json` file lists JavaScript dependencies.
- **Paynow**: A payment integration library included in `vendor/paynow/`.

## Developer Workflows

### Setting Up the Project
1. Clone the repository.
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Install JavaScript dependencies:
   ```bash
   npm install
   ```
4. Set up the environment file:
   ```bash
   cp .env.example .env
   ```
   Update `.env` with database credentials and other configurations.
5. Generate the application key:
   ```bash
   php artisan key:generate
   ```
6. Run database migrations:
   ```bash
   php artisan migrate
   ```

### Running the Application
- Start the development server:
  ```bash
  php artisan serve
  ```
- Compile frontend assets:
  ```bash
  npm run dev
  ```

### Testing
- Run all tests:
  ```bash
  php artisan test
  ```
- Feature tests are in `tests/Feature/`.
- Unit tests are in `tests/Unit/`.

### Debugging
- Use Laravel's built-in debugging tools like `dd()` and `dump()`.
- Check logs in `storage/logs/`.

## Project-Specific Conventions
- **Controllers**: Follow RESTful conventions (e.g., `index`, `store`, `update`, `destroy`).
- **Models**: Use Laravel's Eloquent ORM for database interactions.
- **Routes**: Group related routes and use middleware for authentication and authorization.
- **Testing**: Write tests for new features in the appropriate `tests/` subdirectory.

## Integration Points
- **Database**: Uses MySQL (configured in `.env`).
- **Email**: Configured via `config/mail.php`.
- **Real-Time Events**: Uses Laravel Echo and Pusher for broadcasting.
- **Payment Gateway**: Integrated with Paynow.

## Helpful Commands
- Clear cache:
  ```bash
  php artisan cache:clear
  ```
- Run database seeders:
  ```bash
  php artisan db:seed
  ```
- Rollback migrations:
  ```bash
  php artisan migrate:rollback
  ```

## Key Files and Directories
- `app/`: Core application logic.
- `routes/`: Route definitions.
- `resources/views/`: Blade templates.
- `database/migrations/`: Database schema definitions.
- `tests/`: Automated tests.

For more details, refer to the [Laravel documentation](https://laravel.com/docs).