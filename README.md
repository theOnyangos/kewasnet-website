# Kewasnet Web Application

[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.x-red.svg)](https://codeigniter.com/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

A comprehensive web application built with CodeIgniter 4 that serves as a multi-functional platform for water management professionals and organizations. The application includes a learning portal, discussion forums, event management, document sharing, and community features.

## Features

### 🌐 **Main Website**
- Modern responsive design
- Company information and services
- Contact and location mapping
- SEO optimized pages

### 📚 **Learning Portal**
- Free and premium course offerings
- Interactive learning materials
- Progress tracking
- Quiz and assessment system
- Certificate generation

### 💬 **Discussion Forums**
- Community discussions
- Forum categories and moderation
- File attachments support
- Voting and reputation system
- Real-time notifications

### 📅 **Event Management**
- Event creation and management
- Registration and booking system
- Payment integration (M-Pesa, Paystack)
- Event organizer profiles
- Location mapping

### 📄 **Document Management**
- Document upload and sharing
- Category-based organization
- Version control
- Access permissions
- Search functionality

### 👥 **User Management**
- User registration and authentication
- Role-based access control
- Profile management
- Social login integration
- Session management

### 🔧 **Admin Dashboard**
- Comprehensive admin panel
- Analytics and reporting
- Content management
- User management
- System configuration

## Technology Stack

- **Backend**: CodeIgniter 4.x (PHP Framework)
- **Frontend**: HTML5, CSS3, JavaScript, jQuery
- **Database**: MySQL
- **Styling**: TailwindCSS, Custom CSS
- **Real-time**: WebSocket (Ratchet)
- **PDF Generation**: DomPDF
- **QR Codes**: Endroid QR Code
- **Email**: PHPMailer
- **Payment**: M-Pesa, Paystack APIs
- **Social Auth**: Facebook, Google APIs

# Installation & Setup

This guide will help you set up and run Kewasnet web application on your local environment.

## Prerequisites

Before you begin, ensure you have the following installed:

- **PHP 7.4 or higher** with the following extensions:
  - intl
  - mbstring
  - json
  - mysqlnd
  - xml
  - curl
  - fileinfo
  - gd
- **MySQL 5.7+** or **MariaDB 10.2+**
- **Composer** (latest version)
- **Web Server** (Apache/Nginx) or use PHP's built-in server
- **Node.js & npm** (for frontend dependencies, optional)

## Installation Steps

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/kewasnet-website.git
cd kewasnet-website
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Configuration

Copy the environment file and configure your settings:

```bash
cp env .env
```

Edit the `.env` file with your configuration:

```ini
#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------
CI_ENVIRONMENT = development

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------
app.baseURL = 'http://localhost:8080/'
app.indexPage = ''

#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------
database.default.hostname = localhost
database.default.database = kewasnet
database.default.username = deploy_kewasnet
database.default.password = Deploy@kewasnet2026
database.default.DBDriver = MySQLi
database.default.DBPrefix = 
database.default.port = 3306
```

### 4. Database Setup

Create a MySQL database named `kewasnet`:

```sql
CREATE DATABASE kewasnet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Run migrations and seed the database:

```bash
php spark migrate
php spark db:seed DatabaseSeeder
```

### 5. Set Permissions

Set appropriate permissions for writable directories:

```bash
chmod -R 755 writable/
chmod -R 755 public/uploads/
```

### 6. Start the Development Server

```bash
php spark serve
```

Your application will be available at `http://localhost:8080`

## Configuration

### Payment Gateways

Configure payment settings in your `.env` file:

```ini
# M-Pesa Configuration
MPESA_CONSUMER_KEY=your_consumer_key
MPESA_CONSUMER_SECRET=your_consumer_secret
MPESA_SHORTCODE=your_shortcode
MPESA_PASSKEY=your_passkey

# Paystack Configuration
PAYSTACK_PUBLIC_KEY=your_public_key
PAYSTACK_SECRET_KEY=your_secret_key
```

### Email Configuration

```ini
email.SMTPHost = smtp.gmail.com
email.SMTPUser = your-email@gmail.com
email.SMTPPass = your-app-password
email.SMTPPort = 587
email.SMTPCrypto = tls
```

### Social Authentication

```ini
# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret

# Facebook OAuth
FACEBOOK_APP_ID=your_facebook_app_id
FACEBOOK_APP_SECRET=your_facebook_app_secret
```

## Directory Structure

```
kewasnet-website/
├── app/
│   ├── Controllers/         # Application controllers
│   │   ├── API/            # API endpoints
│   │   ├── BackendV2/      # Admin panel controllers
│   │   └── FrontendV2/     # Frontend controllers
│   ├── Models/             # Database models
│   ├── Views/              # View templates
│   │   ├── backendV2/      # Admin panel views
│   │   └── frontendV2/     # Frontend views
│   ├── Services/           # Business logic services
│   ├── Database/           # Migrations and seeds
│   ├── Helpers/            # Custom helper functions
│   └── Config/             # Configuration files
├── public/                 # Web accessible files
│   ├── assets/            # CSS, JS, images
│   └── uploads/           # User uploaded files
├── writable/              # Cache, logs, uploads
└── vendor/                # Composer dependencies
```

## Usage

### Admin Access

1. Create an admin user by running:
```bash
php spark make:admin
```

2. Access the admin panel at: `http://localhost:8080/auth/dashboard`

### Key Features Access

- **Forums**: `/ksp/networking-corner/forums`
- **Learning Portal**: `/ksp/learning-corner`
- **Events**: `/ksp/events`
- **Resources**: `/ksp/resources`
- **Blog**: `/blog`

## Development

### Running Tests

```bash
vendor/bin/phpunit
```

### Code Style

Follow PSR-12 coding standards. You can use PHP CS Fixer:

```bash
vendor/bin/php-cs-fixer fix
```

### Database Migrations

Create a new migration:

```bash
php spark make:migration create_table_name
```

Run migrations:

```bash
php spark migrate
```

### Adding New Features

1. Create controllers in appropriate directories
2. Define routes in `app/Config/Routes.php`
3. Create corresponding models and views
4. Add any necessary database migrations

## Deployment

### Production Setup

1. Set environment to production in `.env`:
```ini
CI_ENVIRONMENT = production
```

2. Configure your web server (Apache/Nginx)
3. Set up SSL certificates
4. Configure caching
5. Set up backup systems
6. Configure monitoring and logging

### Server Requirements

- **Minimum**: 2GB RAM, 2 CPU cores, 20GB storage
- **Recommended**: 4GB RAM, 4 CPU cores, 50GB storage
- **PHP Memory Limit**: 256MB minimum
- **Max Upload Size**: 50MB minimum

## Troubleshooting

### Common Issues

1. **Permission Errors**: Ensure writable directories have correct permissions
2. **Database Connection**: Verify database credentials and server connectivity
3. **Missing Extensions**: Install required PHP extensions
4. **Memory Errors**: Increase PHP memory limit

### Debug Mode

Enable debug mode for development:

```ini
CI_ENVIRONMENT = development
```

Check logs in `writable/logs/` for detailed error information.

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support and questions:

- Email: support@kewasnet.co.ke
- Documentation: [Project Wiki](https://github.com/your-username/kewasnet-website/wiki)
- Issues: [GitHub Issues](https://github.com/your-username/kewasnet-website/issues)

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a list of changes and version history.

---

## API Documentation

The application provides a comprehensive REST API for various functionalities. All API endpoints are prefixed with `/api/v1/` and most require authentication.

### Authentication

Most API endpoints require authentication using JWT tokens or session-based authentication.

```bash
# Login to get access token
POST /api/auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

### Blog Management

#### Create Blog Post
```bash
POST /api/blogs/store
Authorization: Bearer {token}

{
    "title": "Blog Post Title",
    "content": "Blog post content...",
    "summary": "Brief summary",
    "category_id": 1,
    "tags": ["tag1", "tag2"]
}
```

#### Get All Blog Posts
```bash
GET /api/blogs
```

#### Get Single Blog Post
```bash
GET /api/blogs/{slug}
```

#### Update Blog Post
```bash
PUT /api/blogs/{id}
Authorization: Bearer {token}

{
    "title": "Updated Title",
    "content": "Updated content..."
}
```

#### Delete Blog Post
```bash
DELETE /api/blogs/{id}
Authorization: Bearer {token}
```

### Events Management

#### Create Event
```bash
POST /api/events
Authorization: Bearer {token}

{
    "title": "Event Title",
    "description": "Event description",
    "start_date": "2024-12-01 10:00:00",
    "end_date": "2024-12-01 18:00:00",
    "location": "Event Location",
    "is_paid": true,
    "price": 5000
}
```

#### Get All Events
```bash
GET /api/events
```

#### Register for Event
```bash
POST /api/events/{id}/register
Authorization: Bearer {token}

{
    "payment_method": "mpesa",
    "phone": "254700000000"
}
```

### Forums & Discussions

#### Create Discussion
```bash
POST /api/discussions
Authorization: Bearer {token}

{
    "forum_id": 1,
    "title": "Discussion Title",
    "content": "Discussion content...",
    "tags": ["water", "conservation"]
}
```

#### Get Forum Discussions
```bash
GET /api/forums/{id}/discussions
```

#### Reply to Discussion
```bash
POST /api/discussions/{id}/replies
Authorization: Bearer {token}

{
    "content": "Reply content...",
    "parent_id": null
}
```

### Resource Management

#### Upload Resource
```bash
POST /api/resources
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "title": "Resource Title",
    "description": "Resource description",
    "category_id": 1,
    "file": [file upload]
}
```

#### Get Resources
```bash
GET /api/resources?category={category_id}&search={query}
```

### User Management

#### User Registration
```bash
POST /api/auth/register

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirm": "password"
}
```

#### Update Profile
```bash
PUT /api/user/profile
Authorization: Bearer {token}

{
    "name": "Updated Name",
    "bio": "User bio...",
    "phone": "254700000000"
}
```

### Response Format

All API responses follow a consistent format:

```json
{
    "status": "success|error",
    "message": "Response message",
    "data": {},
    "errors": {},
    "meta": {
        "pagination": {},
        "total": 0
    }
}
```

### Error Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

### Rate Limiting

API endpoints are rate-limited to prevent abuse:
- Authenticated users: 1000 requests per hour
- Anonymous users: 100 requests per hour
