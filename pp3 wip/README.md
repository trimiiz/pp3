# TechStore - E-Commerce Website

A complete PHP-based e-commerce website for selling electronic products (phones, TVs, laptops, tablets) with a full admin dashboard.

## Features

✅ **PHP Requirements Met:**
- Variables used throughout
- Functions with parameters
- Conditionals (if/else statements)
- Loops (for, foreach)
- PDO database connection
- CRUD operations (Create, Read, Update, Delete)

✅ **Frontend Features:**
- Interactive hero slider with auto-advance
- JavaScript events (click, hover, form validation)
- Responsive design
- Modern UI with smooth animations

✅ **Admin Dashboard:**
- Statistics overview
- Product management (CRUD)
- Category management (CRUD)
- Data tables with actions

## Installation

### 1. Database Setup

1. Create a MySQL database:
```sql
CREATE DATABASE ecommerce_db;
```

2. Import the schema:
   - Open `database/schema.sql` in your MySQL client
   - Or run: `mysql -u root -p ecommerce_db < database/schema.sql`

3. Update database credentials in `config/database.php`:
```php
$db_host = 'localhost';
$db_name = 'ecommerce_db';
$db_user = 'root';
$db_pass = ''; // Your MySQL password
```

### 2. Web Server Setup

1. Place all files in your web server directory:
   - XAMPP: `C:\xampp\htdocs\pp3 wip\`
   - WAMP: `C:\wamp64\www\pp3 wip\`
   - MAMP: `/Applications/MAMP/htdocs/pp3 wip/`

2. Ensure PHP 7.4+ is installed with PDO MySQL extension enabled

3. Start your web server (Apache) and MySQL

### 3. Access the Website

- **Homepage**: `http://localhost/pp3 wip/index.php`
- **Products**: `http://localhost/pp3 wip/products.php`
- **Admin Dashboard**: `http://localhost/pp3 wip/admin/index.php`

## Default Admin Credentials

The database includes a default admin user:
- Username: `admin`
- Password: `admin123` (hashed in database)

*Note: For production, change the default password and implement proper authentication.*

## Project Structure

```
pp3 wip/
├── config/
│   └── database.php          # PDO database configuration
├── database/
│   └── schema.sql            # Database schema
├── includes/
│   ├── functions.php         # Reusable functions
│   ├── header.php            # Site header
│   └── footer.php            # Site footer
├── admin/
│   ├── index.php             # Admin dashboard
│   ├── products.php          # Product CRUD
│   └── categories.php        # Category CRUD
├── assets/
│   ├── css/
│   │   ├── style.css         # Main styles
│   │   └── admin.css         # Admin styles
│   └── js/
│       └── main.js           # JavaScript functions
├── index.php                 # Homepage
├── products.php              # Products listing
├── product-detail.php        # Product details
├── PROJECT_DESIGN.md         # Detailed design document
└── README.md                 # This file
```

## Key PHP Features Demonstrated

### Variables
- `$product_id`, `$product_name`, `$categories`, `$message`, etc.

### Functions with Parameters
- `getAllProducts($category_id)`
- `getProductById($product_id)`
- `formatPrice($price)`
- `sanitizeInput($data)`
- `validateProduct($data)`

### Conditionals
- Product existence checks
- Stock availability checks
- Form validation
- Display logic based on conditions

### Loops
- **foreach**: Iterating through products, categories
- **for**: Creating slider dots, limiting items

### PDO Connection
- Secure database connection in `config/database.php`
- Prepared statements to prevent SQL injection
- Error handling

### CRUD Operations
- **Create**: Add new products/categories
- **Read**: Display products, categories, statistics
- **Update**: Edit existing products/categories
- **Delete**: Remove products/categories

## JavaScript Features

- Hero slider with navigation
- Auto-advancing slides
- Form validation
- Image zoom on product detail
- Hover effects
- Event listeners for interactions

## Customization

### Adding Products
1. Go to Admin Dashboard → Manage Products
2. Click "Add New Product"
3. Fill in the form and submit

### Changing Colors
Edit CSS variables in `assets/css/style.css`:
```css
:root {
    --primary-color: #2563eb;
    --secondary-color: #64748b;
    /* ... */
}
```

## Troubleshooting

### Database Connection Error
- Check MySQL is running
- Verify credentials in `config/database.php`
- Ensure database exists and schema is imported

### Images Not Displaying
- Product images use placeholder URLs
- Replace with actual image URLs or upload images to server

### PHP Errors
- Ensure PHP version is 7.4 or higher
- Check PDO MySQL extension is enabled
- Verify file permissions

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## License

This project is created for educational purposes.

## Support

For issues or questions, refer to `PROJECT_DESIGN.md` for detailed documentation.
