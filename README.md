## Quick Start

```bash
# Navigate to project
cd C:\laragon\www\voting-management-system

# Create database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS votingmanagementsystem;"

# Import schema
mysql -u root votingmanagementsystem < database\schema.sql

# Copy environment file
copy .env.example .env

# Start development server
php -S localhost:8000 -t public
```

Open browser: **http://localhost:8000**

Default logins:
- Admin: `admin` / `password`
- Student: `student` / `password`

## Milestone 1 Complete
- Strict MVC structure with reusable layouts and partials
- Router + middleware-ready request flow
- Base controller and base model
- PDO database connection configuration
- Bootstrap 5 integrated UI shell
- Session-based authentication scaffolding

## Milestone 2 Complete
- Real admin/student authentication from `users` table
- Password verification using `password_verify`
- Validation errors and flash notifications on login
- Role-based route protection (`admin` and `student`)
- Secure logout and unauthorized access protection

## Milestone 3 Complete
- Admin dashboard cards now use real database counts
- Added chart-ready JSON endpoints for dashboard analytics
- Added line chart visualization for vote trend (last 7 days)
- Updated schema with core tables required for dashboard metrics

## Milestone 4 Complete
- Election Management module with MVC CRUD operations
- Activate/deactivate election workflow with single-active enforcement
- Bootstrap table listing with create/edit modals and action buttons
- Form validation for required fields and schedule consistency

## Milestone 5 Complete
- Position Management module with MVC CRUD operations
- Maximum vote validation (`max_votes` must be an integer >= 1)
- Responsive Bootstrap table with create/edit forms and delete action
- Added `positions` table schema for position records

## Milestone 6 Complete
- Party List module with MVC CRUD operations
- Bootstrap card-based listing with modal forms for create/edit
- Server-side validation for party name and description length
- Added `partylists` table schema for party records
