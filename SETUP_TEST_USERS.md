# Setup Test Users for Partnership System

## Quick Setup

Run the following commands to set up the database with test users:

```bash
php artisan migrate
php artisan db:seed --class=AdminRoleSeeder
php artisan db:seed --class=TestUsersSeeder
```

Or run all seeders at once:

```bash
php artisan migrate
php artisan db:seed
```

## Test Users Created

### Admin Users

1. **Super Administrator**
   - Email: `superadmin@brighttoys.com`
   - Password: `password123`
   - Role: Super Admin (full access to everything)

2. **Finance Administrator**
   - Email: `finance@brighttoys.com`
   - Password: `password123`
   - Role: Finance Admin (can manage partners, financial records, documents, activity logs, and create admins)

3. **Store Administrator**
   - Email: `store@brighttoys.com`
   - Password: `password123`
   - Role: Store Admin (can manage products, categories, orders, customers, and support tickets)

4. **Multi Role Admin**
   - Email: `multirole@brighttoys.com`
   - Password: `password123`
   - Roles: Finance Admin + Store Admin (has both roles)

### Partner Users (10 Partners)

All partners have:
- Email: `partner1@brighttoys.com` through `partner10@brighttoys.com`
- Password: `password123`
- Ownership: 10% each (total 100%)
- Status: Active

## Role-Based Navigation

### Super Admin
- ✅ Dashboard
- ✅ Products
- ✅ Categories
- ✅ Orders
- ✅ Customers
- ✅ Admins
- ✅ Partners
- ✅ Financial
- ✅ Documents
- ✅ Activity Logs
- ✅ Support & Messages
- ✅ Profile
- ✅ Settings

### Finance Admin
- ✅ Dashboard
- ✅ Admins (can create/edit)
- ✅ Partners
- ✅ Financial
- ✅ Documents
- ✅ Activity Logs
- ✅ Profile
- ✅ Settings

### Store Admin
- ✅ Dashboard
- ✅ Products
- ✅ Categories
- ✅ Orders
- ✅ Customers
- ✅ Support & Messages
- ✅ Profile
- ✅ Settings

### Multi Role Admin (Finance + Store)
- ✅ Dashboard
- ✅ Products
- ✅ Categories
- ✅ Orders
- ✅ Customers
- ✅ Admins (can create/edit)
- ✅ Partners
- ✅ Financial
- ✅ Documents
- ✅ Activity Logs
- ✅ Support & Messages
- ✅ Profile
- ✅ Settings

## Testing the System

1. **Login as Super Admin** to see all navigation items
2. **Login as Finance Admin** to see only finance-related items
3. **Login as Store Admin** to see only store-related items
4. **Login as Partner** (partner1@brighttoys.com) to access partner dashboard

## Notes

- All test users have the same password: `password123`
- Partners are automatically linked to their user accounts
- Each partner has 10% ownership
- You can modify roles and permissions as needed
