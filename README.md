# 🚖 RideSharing App - Laravel + Vue.js

A real-time ride-sharing application built with Laravel backend and Vue.js frontend, featuring WebSocket connections, live GPS tracking, payment system and real-time notifications.

## 📋 Table of Contents
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Real-time Features](#real-time-features)
- [Usage](#usage)
- [Testing](#testing)

## ✨ Features

### 👤 User Roles
- **Passenger**: Request rides, track drivers,
- **Driver**: Accept ride requests, navigate routes, update availability

### 🗺️ Map & Navigation
- Google Maps integration
- Real-time GPS tracking of drivers and passengers
- Pickup and drop-off location selection

### 🔔 Real-time Features
- Live ride request notifications
- Driver location updates every 5 seconds
- Ride status updates (requested, accepted, arriving, in-progress, completed)

## 🛠️ Tech Stack

### Backend (Laravel 10+)
- **PHP 8.2+**
- **Laravel 12+** - PHP framework
- **Laravel Reverb** - WebSocket server
- **Laravel Sanctum** - API authentication
- **Laravel Echo** - WebSocket client
- **MySQLite** - Database

### Frontend (Vue.js 3)
- **Vue.js 3** - Progressive JavaScript framework
- **Pinia** - State management
- **Vue Router** - Client-side routing
- **Tailwind CSS** - Utility-first CSS framework
- **Google Maps JavaScript API** - Maps and geolocation
- **Laravel Echo** - WebSocket client
- **Axios** - HTTP client

### Additional Services
- **Google Maps Platform** - Maps, Routes, Places
- **Cashier(Stripe) Platform** - one-time payment,...
- **Paystack Platform** - one-time payment, webhook...

## 📋 Prerequisites

Before installation, ensure you have:
- PHP 8.2 or higher
- Composer
- Node.js 16+ and npm/yarn
- Google Maps API key
- Stripe API key
- Paystack API key

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/ayowandeapp/ride-sharing-app.git
cd ride-sharing-app
```

### 2. Backend Setup
```bash
# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate --seed

# Install Laravel Reverb
php artisan install:broadcasting
```

### 3. Frontend Setup
```bash
# Install Node.js dependencies
npm install

# Build assets
npm run build
```

## ⚙️ Configuration

### Google Maps API Setup
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Enable these APIs:
   - Maps JavaScript API
   - Directions API
   - Geocoding API
   - Places API
3. Create API key with restrictions
4. Add key to `echo` file


## 🎮 Usage

### Starting the Application
```bash
# Terminal 1 - Backend server
php artisan serve

# Terminal 2 - Reverb WebSocket server
php artisan reverb:start

# Terminal 3 - Frontend development
npm run dev
```

### Development Workflow
1. **For Passengers:**
   - Register/Login as passenger
   - Set pickup and drop-off locations
   - Request a ride
   - Track driver in real-time

2. **For Drivers:**
   - Register/Login as driver
   - Submit vehicle details
   - Accept ride requests
   - Navigate to pickup/drop-off
   - Complete rides
```

## 📄 License

Happy Coding! 🚀