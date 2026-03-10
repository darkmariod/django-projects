<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'Juan Perez', 'identification' => '1234567890', 'identification_type' => 'cedula', 'email' => 'juan.perez@example.com', 'phone' => '+593987654321', 'address' => 'Guayaquil, Ecuador', 'is_active' => true],
            ['name' => 'Maria Garcia', 'identification' => '0912345678', 'identification_type' => 'ruc', 'email' => 'maria.garcia@example.com', 'phone' => '+593912345678', 'address' => 'Quito, Ecuador', 'is_active' => true],
            ['name' => 'Carlos Rodriguez', 'identification' => '1723456789', 'identification_type' => 'cedula', 'email' => 'carlos.r@example.com', 'phone' => '+593998765432', 'address' => 'Cuenca, Ecuador', 'is_active' => true],
            ['name' => 'Ana Martinez', 'identification' => '2012345678901', 'identification_type' => 'ruc', 'email' => 'ana.martinez@example.com', 'phone' => '+593994456789', 'address' => 'Manta, Ecuador', 'is_active' => true],
            ['name' => 'Pedro Lopez', 'identification' => 'ABC123456', 'identification_type' => 'passport', 'email' => 'pedro.lopez@example.com', 'phone' => '+593981234567', 'address' => 'Ambato, Ecuador', 'is_active' => true],
            ['name' => 'Laura Sanchez', 'identification' => '2345678901', 'identification_type' => 'cedula', 'email' => 'laura.s@example.com', 'phone' => '+593997654321', 'address' => 'Ibarra, Ecuador', 'is_active' => false],
            ['name' => 'Jorge Torres', 'identification' => '1812345678901', 'identification_type' => 'ruc', 'email' => 'jorge.torres@example.com', 'phone' => '+593991234567', 'address' => 'Riobamba, Ecuador', 'is_active' => true],
            ['name' => 'Sofia Hernandez', 'identification' => '1567890123', 'identification_type' => 'cedula', 'email' => 'sofia.h@example.com', 'phone' => '+593996789012', 'address' => 'Latacunga, Ecuador', 'is_active' => true],
            ['name' => 'Miguel Diaz', 'identification' => 'ED12345678', 'identification_type' => 'passport', 'email' => 'miguel.diaz@example.com', 'phone' => '+593987890123', 'address' => 'Machala, Ecuador', 'is_active' => true],
            ['name' => 'Carmen Flores', 'identification' => '1012345678901', 'identification_type' => 'ruc', 'email' => 'carmen.flores@example.com', 'phone' => '+593992345678', 'address' => 'Loja, Ecuador', 'is_active' => true],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
