Restaurant Management system: Project Structure

restaurant-management/
├── config/
│   └── db.php              // Database connection configuration
├── controllers/
│   └── authController.php   // Handles predefined admin login credentials
│   └── menuController.php   // CRUD operations for menu items
│   └── orderController.php  // CRUD operations for orders
├── pages/
│   ├── login.php            // Admin login page
│   ├── dashboard.php        // Admin dashboard
│   ├── menu_items.php       // Page for viewing and managing menu items
│   ├── orders.php           // Page for viewing and managing orders
├── public/
│   ├── index.php            // Entry point, includes navigation
│   ├── assets/
│   │   ├── css/
│   │   │   └── styles.css    // CSS styles for UI
│   │   └── js/
│   │       └── scripts.js    // JavaScript, optional for interactivity
├── models/
│   ├── MenuItem.php         // Model for menu item operations
│   └── Order.php            // Model for order operations
├── sql/
│   └── schema.sql           // Database schema definition
└── README.md                // Project documentation
