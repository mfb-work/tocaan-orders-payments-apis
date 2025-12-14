# Extendable Order & Payment API (Laravel)

A clean, extensible RESTful API built with Laravel that manages Orders and Payments using a flexible, strategy-based payment architecture.

---

## ✨ Features

- JWT Authentication (Secure APIs)
- Orders Management
  - Create / Update / Delete Orders
  - Order Items support
  - Automatic total amount calculation
  - Filtering & Pagination
- Payments Management
  - Process payments for confirmed orders only
  - Multiple payment gateways (Strategy Pattern)
  - View all payments or payments per order
- Extensible Payment Architecture
- Clean error handling
- Feature tests for core business rules

---

## 🛠 Tech Stack

- Laravel (API-only)
- PHP 8+
- MySQL / MariaDB
- tymon/jwt-auth (JWT Authentication)

---

## 🚀 Installation & Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
