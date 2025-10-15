# PMS-PHP - Pharmacy Management System

A comprehensive Pharmacy Management System built with PHP and MySQL for managing pharmacy operations, inventory, sales, and reports.

## Features

- **Medicine Management**: Add, edit, delete, and search medicines
- **Inventory Control**: Track stock levels, low stock alerts, expiry notifications
- **Sales Management**: Process sales, generate invoices, track payment methods
- **Purchase Management**: Record purchases, manage suppliers
- **Customer Management**: Maintain customer records and purchase history
- **Reports**: Sales reports, inventory reports, financial reports, expiry reports
- **User Management**: Role-based access control (Admin, Pharmacist, Cashier, Manager)
- **Dashboard**: Real-time statistics and quick actions
- **Barcode Support**: Barcode scanning for quick product lookup

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- PDO PHP Extension
- GD PHP Extension (for image processing)

## Installation

### 1. Clone or Download

Download the project files to your web server directory.

### 2. Database Setup

1. Create a new MySQL database:
```sql
CREATE DATABASE pms_database;
```

2. Import the database schema:
```bash
mysql -u root -p pms_database < database/schema.sql
```

### 3. Configuration

Edit `config/database.php` and update the database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'pms_database');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

Edit `config/constants.php` and update the application URL:

```php
define('APP_URL', 'http://localhost/pms-php');
```

### 4. File Permissions

Ensure the uploads directory is writable:

```bash
chmod 755 assets/uploads
chmod 755 assets/uploads/medicines
chmod 755 assets/uploads/invoices
```

### 5. Access the Application

Open your browser and navigate to:
```
http://localhost/pms-php
```

## Default Login Credentials

**Administrator:**
- Username: `admin`
- Password: `admin123`

**Pharmacist:**
- Username: `pharmacist1`
- Password: `admin123`

**Cashier:**
- Username: `cashier1`
- Password: `admin123`

**⚠️ Important:** Change these passwords immediately after first login!

## Project Structure

```
pms-php/
├── config/              # Configuration files
├── includes/            # Header, footer, sidebar, functions
├── classes/             # PHP classes (OOP)
├── modules/             # Feature modules
│   ├── auth/            # Authentication
│   ├── dashboard/       # Dashboard
│   ├── medicines/       # Medicine management
│   ├── inventory/       # Inventory management
│   ├── sales/           # Sales processing
│   ├── purchases/       # Purchase management
│   ├── customers/       # Customer management
│   ├── reports/         # Reports
│   └── settings/        # System settings
├── assets/              # CSS, JS, images
├── ajax/                # AJAX handlers
└── database/            # Database schema
```

## Usage

### Medicine Management

1. Navigate to **Medicines** → **Add Medicine**
2. Fill in medicine details (name, category, price, quantity, expiry date)
3. Upload medicine image (optional)
4. Click **Save**

### Processing a Sale

1. Navigate to **New Sale**
2. Search and add medicines to cart
3. Enter customer details (optional)
4. Apply discount if needed
5. Select payment method
6. Click **Complete Sale**
7. Print invoice

### Purchase Management

1. Navigate to **New Purchase**
2. Select supplier
3. Add medicines with quantities and prices
4. Enter payment details
5. Click **Save Purchase**

### Inventory Monitoring

- **Stock Levels**: View current stock of all medicines
- **Low Stock Alerts**: Automatic alerts when stock falls below reorder level
- **Expiry Alerts**: Notifications for medicines expiring within 90 days
- **Stock Updates**: Adjust stock levels manually when needed

### Reports

- **Sales Report**: Daily, weekly, monthly, yearly sales analysis
- **Inventory Report**: Current stock status and valuation
- **Financial Report**: Revenue, expenses, profit analysis
- **Expiry Report**: List of expiring and expired medicines

## User Roles & Permissions

- **Admin**: Full system access, user management, system settings
- **Manager**: Sales, purchases, reports, inventory management
- **Pharmacist**: Medicine management, sales, inventory
- **Cashier**: Sales processing, customer management

## Security Features

- Password hashing with bcrypt
- SQL injection prevention (PDO prepared statements)
- XSS protection (input sanitization)
- CSRF token validation
- Session management with timeout
- Role-based access control
- Activity logging

## Customization

### Adding New Medicine Categories

Edit `config/constants.php`:

```php
define('MEDICINE_CATEGORIES', [
    'Tablet' => 'Tablet',
    'Capsule' => 'Capsule',
    // Add more categories
]);
```

### Changing Tax Rate

Edit `config/constants.php`:

```php
define('TAX_RATE', 0.10); // 10% tax
```

### Adjusting Stock Alert Levels

Edit `config/constants.php`:

```php
define('LOW_STOCK_THRESHOLD', 50);
define('CRITICAL_STOCK_THRESHOLD', 20);
define('EXPIRY_ALERT_DAYS', 90);
```

## Troubleshooting

### Database Connection Error

- Verify database credentials in `config/database.php`
- Ensure MySQL service is running
- Check database exists and user has permissions

### Upload Issues

- Check file permissions on `assets/uploads/` directory
- Verify `upload_max_filesize` in php.ini
- Ensure disk space available

### Session Problems

- Check PHP session configuration
- Verify `session.save_path` is writable
- Clear browser cookies

## Future Enhancements

- [ ] Barcode generation and printing
- [ ] SMS notifications for low stock
- [ ] Email reports
- [ ] Multi-branch support
- [ ] Online ordering system
- [ ] Mobile app integration
- [ ] Prescription management
- [ ] Insurance claim processing

## Support

For issues and questions, please contact the development team.

## License

This project is licensed under the MIT License.

## Credits

Developed for modern pharmacy management.

---

**Version:** 1.0.0  
**Last Updated:** October 2025
