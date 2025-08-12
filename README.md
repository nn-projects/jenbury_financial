# Jenbury Financial - Financial Education Platform

A CakePHP-based web application for Jenbury Financial, a trusted financial advisory firm led by Andrea Jenkins. This platform offers modular financial courses with multimedia content, secure user authentication, and a content management system.

## Overview

Jenbury Financial's online platform provides:

- Modular financial education courses
- Secure user authentication and account management
- Course and module purchase functionality
- Multimedia content delivery
- User-friendly interface with responsive design
- Admin dashboard for content management

## Requirements

- PHP 8.1 or higher
- MySQL 5.7 or higher
- Composer
- Web server (Apache or Nginx)

## Installation

1. Clone the repository:
   ```
   git clone https://github.com/nn-projects/jenbury_financial
   cd JenburyFinancial
   ```

2. Install dependencies:
   ```
   composer install
   ```

3. Configure the database:
   - Create a database named `jenbury_financial`
   - Update database credentials in `config/app_local.php`

4. Create database tables and sample data:
   - Import the SQL file: `config/sample_data.sql`
   ```
   mysql -u [username] -p [database_name] < config/sample_data.sql
   ```

5. Set permissions:
   ```
   chmod +x bin/cake
   ```

6. Start the development server:
   ```
   bin/cake server
   ```

7. Access the application:
   - Frontend: `http://localhost:8765/`
   - Admin: `http://localhost:8765/admin`

## Default Users

- Admin:
  - Email: admin@jenburyfinancial.com
  - Password: Admin123!@#

- Regular User:
  - Email: user@example.com
  - Password: User123!@#

## Features

### User Features

- User registration and authentication
- Browse courses and modules
- Purchase courses or individual modules
- Access purchased content
- Track learning progress
- View purchase history

### Admin Features

- Manage courses and modules
- Add/edit/delete content
- View user information
- Monitor purchases and revenue
- Customize website content

## Project Structure

- `src/Controller/`: Application controllers
- `src/Model/`: Data models and business logic
- `templates/`: View templates
- `webroot/`: Public assets (CSS, JS, images)
- `config/`: Configuration files

## Customization

- Edit templates in `templates/` to modify the UI
- Update CSS in `webroot/css/jenbury.css` to change styling
- Modify JavaScript in `webroot/js/jenbury.js` for frontend functionality

## License

This project is proprietary and confidential. Unauthorized copying, distribution, or use is strictly prohibited.

## Credits

Developed by Team 109 for Jenbury Financial.
