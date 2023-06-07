<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use \App\Models\Musician;
use \App\Models\Instrument;
use \App\Models\Profile;
use \App\Models\MusicianDetail;
use \App\Models\DetailType;


class DatabaseSeeder extends Seeder
{


    public function run()
    {

        Instrument::insert([
            ['name' => 'piano'],
            ['name' => 'double bass'],
            ['name' => 'drums'],
            ['name' => 'percussion'],
            ['name' => 'bass'],
            ['name' => 'soprano saxophone'],
            ['name' => 'alto saxophone'],
            ['name' => 'tenor saxophone'],
            ['name' => 'baritone saxophone'],
            ['name' => 'trumpet'],
            ['name' => 'vocals'],
            ['name' => 'violin'],
            ['name' => 'viola'],
            ['name' => 'cello'],
            ['name' => 'guitar'],
            ['name' => 'vibrophone'],
            ['name' => 'harmonica'],
            ['name' => 'clarinet'],
            ['name' => 'tuba'],
            ['name' => 'accordion'],
            ['name' => 'ukelele'],
            ['name' => 'banjo'],
            ['name' => 'oboe'],
            ['name' => 'bassoon'],
            ['name' => 'sitar'],
            ['name' => 'tabla'],
            ['name' => 'piccolo'],
            ['name' => 'flute'],
            ['name' => 'french horn'],
            ['name' => 'melodica'],
            ['name' => 'oud'],
            ['name' => 'recorder'],
            ['name' => 'trombone'],
        ]);

        DetailType::insert([
            ['detail_type_text' => 'phone'],
            ['detail_type_text' => 'email'],
            ['detail_type_text' => 'bank details'],
            ['detail_type_text' => 'address'],
            ['detail_type_text' => 'website'],
            ['detail_type_text' => 'instagram'],
            ['detail_type_text' => 'facebook']
        ]);

        $musicianCount = 18000;
        Musician::factory($musicianCount)->create();


        $rand_num_of_instruments = [1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 3, 4, 5, 6]; // 15 numbers in array
        $instrument_ids = range(1, 31);
        $faker = \Faker\Factory::create('en_GB');

        // loop through all the musicians' ids and create random associations with instruments and some profiles
        for ($musician_id = 1; $musician_id <= $musicianCount; $musician_id++) {

            $random = rand(0, 14);
            $num_instruments = $rand_num_of_instruments[$random];

            $musician = Musician::find($musician_id);

            // make one in nine musicians a pianist
            if ($random === 1) {
                $musician->instruments()->attach(1);
            }
            // make one in nine musicians a singer / pianist
            elseif ($random === 3) {
                $musician->instruments()->attach(1);
                $musician->instruments()->attach(11);
            }
            else {
                shuffle($instrument_ids);
                $rand_instrument_ids = array_slice($instrument_ids, 0, $num_instruments);

                for ($i = 0; $i < $num_instruments; $i++) {
                    $musician->instruments()->attach($rand_instrument_ids[$i]);
                }
            }

            // make profile pages for 1 in 3 of the musicians
            if (rand(0, 2) === 2) {
                Profile::create([
                    'musician_id' => $musician_id,
                    'text' => implode("\r\n\r\n", $faker->paragraphs(rand(3, 9)))
                ]);
            }

            // every musician has a phone number
            MusicianDetail::create([
                'musician_id' => $musician_id,
                'detail_types_id' => 1,
                'musician_details_text' => $faker->phoneNumber
            ]);


            // as well as the above, each musician also has between 0 and 3 further random details
            for ($i = 0; $i < rand(0, 4); $i++) {

                $random_detail = rand(1, 7);

                if ($random_detail === 1)
                    $musician_details_text = $faker->phoneNumber;

                elseif ($random_detail === 2) $musician_details_text = $faker->email;
                elseif ($random_detail === 3)
                    $musician_details_text = $faker->randomNumber(8) . ' / ' . $faker->randomNumber(2) . '-' . $faker->randomNumber(2) . '-' . $faker->randomNumber(2); // bank account / sort no.
                elseif ($random_detail === 4) $musician_details_text = $faker->address;
                elseif ($random_detail === 5) $musician_details_text = $faker->url;
                elseif ($random_detail === 6 || $random_detail === 7) $musician_details_text = '@' . $faker->word . '_' . $faker->word;
                
                // && $random_detail <=8) $musician_details_text = $faker->url;

                MusicianDetail::create([
                    'musician_id' => $musician_id,
                    'detail_types_id' => $random_detail,
                    'musician_details_text' => $musician_details_text
                ]);

            }

        }







    }
}
