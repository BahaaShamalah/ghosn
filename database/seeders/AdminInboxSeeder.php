<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use App\Models\VolunteerApplication;
use Illuminate\Database\Seeder;

class AdminInboxSeeder extends Seeder
{
    public function run(): void
    {
        if (VolunteerApplication::query()->exists()) {
            return;
        }

        $volunteers = [
            ['name' => 'Nadia Farouk', 'email' => 'nadia@email.com', 'area' => 'field_relief', 'status' => VolunteerApplication::STATUS_PENDING],
            ['name' => 'Tariq Sami', 'email' => 'tariq@email.com', 'area' => 'logistics', 'status' => VolunteerApplication::STATUS_APPROVED],
            ['name' => 'Huda Nasser', 'email' => 'huda@email.com', 'area' => 'media', 'status' => VolunteerApplication::STATUS_PENDING],
            ['name' => 'Yousef Adel', 'email' => 'yousef@email.com', 'area' => 'fundraising', 'status' => VolunteerApplication::STATUS_APPROVED],
        ];

        foreach ($volunteers as $row) {
            VolunteerApplication::query()->create([
                ...$row,
                'phone' => '+970599000000',
                'message' => 'I would like to contribute my time to GHOSN relief efforts.',
                'locale' => 'en',
            ]);
        }

        $messages = [
            ['name' => 'Rania Kamal', 'email' => 'rania@email.com', 'subject' => 'Partnership proposal', 'message' => 'We would love to explore a corporate partnership for the clean water program.', 'is_read' => false],
            ['name' => 'Sami Deeb', 'email' => 'sami@email.com', 'subject' => 'Question about my donation', 'message' => 'Hi, I wanted to confirm my monthly donation was processed correctly.', 'is_read' => false],
            ['name' => 'Fatima Zahra', 'email' => 'fatima@email.com', 'subject' => 'Thank you!', 'message' => 'Just wanted to say your team changed my village. Grateful beyond words.', 'is_read' => true],
        ];

        foreach ($messages as $row) {
            ContactMessage::query()->create([
                ...$row,
                'locale' => 'en',
            ]);
        }
    }
}
