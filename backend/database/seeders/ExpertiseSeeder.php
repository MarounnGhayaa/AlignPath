<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ExpertiseSeeder extends Seeder {
public function run(): void {
    $now = now();


    $expertises = [
    // Computing & Data
    'Computer Science', 'Software Engineering', 'Information Systems', 'Information Technology',
    'Data Science', 'Artificial Intelligence', 'Machine Learning', 'Data Engineering', 'Big Data',
    'Business Analytics', 'Statistics', 'Mathematics', 'Applied Mathematics',
    'Cybersecurity', 'Network Engineering', 'Cloud Computing', 'Cloud & Infrastructure', 'DevOps',
    'Blockchain', 'Internet of Things (IoT)', 'Robotics', 'Embedded Systems', 'AR / VR',
    'Mobile Development', 'Web Development', 'Frontend Development', 'Backend Development', 'Full-Stack Development', 'Game Development', 'Quality Assurance (QA)', 'Product Management', 'Project Management',


    // Engineering
    'Computer Engineering', 'Electrical Engineering', 'Electronics & Communication', 'Mechanical Engineering',
    'Civil Engineering', 'Industrial Engineering', 'Chemical Engineering', 'Biomedical Engineering',
    'Aerospace Engineering', 'Automotive Engineering', 'Marine Engineering', 'Environmental Engineering',
    'Petroleum Engineering', 'Renewable Energy', 'Architecture', 'Urban Planning',


    // Natural Sciences
    'Physics', 'Chemistry', 'Biology', 'Biotechnology', 'Environmental Science', 'Geology', 'Geography', 'Marine Biology',


    // Health & Life
    'Medicine', 'Nursing', 'Pharmacy', 'Dentistry', 'Public Health', 'Nutrition & Dietetics',


    // Business & Economics
    'Business Administration', 'Management', 'Entrepreneurship', 'Finance', 'Accounting', 'Economics',
    'Marketing', 'Digital Marketing', 'Human Resources', 'Supply Chain Management', 'Operations Management',
    'International Business', 'Real Estate', 'Logistics',


    // Social Sciences & Humanities
    'Psychology', 'Sociology', 'Political Science', 'International Relations', 'Law',
    'History', 'Philosophy', 'Linguistics', 'English Literature', 'Education', 'Communications',
    'Journalism', 'Public Relations', 'Media Studies', 'Film & Television',


    // Arts & Design
    'Fine Arts', 'Graphic Design', 'Product Design', 'UI / UX Design', 'Music', 'Performing Arts',


    // Hospitality & Others
    'Hospitality & Tourism', 'Culinary Arts', 'Agriculture', 'Veterinary Science',
        'Software Engineering',

    'Artist',
    'Doctor',
    'Teacher',
    'Data Scientist',
    'Technology & IT',
    'Healthcare & Medicine',
    'Education & Academia',
    'Business & Finance',
    'Arts & Design',
    ];


    foreach ($expertises as $name) {
        DB::table('expertises')->updateOrInsert(
            ['name' => $name],
            ['created_at' => $now, 'updated_at' => $now]
            );
        }
    }
}