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
        Schema::create('tickets', function (Blueprint $table) {
                $table->id();

                $table->string('ticket_id')->nullable();        // e.g. TK-1024
                $table->string('user_id')->nullable();          // who submitted
                $table->string('responder_id')->nullable();     // IT, Facility, Electrical, Aircondition

                $table->string('subject')->nullable();
                $table->text('description')->nullable();

                $table->string('priority_id')->nullable();      // High, Medium, Low
                $table->string('status_id')->nullable();        // Open, In Progress, Resolved
                $table->string('service_category_id')->nullable();

                $table->string('service_start_at')->nullable();
                $table->string('service_end_at')->nullable();

                $table->string('assigned_to')->nullable();

                $table->timestamps();                           // created_at, updated_at

                $table->string('resolved_at')->nullable();
                $table->string('closed_at')->nullable();
        });
    }

    // public function up(): void
    // {
    //     Schema::create('tickets', function (Blueprint $table) {
    //         $table->id();

    //         // Identifiers
    //         $table->string('ticket_id')->unique();           // e.g. TK-1024
    //         $table->foreignId('user_id')                     // who submitted
    //               ->constrained('users')
    //               ->cascadeOnDelete();
    //         $table->foreignId('responder_id')                // IT, Facility, etc.
    //               ->nullable()
    //               ->constrained('responders')
    //               ->nullOnDelete();

    //         // Core content
    //         $table->string('subject');
    //         $table->text('description')->nullable();

    //         // Lookups
    //         $table->foreignId('priority_id')
    //               ->nullable()
    //               ->constrained('priorities')
    //               ->nullOnDelete();
    //         $table->foreignId('status_id')
    //               ->nullable()
    //               ->constrained('statuses')
    //               ->nullOnDelete();
    //         $table->foreignId('service_category_id')
    //               ->nullable()
    //               ->constrained('service_categories')
    //               ->nullOnDelete();

    //         // Assignee (could be a user or a team label)
    //         $table->string('assigned_to')->nullable();

    //         // Service window
    //         $table->timestamp('service_start_at')->nullable();
    //         $table->timestamp('service_end_at')->nullable();

    //         // Lifecycle timestamps
    //         $table->timestamp('resolved_at')->nullable();
    //         $table->timestamp('closed_at')->nullable();

    //         // Created / updated
    //         $table->timestamps();   // creates created_at and updated_at

    //         // Optional: index for common filters
    //         $table->index(['status_id', 'priority_id']);
    //         $table->index('assigned_to');
    //     });
    // }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

