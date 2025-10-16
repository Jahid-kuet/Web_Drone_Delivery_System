# 🧪 **Testing Guide - Login & Test New Features**

## 📋 **Table of Contents**
1. [Default User Accounts](#default-user-accounts)
2. [How to Login](#how-to-login)
3. [Testing Bangladesh Location Validation](#testing-bangladesh-location-validation)
4. [Testing Khulna Hubs System](#testing-khulna-hubs-system)
5. [Testing Inventory Management](#testing-inventory-management)
6. [Common Issues & Solutions](#common-issues--solutions)

---

## 🔑 **Default User Accounts**

After running `php artisan db:seed`, these test accounts are created:

### **1. Admin Account** (Full System Access)
```
Email: admin@drone.com
Password: password123
Role: Administrator
Access: All modules (hospitals, drones, deliveries, users, hubs)
Dashboard: /admin/dashboard
```

### **2. Hospital Admin Account** (Hospital Management)
```
Email: hospital@drone.com
Password: password123
Role: Hospital Administrator
Hospital: Square Hospital Dhaka
Access: Hospital operations, delivery requests
Dashboard: /hospital/dashboard
```

### **3. Drone Operator Account** (Drone Operations)
```
Email: operator@drone.com
Password: password123
Role: Drone Operator
Access: Drone flight operations, delivery tracking
Dashboard: /operator/dashboard
```

---

## 🚀 **How to Login**

### **Step 1: Start Your Server**

```bash
# Make sure your server is running
php artisan serve
```

Your application will be available at: `http://127.0.0.1:8000`

### **Step 2: Access Login Page**

Open your browser and go to:
```
http://127.0.0.1:8000/login
```

### **Step 3: Login with Test Account**

**For Admin Testing:**
- Email: `admin@drone.com`
- Password: `password123`
- Click "Login" button

**For Hospital Testing:**
- Email: `hospital@drone.com`
- Password: `password123`
- Click "Login" button

**For Operator Testing:**
- Email: `operator@drone.com`
- Password: `password123`
- Click "Login" button

### **Step 4: You'll be redirected to your dashboard**

- **Admin** → `/admin/dashboard`
- **Hospital Admin** → `/hospital/dashboard`
- **Drone Operator** → `/operator/dashboard`

---

## 🗺️ **Testing Bangladesh Location Validation**

This feature ensures all hospitals must be located in **Khulna Division, Bangladesh**.

### **Test as Admin**

**1. Navigate to Hospital Management**
```
Login as: admin@drone.com
Go to: Admin Dashboard → Hospitals → Create New Hospital
```

**2. Test Valid Khulna Location** ✅

Enter these details:
```
Hospital Name: Test Hospital Khulna
Code: TEST-KHL-001
Email: test@khulna.com
Type: General Hospital
Address: Khulna Medical College Road
City: Khulna
State/Province: Khulna Division
Postal Code: 9100
Country: Bangladesh (auto-set)

GPS Coordinates:
Latitude: 22.8456
Longitude: 89.5403

Phone: +880-41-761020
Bed Capacity: 100
Has Emergency Department: Yes
Has Drone Landing Pad: Yes
```

**Expected Result:** ✅ Hospital created successfully! Location validated.

---

**3. Test Invalid Location (Outside Khulna)** ❌

Try creating hospital with **Dhaka coordinates**:
```
Hospital Name: Test Hospital Dhaka
Latitude: 23.8103
Longitude: 90.4125
(Other fields same as above)
```

**Expected Result:** ❌ Error message:
```
"This location is not within Khulna Division. 
Current launch phase is limited to Khulna Division only."
```

---

**4. Test Completely Outside Bangladesh** ❌

Try creating hospital with **Indian coordinates**:
```
Hospital Name: Test Hospital India
Latitude: 28.6139
Longitude: 77.2090
(Other fields same as above)
```

**Expected Result:** ❌ Error message:
```
"Location must be within Bangladesh. 
The provided coordinates are outside Bangladesh territory."
```

---

### **Valid Test Coordinates for Khulna Division**

Use these for successful testing:

| Location | Latitude | Longitude | Hospital Name |
|----------|----------|-----------|---------------|
| **Khulna Central** | 22.8456 | 89.5403 | Khulna Medical College |
| **Jessore** | 23.1634 | 89.2182 | Jessore Sadar Hospital |
| **Satkhira** | 22.7185 | 89.0700 | Satkhira Sadar Hospital |
| **Bagerhat** | 22.6516 | 89.7851 | Bagerhat District Hospital |
| **Narail** | 23.1163 | 89.5840 | Narail Sadar Hospital |

---

## 🏢 **Testing Khulna Hubs System**

Hubs are regional distribution centers for medical supplies.

### **Check Existing Hubs**

**1. Login as Admin**
```
Email: admin@drone.com
Password: password123
```

**2. View Hubs** (Need to create hub management page or use database)

**Option A: Using Database Client (HeidiSQL, TablePlus, phpMyAdmin)**
```sql
SELECT * FROM hubs;
```

You should see 3 hubs:
- **HUB-KHL-001** - Khulna Central Medical Hub (24/7, Main Hub)
- **HUB-JES-001** - Jessore District Hub (06:00-22:00)
- **HUB-SAT-001** - Satkhira District Hub (08:00-20:00)

**Option B: Using Artisan Tinker**
```bash
php artisan tinker

# View all hubs
>>> \App\Models\Hub::all();

# View hub with statistics
>>> \App\Models\Hub::where('code', 'HUB-KHL-001')->first()->getStatistics();

# Find nearest hub to coordinates
>>> \App\Models\Hub::findNearestTo(22.8456, 89.5403);

# Check if hub has stock
>>> $hub = \App\Models\Hub::first();
>>> $hub->hasStock(1, 5); // Check if hub has 5 units of supply ID 1
```

---

### **Test Hub Features in Tinker**

```bash
php artisan tinker

# 1. Get all active hubs
>>> \App\Models\Hub::active()->get();

# 2. Get hubs with cold storage
>>> \App\Models\Hub::withColdStorage()->get();

# 3. Get 24/7 operational hubs
>>> \App\Models\Hub::operating24_7()->get();

# 4. Get hubs in Khulna division
>>> \App\Models\Hub::inDivision('Khulna')->get();

# 5. Find nearest hub to hospital coordinates
>>> $hub = \App\Models\Hub::findNearestHubForDelivery(22.8456, 89.5403, true);
>>> echo $hub->name;

# 6. Check available drones at hub
>>> $hub = \App\Models\Hub::first();
>>> $hub->availableDrones()->count();

# 7. Get hub full address
>>> echo $hub->full_address;

# 8. Check charging capacity
>>> echo $hub->available_charging_stations;

# 9. Get hub statistics
>>> $stats = $hub->getStatistics();
>>> print_r($stats);
```

---

## 📦 **Testing Inventory Management**

### **Check Hub Inventories**

**Using Database:**
```sql
SELECT 
    h.name as hub_name,
    h.code as hub_code,
    ms.name as supply_name,
    hi.quantity_available,
    hi.minimum_stock_level,
    hi.needs_cold_storage
FROM hub_inventories hi
JOIN hubs h ON hi.hub_id = h.id
JOIN medical_supplies ms ON hi.medical_supply_id = ms.id
ORDER BY h.name, ms.name;
```

**Using Artisan Tinker:**
```bash
php artisan tinker

# 1. View all inventories for a hub
>>> $hub = \App\Models\Hub::where('code', 'HUB-KHL-001')->first();
>>> $hub->inventories;

# 2. Check low stock items
>>> \App\Models\HubInventory::lowStock()->get();

# 3. Check items needing reorder
>>> \App\Models\HubInventory::needsReorder()->get();

# 4. Check out of stock items
>>> \App\Models\HubInventory::outOfStock()->get();

# 5. Check cold storage requirements
>>> \App\Models\HubInventory::requiresColdStorage()->get();

# 6. Get specific inventory
>>> $inventory = \App\Models\HubInventory::first();
>>> echo $inventory->stock_status; // Returns: adequate, low_stock, etc.
>>> echo $inventory->stock_percentage; // Returns: 0-100

# 7. Test decrease stock (simulate delivery)
>>> $inventory = \App\Models\HubInventory::first();
>>> $inventory->decreaseStock(5, 'Delivery #123');
>>> echo $inventory->quantity_available;

# 8. Test restock
>>> $inventory->restock(20);
>>> echo $inventory->quantity_available;

# 9. Check if can fulfill order
>>> $inventory->canFulfill(10); // Returns true/false
```

---

## 🔬 **Complete Testing Workflow**

### **Scenario: Create Emergency Blood Delivery from Hub**

**Step 1: Login as Hospital Admin**
```
Email: hospital@drone.com
Password: password123
```

**Step 2: Check Available Blood Supply**
```bash
php artisan tinker

# Find nearest hub to hospital
>>> $hospital = \App\Models\Hospital::first();
>>> $hub = \App\Models\Hub::findNearestHubForDelivery(
    $hospital->latitude, 
    $hospital->longitude, 
    true // Requires cold chain
);
>>> echo $hub->name;

# Check blood availability (assuming medical_supply_id = 1 is blood)
>>> $bloodSupply = \App\Models\MedicalSupply::where('code', 'BLOOD-O-NEG')->first();
>>> $hub->hasStock($bloodSupply->id, 5); // Check for 5 units
```

**Step 3: Create Delivery Request** (through web interface or API)
- Delivery should automatically select nearest hub
- Hub inventory should decrease when delivery is assigned

---

## 🐛 **Common Issues & Solutions**

### **Issue 1: "Login page not found" (404 Error)**

**Solution:**
```bash
# Clear route cache
php artisan route:clear

# Re-cache routes
php artisan route:cache

# Restart server
php artisan serve
```

---

### **Issue 2: "Credentials don't match"**

**Solution:**
```bash
# Re-run seeders to recreate users
php artisan db:seed --class=DatabaseSeeder

# Or migrate fresh with seeding
php artisan migrate:fresh --seed
```

---

### **Issue 3: "SQLSTATE[HY000]: General error" when creating hospital**

**Solution:**
```bash
# Check if hubs table exists
php artisan migrate:status

# If hubs migration not run, run it
php artisan migrate

# Seed hubs
php artisan db:seed --class=HubSeeder
```

---

### **Issue 4: No hospitals in Khulna after seeding**

**Solution:**
The default seeder creates Square Hospital in **Dhaka**, which is outside Khulna. This is expected. Create new hospitals using Khulna coordinates from the table above.

---

### **Issue 5: Can't access admin dashboard after login**

**Solution:**
```bash
# Check user role assignment
php artisan tinker

>>> $user = \App\Models\User::where('email', 'admin@drone.com')->first();
>>> $user->roles;

# If no roles, assign admin role
>>> $adminRole = \App\Models\Role::where('slug', 'admin')->first();
>>> $user->roles()->sync([$adminRole->id]);
```

---

## 📊 **Verification Checklist**

Use this checklist to verify everything is working:

### **✅ Database Setup**
- [ ] Migrations run successfully (`php artisan migrate:status`)
- [ ] Hubs table exists and has 3 records
- [ ] Hub inventories table has records
- [ ] Users table has 3 test users

### **✅ Authentication**
- [ ] Can access login page at `/login`
- [ ] Can login as admin (`admin@drone.com`)
- [ ] Can login as hospital admin (`hospital@drone.com`)
- [ ] Can login as operator (`operator@drone.com`)
- [ ] Each user redirects to correct dashboard

### **✅ Location Validation**
- [ ] Creating hospital with Khulna coordinates succeeds
- [ ] Creating hospital with Dhaka coordinates fails
- [ ] Creating hospital outside Bangladesh fails
- [ ] Error messages are clear and user-friendly

### **✅ Hub System**
- [ ] 3 hubs exist in database
- [ ] Can query hubs using `Hub::all()`
- [ ] Can find nearest hub using `findNearestTo()`
- [ ] Hub statistics method works

### **✅ Inventory System**
- [ ] Hub inventories exist in database
- [ ] Can check stock levels
- [ ] Can decrease stock
- [ ] Can restock items
- [ ] Low stock alerts work

---

## 🎯 **Quick Testing Commands**

Copy and paste these for quick testing:

```bash
# 1. Check database status
php artisan migrate:status

# 2. View all hubs
php artisan tinker
>>> \App\Models\Hub::all(['name', 'code', 'city']);
>>> exit

# 3. View all users with roles
php artisan tinker
>>> \App\Models\User::with('roles')->get(['name', 'email']);
>>> exit

# 4. Test location validation
php artisan tinker
>>> \App\Services\BangladeshLocationService::validateLocation(22.8456, 89.5403, true);
>>> \App\Services\BangladeshLocationService::validateLocation(23.8103, 90.4125, true);
>>> exit

# 5. Check hub inventories
php artisan tinker
>>> \App\Models\HubInventory::with(['hub', 'medicalSupply'])->get(['hub_id', 'medical_supply_id', 'quantity_available']);
>>> exit
```

---

## 🌐 **API Testing (Optional)**

If you want to test the API endpoints:

### **Get Auth Token**
```bash
# Using Postman or curl
POST http://127.0.0.1:8000/api/login
Content-Type: application/json

{
    "email": "admin@drone.com",
    "password": "password123"
}
```

### **Get Hubs (if API endpoint exists)**
```bash
GET http://127.0.0.1:8000/api/v1/hubs
Authorization: Bearer {your_token_here}
```

---

## 📝 **Next Steps After Testing**

Once you've verified everything works:

1. ✅ **Proceed with Emergency Priority Queue** implementation
2. ✅ **Implement OTP Delivery Confirmation** system
3. ✅ **Add Photo Upload** for delivery proof
4. ✅ **Create Hub Management UI** for admins
5. ✅ **Update dashboards** to show hub statistics

---

## 💡 **Tips for Testing**

1. **Use Browser Incognito Mode** for testing different user roles simultaneously
2. **Keep Tinker open** for quick database queries while testing UI
3. **Check Laravel logs** if errors occur: `storage/logs/laravel.log`
4. **Use database client** (HeidiSQL, TablePlus) for visual inspection
5. **Test on real devices** if testing mobile interface

---

## 📞 **Need Help?**

If you encounter issues:

1. Check `storage/logs/laravel.log` for error details
2. Run `php artisan route:list` to verify routes exist
3. Clear all caches: `php artisan optimize:clear`
4. Check database connection in `.env` file
5. Verify seeders ran: `SELECT COUNT(*) FROM users;` (should be ≥ 3)

---

**Happy Testing! 🚀**

Last Updated: October 16, 2025
