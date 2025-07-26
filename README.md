# tipofmygif

![Tip of My GIF Logo](logo.jpeg)

> **Save, rate and categorize Giphy gifs for easy retrieval later.**

## 🎯 About

Tip of My GIF is a comprehensive solution for managing and organizing your favorite GIFs from Giphy. Ever had that moment where you remember a perfect GIF but can't find it? This project solves that problem by allowing you to save, rate, and categorize GIFs with your own comments and ratings, making them easily searchable later.

### ✨ Features

- **🔍 Giphy Integration**: Search and browse millions of GIFs from Giphy
- **💾 Save & Organize**: Save your favorite GIFs to your personal collection
- **⭐ Rate & Review**: Rate GIFs (1-5 stars) and add personal comments
- **🏷️ Smart Categorization**: Organize GIFs with custom tags and descriptions
- **🔎 Advanced Search**: Find saved GIFs by rating, comments, or tags
- **🔐 User Accounts**: Secure authentication with personal collections
- **📱 Responsive Design**: Works on desktop and mobile devices

## 🏗️ Project Structure

This project consists of two main components:

```
tipofmygif/
├── 📁 giphy-api/          # Backend API (Laravel + Sail)
│   ├── 🐳 Laravel Sail    # Docker-based development environment
│   ├── 🔐 Laravel Sanctum # API authentication
│   ├── 📚 OpenAPI/Swagger # Interactive API documentation
│   ├── 🗄️ MariaDB        # Database
│   └── 📧 Mailpit         # Email testing
├── 📁 frontend/           # Frontend Application (Coming Soon)
│   ├── 🎨 Modern UI       # User interface
│   ├── 📱 Responsive      # Mobile-friendly design
│   └── 🔗 API Integration # Backend connectivity
└── 📄 README.md           # This file
```

### 🔧 Backend (giphy-api/)

The backend is a **Laravel-based REST API** powered by Laravel Sail for easy development:

- **Framework**: Laravel 11 with Laravel Sail
- **Database**: MariaDB (MySQL compatible)
- **Authentication**: Laravel Sanctum (API tokens)
- **Documentation**: OpenAPI/Swagger with interactive UI
- **Development**: Docker-based with hot reloading

**🚀 Quick Backend Start:**
```bash
cd giphy-api
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./generate-api-docs.sh
# Access Swagger UI: http://localhost:80/api/documentation
```

**📚 [Full Backend Documentation →](giphy-api/README.md)**

### 🎨 Frontend (Coming Soon)

The frontend will provide a modern, responsive web interface for:
- Browsing and searching Giphy GIFs
- Managing your saved GIF collection
- Rating and commenting on GIFs
- Advanced search and filtering

## 🚀 Getting Started

### Prerequisites

- **Docker Desktop** - [Download here](https://www.docker.com/products/docker-desktop/)
- **Giphy API Key** - [Get one here](https://developers.giphy.com/)
- **Git** - [Download here](https://git-scm.com/downloads)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd tipofmygif
   ```

2. **Setup the backend**
   ```bash
   cd giphy-api
   ./vendor/bin/sail up -d
   ./vendor/bin/sail artisan migrate --seed
   ./generate-api-docs.sh
   ```

3. **Configure Giphy API Key**
   ```bash
   # Copy environment file
   cp .env.example .env
   
   # Edit .env and add your Giphy API key
   GIPHY_API_KEY=your_giphy_api_key_here
   ```

4. **Access the application**
   - **Backend API**: http://localhost:80
   - **API Documentation**: http://localhost:80/api/documentation
   - **Test Users**: See [Backend README](giphy-api/README.md#-available-test-users)

## 🔑 API Key Setup

After you have confirmed the app is up and running, there is only one more thing you nneed to do to start searching. Get a **Giphy API Key** enabled:

1. **Get a Giphy API Key**:
   - Visit [Giphy Developers](https://developers.giphy.com/)
   - Create an account and register your app
   - Copy your API key

2. **Configure the API Key**:
   ```bash
   cd giphy-api
   # Edit .env file
   GIPHY_API_KEY=your_actual_api_key_here
   ```

## 📚 Documentation

- **[Backend API Documentation](giphy-api/README.md)** - Complete Laravel Sail setup and API guide
- **[Swagger UI](http://localhost:80/api/documentation)** - Interactive API documentation
- **[OpenAPI Specification](giphy-api/storage/api-docs/api-docs.json)** - Machine-readable API spec

## 🛠️ Development Overview

Currently this is the way things are setup. Pretty standard. The backend uses OpenAPI and Swagger powered by Laravel. 

```
User (1) ←→ (Many) Gif
User (1) ←→ (Many) Rating  
User (1) ←→ (Many) Comment
Gif (1) ←→ (Many) Rating
Gif (1) ←→ (Many) Comment
```

### Frontend Development
*Frontend development setup coming soon...*

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- [Giphy](https://giphy.com/) for providing the GIF API
- [Laravel](https://laravel.com/) for the amazing PHP framework
- [Laravel Sail](https://laravel.com/docs/sail) for the Docker development environment
- [Laravel Sanctum](https://laravel.com/docs/sanctum) for API authentication 

---

![Giphy GIF](giphy.gif)

