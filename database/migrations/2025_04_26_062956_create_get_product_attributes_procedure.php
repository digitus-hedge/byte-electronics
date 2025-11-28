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
    public function up(): void
    {
        DB::unprepared('
        DROP PROCEDURE IF EXISTS GetProductAttributes;
    ');

    DB::unprepared('
        CREATE PROCEDURE GetProductAttributes(
            IN category_ids VARCHAR(255),
            IN sub_category_ids VARCHAR(255)
        )
        BEGIN
            SELECT 
                pad.name AS attribute_name,
                CONCAT("[", GROUP_CONCAT(
                    CONCAT(
                        "{\"document_id\":", JSON_QUOTE(CAST(pad.id AS CHAR)),
                        ",\"value\":", JSON_QUOTE(pad.value),
                        "}"
                    )
                    ORDER BY pad.value
                    SEPARATOR ","
                ), "]") AS attribute_values
            FROM 
                products p
                LEFT JOIN categories c ON p.category_id = c.id AND c.deleted_at IS NULL
                LEFT JOIN product_extended pe ON p.id = pe.product_id
                LEFT JOIN product_attributes pa ON pe.product_attr_id = pa.id
                LEFT JOIN product_headers ph ON pa.prd_header_id = ph.id
                LEFT JOIN product_attr_documents pad ON pe.id = pad.product_ext_id
            WHERE 
                p.deleted_at IS NULL
                AND ph.name = "Specifications"
                AND pad.name IS NOT NULL
                AND pad.value IS NOT NULL
                AND pad.name NOT IN (
                    "Product Image", "Manufacturer", "Product Type", "Product Category", "Product",
                    "Application Notes", "Brand", "Description", "Datasheet", "Image",
                    "Image Alt text", "Models", "Title", "Tradename", "Factory Pack Quantity"
                )
                AND (
                    category_ids = "" OR FIND_IN_SET(p.category_id, category_ids)
                )
                AND (
                    sub_category_ids = "" OR FIND_IN_SET(p.sub_category_id, sub_category_ids)
                    OR p.sub_category_id = 0
                )
            GROUP BY pad.name
            ORDER BY pad.name;
        END
    ');
}

public function down()
{
    DB::unprepared('DROP PROCEDURE IF EXISTS GetProductAttributes;');
}
};
