<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Personal Information
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('phone')->nullable()->after('email');
            $table->date('date_of_birth')->nullable()->after('phone');
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable()->after('date_of_birth');
            
            // Professional Information
            $table->string('employee_id')->nullable()->unique()->after('gender');
            $table->string('department')->nullable()->after('employee_id');
            $table->string('position_title')->nullable()->after('department');
            $table->foreignId('hospital_id')->nullable()->constrained('hospitals')->onDelete('set null')->after('position_title');
            $table->string('license_number')->nullable()->after('hospital_id');
            $table->date('license_expiry_date')->nullable()->after('license_number');
            $table->json('certifications')->nullable()->after('license_expiry_date');
            
            // Address Information
            $table->text('address')->nullable()->after('certifications');
            $table->string('city')->nullable()->after('address');
            $table->string('state_province')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('state_province');
            $table->string('country')->nullable()->after('postal_code');
            
            // Account Status
            $table->enum('status', [
                'active',
                'inactive',
                'suspended',
                'pending_approval',
                'terminated'
            ])->default('pending_approval')->after('country');
            $table->boolean('is_verified')->default(false)->after('status');
            $table->datetime('verified_at')->nullable()->after('is_verified');
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->onDelete('set null')->after('verified_at');
            
            // Security & Access
            $table->datetime('last_login_at')->nullable()->after('verified_by_user_id');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->boolean('two_factor_enabled')->default(false)->after('last_login_ip');
            $table->string('two_factor_secret')->nullable()->after('two_factor_enabled');
            $table->json('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            $table->datetime('password_changed_at')->nullable()->after('two_factor_recovery_codes');
            $table->boolean('must_change_password')->default(false)->after('password_changed_at');
            
            // Preferences & Settings
            $table->string('timezone')->default('UTC')->after('must_change_password');
            $table->string('language')->default('en')->after('timezone');
            $table->json('notification_preferences')->nullable()->after('language');
            $table->string('profile_photo_path')->nullable()->after('notification_preferences');
            
            // Emergency Contact
            $table->string('emergency_contact_name')->nullable()->after('profile_photo_path');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_phone');
            
            // Additional Information
            $table->text('bio')->nullable()->after('emergency_contact_relationship');
            $table->json('skills')->nullable()->after('bio');
            $table->json('metadata')->nullable()->after('skills');
            
            // Indexes
            $table->index(['status', 'is_verified']);
            $table->index(['hospital_id', 'status']);
            $table->index(['employee_id', 'status']);
            $table->index('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove indexes first
            $table->dropIndex(['status', 'is_verified']);
            $table->dropIndex(['hospital_id', 'status']);
            $table->dropIndex(['employee_id', 'status']);
            $table->dropIndex(['last_login_at']);
            
            // Remove foreign key constraints
            $table->dropForeign(['hospital_id']);
            $table->dropForeign(['verified_by_user_id']);
            
            // Remove columns
            $table->dropColumn([
                'first_name',
                'last_name',
                'phone',
                'date_of_birth',
                'gender',
                'employee_id',
                'department',
                'position_title',
                'hospital_id',
                'license_number',
                'license_expiry_date',
                'certifications',
                'address',
                'city',
                'state_province',
                'postal_code',
                'country',
                'status',
                'is_verified',
                'verified_at',
                'verified_by_user_id',
                'last_login_at',
                'last_login_ip',
                'two_factor_enabled',
                'two_factor_secret',
                'two_factor_recovery_codes',
                'password_changed_at',
                'must_change_password',
                'timezone',
                'language',
                'notification_preferences',
                'profile_photo_path',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relationship',
                'bio',
                'skills',
                'metadata'
            ]);
        });
    }
};
