<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DailyStockCountExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'Product ID',
            'Product Name',
            'Brand',
            'Pack Size',
            'Sold Quantity',
            'Quantity On Hand',
            'Physical Stock',
            'Difference',
        ];
    }

    public function map($row): array
    {
        // Ensure all keys exist to prevent errors
        $product_id = $row['product_id'] ?? 'N/A';
        $product_name = $row['product_name'] ?? 'N/A';
        $brand = $row['brand'] ?? 'N/A';
        $pack_size = $row['pack_size'] ?? 'N/A';
        $quantity_sold = $row['quantity_sold'] ?? 0;
        $quantity_on_hand = $row['quantity_on_hand'] ?? 0;
        
     // This needs clarification based on exactly what data should be exported.
        $physical_stock = $row['physical_stock'] ?? 'N/A'; 
        $difference = $row['difference'] ?? 'N/A';

        return [
            $product_id,
            $product_name,
            $brand,
            $pack_size,
            $quantity_sold,
            $quantity_on_hand,
            $physical_stock,
            $difference,
        ];
    }
} 