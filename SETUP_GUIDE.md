# 🚀 Drone Delivery System - Setup & Running Guide

## ✅ Setup Complete!

Your Drone Delivery System is now properly configured and ready to use!

---

## 📋 Quick Start Guide

### **1. Start the Development Server**

```bash
php artisan serve
```

The application will be available at: **http://127.0.0.1:8000**

---

## 🔑 Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| **Administrator** | admin@drone.com | password123 |
| **Drone Operator** | operator@drone.com | password123 |
| **Hospital Admin** | hospital@drone.com | password123 |

---

## 🌐 Available URLs

### **Public Routes:**
- Homepage: `http://127.0.0.1:8000/`
- Login: `http://127.0.0.1:8000/login`
- Register: `http://127.0.0.1:8000/register`
- Public Tracking: `http://127.0.0.1:8000/track`

### **Admin Panel** (Login as: admin@drone.com):
- Dashboard: `http://127.0.0.1:8000/admin/dashboard`
- Medical Supplies: `http://127.0.0.1:8000/admin/supplies`
- Drones: `http://127.0.0.1:8000/admin/drones`
- Hospitals: `http://127.0.0.1:8000/admin/hospitals`
- Delivery Requests: `http://127.0.0.1:8000/admin/delivery-requests`
- Deliveries: `http://127.0.0.1:8000/admin/deliveries`
- Users: `http://127.0.0.1:8000/admin/users`
- Roles: `http://127.0.0.1:8000/admin/roles`

### **Hospital Portal** (Login as: hospital@drone.com):
- Dashboard: `http://127.0.0.1:8000/hospital/dashboard`
- Create Request: `http://127.0.0.1:8000/hospital/requests/create`
- Track Deliveries: `http://127.0.0.1:8000/hospital/deliveries`

### **Operator Portal** (Login as: operator@drone.com):
- Dashboard: `http://127.0.0.1:8000/operator/dashboard`
- My Deliveries: `http://127.0.0.1:8000/operator/deliveries`

### **Profile:**
- View Profile: `http://127.0.0.1:8000/profile`
- Edit Profile: `http://127.0.0.1:8000/profile/edit`

---

## 🔧 Troubleshooting Common Issues

### **Issue 1: "Route not found" or 404 errors**

**Solution:**
```bash
php artisan optimize:clear
php artisan route:cache
```

---

### **Issue 2: "CSRF token mismatch"**

**Solution:**
```bash
php artisan cache:clear
php artisan config:clear
```
Then refresh your browser and clear browser cache (Ctrl+Shift+Delete)

---

### **Issue 3: "Call to undefined method hasRole()"**

**Solution:**
```bash
php artisan db:seed --force
```
This creates the necessary roles and assigns them to users.

---

### **Issue 4: Database connection errors**

**Check your `.env` file:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=drone_delivery_system
DB_USERNAME=root
DB_PASSWORD=
```

**Make sure XAMPP MySQL is running!**

---

### **Issue 5: "Page expired" error on login**

**Solution:**
```bash
php artisan session:table
php artisan migrate
php artisan optimize:clear
```

---

### **Issue 6: Authentication not working**

**Verify users exist:**
```bash
php artisan tinker
>>> App\Models\User::count();
>>> App\Models\User::all(['email', 'name']);
>>> exit
```

**If no users, run:**
```bash
php artisan db:seed --force
```

---

## 📝 Common Commands

### **Development:**
```bash
# Start server
php artisan serve

# Clear all caches
php artisan optimize:clear

# View routes
php artisan route:list

# Check database connection
php artisan migrate:status
```

### **Database:**
```bash
# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Reset and reseed database
php artisan migrate:fresh --seed
```

### **Troubleshooting:**
```bash
# Clear everything
php artisan optimize:clear

# Regenerate autoload files
composer dump-autoload

# Check for errors
php artisan route:list
php artisan config:show database
```

---

## 🎯 Testing Your Setup

### **Step 1: Access Login Page**
Go to: `http://127.0.0.1:8000/login`

### **Step 2: Login as Admin**
- Email: `admin@drone.com`
- Password: `password123`

### **Step 3: Check Dashboard**
After login, you should see: `http://127.0.0.1:8000/admin/dashboard`

### **Step 4: Test Navigation**
Click through the menu items:
- Medical Supplies
- Drones
- Hospitals
- Deliveries
- Users
- Roles

All pages should load without errors!

---

## 🔒 Security Notes

1. **Change default passwords** in production!
2. **Set `APP_DEBUG=false`** in production `.env`
3. **Set `APP_ENV=production`** in production
4. **Use HTTPS** in production
5. **Set strong `APP_KEY`** (already generated)

---

## 📊 Database Structure

Your database has **24 tables**:
- users, roles, permissions
- medical_supplies
- drones
- hospitals
- delivery_requests
- deliveries
- delivery_tracking
- delivery_confirmations
- drone_assignments
- notifications
- audit_logs
- settings
- And more...

---

## 🎨 Frontend Stack

- **Tailwind CSS 3** (via CDN)
- **Alpine.js 3.x** (for interactivity)
- **Font Awesome 6.4.0** (icons)
- **Chart.js 4.4** (charts)
- **Axios 1.6** (AJAX)

---

## 📱 Browser Compatibility

- ✅ Chrome (Recommended)
- ✅ Firefox
- ✅ Edge
- ✅ Safari

---

## 🆘 Still Having Issues?

### **1. Check server is running:**
Look for this in your terminal:
```
INFO  Server running on [http://127.0.0.1:8000].
```

### **2. Check XAMPP:**
- Apache: ✅ Running
- MySQL: ✅ Running

### **3. Verify database:**
- Open phpMyAdmin: `http://localhost/phpmyadmin`
- Check `drone_delivery_system` database exists
- Verify tables are created

### **4. Check logs:**
```bash
# View Laravel logs
type storage\logs\laravel.log
```

### **5. Nuclear option (Reset everything):**
```bash
# WARNING: This deletes all data!
php artisan migrate:fresh --seed
php artisan optimize:clear
```

---

## ✅ Success Checklist

- [x] XAMPP running (Apache + MySQL)
- [x] Database created and migrated
- [x] Users seeded (admin, operator, hospital)
- [x] Routes working (all fixed!)
- [x] Authentication working (login/logout)
- [x] CSRF protection enabled
- [x] Role-based access control active
- [x] All views created and working
- [x] Middleware configured

---

## 🎉 You're All Set!

Your Drone Delivery System is now fully functional and ready for development or testing!

**Start with:** Login as admin → Create medical supplies → Add drones → Add hospitals → Create delivery requests

---

**Need help?** Check the error logs or run `php artisan` to see available commands.

**Happy Coding! 🚁**
