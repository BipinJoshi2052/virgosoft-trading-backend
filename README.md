# Trading Platform (Laravel Backend)

## 📌 Project Overview

This project is a **mini trading platform backend** built using **Laravel**.  
It simulates a **crypto-style limit order trading system** with:

- USD wallet balance
- Asset balances (BTC / ETH)
- Buy & Sell limit orders
- Order matching engine
- Trade execution with commission
- Admin-controlled order matching
- API support for SPA (Vue.js frontend)
- Blade views for direct browser access

The project was designed with **financial data integrity, atomic transactions, and scalability** in mind.

---

## 🏗 System Architecture

### Backend
- **Laravel (latest stable)**
- **MySQL / PostgreSQL**
- **Sanctum authentication**
- **Service-based business logic**
- **Repository-style separation**
- **Atomic DB transactions**

### Backend Views (Blade)
Accessible at:

```
http://localhost:8000
```

These views allow:
- User login
- Wallet and order inspection
- Order placement & cancellation
- Admin order matching
- Manual trade execution

### Frontend (SPA – Separate Project)
- Vue 3 + Vite + Tailwind
- Communicates via REST APIs
- Uses the same backend services & logic

---

## 🔑 Core Features

### Wallet & Assets
- USD balance per user
- Asset balances per symbol (BTC, ETH)
- Locked asset tracking for sell orders

### Orders
- Limit Buy & Sell orders
- Order statuses:
  - `1` → Open
  - `2` → Filled
  - `3` → Cancelled
- Order cancellation with locked fund release
- Order filtering

### Matching Engine
- Full match only (no partial matching)
- Matching rules:
  - Buy price ≥ Sell price
  - Same symbol
- Trade price = **Seller (maker) price**
- Atomic execution using DB transactions

### Commission
- Configurable via `.env`
- Default: **1.5%**
- Deducted consistently during trade execution


### Real-time Updates
- Used pusher for real time updates
- notifications are sent to private channels of users
---

## 📂 Database Structure

| Table | Description |
|-----|------------|
| users | Users with USD balance |
| assets | User assets & locked amounts |
| orders | Buy/Sell limit orders |
| trades | Executed trade records |

---

## 🔐 Authentication

- Laravel Sanctum
- Session-based authentication
- Used by:
  - Blade views
  - API consumers (Vue SPA)

---

## 🌐 API Endpoints

| Method | Endpoint | Description |
|------|--------|-------------|
| POST | /api/login | Login user |
| POST | /api/logout | Logout user |
| GET | /api/profile | Wallet & assets |
| GET | /api/orders | List orders |
| POST | /api/orders | Create order |
| POST | /api/orders/{id}/cancel | Cancel order |
| GET | /api/match-orders | List matching orders |
| POST | /api/match-orders/{buy}/{sell} | Execute trade |

---

## ⚙ Installation & Setup

### 1️⃣ Clone Repository

```bash
git clone <repository-url>
cd trading
```

### 2️⃣ Install Dependencies

```bash
composer install
```

### 3️⃣ Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env`:

```env
DB_DATABASE=your_db
DB_USERNAME=your_user
DB_PASSWORD=your_password

TRADING_COMMISSION=0.015
```

### 4️⃣ Run Migrations & Seeders

```bash
php artisan migrate --seed
```

Seeders will:
- Create two default users
- Create BTC & ETH assets for each user
- Assign initial balances

### 5️⃣ Start Server

```bash
php artisan serve
```

Visit:

```
http://localhost:8000
```

---

## 👥 Default Seeded Users

| Email | Password |
|-----|---------|
| alice@example.com | password |
| bob@example.com | password |

---

## 🧠 Design Decisions

- Business logic isolated in Services
- Controllers kept thin
- Database transactions for safety
- Locked balances prevent double-spending
- Same logic reused by Blade & API

---

## 🚀 Possible Enhancements

- Partial order matching
- Trade history per user
- Order book depth aggregation

---

## 📌 Notes for Reviewers

- Backend works independently using Blade views
- APIs are SPA-ready
- Vue frontend is intentionally separated
- Focus areas:
  - Data integrity
  - Clean architecture
  - Financial correctness
