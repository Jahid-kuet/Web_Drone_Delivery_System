<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add additional database indexes for performance optimization.
     * NOTE: Most core indexes already exist in original migrations.
     * This migration adds supplementary indexes for cache optimization and reporting.
     */
    public function up(): void
    {
        // ==================== DELIVERIES TABLE ====================
        // Original migration already has: delivery_number (unique), status+scheduled_departure_time, 
        // drone_id+status, hospital_id+delivery_completed_time
        Schema::table('deliveries', function (Blueprint $table) {
            // Add OTP verification index if column exists (from Task 5)
            if (Schema::hasColumn('deliveries', 'otp_verified_at')) {
                $table->index('otp_verified_at', 'idx_deliveries_otp_verified');
            }
            
            // Add created_at for report queries
            $table->index('created_at', 'idx_deliveries_created_at');
        });

        // ==================== DELIVERY_REQUESTS TABLE ====================
        // Original migration already has: status+priority, hospital_id+status, request_number, etc.
        Schema::table('delivery_requests', function (Blueprint $table) {
            // Add created_at for report queries
            $table->index('created_at', 'idx_requests_created_at');
        });

        // ==================== DRONES TABLE ====================
        // Original migration already has: status+type, current_battery_level+status, 
        // next_maintenance_due+is_active, last_maintenance_date
        Schema::table('drones', function (Blueprint $table) {
            // Add hub relationship indexes (from Task 2)
            if (Schema::hasColumn('drones', 'current_hub_id')) {
                $table->index('current_hub_id', 'idx_drones_current_hub');
            }
            if (Schema::hasColumn('drones', 'assigned_operator_id')) {
                $table->index('assigned_operator_id', 'idx_drones_operator');
            }
        });

        // ==================== DELIVERY_TRACKING TABLE ====================
        Schema::table('delivery_tracking', function (Blueprint $table) {
            // Foreign key for delivery lookups
            $table->index('delivery_id', 'idx_tracking_delivery_id');
            
            // Timestamp for chronological ordering
            $table->index('created_at', 'idx_tracking_created');
            
            // Composite for delivery timeline
            $table->index(['delivery_id', 'created_at'], 'idx_tracking_delivery_timeline');
        });

        // ==================== HOSPITALS TABLE ====================
        Schema::table('hospitals', function (Blueprint $table) {
            // Search by name
            $table->index('name', 'idx_hospitals_name');
            
            // Hub relationship (from Task 2)
            if (Schema::hasColumn('hospitals', 'hub_id')) {
                $table->index('hub_id', 'idx_hospitals_hub_id');
            }
            
            // Location queries (if columns exist)
            if (Schema::hasColumn('hospitals', 'district')) {
                $table->index('district', 'idx_hospitals_district');
            }
        });

        // ==================== MEDICAL_SUPPLIES TABLE ====================
        Schema::table('medical_supplies', function (Blueprint $table) {
            // Search by name and category
            $table->index('name', 'idx_supplies_name');
            if (Schema::hasColumn('medical_supplies', 'category')) {
                $table->index('category', 'idx_supplies_category');
            }
            
            // Stock level queries (if columns exist)
            if (Schema::hasColumn('medical_supplies', 'stock_quantity')) {
                $table->index('stock_quantity', 'idx_supplies_stock');
            }
        });

        // ==================== NOTIFICATIONS TABLE ====================
        Schema::table('notifications', function (Blueprint $table) {
            // Recipient queries (if column exists)
            if (Schema::hasColumn('notifications', 'recipient_id')) {
                $table->index('recipient_id', 'idx_notifications_recipient');
            }
            
            // User ID if different column name
            if (Schema::hasColumn('notifications', 'user_id')) {
                $table->index('user_id', 'idx_notifications_user');
            }
            
            // Timestamp ordering
            $table->index('created_at', 'idx_notifications_created');
        });

        // ==================== AUDIT_LOGS TABLE ====================
        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                // User activity tracking
                if (Schema::hasColumn('audit_logs', 'user_id')) {
                    $table->index('user_id', 'idx_audit_user_id');
                }
                
                // Event type filtering
                if (Schema::hasColumn('audit_logs', 'event')) {
                    $table->index('event', 'idx_audit_event');
                }
                
                // Timestamp ordering
                $table->index('created_at', 'idx_audit_created');
            });
        }

        // ==================== USER_ROLES TABLE ====================
        if (Schema::hasTable('user_roles')) {
            Schema::table('user_roles', function (Blueprint $table) {
                // Foreign keys (if not already indexed)
                $table->index('user_id', 'idx_user_roles_user_id');
                $table->index('role_id', 'idx_user_roles_role_id');
            });
        }

        // ==================== HUBS TABLE ====================
        if (Schema::hasTable('hubs')) {
            Schema::table('hubs', function (Blueprint $table) {
                // Name and code searches
                if (Schema::hasColumn('hubs', 'name')) {
                    $table->index('name', 'idx_hubs_name');
                }
                if (Schema::hasColumn('hubs', 'code')) {
                    $table->index('code', 'idx_hubs_code');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop all created indexes
        
        // Deliveries
        Schema::table('deliveries', function (Blueprint $table) {
            if (Schema::hasColumn('deliveries', 'otp_verified_at')) {
                $table->dropIndex('idx_deliveries_otp_verified');
            }
            $table->dropIndex('idx_deliveries_created_at');
        });

        // Delivery Requests
        Schema::table('delivery_requests', function (Blueprint $table) {
            $table->dropIndex('idx_requests_created_at');
        });

        // Drones
        Schema::table('drones', function (Blueprint $table) {
            if (Schema::hasColumn('drones', 'current_hub_id')) {
                $table->dropIndex('idx_drones_current_hub');
            }
            if (Schema::hasColumn('drones', 'assigned_operator_id')) {
                $table->dropIndex('idx_drones_operator');
            }
        });

        // Delivery Tracking
        Schema::table('delivery_tracking', function (Blueprint $table) {
            $table->dropIndex('idx_tracking_delivery_id');
            $table->dropIndex('idx_tracking_created');
            $table->dropIndex('idx_tracking_delivery_timeline');
        });

        // Hospitals
        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropIndex('idx_hospitals_name');
            if (Schema::hasColumn('hospitals', 'hub_id')) {
                $table->dropIndex('idx_hospitals_hub_id');
            }
            if (Schema::hasColumn('hospitals', 'district')) {
                $table->dropIndex('idx_hospitals_district');
            }
        });

        // Medical Supplies
        Schema::table('medical_supplies', function (Blueprint $table) {
            $table->dropIndex('idx_supplies_name');
            if (Schema::hasColumn('medical_supplies', 'category')) {
                $table->dropIndex('idx_supplies_category');
            }
            if (Schema::hasColumn('medical_supplies', 'stock_quantity')) {
                $table->dropIndex('idx_supplies_stock');
            }
        });

        // Notifications
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'recipient_id')) {
                $table->dropIndex('idx_notifications_recipient');
            }
            if (Schema::hasColumn('notifications', 'user_id')) {
                $table->dropIndex('idx_notifications_user');
            }
            $table->dropIndex('idx_notifications_created');
        });

        // Audit Logs
        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                if (Schema::hasColumn('audit_logs', 'user_id')) {
                    $table->dropIndex('idx_audit_user_id');
                }
                if (Schema::hasColumn('audit_logs', 'event')) {
                    $table->dropIndex('idx_audit_event');
                }
                $table->dropIndex('idx_audit_created');
            });
        }

        // User Roles
        if (Schema::hasTable('user_roles')) {
            Schema::table('user_roles', function (Blueprint $table) {
                $table->dropIndex('idx_user_roles_user_id');
                $table->dropIndex('idx_user_roles_role_id');
            });
        }

        // Hubs
        if (Schema::hasTable('hubs')) {
            Schema::table('hubs', function (Blueprint $table) {
                if (Schema::hasColumn('hubs', 'name')) {
                    $table->dropIndex('idx_hubs_name');
                }
                if (Schema::hasColumn('hubs', 'code')) {
                    $table->dropIndex('idx_hubs_code');
                }
            });
        }
    }
};
