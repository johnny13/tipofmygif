# GIPHY-API

This is an API for browsing Giphy, taking the results, and saving them to your account, where you can then comment on the Gif, and Rate the Gif. Then in the future when you want to find the gif, you can either search for things you've said about the Gif, maybe something like 'only real ones get this', or search by ratings, allowing for easy retreival of things you would normally not be able to find. 

There is no UI to this portion, outside of the Swagger Documentation. This project uses Laravel Sail.

## 🚀 Quick Start

### Prerequisites

Before you begin, ensure you have the following installed:

- **Docker Desktop** - [Download here](https://www.docker.com/products/docker-desktop/)
- **Git** - [Download here](https://git-scm.com/downloads)
- **Composer** (optional, for local development) - [Download here](https://getcomposer.org/download/)

### System Requirements

- **macOS**: Docker Desktop for Mac
- **Windows**: Docker Desktop for Windows (WSL 2 recommended)
- **Linux**: Docker Engine and Docker Compose

**Minimum Docker Resources:**
- **RAM**: 4GB (8GB recommended)
- **CPU**: 2 cores (4 cores recommended)
- **Disk**: 20GB free space

### Installation & Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd giphy-api
   ```

2. **Start Laravel Sail**
   ```bash
   # Start all containers in the background
   ./vendor/bin/sail up -d
   
   # Or start with logs visible
   ./vendor/bin/sail up
   ```

3. **Run database migrations**
   ```bash
   ./vendor/bin/sail artisan migrate
   ```

4. **Seed the database with test users**
   ```bash
   ./vendor/bin/sail artisan db:seed
   ```

5. **Generate API documentation**
   ```bash
   ./generate-api-docs.sh
   ```

### 🐳 Laravel Sail Commands

**Essential Commands:**
```bash
# Start containers
./vendor/bin/sail up -d

# Stop containers
./vendor/bin/sail down

# View running containers
./vendor/bin/sail ps

# View logs
./vendor/bin/sail logs

# Access Laravel application
./vendor/bin/sail artisan

# Run tests
./vendor/bin/sail test

# Access database
./vendor/bin/sail mysql

# Access container shell
./vendor/bin/sail shell
```

**Development Commands:**
```bash
# Install Composer dependencies
./vendor/bin/sail composer install

# Run migrations
./vendor/bin/sail artisan migrate

# Run migrations with seeders
./vendor/bin/sail artisan migrate:fresh --seed

# Clear caches
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan route:clear

# Generate API documentation
./generate-api-docs.sh
```

### 🌐 Access Points

Once Laravel Sail is running, you can access:

- **Main Application**: http://localhost:80
- **Swagger UI**: http://localhost:80/api/documentation
- **API Documentation**: http://localhost:80/api/docs
- **Mailpit (Email Testing)**: http://localhost:8025
- **Database**: localhost:3306 (MySQL/MariaDB)

### 🔧 Troubleshooting

**Common Issues:**

1. **Port already in use**
   ```bash
   # Check what's using port 80
   lsof -i :80
   
   # Or use a different port
   ./vendor/bin/sail up -d --build
   ```

2. **Docker not running**
   - Ensure Docker Desktop is started
   - Check Docker status: `docker --version`

3. **Permission issues**
   ```bash
   # Fix file permissions
   sudo chown -R $USER:$USER .
   chmod +x vendor/bin/sail
   ```

4. **Container build issues**
   ```bash
   # Rebuild containers
   ./vendor/bin/sail down
   ./vendor/bin/sail build --no-cache
   ./vendor/bin/sail up -d
   ```

5. **Database connection issues**
   ```bash
   # Reset database
   ./vendor/bin/sail artisan migrate:fresh --seed
   ```

## 📁 Project Structure

```
giphy-api/
├── app/
│   ├── Http/Controllers/Api/     # API Controllers (Giphy, Ratings, Comments)
│   ├── Http/Controllers/Auth/    # Authentication Controllers
│   ├── Http/Annotations/         # OpenAPI base configuration
│   ├── Models/                   # Eloquent Models (User, Rating, Comment)
│   ├── Services/                 # Business Logic (GiphyService)
│   └── Console/Commands/         # Custom Artisan Commands
├── database/
│   ├── migrations/               # Database migrations
│   └── seeders/                  # Database seeders
├── routes/
│   └── api.php                   # API route definitions
├── storage/
│   └── api-docs/                 # Generated OpenAPI documentation
├── docker-compose.yml            # Laravel Sail configuration
├── generate-api-docs.sh          # API documentation generator script
└── README.md                     # This file
```

## 🔧 Services & Components

- **Laravel Sail**: Docker-based development environment
- **Laravel Sanctum**: API token authentication
- **L5-Swagger**: OpenAPI/Swagger documentation
- **MariaDB**: Database (MySQL compatible)
- **Mailpit**: Email testing interface
- **Typesense**: Search engine (for future features)

## 📚 OpenAPI Documentation

This project includes comprehensive OpenAPI documentation for all API endpoints. The documentation is automatically generated from annotations in the controllers and models.

### Generating API Documentation

To generate the OpenAPI documentation, run:

```bash
./generate-api-docs.sh
```

Or manually:

```bash
./vendor/bin/openapi --output storage/api-docs/api-docs.json app/Http/Controllers app/Http/Annotations app/Models
```

### Viewing the Documentation

The generated OpenAPI specification is saved to `storage/api-docs/api-docs.json`. You can:

1. **View the JSON file directly** - The specification follows the OpenAPI 3.0 standard
2. **Use Swagger UI** - If you have L5-Swagger configured, visit `http://localhost:8000/api/documentation`
3. **Import into tools** - Use the JSON file with tools like Postman, Insomnia, or any OpenAPI-compatible client

### Adding New Endpoints

When adding new API endpoints:

1. Add `@OA\` annotations to your controller methods
2. Define schemas for your models using `@OA\Schema` annotations
3. Run the generation script to update the documentation

### Current API Endpoints

- **Giphy Search**: `GET /api/gifs/search` - Search for GIFs on Giphy
- **Ratings**: Full CRUD operations for user ratings
  - `GET /api/ratings` - Get user's ratings
  - `POST /api/ratings` - Create/update a rating
  - `GET /api/ratings/{rating}` - Get specific rating
  - `PUT /api/ratings/{rating}` - Update a rating
  - `DELETE /api/ratings/{rating}` - Delete a rating
- **Comments**: Full CRUD operations for GIF comments
  - `GET /api/comments` - Get comments for a GIF (requires gif_id parameter)
  - `POST /api/comments` - Create a new comment
  - `GET /api/comments/{comment}` - Get specific comment
  - `PUT /api/comments/{comment}` - Update a comment
  - `DELETE /api/comments/{comment}` - Delete a comment

**All endpoints require authentication via Laravel Sanctum**. 

We will cover how to use the default seeded accounts or creating your own account next.

### 👥 Available Test Users

The application comes with 3 pre-seeded test users ready to use:

| User | Email | Password | API Token |
|------|-------|----------|-----------|
| Test User | `test@example.com` | `password123` | `1|SNnLmfatBn7vCkPN97xTNloLwTnlgO67Jf3e1Iiye409eb09` |
| John Doe | `john@example.com` | `password123` | `2|ugSdoHr0csWynV9x2uGlVhRlonhnzJKzEVNDyH5Oc763aa78` |
| Jane Smith | `jane@example.com` | `password123` | `3|iRoY1oAo7fvTsy2czBo97fCkVz5xCqBEiC6Oh8B3dde41b5f` |

**Quick Start with Test Users:**
```bash
# Test the API with Jane's token
curl -X GET http://localhost:80/api/user \
  -H "Authorization: Bearer 3|iRoY1oAo7fvTsy2czBo97fCkVz5xCqBEiC6Oh8B3dde41b5f" \
  -H "Accept: application/json"
```

### Users

There are 3 differnt users that are seeded when you run the seeder `./vendor/bin/sail artisan db:seed`. You can use one of those accounts to login. Once you have logged in, you will need to provide your token to make subsequent api requests. If you would like to create your own user, do somethingn like this or use `artisan tinker` and then `User::create()`. 

```shell
curl -X POST http://localhost:80/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New User",
    "email": "newuser@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

#### Grab A Token

Using the `artisan tinker` command you simulate a login + token creation. Once you have the token, you can then use the remaining endpoints that require authentication. 

```shell
./vendor/bin/sail artisan tinker

# Then run:
use App\Models\User;
$user = User::where('email', 'user@example.com')->first();
$token = $user->createToken('My Token');
echo $token->plainTextToken; # ex 1|iRoY1oAo7fvTsy2czBo97fCkVz5xCqBEiC6Oh8B3dde41b5f
```

The token format is: `{id}|{random_string}`

```
1 = The token ID from the database
| = Separator
iRoY1oAo7fvTsy2czBo97fCkVz5xCqBEiC6Oh8B3dde41b5f = Random 40-character string
```

**How Tokens Work:**
- Tokens are generated by **Laravel Sanctum** using the `HasApiTokens` trait
- Each token is stored in the `personal_access_tokens` database table
- The random string is generated using Laravel's `Str::random(40)` method
- Tokens are hashed in the database but returned as plain text for API use
- You can create multiple tokens per user with different names

#### Get to the GIFs already!

Once you have that magic token we can then finally look at some GIFs

```
curl -X 'GET' \
  'http://localhost/api/gifs/search?query=star&limit=25&offset=0' \
  -H 'accept: application/json' \
  -H 'Authorization: Bearer 3|iRoY1oAo7fvTsy2czBo97fCkVz5xCqBEiC6Oh8B3dde41b5f' \
  -H 'X-CSRF-TOKEN: 
```

