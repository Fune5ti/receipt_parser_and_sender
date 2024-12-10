<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        $employees = [
            [
                'contrib_number' => '128529962',
                'name' => 'Eudisio Andrade',
                'email' => 'eudisio.andrade@devgo.org',
            ],
            [
                'contrib_number' => '140139001',
                'name' => 'Gil Pires',
                'email' => 'gil.pires@devgo.org',
            ],
            [
                'contrib_number' => '121699943',
                'name' => 'Antonio P Monteiro',
                'email' => 'antonio.p.monteiro@devgo.org',
            ],
            [
                'contrib_number' => '138755701',
                'name' => 'Daniel Pires',
                'email' => 'daniel.pires@devgo.org',
            ],
            [
                'contrib_number' => '100800106',
                'name' => 'Christian Huber',
                'email' => 'christian.huber@devgo.org',
            ],
            [
                'contrib_number' => '100890415',
                'name' => 'Marvin Leonhard',
                'email' => 'marvin.leonhard@devgo.org',
            ],
            [
                'contrib_number' => '143907280',
                'name' => 'Macielle Costa',
                'email' => 'macielle.costa@devgo.org',
            ],
            [
                'contrib_number' => '136531806',
                'name' => 'Humberto Nascimento',
                'email' => 'humberto.nascimento@devgo.org',
            ],
            [
                'contrib_number' => '162531591',
                'name' => 'Aires Fortes',
                'email' => 'aires.fortes@devgo.org',
            ],
            [
                'contrib_number' => '147495300',
                'name' => 'Keny Fortes',
                'email' => 'keny.fortes@devgo.org',
            ],
            [
                'contrib_number' => '138043302',
                'name' => 'Kelvin Pereira',
                'email' => 'kelvin.pereira@devgo.org',
            ],
            [
                'contrib_number' => '143783904',
                'name' => 'Ruben Martins',
                'email' => 'ruben.martins@devgo.org',
            ],
            [
                'contrib_number' => '134849108',
                'name' => 'Rafael Lopes',
                'email' => 'rafael.lopes@devgo.org',
            ],
            [
                'contrib_number' => '130011703',
                'name' => 'Yannick de Figueiredo',
                'email' => 'yannick.de.figueiredo@devgo.org',
            ],
            [
                'contrib_number' => '170867404',
                'name' => 'Bento Lima',
                'email' => 'bento.lima@devgo.org',
            ],
            [
                'contrib_number' => '108030920',
                'name' => 'Angelo Correia',
                'email' => 'angelo.correia@devgo.org',
            ],
            [
                'contrib_number' => '137994001',
                'name' => 'Eric Gomes',
                'email' => 'eric.gomes@devgo.org',
            ],
            [
                'contrib_number' => '135714702',
                'name' => 'Warren Delgado',
                'email' => 'warren.delgado@devgo.org',
            ],
            [
                'contrib_number' => '141737603',
                'name' => 'Carla Santos',
                'email' => 'carla.santos@devgo.org',
            ],
            [
                'contrib_number' => '144997703',
                'name' => 'Danilson Reis',
                'email' => 'danilson.reis@devgo.org',
            ],
            [
                'contrib_number' => '166253405',
                'name' => 'Alvaro Lima',
                'email' => 'alvaro.lima@devgo.org',
            ],
            [
                'contrib_number' => '144984504',
                'name' => 'Irian Lopes',
                'email' => 'irian.lopes@devgo.org',
            ],
            [
                'contrib_number' => '154139262',
                'name' => 'Maksym Zinchenko',
                'email' => 'maksym.zinchenko@devgo.org',
            ],
            [
                'contrib_number' => '128942304',
                'name' => 'Kristopher Brandao',
                'email' => 'kristopher.brandao@devgo.org',
            ],
        ];

        foreach ($employees as $employee) {
            DB::table('employees')->insert($employee);
        }
    }
}
