<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // أنواع العقارات
        Schema::create('ddf_property_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('source')->default('api');
            $table->timestamps();
        });

        // أنواع التعاملات
        Schema::create('ddf_transaction_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('source')->default('api');
            $table->timestamps();
        });

        // حالات العقارات
        Schema::create('ddf_property_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('source')->default('api');
            $table->timestamps();
        });

        // جدول العقارات
        Schema::create('ddf_properties', function (Blueprint $table) {
            $table->id();
            $table->string('mls_number')->unique();
            $table->unsignedBigInteger('property_type_id')->nullable();
            $table->unsignedBigInteger('transaction_type_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->unsignedBigInteger('office_id')->nullable();
            $table->date('listing_date')->nullable();
            $table->json('raw_data')->nullable();
            $table->string('source')->default('api');
            $table->timestamps();

            $table->foreign('property_type_id')->references('id')->on('ddf_property_types')->nullOnDelete();
            $table->foreign('transaction_type_id')->references('id')->on('ddf_transaction_types')->nullOnDelete();
            $table->foreign('status_id')->references('id')->on('ddf_property_statuses')->nullOnDelete();
        });

        // جدول الأسعار
        Schema::create('ddf_property_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->string('price_type')->default('sale');
            $table->decimal('amount', 12, 2)->nullable();
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('ddf_properties')->onDelete('cascade');
        });

        // جدول الصور
        Schema::create('ddf_property_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->string('url');
            $table->integer('position')->default(0);
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('ddf_properties')->onDelete('cascade');
        });

        // جدول الوكلاء
        Schema::create('ddf_agents', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('photo')->nullable();
            $table->string('agent_code')->nullable();
            $table->timestamps();
        });

        // جدول المكاتب
        Schema::create('ddf_offices', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('office_code')->nullable();
            $table->timestamps();
        });

        // جدول الموقع
        Schema::create('ddf_property_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('ddf_properties')->onDelete('cascade');
        });

        // جدول المواصفات
        Schema::create('ddf_property_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->string('feature_name');
            $table->string('feature_value')->nullable();
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('ddf_properties')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('ddf_property_features');
        Schema::dropIfExists('ddf_property_locations');
        Schema::dropIfExists('ddf_offices');
        Schema::dropIfExists('ddf_agents');
        Schema::dropIfExists('ddf_property_photos');
        Schema::dropIfExists('ddf_property_prices');
        Schema::dropIfExists('ddf_properties');
        Schema::dropIfExists('ddf_property_statuses');
        Schema::dropIfExists('ddf_transaction_types');
        Schema::dropIfExists('ddf_property_types');
    }
};

