<?php

namespace Database\Seeders;

use App\Models\EmasSubject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmasSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            // Core Subjects
            ['name' => 'Mathematics', 'code' => 'MATH', 'description' => 'Basic Mathematics and Advanced Mathematics', 'status' => 'active'],
            ['name' => 'English Language', 'code' => 'ENG', 'description' => 'English Language', 'status' => 'active'],
            ['name' => 'Kiswahili', 'code' => 'KIS', 'description' => 'Kiswahili Language', 'status' => 'active'],
            ['name' => 'Civics', 'code' => 'CIV', 'description' => 'Civics Studies', 'status' => 'active'],
            
            // Science Subjects
            ['name' => 'Biology', 'code' => 'BIO', 'description' => 'Biology', 'status' => 'active'],
            ['name' => 'Chemistry', 'code' => 'CHEM', 'description' => 'Chemistry', 'status' => 'active'],
            ['name' => 'Physics', 'code' => 'PHY', 'description' => 'Physics', 'status' => 'active'],
            
            // Arts & Humanities
            ['name' => 'History', 'code' => 'HIST', 'description' => 'History', 'status' => 'active'],
            ['name' => 'Geography', 'code' => 'GEO', 'description' => 'Geography', 'status' => 'active'],
            ['name' => 'Bible Knowledge', 'code' => 'BK', 'description' => 'Christian Religious Education (CRE)', 'status' => 'active'],
            ['name' => 'Islamic Knowledge', 'code' => 'IK', 'description' => 'Islamic Religious Education (IRE)', 'status' => 'active'],
            
            // Commerce & Business
            ['name' => 'Book Keeping', 'code' => 'BK', 'description' => 'Book Keeping', 'status' => 'active'],
            ['name' => 'Commerce', 'code' => 'COMM', 'description' => 'Commerce', 'status' => 'active'],
            ['name' => 'Accountancy', 'code' => 'ACC', 'description' => 'Accountancy (Advanced Level)', 'status' => 'active'],
            
            // Languages
            ['name' => 'French', 'code' => 'FRE', 'description' => 'French Language', 'status' => 'active'],
            ['name' => 'Arabic', 'code' => 'ARA', 'description' => 'Arabic Language', 'status' => 'active'],
            ['name' => 'Chinese', 'code' => 'CHI', 'description' => 'Chinese Language', 'status' => 'active'],
            
            // Technical & Practical
            ['name' => 'Computer Studies', 'code' => 'COMP', 'description' => 'Computer Studies / ICT', 'status' => 'active'],
            ['name' => 'Agriculture', 'code' => 'AGR', 'description' => 'Agriculture', 'status' => 'active'],
            ['name' => 'Home Economics', 'code' => 'HE', 'description' => 'Home Economics', 'status' => 'active'],
            ['name' => 'Food & Nutrition', 'code' => 'FN', 'description' => 'Food and Nutrition', 'status' => 'active'],
            
            // Arts & Creative
            ['name' => 'Fine Art', 'code' => 'ART', 'description' => 'Fine Art', 'status' => 'active'],
            ['name' => 'Music', 'code' => 'MUS', 'description' => 'Music', 'status' => 'active'],
            
            // Physical Education
            ['name' => 'Physical Education', 'code' => 'PE', 'description' => 'Physical Education and Sports', 'status' => 'active'],
        ];

        foreach ($subjects as $subject) {
            EmasSubject::create($subject);
        }

        $this->command->info('✓ ' . count($subjects) . ' subjects created successfully!');
    }
}
