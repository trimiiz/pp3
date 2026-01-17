# E-Commerce Website Project Design

## Project Overview
This is a PHP-based e-commerce website called "TechStore" that sells electronic products including phones, TVs, laptops, and tablets. The website includes a customer-facing storefront and an admin dashboard for managing products and categories.

## Technology Stack
- **Backend**: PHP 7.4+
- **Database**: MySQL (using PDO)
- **Frontend**: HTML5, CSS3, JavaScript
- **Architecture**: MVC-inspired structure

## Project Structure
```
pp3 wip/
├── config/
│   └── database.php          # PDO database configuration
├── database/
│   └── schema.sql            # Database schema and sample data
├── includes/
│   ├── functions.php         # Reusable PHP functions
│   ├── header.php            # Common header
│   └── footer.php            # Common footer
├── admin/
│   ├── index.php             # Admin dashboard
│   ├── products.php          # Product CRUD operations
│   └── categories.php        # Category CRUD operations
├── assets/
│   ├── css/
│   │   ├── style.css         # Main stylesheet
│   │   └── admin.css         # Admin panel styles
│   └── js/
│       └── main.js           # JavaScript for events and sliders
├── index.php                 # Home page
├── products.php              # Products listing page
├── product-detail.php        # Product detail page
└── PROJECT_DESIGN.md         # This file
```

## Database Design

### Tables

1. **categories**
   - id (INT, PRIMARY KEY, AUTO_INCREMENT)
   - name (VARCHAR(100))
   - description (TEXT)
   - created_at (TIMESTAMP)

2. **products**
   - id (INT, PRIMARY KEY, AUTO_INCREMENT)
   - name (VARCHAR(255))
   - description (TEXT)
   - price (DECIMAL(10,2))
   - category_id (INT, FOREIGN KEY)
   - image_url (VARCHAR(500))
   - stock_quantity (INT)
   - created_at (TIMESTAMP)
   - updated_at (TIMESTAMP)

3. **users**
   - id (INT, PRIMARY KEY, AUTO_INCREMENT)
   - username (VARCHAR(50), UNIQUE)
   - email (VARCHAR(100), UNIQUE)
   - password (VARCHAR(255))
   - role (ENUM: 'admin', 'customer')
   - created_at (TIMESTAMP)

## Features Implementation

### 1. PHP Requirements

#### Variables
- Used throughout all PHP files for storing data
- Examples: `$product_id`, `$product_name`, `$categories`, `$message`

#### Functions
- `getDBConnection()` - Establishes PDO connection
- `getAllProducts($category_id)` - Retrieves products from database
- `getProductById($product_id)` - Gets single product
- `getAllCategories()` - Retrieves all categories
- `formatPrice($price)` - Formats price with currency
- `isInStock($stock_quantity)` - Checks stock availability
- `getStockStatus($stock_quantity)` - Returns stock status message
- `sanitizeInput($data)` - Sanitizes user input
- `validateProduct($data)` - Validates product data

#### Parameters
- Functions accept parameters: `getAllProducts($category_id)`, `getProductById($product_id)`, etc.
- URL parameters: `?category=1`, `?id=5`, `?action=edit`
- Form POST parameters for CRUD operations

#### Conditionals
- `if/else` statements throughout for:
  - Checking if products exist
  - Validating user input
  - Determining stock status
  - Displaying different content based on conditions

#### Loops
- **foreach loops**: Used extensively to iterate through:
  - Categories array
  - Products array
  - Form options
- **for loops**: Used for:
  - Creating slider dots
  - Limiting displayed items

### 2. CRUD Operations

#### Create
- Forms in `admin/products.php` and `admin/categories.php`
- POST method with validation
- PDO prepared statements for security

#### Read
- Product listing on `products.php`
- Product details on `product-detail.php`
- Dashboard statistics on `admin/index.php`

#### Update
- Edit forms pre-populated with existing data
- Update queries using PDO prepared statements

#### Delete
- Delete buttons with confirmation dialogs
- Cascade deletes for related records

### 3. PDO Database Connection

- Centralized connection in `config/database.php`
- Uses PDO with error handling
- Prepared statements to prevent SQL injection
- Connection options for error reporting and fetch modes

### 4. Events (JavaScript)

- Slider navigation (click events)
- Form submission validation
- Image zoom on click
- Hover effects on product cards
- Auto-advancing slider
- Search functionality (keypress events)

### 5. Sliders

- Hero slider on homepage with:
  - Multiple slides
  - Navigation buttons
  - Dot indicators
  - Auto-advance functionality
  - Pause on hover

### 6. Dashboard

- Statistics cards showing:
  - Total products
  - Total categories
  - Total stock
  - Low stock items
- Recent products table
- Navigation to CRUD pages
- Responsive sidebar navigation

## User Interface Design

### Color Scheme
- Primary: Blue (#2563eb)
- Secondary: Gray (#64748b)
- Success: Green (#10b981)
- Danger: Red (#ef4444)
- Background: Light gray (#f8fafc)

### Layout
- Responsive grid system
- Card-based product display
- Sticky header navigation
- Sidebar filters on products page
- Two-column layout for product details

### Components
- Product cards with hover effects
- Stock status badges
- Category filter sidebar
- Breadcrumb navigation
- Admin dashboard with statistics

## Security Features

1. **Input Sanitization**: All user inputs are sanitized
2. **PDO Prepared Statements**: Prevents SQL injection
3. **HTML Escaping**: Output is escaped to prevent XSS
4. **Form Validation**: Server-side validation for all forms

## Future Enhancements

1. User authentication system
2. Shopping cart functionality
3. Order management
4. Payment integration
5. Product search functionality
6. Product image upload
7. Customer reviews
8. Wishlist feature

## Setup Instructions

1. **Database Setup**:
   - Create MySQL database
   - Import `database/schema.sql`
   - Update credentials in `config/database.php`

2. **Web Server**:
   - Place files in web server directory (htdocs, www, etc.)
   - Ensure PHP 7.4+ is installed
   - Enable PDO MySQL extension

3. **Access**:
   - Homepage: `http://localhost/index.php`
   - Admin Dashboard: `http://localhost/admin/index.php`

## Testing Checklist

- [x] Database connection works
- [x] Products display correctly
- [x] Categories filter works
- [x] Product detail page loads
- [x] Admin dashboard shows statistics
- [x] Create product form works
- [x] Update product form works
- [x] Delete product works
- [x] Slider auto-advances
- [x] All loops execute correctly
- [x] Conditionals work as expected
- [x] Functions return correct data
