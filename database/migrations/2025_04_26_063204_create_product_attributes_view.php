<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::unprepared('
            DROP VIEW IF EXISTS product_attributes_view;
        ');

        DB::unprepared('
            CREATE VIEW product_attributes_view AS
            SELECT 
                pad.name AS attribute_name,
                CONCAT("[", GROUP_CONCAT(
                    JSON_QUOTE(pad.value)
                    ORDER BY pad.value
                    SEPARATOR ","
                ), "]") AS attribute_values
            FROM 
                products p
                JOIN categories c ON p.category_id = c.id
                JOIN sub_categories sc ON p.sub_category_id = sc.id
                JOIN product_extended pe ON p.id = pe.product_id
                JOIN product_attributes pa ON pe.product_attr_id = pa.id
                JOIN product_headers ph ON pa.prd_header_id = ph.id
                JOIN product_attr_documents pad ON pe.id = pad.product_ext_id
            WHERE 
                p.deleted_at IS NULL
                AND c.deleted_at IS NULL
                AND sc.deleted_at IS NULL
                AND ph.name = "Specifications"
                AND pad.name IS NOT NULL
                AND pad.value IS NOT NULL
            GROUP BY pad.name
            ORDER BY pad.name;
        ');
    }

    public function down()
    {
        DB::unprepared('DROP VIEW IF EXISTS product_attributes_view;');
    }
};
