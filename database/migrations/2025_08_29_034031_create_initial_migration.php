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
        // Users Table
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['employee', 'manager', 'admin']);
            $table->string('freshteams_id')->nullable();
        });

        // Invoice Templates Table
        Schema::create('invoice_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('file_path');
            $table->timestamps();
        });
        
        // Projects Table
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_id')->unique();
            $table->string('name');
            $table->enum('status', ['active', 'inactive', 'pending'])->default('inactive');
            $table->timestamps();
        });
        
        // Employees Table
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique();
            $table->enum('status', ['probation', 'regular', 'terminated', 'contract'])->default('probation');
            $table->string('email')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number');
            $table->foreignId('project_id')->constrained('projects');
            $table->timestamps();
        });

        // Invoices Table
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->enum('status', ['draft', 'pending', 'paid', 'void'])->default('draft');
            $table->foreignId('issued_by_id')->constrained('users');
            $table->foreignId('for_employee_id')->constrained('employees');
            $table->decimal('total_amount', 10, 2);
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('sent_date')->nullable();
            $table->foreignId('template_id')->constrained('invoice_templates');
            $table->timestamps();
        });

        // Invoice Items Table
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->string('reimbursement_keyword')->nullable();
            $table->timestamps();
        });

        // Reports Table
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('report_type', ['monthly', 'on-demand']);
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignId('generated_by_id')->constrained('users');
            $table->string('file_path');
            $table->timestamps();
        });

        // Navigation Table
        Schema::create('navigation', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url')->unique();
            $table->integer('order')->default(0);
            $table->json('roles'); // To store an array of roles (e.g., ['admin', 'manager'])
            $table->string('icon')->nullable(); // For a class name or emoji
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('invoice_templates');
        Schema::dropIfExists('navigation');
        Schema::dropIfExists('users');
    }
};
