<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Names
            $table->string('first_name')->after('user_id');
            $table->string('last_name')->after('first_name');

            // Billing address fields
            $table->string('billing_first_name')->nullable();
            $table->string('billing_last_name')->nullable();
            $table->string('billing_company_name')->nullable();
            $table->string('billing_address_line1')->nullable();
            $table->string('billing_address_line2')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_postal_code')->nullable();
            $table->string('billing_country_code')->nullable();
            $table->unsignedBigInteger('billing_country_id')->nullable();
            $table->string('billing_phone')->nullable();
            $table->text('billing_attention')->nullable();

            // Shipping address fields
            $table->string('shipping_first_name')->nullable();
            $table->string('shipping_last_name')->nullable();
            $table->string('shipping_company_name')->nullable();
            $table->string('shipping_address_line1')->nullable();
            $table->string('shipping_address_line2')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_state')->nullable();
            $table->string('shipping_postal_code')->nullable();
            $table->string('shipping_country_code')->nullable();
            $table->unsignedBigInteger('shipping_country_id')->nullable();
            $table->string('shipping_phone')->nullable();
            $table->text('shipping_attention')->nullable();

            // Optional address reference
            $table->unsignedBigInteger('billing_address_id')->nullable();
            $table->unsignedBigInteger('shipping_address_id')->nullable();

            $table->foreign('billing_address_id')->references('id')->on('customer_addresses')->onDelete('set null');
            $table->foreign('shipping_address_id')->references('id')->on('customer_addresses')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['billing_address_id']);
            $table->dropForeign(['shipping_address_id']);

            $table->dropColumn([
                'first_name', 'last_name',
                'billing_first_name', 'billing_last_name', 'billing_company_name', 'billing_address_line1', 'billing_address_line2', 'billing_city', 'billing_state', 'billing_postal_code', 'billing_country_code', 'billing_country_id', 'billing_phone', 'billing_attention',
                'shipping_first_name', 'shipping_last_name', 'shipping_company_name', 'shipping_address_line1', 'shipping_address_line2', 'shipping_city', 'shipping_state', 'shipping_postal_code', 'shipping_country_code', 'shipping_country_id', 'shipping_phone', 'shipping_attention',
                'billing_address_id', 'shipping_address_id',
            ]);
        });
    }
};
