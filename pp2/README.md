# E-Commerce PHP Website

A comprehensive e-commerce website built with PHP and MySQL that sells TVs, phones, tablets, and laptops.

## Features Implemented

This project includes all the features listed in the PHP & MySQL presentation:

- ✅ **Loops (for, foreach)** - Used throughout for displaying products, categories, and pagination
- ✅ **Events** - JavaScript event handlers for cart, forms, and slider interactions
- ✅ **Sliders** - Hero slider on homepage with auto-advance and manual controls
- ✅ **Conditionals** - Extensive use of if/else statements for logic flow
- ✅ **Functions** - Reusable functions in `includes/functions.php`
- ✅ **Variables** - Proper variable usage throughout the application
- ✅ **Parameters** - GET/POST parameters for filtering, searching, and pagination
- ✅ **CRUD Forms** - Complete Create, Read, Update, Delete operations in admin panel
- ✅ **Project Design** - Modern, responsive UI with clean architecture
- ✅ **PHP Data Objects (PDO)** - Secure database connection using PDO
- ✅ **Dashboard** - Admin dashboard with statistics and management tools
- ✅ **User Sign Up** - Create new customer accounts
- ✅ **User Dashboard** - Profile, orders, wishlist for customers
- ✅ **Wishlist** - Add products to wishlist (logged-in users)
- ✅ **Checkout Auto-fill** - Pre-fill shipping info from user profile

## Installation

1. **Prerequisites:**
   - PHP 7.4 or higher
   - MySQL 5.7 or higher
   - Web server (Apache/Nginx) or PHP built-in server

2. **Database Setup:**
   - Update database credentials in `config/database.php` if needed
   - Run `php config/init_db.php` to create the database and sample data
   - **For existing installations:** Run `config/update_db.php` in your browser to add user profile fields and wishlist support
   - Default admin credentials:
     - Username: `admin`
     - Password: `admin123`

3. **Run the Application:**
   ```bash
   # Using PHP built-in server
   php -S localhost:8000
   ```
   Then open `http://localhost:8000` in your browser

## Project Structure

```
pp2/
├── config/
│   ├── database.php      # PDO database connection
│   └── init_db.php       # Database initialization script
├── includes/
│   ├── functions.php     # Utility functions
│   ├── header.php        # Site header
│   └── footer.php        # Site footer
├── admin/
│   ├── dashboard.php     # Admin dashboard
│   └── products.php      # Product CRUD management
├── assets/
│   ├── css/
│   │   └── style.css     # Main stylesheet
│   ├── js/
│   │   └── main.js       # JavaScript for events and interactions
│   └── images/           # Product images (add your images here)
├── index.php             # Homepage with slider
├── products.php          # Product listing page
├── product_detail.php    # Product detail page
├── cart.php              # Shopping cart
├── checkout.php          # Checkout process
├── order_success.php     # Order confirmation
├── login.php             # User login
├── logout.php            # Logout handler
└── add_to_cart.php       # AJAX cart handler
```

## Key Features

### Frontend
- Responsive design that works on all devices
- Interactive product slider on homepage
- Product search and category filtering
- Shopping cart with real-time updates
- Modern UI with smooth animations

### Backend
- Secure PDO database connections
- Session-based authentication
- Admin dashboard with statistics
- Complete CRUD operations for products
- Order management system

### Security
- Prepared statements to prevent SQL injection
- Input sanitization functions
- Password hashing
- Session management

## Usage

1. **Browse Products:** Navigate through categories or search for specific items
2. **Add to Cart:** Click "Add to Cart" on any product
3. **View Cart:** Check your cart and update quantities
4. **Checkout:** Complete the order form
5. **Admin Panel:** Login as admin to manage products and view orders

## Technologies Used

- PHP 7.4+
- MySQL
- PDO (PHP Data Objects)
- HTML5/CSS3
- JavaScript (Vanilla)
- Font Awesome Icons

## Notes

- Product images should be placed in `assets/images/` directory
- Default image filenames are used in the sample data
- You can add your own product images or use placeholder images
- The database will be created automatically when you run `init_db.php`
