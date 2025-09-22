<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MentorSeeder extends Seeder {
    public function run(): void {
        $now = now();
        $mentors = [
            ['username'=>'sara.haddad','email'=>'sara.haddad@example.com','role'=>'mentor','position'=>'Senior Frontend Engineer','company'=>'Cedar Labs','location'=>'Beirut, LB','expertises'=>['Software Engineering','Frontend Development','Web Development','UI / UX Design','Arts & Design']],
            ['username'=>'ziad.ayoub','email'=>'ziad.ayoub@example.com','role'=>'mentor','position'=>'Full-Stack Engineer','company'=>'Cedar Labs','location'=>'Beirut, LB','expertises'=>['Software Engineering','Full-Stack Development','Web Development']],
            ['username'=>'rami.nassar','email'=>'rami.nassar@example.com','role'=>'mentor','position'=>'Backend Engineer','company'=>'Cedar Labs','location'=>'Beirut, LB','expertises'=>['Software Engineering','Backend Development']],
            ['username'=>'rita.sleiman','email'=>'rita.sleiman@example.com','role'=>'mentor','position'=>'Mobile Engineer','company'=>'Phoenicia Digital','location'=>'Jounieh, LB','expertises'=>['Software Engineering','Mobile Development']],
            ['username'=>'lina.fares','email'=>'lina.fares@example.com','role'=>'mentor','position'=>'Product Designer','company'=>'Phoenicia Digital','location'=>'Tripoli, LB','expertises'=>['UI / UX Design','Product Design','Arts & Design']],
            ['username'=>'rita.harb','email'=>'rita.harb@example.com','role'=>'mentor','position'=>'Frontend Engineer','company'=>'Cedar Labs','location'=>'Beirut, LB','expertises'=>['Software Engineering','Frontend Development','Web Development']],
            ['username'=>'ali.taher','email'=>'ali.taher@example.com','role'=>'mentor','position'=>'Backend Engineer','company'=>'Byblos Apps','location'=>'Byblos, LB','expertises'=>['Software Engineering','Backend Development','Information Systems']],
            ['username'=>'maya.ghosn','email'=>'maya.ghosn@example.com','role'=>'mentor','position'=>'Product Manager','company'=>'Byblos Apps','location'=>'Byblos, LB','expertises'=>['Product Management','Project Management','Software Engineering']],

            // --- Cloud / DevOps / Security / IT ---
            ['username'=>'mohamad.ali','email'=>'mohamad.ali@example.com','role'=>'mentor','position'=>'DevOps Engineer','company'=>'Beirut Cloud','location'=>'Saida, LB','expertises'=>['DevOps','Cloud & Infrastructure','Cloud Computing','Technology & IT']],
            ['username'=>'karim.abdallah','email'=>'karim.abdallah@example.com','role'=>'mentor','position'=>'Cloud Architect','company'=>'Beirut Cloud','location'=>'Beirut, LB','expertises'=>['Cloud Computing','Cloud & Infrastructure','Technology & IT']],
            ['username'=>'rana.kassem','email'=>'rana.kassem@example.com','role'=>'mentor','position'=>'Cloud Architect','company'=>'Levant Cloud','location'=>'Beirut, LB','expertises'=>['Cloud & Infrastructure','Technology & IT']],
            ['username'=>'karim.itani','email'=>'karim.itani@example.com','role'=>'mentor','position'=>'IT Consultant','company'=>'Cedar IT','location'=>'Beirut, LB','expertises'=>['Technology & IT','Information Technology']],
            ['username'=>'nour.karam','email'=>'nour.karam@example.com','role'=>'mentor','position'=>'Security Analyst','company'=>'Marhaba Security','location'=>'Zahle, LB','expertises'=>['Cybersecurity','Network Engineering','Technology & IT']],
            ['username'=>'omar.ismail','email'=>'omar.ismail@example.com','role'=>'mentor','position'=>'Network Engineer','company'=>'NetLevant','location'=>'Saida, LB','expertises'=>['Network Engineering','Cybersecurity','Technology & IT']],

            // --- Data / AI ---
            ['username'=>'elie.khoury','email'=>'elie.khoury@example.com','role'=>'mentor','position'=>'Data Scientist','company'=>'Levant Tech','location'=>'Byblos, LB','expertises'=>['Data Scientist','Data Science','Machine Learning','Artificial Intelligence','Statistics']],
            ['username'=>'dalia.hussein','email'=>'dalia.hussein@example.com','role'=>'mentor','position'=>'Data Engineer','company'=>'Levant Data','location'=>'Tripoli, LB','expertises'=>['Data Engineering','Big Data','Data Science']],
            ['username'=>'jad.harb','email'=>'jad.harb@example.com','role'=>'mentor','position'=>'AI Researcher','company'=>'Levant AI','location'=>'Beirut, LB','expertises'=>['Artificial Intelligence','Machine Learning','Data Science']],
            ['username'=>'karim.mansour','email'=>'karim.mansour@example.com','role'=>'mentor','position'=>'Senior Data Scientist','company'=>'Cedar Analytics','location'=>'Beirut, LB','expertises'=>['Data Scientist','Data Science','Business Analytics']],
            ['username'=>'salma.azzam','email'=>'salma.azzam@example.com','role'=>'mentor','position'=>'Statistician','company'=>'Stats Lab','location'=>'Beirut, LB','expertises'=>['Statistics','Mathematics','Data Science']],

            // --- Games & XR ---
            ['username'=>'tony.azar','email'=>'tony.azar@example.com','role'=>'mentor','position'=>'Game Developer','company'=>'Cedars Games','location'=>'Beirut, LB','expertises'=>['Game Development','AR / VR','Software Engineering']],
            ['username'=>'sami.nahas','email'=>'sami.nahas@example.com','role'=>'mentor','position'=>'XR Engineer','company'=>'Levant XR','location'=>'Beirut, LB','expertises'=>['AR / VR','Game Development','Graphics']],

            // --- Business & Finance ---
            ['username'=>'nancy.kfoury','email'=>'nancy.kfoury@example.com','role'=>'mentor','position'=>'Business Analyst','company'=>'Cedar Capital','location'=>'Beirut, LB','expertises'=>['Business & Finance','Business Analytics','Finance']],
            ['username'=>'ghassan.said','email'=>'ghassan.said@example.com','role'=>'mentor','position'=>'Finance Manager','company'=>'Levant Finance','location'=>'Beirut, LB','expertises'=>['Business & Finance','Finance','Economics']],
            ['username'=>'george.hanna','email'=>'george.hanna@example.com','role'=>'mentor','position'=>'Accounting Lead','company'=>'Phoenicia Group','location'=>'Beirut, LB','expertises'=>['Accounting','Business & Finance']],
            ['username'=>'bashir.safadi','email'=>'bashir.safadi@example.com','role'=>'mentor','position'=>'Supply Chain Manager','company'=>'Cedar Logistics','location'=>'Beirut, LB','expertises'=>['Supply Chain Management','Operations Management','Business & Finance']],
            ['username'=>'farah.hobeika','email'=>'farah.hobeika@example.com','role'=>'mentor','position'=>'HR Manager','company'=>'LebTalent','location'=>'Zahle, LB','expertises'=>['Human Resources','Management','Business Administration']],

            // --- Education & Academia / Teacher ---
            ['username'=>'nada.mortada','email'=>'nada.mortada@example.com','role'=>'mentor','position'=>'University Lecturer','company'=>'Phoenicia University','location'=>'Zahle, LB','expertises'=>['Education & Academia','Teacher','Education']],
            ['username'=>'hani.ghobeiri','email'=>'hani.ghobeiri@example.com','role'=>'mentor','position'=>'High School Teacher','company'=>'Cedars High','location'=>'Beirut, LB','expertises'=>['Teacher','Education & Academia','Education']],
            ['username'=>'rima.shammas','email'=>'rima.shammas@example.com','role'=>'mentor','position'=>'Instructional Designer','company'=>'EdTech Levant','location'=>'Beirut, LB','expertises'=>['Education','Education & Academia']],

            // --- Healthcare & Medicine / Doctor ---
            ['username'=>'rania.harb','email'=>'rania.harb@example.com','role'=>'mentor','position'=>'Physician','company'=>'MedEast Hospital','location'=>'Beirut, LB','expertises'=>['Doctor','Medicine','Healthcare & Medicine']],
            ['username'=>'samer.ghanem','email'=>'samer.ghanem@example.com','role'=>'mentor','position'=>'Public Health Specialist','company'=>'Leb Health','location'=>'Byblos, LB','expertises'=>['Public Health','Healthcare & Medicine']],
            ['username'=>'lina.karam','email'=>'lina.karam@example.com','role'=>'mentor','position'=>'Nurse Supervisor','company'=>'St. George Medical','location'=>'Beirut, LB','expertises'=>['Nursing','Healthcare & Medicine']],
            ['username'=>'faten.salem','email'=>'faten.salem@example.com','role'=>'mentor','position'=>'Clinical Pharmacist','company'=>'Cedar Pharma','location'=>'Beirut, LB','expertises'=>['Pharmacy','Healthcare & Medicine']],

            // --- Arts & Design / Artist ---
            ['username'=>'mira.kaddoum','email'=>'mira.kaddoum@example.com','role'=>'mentor','position'=>'Visual Artist','company'=>'Studio Mira','location'=>'Zahle, LB','expertises'=>['Artist','Fine Arts','Arts & Design']],
            ['username'=>'reem.saad','email'=>'reem.saad@example.com','role'=>'mentor','position'=>'Marketing Designer','company'=>'Phoenicia Digital','location'=>'Beirut, LB','expertises'=>['Arts & Design','UI / UX Design','Digital Marketing']],
            ['username'=>'dana.saad','email'=>'dana.saad@example.com','role'=>'mentor','position'=>'Graphic Designer','company'=>'Cedar Creative','location'=>'Beirut, LB','expertises'=>['Graphic Design','Arts & Design']],
            ['username'=>'joelle.ataya','email'=>'joelle.ataya@example.com','role'=>'mentor','position'=>'Music Producer','company'=>'Ataya Studio','location'=>'Beirut, LB','expertises'=>['Music','Performing Arts','Arts & Design']],

            // --- Engineering branches ---
            ['username'=>'fadi.mansour','email'=>'fadi.mansour@example.com','role'=>'mentor','position'=>'Electrical Engineer','company'=>'EM Power','location'=>'Beirut, LB','expertises'=>['Electrical Engineering','Electronics & Communication']],
            ['username'=>'yasmin.hoteit','email'=>'yasmin.hoteit@example.com','role'=>'mentor','position'=>'Civil Engineer','company'=>'Levant Builders','location'=>'Beirut, LB','expertises'=>['Civil Engineering','Urban Planning']],
            ['username'=>'samer.hassan','email'=>'samer.hassan@example.com','role'=>'mentor','position'=>'Mechanical Engineer','company'=>'Levant Motors','location'=>'Beirut, LB','expertises'=>['Mechanical Engineering','Automotive Engineering']],
            ['username'=>'ralph.bassil','email'=>'ralph.bassil@example.com','role'=>'mentor','position'=>'Industrial Engineer','company'=>'Phoenicia Manufacturing','location'=>'Beirut, LB','expertises'=>['Industrial Engineering','Operations Management']],
            ['username'=>'adnan.kassis','email'=>'adnan.kassis@example.com','role'=>'mentor','position'=>'Robotics Engineer','company'=>'Robotics Levant','location'=>'Beirut, LB','expertises'=>['Robotics','Embedded Systems','Mechanical Engineering']],
            ['username'=>'issam.majdalani','email'=>'issam.majdalani@example.com','role'=>'mentor','position'=>'Energy Engineer','company'=>'Cedar Energy','location'=>'Tripoli, LB','expertises'=>['Renewable Energy','Environmental Engineering']],

            // --- Built environment / Law / Communications ---
            ['username'=>'tala.kassem','email'=>'tala.kassem@example.com','role'=>'mentor','position'=>'Architect','company'=>'Urban Cedar','location'=>'Byblos, LB','expertises'=>['Architecture','Urban Planning']],
            ['username'=>'karam.halabi','email'=>'karam.halabi@example.com','role'=>'mentor','position'=>'Attorney','company'=>'Halabi & Partners','location'=>'Beirut, LB','expertises'=>['Law']],
            ['username'=>'noura.yammine','email'=>'noura.yammine@example.com','role'=>'mentor','position'=>'Communications Lead','company'=>'Phoenicia Media','location'=>'Beirut, LB','expertises'=>['Communications','Journalism','Media Studies']],

            // --- Science / Environment ---
            ['username'=>'bilal.darwish','email'=>'bilal.darwish@example.com','role'=>'mentor','position'=>'Environmental Scientist','company'=>'Green Levant','location'=>'Beirut, LB','expertises'=>['Environmental Science','Environmental Engineering']],
            ['username'=>'zeina.daou','email'=>'zeina.daou@example.com','role'=>'mentor','position'=>'Entrepreneur in Residence','company'=>'Startup Cedar','location'=>'Beirut, LB','expertises'=>['Entrepreneurship','International Business','Business Administration']],
            ['username'=>'salim.mouawad','email'=>'salim.mouawad@example.com','role'=>'mentor','position'=>'Geologist','company'=>'Cedar Geo','location'=>'Tripoli, LB','expertises'=>['Geology','Environmental Science']],
            ['username'=>'yara.ismail','email'=>'yara.ismail@example.com','role'=>'mentor','position'=>'Policy Analyst','company'=>'IR Center','location'=>'Beirut, LB','expertises'=>['International Relations','Political Science']],

            // --- Extra coverage for preferences (duplicates fine; upserts by email) ---
            ['username'=>'hend.aziz','email'=>'hend.aziz@example.com','role'=>'mentor','position'=>'Software Engineer','company'=>'Levant Soft','location'=>'Beirut, LB','expertises'=>['Software Engineering','Web Development']],
            ['username'=>'tarek.hobeika','email'=>'tarek.hobeika@example.com','role'=>'mentor','position'=>'Data Scientist','company'=>'Data Levant','location'=>'Beirut, LB','expertises'=>['Data Scientist','Data Science']],
            ['username'=>'mona.aboufarhat','email'=>'mona.aboufarhat@example.com','role'=>'mentor','position'=>'Teacher','company'=>'Cedars High','location'=>'Beirut, LB','expertises'=>['Teacher','Education & Academia','Education']],
            ['username'=>'hussein.abbas','email'=>'hussein.abbas@example.com','role'=>'mentor','position'=>'IT Support Lead','company'=>'Cedar IT','location'=>'Beirut, LB','expertises'=>['Technology & IT','Information Technology']],
            ['username'=>'nawal.mansour','email'=>'nawal.mansour@example.com','role'=>'mentor','position'=>'Surgeon','company'=>'MedEast Hospital','location'=>'Beirut, LB','expertises'=>['Doctor','Medicine','Healthcare & Medicine']],
        ];

        $expertiseMap = DB::table('expertises')->pluck('id', 'name')->toArray();

        foreach ($mentors as $m) {
            DB::table('users')->updateOrInsert(
                ['email' => $m['email']],
                [
                    'username'   => $m['username'],
                    'password'   => Hash::make('password'),
                    'role'       => $m['role'] ?? 'mentor',
                    'position'   => $m['position'] ?? null,
                    'company'    => $m['company'] ?? null,
                    'location'   => $m['location'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $userId = DB::table('users')->where('email', $m['email'])->value('id');

            if ($userId && !empty($m['expertises'])) {
                foreach ($m['expertises'] as $name) {
                    $eid = $expertiseMap[$name] ?? null;
                    if ($eid) {
                        DB::table('user_expertises')->updateOrInsert(
                            ['user_id' => $userId, 'expertise_id' => $eid],
                            []
                        );
                    }
                }
            }
        }
    }
}
