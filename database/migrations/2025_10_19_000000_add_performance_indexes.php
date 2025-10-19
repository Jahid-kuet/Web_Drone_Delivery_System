<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add database indexes for performance optimization.
     * Indexes are added to frequently queried columns and foreign keys.
     */
    public function up(): void
    {
        // ==================== DELIVERIES TABLE ====================
        Schema::table('deliveries', function (Blueprint $table) {
            // Status-based queries (frequently filtered by status)
            $table->index('status', 'idx_deliveries_status');
            
            // Tracking number lookups (public tracking feature)
            $table->index('tracking_number', 'idx_deliveries_tracking_number');
            
            // Foreign key indexes (join optimization)
            $table->index('drone_id', 'idx_deliveries_drone_id');
            $table->index('hospital_id', 'idx_deliveries_hospital_id');
            $table->index('delivery_request_id', 'idx_deliveries_request_id');
            $table->index('assigned_pilot_id', 'idx_deliveries_pilot_id');
            
            // Date-based queries (reports and filtering)
            $table->index('scheduled_departure_time', 'idx_deliveries_scheduled_departure');
            $table->index('created_at', 'idx_deliveries_created_at');
            
            // OTP verification queries
            $table->index('otp_verified_at', 'idx_deliveries_otp_verified');
            
            // Composite indexes for common query patterns
            $table->index(['status', 'created_at'], 'idx_deliveries_status_created');
            $table->index(['hospital_id', 'status'], 'idx_deliveries_hospital_status');
            $table->index(['drone_id', 'status'], 'idx_deliveries_drone_status');
        });

        // ==================== DELIVERY_REQUESTS TABLE ====================
        Schema::table('delivery_requests', function (Blueprint $table) {
            // Status and priority queries
            $table->index('status', 'idx_requests_status');
            $table->index('priority', 'idx_requests_priority');
            
            // Foreign keys
            $table->index('hospital_id', 'idx_requests_hospital_id');
            $table->index('requested_by_user_id', 'idx_requests_user_id');
            
            // Date queries
            $table->index('requested_at', 'idx_requests_requested_at');
            $table->index('created_at', 'idx_requests_created_at');
            
            // Composite for priority queue
            $table->index(['status', 'priority', 'requested_at'], 'idx_requests_queue');
        });

        // ==================== DRONES TABLE ====================
        Schema::table('drones', function (Blueprint $table) {
            // Status and availability queries
            $table->index('status', 'idx_drones_status');
            $table->index('battery_level', 'idx_drones_battery');
            
            // Foreign keys
            $table->index('current_hub_id', 'idx_drones_hub_id');
            $table->index('assigned_operator_id', 'idx_drones_operator_id');
            
            // Maintenance queries
            $table->index('last_maintenance_date', 'idx_drones_maintenance');
            $table->index('next_maintenance_date', 'idx_drones_next_maintenance');
            
            // Composite for availability checks
            $table->index(['status', 'battery_level'], 'idx_drones_availability');
        });

        // ==================== DELIVERY_TRACKING TABLE ====================
        Schema::table('delivery_tracking', function (Blueprint $table) {
            // Foreign key for delivery lookups
            $table->index('delivery_id', 'idx_tracking_delivery_id');
            
            // Status tracking
            $table->index('status', 'idx_tracking_status');
            
            // Timestamp for chronological ordering
            $table->index('timestamp', 'idx_tracking_timestamp');
            
            // Composite for delivery timeline
            $table->index(['delivery_id', 'timestamp'], 'idx_tracking_delivery_timeline');
        });

        // ==================== HOSPITALS TABLE ====================
        Schema::table('hospitals', function (Blueprint $table) {
            // Search by name
            $table->index('name', 'idx_hospitals_name');
            
            // Status queries
            $table->index('is_active', 'idx_hospitals_active');
            
            // Location queries
            $table->index('district', 'idx_hospitals_district');
            $table->index('upazila', 'idx_hospitals_upazila');
            
            // Hub relationship
            $table->index('hub_id', 'idx_hospitals_hub_id');
        });

        // ==================== MEDICAL_SUPPLIES TABLE ====================
        Schema::table('medical_supplies', function (Blueprint $table) {
            // Search by name and category
            $table->index('name', 'idx_supplies_name');
            $table->index('category', 'idx_supplies_category');
            
            // Stock level queries
            $table->index('stock_quantity', 'idx_supplies_stock');
            $table->index('reorder_level', 'idx_supplies_reorder');
            
            // Expiry tracking
            $table->index('expiry_date', 'idx_supplies_expiry');
            
            // Composite for low stock alerts
            $table->index(['stock_quantity', 'reorder_level'], 'idx_supplies_low_stock');
        });

        // ==================== USERS TABLE ====================
        Schema::table('users', function (Blueprint $table) {
            // Email is already unique, but add index for faster lookups
            // Phone number searches
            $table->index('phone', 'idx_users_phone');
            
            // Hospital relationship
            $table->index('hospital_id', 'idx_users_hospital_id');
        });

        // ==================== USER_ROLES TABLE ====================
        Schema::table('user_roles', function (Blueprint $table) {
            // Foreign keys (if not already indexed)
            $table->index('user_id', 'idx_user_roles_user_id');
            $table->index('role_id', 'idx_user_roles_role_id');
            
            // Composite for role checks
            $table->index(['user_id', 'role_id'], 'idx_user_roles_composite');
        });

        // ==================== NOTIFICATIONS TABLE ====================
        Schema::table('notifications', function (Blueprint $table) {
            // Recipient queries
            $table->index('recipient_id', 'idx_notifications_recipient');
            
            // Read status
            $table->index('is_read', 'idx_notifications_read');
            
            // Timestamp ordering
            $table->index('created_at', 'idx_notifications_created');
            
            // Composite for inbox queries
            $table->index(['recipient_id', 'is_read', 'created_at'], 'idx_notifications_inbox');
        });

        // ==================== AUDIT_LOGS TABLE ====================
        Schema::table('audit_logs', function (Blueprint $table) {
            // User activity tracking
            $table->index('user_id', 'idx_audit_user_id');
            
            // Action type filtering
            $table->index('action', 'idx_audit_action');
            
            // Table name filtering
            $table->index('table_name', 'idx_audit_table');
            
            // Timestamp ordering
            $table->index('created_at', 'idx_audit_created');
            
            // Composite for user activity reports
            $table->index(['user_id', 'created_at'], 'idx_audit_user_activity');
        });

        // ==================== HUB_INVENTORIES TABLE ====================
        Schema::table('hub_inventories', function (Blueprint $table) {
            // Foreign keys
            $table->index('hub_id', 'idx_hub_inv_hub_id');
            $table->index('medical_supply_id', 'idx_hub_inv_supply_id');
            
            // Stock queries
            $table->index('quantity_available', 'idx_hub_inv_quantity');
            
            // Composite for hub stock checks
            $table->index(['hub_id', 'medical_supply_id'], 'idx_hub_inv_composite');
        });

        // ==================== DELIVERY_CONFIRMATIONS TABLE ====================
        Schema::table('delivery_confirmations', function (Blueprint $table) {
            // Foreign key
            $table->index('delivery_id', 'idx_confirmations_delivery_id');
            
            // Confirmation method
            $table->index('confirmation_method', 'idx_confirmations_method');
            
            // Timestamp
            $table->index('confirmed_at', 'idx_confirmations_confirmed_at');
        });

        // ==================== JOBS TABLE (Queue) ====================
        Schema::table('jobs', function (Blueprint $table) {
            // Queue name
            $table->index('queue', 'idx_jobs_queue');
            
            // Available at (for delayed jobs)
            $table->index('available_at', 'idx_jobs_available');
            
            // Composite for queue processing
            $table->index(['queue', 'available_at'], 'idx_jobs_processing');
        });

        // ==================== CACHE TABLE ====================
        Schema::table('cache', function (Blueprint $table) {
            // Expiration cleanup
            $table->index('expiration', 'idx_cache_expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ==================== DROP INDEXES ====================
        
        // Deliveries
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropIndex('idx_deliveries_status');
            $table->dropIndex('idx_deliveries_tracking_number');
            $table->dropIndex('idx_deliveries_drone_id');
            $table->dropIndex('idx_deliveries_hospital_id');
            $table->dropIndex('idx_deliveries_request_id');
            $table->dropIndex('idx_deliveries_pilot_id');
            $table->dropIndex('idx_deliveries_scheduled_departure');
            $table->dropIndex('idx_deliveries_created_at');
            $table->dropIndex('idx_deliveries_otp_verified');
            $table->dropIndex('idx_deliveries_status_created');
            $table->dropIndex('idx_deliveries_hospital_status');
            $table->dropIndex('idx_deliveries_drone_status');
        });

        // Delivery Requests
        Schema::table('delivery_requests', function (Blueprint $table) {
            $table->dropIndex('idx_requests_status');
            $table->dropIndex('idx_requests_priority');
            $table->dropIndex('idx_requests_hospital_id');
            $table->dropIndex('idx_requests_user_id');
            $table->dropIndex('idx_requests_requested_at');
            $table->dropIndex('idx_requests_created_at');
            $table->dropIndex('idx_requests_queue');
        });

        // Drones
        Schema::table('drones', function (Blueprint $table) {
            $table->dropIndex('idx_drones_status');
            $table->dropIndex('idx_drones_battery');
            $table->dropIndex('idx_drones_hub_id');
            $table->dropIndex('idx_drones_operator_id');
            $table->dropIndex('idx_drones_maintenance');
            $table->dropIndex('idx_drones_next_maintenance');
            $table->dropIndex('idx_drones_availability');
        });

        // Delivery Tracking
        Schema::table('delivery_tracking', function (Blueprint $table) {
            $table->dropIndex('idx_tracking_delivery_id');
            $table->dropIndex('idx_tracking_status');
            $table->dropIndex('idx_tracking_timestamp');
            $table->dropIndex('idx_tracking_delivery_timeline');
        });

        // Hospitals
        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropIndex('idx_hospitals_name');
            $table->dropIndex('idx_hospitals_active');
            $table->dropIndex('idx_hospitals_district');
            $table->dropIndex('idx_hospitals_upazila');
            $table->dropIndex('idx_hospitals_hub_id');
        });

        // Medical Supplies
        Schema::table('medical_supplies', function (Blueprint $table) {
            $table->dropIndex('idx_supplies_name');
            $table->dropIndex('idx_supplies_category');
            $table->dropIndex('idx_supplies_stock');
            $table->dropIndex('idx_supplies_reorder');
            $table->dropIndex('idx_supplies_expiry');
            $table->dropIndex('idx_supplies_low_stock');
        });

        // Users
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_phone');
            $table->dropIndex('idx_users_hospital_id');
        });

        // User Roles
        Schema::table('user_roles', function (Blueprint $table) {
            $table->dropIndex('idx_user_roles_user_id');
            $table->dropIndex('idx_user_roles_role_id');
            $table->dropIndex('idx_user_roles_composite');
        });

        // Notifications
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('idx_notifications_recipient');
            $table->dropIndex('idx_notifications_read');
            $table->dropIndex('idx_notifications_created');
            $table->dropIndex('idx_notifications_inbox');
        });

        // Audit Logs
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex('idx_audit_user_id');
            $table->dropIndex('idx_audit_action');
            $table->dropIndex('idx_audit_table');
            $table->dropIndex('idx_audit_created');
            $table->dropIndex('idx_audit_user_activity');
        });

        // Hub Inventories
        Schema::table('hub_inventories', function (Blueprint $table) {
            $table->dropIndex('idx_hub_inv_hub_id');
            $table->dropIndex('idx_hub_inv_supply_id');
            $table->dropIndex('idx_hub_inv_quantity');
            $table->dropIndex('idx_hub_inv_composite');
        });

        // Delivery Confirmations
        Schema::table('delivery_confirmations', function (Blueprint $table) {
            $table->dropIndex('idx_confirmations_delivery_id');
            $table->dropIndex('idx_confirmations_method');
            $table->dropIndex('idx_confirmations_confirmed_at');
        });

        // Jobs
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropIndex('idx_jobs_queue');
            $table->dropIndex('idx_jobs_available');
            $table->dropIndex('idx_jobs_processing');
        });

        // Cache
        Schema::table('cache', function (Blueprint $table) {
            $table->dropIndex('idx_cache_expiration');
        });
    }
};
