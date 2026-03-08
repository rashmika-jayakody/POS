# POS System Documentation

## Table of Contents
1. [System Overview](#system-overview)
2. [POS Types](#pos-types)
3. [Features by POS Type](#features-by-pos-type)
4. [System Owner Features](#system-owner-features)
5. [User Flow](#user-flow)
6. [Database Structure](#database-structure)
7. [Key Functionalities](#key-functionalities)
8. [API Endpoints](#api-endpoints)

---

## System Overview

This is a multi-tenant Point of Sale (POS) system that supports two types of businesses:
- **Retail POS**: For grocery stores, retail shops, and inventory-based businesses
- **Restaurant POS**: For restaurants, cafes, and food service businesses

The system is built with Laravel and supports:
- Multi-tenancy (multiple businesses on one platform)
- Role-based access control
- Real-time inventory management (for retail)
- Order management (for restaurants)
- Comprehensive reporting and analytics
- System owner dashboard for managing all tenants

---

## POS Types

### Retail POS
Designed for traditional retail businesses that:
- Manage physical inventory
- Track stock levels
- Handle product sales with immediate payment
- Require inventory tracking and stock management

### Restaurant POS
Designed for food service businesses that:
- Manage menu items (not physical inventory)
- Handle table management
- Support "pay after dining" functionality
- Track orders from kitchen to table
- Support order modifiers and special instructions

---

## Features by POS Type

### Retail POS Features

#### 1. Cash Drawer
- **Location**: `/cash-drawer`
- **Features**:
  - Product search and selection
  - Cart management (add, remove, update quantities)
  - Stock level checking (prevents selling out-of-stock items)
  - Multiple payment methods (Cash, Card, Mobile Wallet, Gift Card)
  - Invoice generation
  - Receipt printing
  - Hold bills functionality
  - Refund processing
  - Real-time stock deduction

#### 2. Inventory Management
- **Product Management**:
  - Add/edit products with cost price, selling price
  - Barcode scanning support
  - Product categories
  - Stock level tracking
  - Low stock alerts
  - Product images
  - Active/inactive status

- **Stock Management**:
  - Stock levels per branch
  - Stock batches with expiry dates
  - GRN (Goods Received Note) processing
  - Stock valuation reports
  - Expiry tracking

#### 3. Suppliers
- Supplier management
- Purchase order tracking
- Supplier contact information

#### 4. Categories & Units
- Product categorization
- Unit of measurement management

#### 5. Reports
- Sales Summary (daily, weekly, monthly)
- Profit & Loss statements
- Itemwise sales reports
- Categorywise sales reports
- Cash summary (payment method breakdown)
- Stock valuation
- Expiry tracking

### Restaurant POS Features

#### 1. Restaurant Cash Drawer
- **Location**: `/restaurant-cash-drawer`
- **Features**:
  - Menu item selection (no stock limits)
  - Optional table assignment
  - Optional waiter assignment
  - Special instructions for orders
  - Order modifiers support
  - **Send to Kitchen** (create unpaid orders)
  - **Process Payment** (immediate payment)
  - Pending payments management
  - Glassmorphism UI with macOS-style modals
  - Draggable modals
  - Smooth animations and transitions

#### 2. Table Management
- **Location**: `/restaurant/tables`
- **Features**:
  - Create and manage restaurant tables
  - Floor layout mapping
  - Table capacity settings
  - Table status (available, occupied, reserved)
  - Position tracking (x, y coordinates for floor plans)

#### 3. Order Management
- **Location**: `/restaurant/orders`
- **Features**:
  - View all orders
  - Order status tracking:
    - `pending`: Initial order
    - `confirmed`: Sent to kitchen (unpaid)
    - `preparing`: Kitchen is preparing
    - `ready`: Order ready for serving
    - `served`: Order served to table
    - `completed`: Order paid and completed
  - Order details (items, modifiers, special instructions)
  - Customer information (name, phone)
  - Split bill support
  - Guest count tracking

#### 4. Kitchen Display System (KDS)
- **Location**: `/restaurant/kitchen`
- **Features**:
  - Real-time order display
  - Color-coded order status
  - Order prioritization
  - Update order status:
    - Start Preparing
    - Mark Ready
    - Mark Served
  - Display table and waiter information
  - Show special instructions per item

#### 5. Reservations
- **Location**: `/restaurant/reservations`
- **Features**:
  - Create reservations
  - Assign tables to reservations
  - Guest count management
  - Reservation status tracking
  - Customer association

#### 6. Customer Management
- **Location**: `/restaurant/customers`
- **Features**:
  - Customer profiles
  - Order history
  - Loyalty points tracking
  - Total spent tracking
  - Dietary preferences
  - Customer notes

#### 7. Menu Management
- **Location**: `/products` (restaurant mode)
- **Features**:
  - Menu items (no stock tracking)
  - Menu categories
  - Product modifiers (e.g., "Extra cheese", "No onions")
  - Dynamic pricing support
  - Special instructions support

#### 8. Reports
- Restaurant-specific sales reports
- Popular items analysis
- Employee performance (sales per server)
- Table turnover reports

---

## System Owner Features

The system owner has access to all tenants and can manage the entire platform.

### Dashboard
- **Location**: `/dashboard`
- **Features**:
  - **Tabs**: All Data | Retail Only | Restaurant Only
  - Sales statistics (this week vs last week)
  - User counts (filtered by POS type)
  - Stock levels (retail only)
  - Location counts
  - Sales charts (last 7 days)
  - Expense charts (by category)
  - Stock valuation charts

### Tenant Management
- **Location**: `/tenants`
- **Features**:
  - View all registered shops/restaurants
  - POS type indicators (Retail/Restaurant badges)
  - Tenant status management (active/suspended)
  - Tenant details (branches, users)
  - Summary statistics (Retail count, Restaurant count, Total)

### User Management
- **Location**: `/users`
- **Features**:
  - **Tabs**: All Users | Retail Users | Restaurant Users
  - View all users across all tenants
  - Filter by POS type
  - User details (name, email, role, branch, status)
  - POS type badges for each user
  - Shop/Tenant column

### Location Management
- **Location**: `/branches`
- **Features**:
  - **Tabs**: All Locations | Retail Locations | Restaurant Locations
  - View all branches across all tenants
  - Filter by POS type
  - Branch details (name, address, phone, staff count)
  - POS type badges for each branch
  - Shop/Tenant column

### Reports
- All reports support POS type filtering
- System-wide analytics
- Cross-tenant comparisons

---

## User Flow

### Retail POS Flow

```
1. User Login
   ↓
2. Select Branch (if multiple)
   ↓
3. Open Cash Drawer
   ↓
4. Search/Select Products
   ↓
5. Add to Cart (with stock validation)
   ↓
6. Process Payment
   ├─ Cash
   ├─ Card
   ├─ Mobile Wallet
   └─ Gift Card
   ↓
7. Generate Invoice
   ↓
8. Print Receipt
   ↓
9. Stock Automatically Deducted
```

### Restaurant POS Flow

#### Order Creation Flow
```
1. User Login
   ↓
2. Open Restaurant Cash Drawer
   ↓
3. (Optional) Select Table
   ↓
4. (Optional) Select Waiter
   ↓
5. Select Menu Items
   ↓
6. Add Modifiers (if applicable)
   ↓
7. Add Special Instructions
   ↓
8. Choose Action:
   ├─ Send to Kitchen (Unpaid Order)
   │   ↓
   │   Order Status: confirmed
   │   ↓
   │   Appears in KDS
   │   ↓
   │   Kitchen Prepares
   │   ↓
   │   Order Status: preparing → ready → served
   │   ↓
   │   Customer Pays Later
   │   ↓
   │   Order Status: completed
   │
   └─ Process Payment (Immediate)
       ↓
       Order Status: completed
       ↓
       Receipt Generated
```

#### Pending Payment Flow
```
1. View Pending Payments
   ↓
2. Select Unpaid Order
   ↓
3. Load Order into POS
   ↓
4. Process Payment
   ↓
5. Mark Order as Completed
```

### System Owner Flow

```
1. System Owner Login
   ↓
2. Dashboard (All Data View)
   ↓
3. Select Filter:
   ├─ All Data
   ├─ Retail Only
   └─ Restaurant Only
   ↓
4. View Filtered Data:
   ├─ Dashboard Metrics
   ├─ Users (with POS type badges)
   ├─ Locations (with POS type badges)
   ├─ Tenants (with POS type badges)
   └─ Reports (filtered by POS type)
```

---

## Database Structure

### Core Tables

#### Tenants
- `id`, `name`, `slug`, `email`, `phone`, `address`
- `status` (active/suspended)
- `plan`
- **`pos_type`** (retail/restaurant) - **Key field for separation**

#### Users
- `id`, `name`, `email`, `password`
- `tenant_id`, `branch_id`
- `is_active`
- Roles: `system_owner`, `business_owner`, `branch_manager`, `cashier`

#### Branches
- `id`, `tenant_id`, `name`, `address`, `phone`
- `is_active`

### Retail-Specific Tables

#### Products
- `id`, `tenant_id`, `category_id`, `unit_id`
- `name`, `code`, `barcode`
- `cost_price`, `selling_price`
- `is_active`, `has_modifiers`

#### Stock
- `id`, `tenant_id`, `branch_id`, `product_id`
- `quantity`
- Real-time stock tracking

#### Stock Batches
- `id`, `tenant_id`, `branch_id`, `product_id`
- `quantity`, `expiry_date`
- Batch tracking for expiry management

#### Sales
- `id`, `tenant_id`, `branch_id`, `user_id`
- `invoice_no`, `sale_date`
- `subtotal`, `discount_total`, `tax_total`, `grand_total`
- `payment_method`

#### Sale Items
- `id`, `sale_id`, `product_id`
- `qty`, `unit_price`, `line_total`
- `cost_price_at_sale`

### Restaurant-Specific Tables

#### Restaurant Tables
- `id`, `tenant_id`, `branch_id`
- `name`, `floor_section`, `capacity`
- `position_x`, `position_y`
- `status`, `is_active`, `notes`

#### Restaurant Orders
- `id`, `tenant_id`, `branch_id`
- `restaurant_table_id`, `user_id` (waiter), `customer_id`
- `customer_name`, `customer_phone`
- `order_no`, `order_type`
- **`status`**: pending, confirmed, preparing, ready, served, completed
- `guest_count`, `special_instructions`, `dietary_preferences`
- `subtotal`, `discount_total`, `tax_total`, `service_charge`, `grand_total`
- `is_split`, `split_count`
- **`is_paid`**: boolean (for pay later functionality)
- Timestamps: `confirmed_at`, `preparing_at`, `ready_at`, `served_at`, `completed_at`

#### Restaurant Order Items
- `id`, `restaurant_order_id`, `product_id`
- `qty`, `unit_price`, `line_total`
- `modifier_total`, `discount_amount`
- `special_instructions`

#### Order Modifiers
- `id`, `restaurant_order_item_id`, `product_modifier_id`
- `name`, `price_adjustment`, `quantity`, `notes`

#### Product Modifiers
- `id`, `tenant_id`, `product_id`
- `name`, `type`, `price_adjustment`
- `is_active`

#### Customers
- `id`, `tenant_id`
- `name`, `email`, `phone`, `address`
- `loyalty_points`, `total_spent`
- `last_visit_at`, `dietary_preferences`, `notes`

#### Reservations
- `id`, `tenant_id`, `branch_id`
- `restaurant_table_id`, `customer_id`
- `reservation_time`, `guest_count`
- `status`, `notes`

---

## Key Functionalities

### 1. Multi-Tenancy
- Each tenant operates independently
- Data isolation through `tenant_id` foreign keys
- Global scopes automatically filter by tenant
- System owner can bypass scopes to view all data

### 2. Role-Based Access Control
- **System Owner**: Full access to all tenants
- **Business Owner**: Full access to their tenant
- **Branch Manager**: Access to their branch
- **Cashier**: Limited to cash drawer operations

### 3. Stock Management (Retail)
- Real-time stock tracking
- Automatic deduction on sale
- Stock batch tracking with expiry dates
- Low stock alerts
- Stock valuation reports

### 4. Order Management (Restaurant)
- Order lifecycle tracking
- Kitchen Display System integration
- Pay later functionality
- Table and waiter assignment
- Special instructions and modifiers

### 5. Payment Processing
- Multiple payment methods
- Split payments support
- Refund processing
- Payment terminal integration ready

### 6. Reporting & Analytics
- Sales reports (daily, weekly, monthly)
- Profit & Loss statements
- Itemwise and categorywise analysis
- Stock valuation
- Employee performance tracking
- POS type filtering for system owner

### 7. UI/UX Features (Restaurant POS)
- Glassmorphism design (frosted backgrounds, blur effects)
- macOS-style modals (fade + scale + slide animations)
- Draggable modals
- Smooth transitions
- Close on outside click or Esc key
- Stacked modal support

---

## API Endpoints

### Authentication
- `POST /login` - User login
- `POST /logout` - User logout

### Dashboard
- `GET /dashboard` - Main dashboard
- `GET /dashboard?pos_type=retail` - Retail-only dashboard
- `GET /dashboard?pos_type=restaurant` - Restaurant-only dashboard

### Cash Drawer (Retail)
- `GET /cash-drawer` - Open cash drawer
- `POST /cash-drawer/open` - Open drawer session
- `POST /cash-drawer/close` - Close drawer session
- `GET /cash-drawer/status` - Get drawer status
- `POST /cash-drawer/process-return` - Process refund

### Restaurant Cash Drawer
- `GET /restaurant-cash-drawer` - Open restaurant POS
- `POST /restaurant/orders` - Create order
- `POST /restaurant/orders/{order}/pay` - Pay existing order
- `GET /restaurant/orders/{order}` - Get order details

### Table Management
- `GET /restaurant/tables` - List tables
- `POST /restaurant/tables` - Create table
- `PUT /restaurant/tables/{table}` - Update table
- `DELETE /restaurant/tables/{table}` - Delete table

### Order Management
- `GET /restaurant/orders` - List orders
- `GET /restaurant/orders/{order}` - View order
- `PUT /restaurant/orders/{order}/status` - Update order status

### Kitchen Display System
- `GET /restaurant/kitchen` - KDS view
- `PUT /restaurant/orders/{order}/status` - Update order status

### Customer Management
- `GET /restaurant/customers` - List customers
- `POST /restaurant/customers` - Create customer
- `PUT /restaurant/customers/{customer}` - Update customer
- `GET /restaurant/customers/{customer}` - View customer

### Reservations
- `GET /restaurant/reservations` - List reservations
- `POST /restaurant/reservations` - Create reservation
- `PUT /restaurant/reservations/{reservation}` - Update reservation
- `GET /restaurant/reservations/{reservation}` - View reservation

### Products
- `GET /products` - List products (menu items for restaurant)
- `POST /products` - Create product
- `PUT /products/{product}` - Update product
- `DELETE /products/{product}` - Delete product

### Users
- `GET /users` - List users
- `GET /users?pos_type=retail` - Retail users only
- `GET /users?pos_type=restaurant` - Restaurant users only
- `POST /users` - Create user
- `PUT /users/{user}` - Update user
- `DELETE /users/{user}` - Delete user

### Branches (Locations)
- `GET /branches` - List branches
- `GET /branches?pos_type=retail` - Retail branches only
- `GET /branches?pos_type=restaurant` - Restaurant branches only
- `POST /branches` - Create branch
- `PUT /branches/{branch}` - Update branch
- `DELETE /branches/{branch}` - Delete branch

### Tenants (System Owner)
- `GET /tenants` - List all tenants
- `GET /tenants/{tenant}` - View tenant details
- `PUT /tenants/{tenant}` - Update tenant status

### Reports
- `GET /reports/sales-summary` - Sales summary report
- `GET /reports/profit-loss` - Profit & Loss report
- `GET /reports/itemwise-sales` - Itemwise sales report
- `GET /reports/categorywise-sales` - Categorywise sales report
- `GET /reports/cash-summary` - Cash summary report
- `GET /reports/stock-valuation` - Stock valuation report
- `GET /reports/expiry-tracking` - Expiry tracking report

All report endpoints support `?pos_type=retail` or `?pos_type=restaurant` filtering for system owners.

---

## Technical Stack

- **Backend**: Laravel (PHP)
- **Frontend**: Blade Templates, JavaScript, Chart.js
- **Database**: MySQL
- **Authentication**: Laravel Auth
- **Authorization**: Spatie Laravel Permission
- **Multi-tenancy**: Custom BelongsToTenant trait with global scopes

---

## Future Enhancements

### Planned Features
1. Split bill functionality (by items, percentage, guests)
2. Loyalty program integration
3. Online ordering integration (Uber Eats, DoorDash)
4. Delivery tracking
5. Dynamic pricing (happy hour, discounts)
6. Advanced analytics dashboard
7. Mobile app support
8. Payment terminal integration
9. Email/SMS notifications
10. Advanced reporting with custom date ranges

---

## Notes

- **Stock Management**: Only applies to Retail POS. Restaurant POS uses menu items without stock tracking.
- **Pay Later**: Restaurant POS supports creating orders without immediate payment. Orders can be paid later through the "Pending Payments" feature.
- **System Owner**: Has special privileges to view and filter all data by POS type across all tenants.
- **UI Design**: Restaurant POS features advanced glassmorphism UI with macOS-style modals for better user experience.

---

*Last Updated: 2026-03-05*
