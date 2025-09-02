<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NavItems extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {        
        DB::table('navigation')->insert([
            'name' => 'Dashboard',
            'url' => '/dashboard',
            'roles' => json_encode(['admin', 'manager']),
            'icon' => 'LayoutGrid',
        ]);

        DB::table('navigation')->insert([
            'name' => 'Invoices',
            'url' => '/invoices',
            'roles' => json_encode(['admin', 'manager']),
            'icon' => 'BookOpen',
        ]);

        DB::table('navigation')->insert([
            'name' => 'InvoiceDB',
            'url' => '/invoicesDB',
            'roles' => json_encode(['admin', 'manager']),
            'icon' => 'BookOpen',
        ]);
    }
}
