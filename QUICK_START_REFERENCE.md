# 🎯 **Quick Reference Card - Login & Testing**

## 🔑 **Login Credentials**

```
┌─────────────────────────────────────────────────────────────┐
│                    TEST USER ACCOUNTS                        │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  👨‍💼 ADMIN (Full Access)                                     │
│     Email:    admin@drone.com                               │
│     Password: password123                                    │
│     URL:      http://127.0.0.1:8000/login                   │
│                                                              │
│  🏥 HOSPITAL ADMIN (Hospital Management)                     │
│     Email:    hospital@drone.com                            │
│     Password: password123                                    │
│     URL:      http://127.0.0.1:8000/login                   │
│                                                              │
│  🚁 DRONE OPERATOR (Flight Operations)                       │
│     Email:    operator@drone.com                            │
│     Password: password123                                    │
│     URL:      http://127.0.0.1:8000/login                   │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

---

## 🚀 **Quick Start Commands**

### **Option 1: Using Quick Start Script (Recommended)**
```bash
# Just double-click this file:
quick-start.bat

# Or run in terminal:
.\quick-start.bat
```

### **Option 2: Manual Start**
```bash
# Clear caches
php artisan optimize:clear

# Start server
php artisan serve

# Open browser to:
# http://127.0.0.1:8000/login
```

---

## 🗺️ **Valid Khulna Test Coordinates**

Use these coordinates when creating hospitals:

```
┌──────────────────────────┬───────────┬────────────┐
│ Location                 │ Latitude  │ Longitude  │
├──────────────────────────┼───────────┼────────────┤
│ Khulna Medical College   │  22.8456  │  89.5403   │
│ Jessore Sadar Hospital   │  23.1634  │  89.2182   │
│ Satkhira District Hosp.  │  22.7185  │  89.0700   │
│ Bagerhat District Hosp.  │  22.6516  │  89.7851   │
│ Narail Sadar Hospital    │  23.1163  │  89.5840   │
└──────────────────────────┴───────────┴────────────┘
```

**⚠️ These will FAIL (outside Khulna):**
```
Dhaka:  23.8103, 90.4125  ❌
Delhi:  28.6139, 77.2090  ❌
```

---

## 🏢 **Existing Hubs (Auto-Seeded)**

```
┌─────────────────────────────────────────────────────────────┐
│  Hub Code: HUB-KHL-001                                      │
│  Name: Khulna Central Medical Hub                           │
│  Location: 22.8456°N, 89.5403°E                             │
│  Type: Medical Depot (Main Hub)                             │
│  Hours: 24/7 Operations                                     │
│  Facilities: 10 charging stations, Cold storage             │
├─────────────────────────────────────────────────────────────┤
│  Hub Code: HUB-JES-001                                      │
│  Name: Jessore District Hub                                 │
│  Location: 23.1634°N, 89.2182°E                             │
│  Hours: 06:00-22:00 (Weekdays)                              │
│  Facilities: 6 charging stations, Cold storage              │
├─────────────────────────────────────────────────────────────┤
│  Hub Code: HUB-SAT-001                                      │
│  Name: Satkhira District Hub                                │
│  Location: 22.7185°N, 89.0700°E                             │
│  Hours: 08:00-20:00 (Weekdays)                              │
│  Facilities: 4 charging stations, Cold storage              │
└─────────────────────────────────────────────────────────────┘
```

---

## 🧪 **Quick Testing Checklist**

### **✅ Step 1: Login Test**
- [ ] Open http://127.0.0.1:8000/login
- [ ] Login as admin@drone.com / password123
- [ ] Verify redirect to /admin/dashboard
- [ ] Logout and login as hospital@drone.com
- [ ] Verify redirect to /hospital/dashboard

### **✅ Step 2: Location Validation Test**
- [ ] Go to Admin → Hospitals → Create New
- [ ] Enter Khulna coordinates (22.8456, 89.5403)
- [ ] Hospital creates successfully ✅
- [ ] Try Dhaka coordinates (23.8103, 90.4125)
- [ ] Should reject with error ❌

### **✅ Step 3: Hub System Test**
```bash
# Open PHP Artisan Tinker
php artisan tinker

# Check hubs exist
>>> \App\Models\Hub::count();
# Should return: 3

# View hub names
>>> \App\Models\Hub::all(['name', 'code']);

# Exit tinker
>>> exit
```

### **✅ Step 4: Inventory Test**
```bash
php artisan tinker

# Check inventories exist
>>> \App\Models\HubInventory::count();
# Should return: 90-150 (depends on medical supplies)

# View inventory summary
>>> \App\Models\HubInventory::with('hub')->first();

>>> exit
```

---

## 🔧 **Troubleshooting Commands**

### **Problem: Can't login**
```bash
# Re-seed database
php artisan db:seed --class=DatabaseSeeder

