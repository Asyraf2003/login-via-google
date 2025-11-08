```
# login-via-google

A minimal, ready-to-use **Laravel 12** example project implementing **Google OAuth2 Login** via Laravel Socialite.  
This repository is built for **learning and reference**, focusing on the **structure and logic flow** of Socialite integration — no UI styling or frontend libraries.

---

## 🔍 Project Preview

This project shows the **core process of Google OAuth login in Laravel**, from redirect to callback handling.  
No CSS, no JS, just functional backend flow — perfect for developers who want to **study the authentication mechanism itself**.

📸 **Recommended screenshot ideas for the preview section:**
- Terminal running `php artisan serve`  
- Browser showing the “Login via Google” raw link or callback success page  
- Optional: Database table view (showing name, email, google_id, avatar stored after login)

---

## ✨ Features

- Google Login integration via official **Laravel Socialite**
- Stores basic user info (name, email, Google ID, avatar)
- Clean, minimal Laravel 12 folder structure
- Works both locally and in production
- Ideal for studying or starting a Socialite-based project

---

## 🧱 Requirements

- **PHP 8.2+**
- **Laravel 12**
- **Composer**
- **Database** (MySQL, MariaDB, or SQLite)
- **Google Cloud Project** with an OAuth consent screen published

---

## 🚀 Quick Setup

```bash
# 1. Install dependencies
composer install

# 2. Copy environment example file
cp .env.example .env

# 3. Generate the application key
php artisan key:generate

# 4. Run migrations (if needed)
php artisan migrate
```

> If you’re using Valet, Nginx, or Docker, make sure your `APP_URL` matches your actual local domain.

---

## 🔧 Google OAuth Configuration

### 1. Create OAuth Credentials

Go to [Google Cloud Console → APIs & Services → Credentials](https://console.cloud.google.com/apis/credentials)

- Click **Create Credentials → OAuth 2.0 Client ID**
- Choose **Web Application**
- Add the following **Authorized redirect URIs**:

```
Local:      http://127.0.0.1:8000/auth/google/callback
Valet:      https://your-app.test/auth/google/callback
Production: https://your-domain.com/auth/google/callback
```

Copy your **Client ID** and **Client Secret**.

---

### 2. Configure Laravel Service

Ensure `config/services.php` includes:

```php
'google' => [
    'client_id'     => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect'      => env('GOOGLE_REDIRECT_URI'),
],
```

---

## ⚙️ Environment Setup (.env)

Open your `.env` file and configure as follows:

```dotenv
APP_NAME="Laravel Google Login"
APP_ENV=local
APP_URL=http://127.0.0.1:8000

# Database configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Google OAuth credentials
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

Clear cached configuration after saving:

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 🧪 Usage Flow

1. Start the Laravel development server:
   ```bash
   php artisan serve
   ```

2. Open your browser and visit:
   ```
   http://127.0.0.1:8000
   ```

3. Click the **Login via Google** link.

4. After authentication, you’ll be redirected back to the app via:
   ```
   /auth/google/callback
   ```
   and automatically logged in (user data stored in DB).

---

## 🧯 Troubleshooting

**redirect_uri_mismatch**  
Make sure the redirect URI in `.env` is identical to one registered in Google Cloud (no extra slashes, same protocol and host).

**invalid_client / unauthorized_client**  
Recheck your Google Client ID and Secret for typos or whitespace.

**State or CSRF errors**  
If testing without sessions or on different hosts, add `.stateless()` in your Socialite callback.

**Database not saving user info**  
Ensure migration includes `google_id` and `avatar` columns, and that `fillable` is set properly in the `User` model.

---

## 📄 License

This project is open-sourced under the [MIT License](https://opensource.org/licenses/MIT).

---

## 💡 Best Practices

- Keep your `.env` file private — never commit credentials.
- Always use HTTPS in production.
- Document all redirect URLs for each environment (local, staging, production).
- If extending this project, add error handling and token revocation.
- Perfect for developers who want to learn the **core logic** before moving on to styled or complex implementations.

---
```
