<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Contact::create([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'email@example.com',
            'Subject' => 'This is a Subject',
            'Message' => 'Message...',
            'ip' => '127.0.0.1',
            'created_by' => 1,
        ]);
    }
}