# Check users exist
php artisan tinker
>>> \App\Models\User::all(['name', 'email']);
>>> exit
```

### **Problem: No hubs found**
```bash
# Seed hubs
php artisan db:seed --class=HubSeeder

# Verify
php artisan tinker
>>> \App\Models\Hub::count();
>>> exit
```

### **Problem: Location validation not working**
```bash
# Clear caches
php artisan optimize:clear

# Test service directly
php artisan tinker
>>> \App\Services\BangladeshLocationService::validateLocation(22.8456, 89.5403, true);
>>> exit
```

### **Problem: 404 errors on routes**
```bash
php artisan route:clear
php artisan route:cache
php artisan optimize:clear
```

### **Problem: Database errors**
```bash
# Check migration status
php artisan migrate:status

# Run missing migrations
php artisan migrate

# Fresh start (⚠️ DELETES ALL DATA)
php artisan migrate:fresh --seed
```

---

## 📊 **Database Quick Queries**

### **Check Users**
```sql
SELECT id, name, email, status FROM users;
```

### **Check Hubs**
```sql
SELECT id, name, code, city, is_active, is_24_7 FROM hubs;
```

### **Check Hub Inventories**
```sql
SELECT 
    h.name as hub_name,
    ms.name as supply_name,
    hi.quantity_available
FROM hub_inventories hi
JOIN hubs h ON hi.hub_id = h.id
JOIN medical_supplies ms ON hi.medical_supply_id = ms.id
LIMIT 10;
```

### **Check Hospital Locations**
```sql
SELECT name, city, latitude, longitude FROM hospitals;
```

---

## 🎯 **What You Can Test Now**

| Feature | Status | How to Test |
|---------|--------|-------------|
| **Login System** | ✅ Working | Use credentials above |
| **Role-Based Access** | ✅ Working | Login as different users |
| **Location Validation** | ✅ Working | Create hospital with Khulna coordinates |
| **Hubs System** | ✅ Working | Check database or use Tinker |
| **Hub Inventory** | ✅ Working | Query hub_inventories table |
| **Emergency Priority** | ⏸️ Pending | Not yet implemented |
| **OTP Delivery Proof** | ⏸️ Pending | Not yet implemented |
| **Photo Upload** | ⏸️ Pending | Not yet implemented |

---

## 📁 **Important Files**

```
📄 Full Documentation
   └─ TESTING_GUIDE.md (Detailed testing instructions)

📄 Implementation Status
   └─ IMPLEMENTATION_PROGRESS.md (What's done, what's pending)

📄 Feature Specs
   └─ MODIFICATION_PLAN_BANGLADESH.md (Complete technical specs)

📄 Quick Reference
   └─ QUICK_REFERENCE.md (Feature comparison)

🎯 This File
   └─ QUICK_START_REFERENCE.md (Quick commands)
```

---

## 💻 **Useful Tinker Commands**

```bash
# Open Tinker
php artisan tinker

# Get admin user
>>> $admin = \App\Models\User::where('email', 'admin@drone.com')->first();

# Check user roles
>>> $admin->roles;

# Get all hubs
>>> \App\Models\Hub::all();

# Find nearest hub to coordinates
>>> \App\Models\Hub::findNearestTo(22.8456, 89.5403);

# Check hub has stock
>>> $hub = \App\Models\Hub::first();
>>> $hub->hasStock(1, 5);

# Test location validation
>>> \App\Services\BangladeshLocationService::isInKhulna(22.8456, 89.5403);

# Exit Tinker
>>> exit
```

---

## 🌐 **URLs Reference**

```
Login Page:           http://127.0.0.1:8000/login
Register Page:        http://127.0.0.1:8000/register
Admin Dashboard:      http://127.0.0.1:8000/admin/dashboard
Hospital Dashboard:   http://127.0.0.1:8000/hospital/dashboard
Operator Dashboard:   http://127.0.0.1:8000/operator/dashboard
```

---

## ⚡ **Pro Tips**

1. **Keep Tinker Open** - Great for quick database checks while testing UI
2. **Use Incognito Mode** - Test multiple user roles simultaneously
3. **Check Logs** - `storage/logs/laravel.log` for error details
4. **Database Client** - Use HeidiSQL or TablePlus for visual inspection
5. **API Testing** - Use Postman for API endpoint testing

---

## 📞 **Getting Help**

If something doesn't work:
1. Check `storage/logs/laravel.log`
2. Run `php artisan optimize:clear`
3. Verify `.env` database settings
4. Check migrations: `php artisan migrate:status`
5. Re-seed if needed: `php artisan db:seed`

---

**🚀 Ready to test! Start with:** `.\quick-start.bat`

---

*Last Updated: October 16, 2025*
