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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop')->constrained()->onDelete('cascade');
            $table->string('order_id');
            $table->string('unique_mailitem_id');
            $table->string('identifier');
            $table->string('event');
            $table->string('ForceDuplicate')->nullable();
            $table->string('MailProductType')->nullable();
            $table->string('EventType')->nullable();
            $table->string('Username')->nullable();
            $table->string('Facility')->nullable();
            $table->string('Timestamp')->nullable();
            $table->string('Weight')->nullable();
            $table->string('Condition')->nullable();
            // Sender fields
            $table->string('SenderName')->nullable();
            $table->string('SenderAddress')->nullable();
            $table->string('SenderPostcode')->nullable();
            $table->string('SenderCity')->nullable();
            $table->string('SenderPhone')->nullable();
            $table->string('SenderEmail')->nullable();
            $table->string('SenderPOBox')->nullable();
            $table->string('RecipientName')->nullable();
            $table->string('RecipientAddress')->nullable();
            $table->string('RecipientPostcode')->nullable();
            $table->string('RecipientCity')->nullable();
            $table->string('RecipientPhone')->nullable();
            $table->string('RecipientEmail')->nullable();
            $table->string('RecipientPOBox')->nullable();
            $table->enum('order_status', [
                'shipment-ready',
                'booked',
                'handed to eps',
                'delivered',
                'delivery-failed'
            ])->default('shipment-ready');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
