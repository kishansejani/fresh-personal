# Fresh Personal Admin Panel Boilerplate

A clean, modular, and modern Laravel Admin Panel starter template featuring:

- **Administration Modules**:
  - **Users**: User account creation, editing, role assignment, status toggling, and search.
  - **Roles & Permissions**: Granular permission matrix, custom role management, and assignment.
  - **Settings**: Dynamic theme color customization, dark/light mode switcher, brand identity, and footer settings.
- **Admin Authentication**: Secure login/logout with email or phone number.
- **Modern Dashboard**: Responsive statistics, recent user overview, system environment information, and command palette (Ctrl+K).

---

## Default Credentials

- **Super Admin**:
  - Email: `superadmin@grocery.com`
  - Password: `admin123`

- **Store Admin**:
  - Email: `admin@grocery.com`
  - Password: `admin123`

---

## Installation & Setup

1. **Clone repository**:
   ```bash
   git clone https://github.com/kishansejani/fresh-personal.git
   cd fresh-personal
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Configure Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Run Migrations & Seeders**:
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Start Development Server**:
   ```bash
   php artisan serve
   ```
