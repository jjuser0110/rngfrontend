<?php

// SAMPLE tours for the website until they are managed in rngcarrental (see App\Catalog\Contracts\TourCatalog).
//
// The places, times and rules are real (checked October 2026). Prices are market rates for similar tours
// from Kuching operators; the client should set their own. Sources:
//   Semenggoh feeding 9:00-9:30 and 15:00-15:30, fewer sightings Nov-Mar:
//     https://etawau.com/Fauna/Zoo/Semenggok.htm, https://www.travelocity.com/things-to-do/semenggoh-orangutan-centre-from-kuching.a1255598.activity-details
//   Bako boats RM40 (foreigners) / RM30 (Malaysians) return, 9am-3pm, max 4 per boat Nov-Feb:
//     https://sg.trip.com/guide/attraction/bako-national-park.html, https://www.holidify.com/places/sarawak/bako-national-park-sightseeing-1255509.html
//   Sarawak Cultural Village show ~45 min, closed during the Rainforest World Music Festival, day tours from RM360:
//     https://www.amazingborneo.com/package/sarawak-cultural-village
//   Annah Rais entrance RM8, hot springs closed for maintenance, 2D1N stays MYR 890-980:
//     https://chinese.sarawaktourism.com/attraction/annah-rais-bidayuh-longhouse/, https://amazingborneo.com/package/3d2n-kuching-longhouse-hotsprings-tour,
//     https://paradesaborneo.rezdy.com/225199/2d1n-annah-rais-longhouse-stay-t2
// Photos (public/images/tours) are freely licensed Wikimedia Commons photos of each place, credited in photo_credits.

return [
    [
        'slug'             => 'semenggoh-orangutan-morning',
        'name'             => 'Semenggoh Orangutan Morning',
        'category'         => 'Wildlife',
        'duration_type'    => 'half_day',
        'duration_label'   => 'Half day (4 hours)',
        'start_time'       => '07:30',
        'price_per_person' => 120,
        'min_pax'          => 2,
        'pickup_point'     => 'Your hotel or homestay in Kuching city',
        'summary'          => 'See semi-wild orangutans come in for the 9am feed at Semenggoh Wildlife Centre, 30 minutes from Kuching.',
        'description'      => [
            'Semenggoh Wildlife Centre is home to semi-wild orangutans, rehabilitated orphans and their wild-born young, that roam the surrounding forest reserve freely. Twice a day rangers bring fruit to the feeding platforms and you watch from the viewing area.',
            'This tour joins the morning feed (9:00 - 9:30am). Sightings are common but never guaranteed: during the wild fruit season (November - March) the orangutans often stay in the forest.',
        ],
        'highlights'       => ['Orangutan feeding session with park rangers', 'Short rainforest walk', 'Hotel pick-up and drop-off'],
        'itinerary'        => [
            ['07:30', 'Pick-up in Kuching', 'Our driver meets you at your hotel or homestay.'],
            ['08:15', 'Semenggoh Wildlife Centre', 'Ranger briefing and short walk to the feeding area.'],
            ['09:00', 'Morning feeding (9:00 - 9:30)', 'Watch the orangutans come down for fruit.'],
            ['10:30', 'Return to Kuching', 'Drop-off at your hotel around 11:30.'],
        ],
        'includes'         => ['Return transport', 'Park entrance fee', 'English-speaking guide', 'Bottled water'],
        'excludes'         => ['Meals', 'Tips'],
        'what_to_bring'    => ['Comfortable shoes', 'Insect repellent', 'Light rain jacket', 'Camera'],
        'good_to_know'     => [
            'Feeding times are 9:00 - 9:30am and 3:00 - 3:30pm. An afternoon departure (13:30) is available on request.',
            'November - March is wild fruit season, when orangutans may not come to the platform.',
        ],
        'images'          => [
            'images/tours/semenggoh-orangutan-morning-1.jpg',
            'images/tours/semenggoh-orangutan-morning-2.jpg',
            'images/tours/semenggoh-orangutan-morning-3.jpg',
        ],
        // Wikimedia Commons, free licences: [caption, author, licence, licence URL, source]
        'photo_credits'   => [
            ['Orangutan at Semenggoh', 'KelvinToh', 'CC BY-SA 4.0', 'https://creativecommons.org/licenses/by-sa/4.0', 'https://commons.wikimedia.org/wiki/File:Orangutan_at_Semenggoh_Nature_reserve.jpg'],
            ['Orangutan on the ropes at Semenggoh', 'Thomas Quine', 'CC BY 2.0', 'https://creativecommons.org/licenses/by/2.0', 'https://commons.wikimedia.org/wiki/File:Orangutan_on_double_ropes_(26515290302).jpg'],
            ['Semenggoh Wildlife Centre', 'Fabio Achilli from Milano, Italy', 'CC BY 2.0', 'https://creativecommons.org/licenses/by/2.0', 'https://commons.wikimedia.org/wiki/File:Semenggoh_Wildlife_Center,_Kuching,_Sarawak.jpg'],
        ],
    ],
    [
        'slug'             => 'bako-national-park-day-trip',
        'name'             => 'Bako National Park Day Trip',
        'category'         => 'Nature',
        'duration_type'    => 'full_day',
        'duration_label'   => 'Full day (8 hours)',
        'start_time'       => '08:00',
        'price_per_person' => 260,
        'min_pax'          => 2,
        'pickup_point'     => 'Your hotel or homestay in Kuching city',
        'summary'          => 'Boat to Sarawak\'s oldest national park to spot proboscis monkeys, bearded pigs and sea stacks.',
        'description'      => [
            'Bako can only be reached by boat, which makes the journey part of the adventure. Inside the park, jungle trails lead to quiet beaches and lookout points over the South China Sea.',
            'Our guide picks a trail to suit the group, with the best chance of spotting proboscis monkeys, silver leaf monkeys and pitcher plants. Boats only run between 9am and 3pm, so the day is planned around the last boat back.',
        ],
        'highlights'       => ['Boat ride along the Bako coast', 'Proboscis monkey spotting', 'Telok Pandan Kecil beach and sea stack', 'Lunch at the park canteen'],
        'itinerary'        => [
            ['08:00', 'Pick-up in Kuching', 'Drive to Bako village jetty (about 40 minutes).'],
            ['09:00', 'Boat to Bako', '20-minute boat ride to the park headquarters.'],
            ['09:30', 'Jungle trek', 'Guided walk to Telok Pandan Kecil beach.'],
            ['12:30', 'Lunch', 'Local lunch at the park canteen.'],
            ['13:30', 'Wildlife walk', 'Look for proboscis monkeys around the mangroves.'],
            ['15:00', 'Return', 'Boat and drive back to Kuching, arriving around 16:30.'],
        ],
        'includes'         => ['Return transport', 'Return boat transfer (RM40 foreigners / RM30 Malaysians)', 'Park entrance fee', 'Lunch', 'Guide'],
        'excludes'         => ['Tips', 'Personal expenses'],
        'what_to_bring'    => ['Trekking shoes', 'Swimwear', 'Sunscreen', 'Hat', 'Insect repellent'],
        'good_to_know'     => [
            'Boats to and from the park run from 9am to 3pm only.',
            'In the wet season (November - February) boats take at most 4 passengers, and trips can change because of sea conditions.',
            'Do not feed the monkeys or leave food unattended; macaques will grab bags.',
        ],
        'images'          => [
            'images/tours/bako-national-park-day-trip-1.jpg',
            'images/tours/bako-national-park-day-trip-2.jpg',
            'images/tours/bako-national-park-day-trip-3.jpg',
        ],
        // Wikimedia Commons, free licences: [caption, author, licence, licence URL, source]
        'photo_credits'   => [
            ['Sea stack at Bako National Park', 'Peter Gronemann from Switzerland', 'CC BY 2.0', 'https://creativecommons.org/licenses/by/2.0', 'https://commons.wikimedia.org/wiki/File:Bako_National_Park_(3679460540).jpg'],
            ['Proboscis monkey, Bako', 'diego_cue', 'CC BY-SA 3.0', 'https://creativecommons.org/licenses/by-sa/3.0', 'https://commons.wikimedia.org/wiki/File:Proboscis_Monkey_-_Bako_National_Park_-_panoramio.jpg'],
            ['Rock formations on a Bako beach', 'Borneo mobilegrapher', 'CC BY-SA 4.0', 'https://creativecommons.org/licenses/by-sa/4.0', 'https://commons.wikimedia.org/wiki/File:Bako_National_Park,_Kuching,_Sarawak,_Malaysia_03.jpg'],
        ],
    ],
    [
        'slug'             => 'kuching-heritage-city-walk',
        'name'             => 'Kuching Heritage City Walk',
        'category'         => 'Culture',
        'duration_type'    => 'half_day',
        'duration_label'   => 'Half day (3 hours)',
        'start_time'       => '09:00',
        'price_per_person' => 80,
        'min_pax'          => 2,
        'pickup_point'     => 'Kuching Waterfront (Square Tower)',
        'summary'          => 'Walk through Kuching\'s old town: temples, colonial buildings, Carpenter Street and the Main Bazaar.',
        'description'      => [
            'Discover how the White Rajahs, Chinese traders and Malay communities shaped Kuching. The walk covers the Tua Pek Kong temple, the Old Courthouse, the Square Tower and the shophouses of Carpenter and India Streets.',
            'Finish with a kek lapis tasting and time to browse the handicraft shops on Main Bazaar.',
        ],
        'highlights'       => ['Tua Pek Kong temple', 'Old Courthouse and Square Tower', 'Carpenter Street and India Street', 'Sarawak layer cake tasting'],
        'itinerary'        => [
            ['09:00', 'Meet at the Square Tower', 'Introduction to Kuching\'s history.'],
            ['09:30', 'Old town walk', 'Temples, shophouses and colonial buildings.'],
            ['11:00', 'Main Bazaar', 'Kek lapis tasting and handicraft shopping.'],
            ['12:00', 'Tour ends', 'Finish at the waterfront.'],
        ],
        'includes'         => ['Guide', 'Kek lapis tasting', 'Bottled water'],
        'excludes'         => ['Transport', 'Meals', 'Shopping'],
        'what_to_bring'    => ['Comfortable shoes', 'Hat', 'Umbrella'],
        'images'          => [
            'images/tours/kuching-heritage-city-walk-1.jpg',
            'images/tours/kuching-heritage-city-walk-2.jpg',
            'images/tours/kuching-heritage-city-walk-3.jpg',
        ],
        // Wikimedia Commons, free licences: [caption, author, licence, licence URL, source]
        'photo_credits'   => [
            ['Tua Pek Kong Temple', 'Damian Pankowiec', 'CC BY-SA 4.0', 'https://creativecommons.org/licenses/by-sa/4.0', 'https://commons.wikimedia.org/wiki/File:Tua_Pek_Kong_Temple_(Pre-1839)_-_Kuching.jpg'],
            ['Old Court House clock tower', '馬姥', 'CC BY-SA 4.0', 'https://creativecommons.org/licenses/by-sa/4.0', 'https://commons.wikimedia.org/wiki/File:Old_Court_House(Clock_Tower).jpg'],
            ['Five-foot way in old Kuching', 'Matthias Bethke', 'CC BY-SA 4.0', 'https://creativecommons.org/licenses/by-sa/4.0', 'https://commons.wikimedia.org/wiki/File:010984_Kuching_(Borneo).jpg'],
        ],
    ],
    [
        'slug'             => 'sarawak-cultural-village-santubong',
        'name'             => 'Sarawak Cultural Village & Santubong',
        'category'         => 'Culture',
        'duration_type'    => 'full_day',
        'duration_label'   => 'Full day (7 hours)',
        'start_time'       => '09:00',
        'price_per_person' => 360,
        'min_pax'          => 2,
        'pickup_point'     => 'Your hotel or homestay in Kuching city',
        'summary'          => 'Visit the "living museum" of Sarawak\'s ethnic groups, then seafood and the beach at Santubong.',
        'description'      => [
            'The Sarawak Cultural Village showcases the traditional houses of the Iban, Bidayuh, Orang Ulu, Melanau, Malay and Chinese communities, with craft demonstrations and a cultural dance show.',
            'After the show, enjoy a seafood lunch at Santubong fishing village and free time at Damai Beach.',
        ],
        'highlights'       => ['Traditional longhouses and craft demonstrations', 'Cultural dance performance', 'Seafood lunch at Santubong', 'Damai Beach'],
        'itinerary'        => [
            ['09:00', 'Pick-up in Kuching', 'Drive to Santubong (about 40 minutes).'],
            ['10:00', 'Sarawak Cultural Village', 'Explore the traditional houses.'],
            ['11:30', 'Cultural show', 'About 45 minutes of traditional music and dance in the theatre.'],
            ['12:30', 'Seafood lunch', 'At Santubong fishing village.'],
            ['14:00', 'Damai Beach', 'Free time at the beach.'],
            ['15:30', 'Return', 'Back in Kuching around 16:00.'],
        ],
        'includes'         => ['Return transport', 'Cultural Village entrance', 'Seafood lunch', 'Guide'],
        'excludes'         => ['Tips', 'Drinks'],
        'what_to_bring'    => ['Sunscreen', 'Swimwear', 'Camera'],
        'good_to_know'     => [
            'Price is per person for a group of 2 - 3; larger groups pay less per person.',
            'The village closes for the Rainforest World Music Festival every June. Ask us for the dates.',
        ],
        'images'          => [
            'images/tours/sarawak-cultural-village-santubong-1.jpg',
            'images/tours/sarawak-cultural-village-santubong-2.jpg',
            'images/tours/sarawak-cultural-village-santubong-3.jpg',
        ],
        // Wikimedia Commons, free licences: [caption, author, licence, licence URL, source]
        'photo_credits'   => [
            ['Sarawak Cultural Village and Mount Santubong', 'Tys', 'CC BY-SA 3.0', 'https://creativecommons.org/licenses/by-sa/3.0', 'https://commons.wikimedia.org/wiki/File:Sarawak_Cultural_Village_-_panoramio.jpg'],
            ['Longhouse at the Cultural Village', 'Peter Gronemann from Switzerland', 'CC BY 2.0', 'https://creativecommons.org/licenses/by/2.0', 'https://commons.wikimedia.org/wiki/File:Kuching_Cultural_Village_(3678653401).jpg'],
            ['Traditional house at the Cultural Village', 'Thomas Quine', 'CC BY 2.0', 'https://creativecommons.org/licenses/by/2.0', 'https://commons.wikimedia.org/wiki/File:Sarawak_aboriginal_village_theatre_(28988425213).jpg'],
        ],
    ],
    [
        'slug'             => 'kuching-wetlands-firefly-cruise',
        'name'             => 'Kuching Wetlands Firefly Cruise',
        'category'         => 'Wildlife',
        'duration_type'    => 'half_day',
        'duration_label'   => 'Half day (4 hours)',
        'start_time'       => '15:30',
        'price_per_person' => 180,
        'min_pax'          => 2,
        'pickup_point'     => 'Your hotel or homestay in Kuching city',
        'summary'          => 'Sunset river cruise through mangroves to look for Irrawaddy dolphins, crocodiles and fireflies.',
        'description'      => [
            'Cruise through the Kuching Wetlands National Park mangroves as the sun sets over Mount Santubong. Look out for Irrawaddy dolphins near the river mouth, proboscis monkeys and estuarine crocodiles.',
            'After dark, the mangrove trees light up with thousands of fireflies.',
        ],
        'highlights'       => ['Sunset over Mount Santubong', 'Irrawaddy dolphin spotting', 'Mangrove wildlife', 'Firefly trees after dark'],
        'itinerary'        => [
            ['15:30', 'Pick-up in Kuching', 'Drive to Santubong jetty.'],
            ['16:30', 'River cruise', 'Wildlife spotting along the river mouth and mangroves.'],
            ['18:30', 'Fireflies', 'Watch the firefly trees after sunset.'],
            ['19:30', 'Return', 'Back in Kuching around 20:30.'],
        ],
        'includes'         => ['Return transport', 'Boat cruise', 'Life jackets', 'Light snack and water'],
        'excludes'         => ['Dinner', 'Tips'],
        'what_to_bring'    => ['Insect repellent', 'Light jacket', 'Camera'],
        'images'          => [
            'images/tours/kuching-wetlands-firefly-cruise-1.jpg',
            'images/tours/kuching-wetlands-firefly-cruise-2.jpg',
            'images/tours/kuching-wetlands-firefly-cruise-3.jpg',
        ],
        // Wikimedia Commons, free licences: [caption, author, licence, licence URL, source]
        'photo_credits'   => [
            ['Salak River, Kuching Wetlands National Park', 'Cerevisae', 'CC BY-SA 4.0', 'https://creativecommons.org/licenses/by-sa/4.0', 'https://commons.wikimedia.org/wiki/File:Salak_River,_part_of_Kuching_Wetlands_National_Park,_and_Mount_Santubong.jpg'],
            ['Santubong River and Mount Santubong', 'Bernard DUPONT from FRANCE', 'CC BY-SA 2.0', 'https://creativecommons.org/licenses/by-sa/2.0', 'https://commons.wikimedia.org/wiki/File:Santubong_Peninsula_seen_from_Santubong_River_(15850215991).jpg'],
            ['Santubong Peninsula from the river', 'Bernard DUPONT from FRANCE', 'CC BY-SA 2.0', 'https://creativecommons.org/licenses/by-sa/2.0', 'https://commons.wikimedia.org/wiki/File:Santubong_Peninsula_seen_from_Santubong_River_(15659139549).jpg'],
        ],
    ],
    [
        'slug'             => 'annah-rais-longhouse-overnight',
        'name'             => 'Annah Rais Longhouse Overnight',
        'category'         => 'Culture',
        'duration_type'    => 'multi_day',
        'duration_label'   => '2 days 1 night',
        'start_time'       => '09:00',
        'price_per_person' => 890,
        'min_pax'          => 2,
        'pickup_point'     => 'Your hotel or homestay in Kuching city',
        'summary'          => 'Stay overnight in a Bidayuh longhouse with a host family and learn about village life.',
        'description'      => [
            'Annah Rais is one of the oldest Bidayuh longhouses in Sarawak, about 90 minutes from Kuching near the Indonesian border. You stay with a host family and join in daily village life.',
            'Walk the bamboo walkways and the communal veranda, try bamboo-cooked food and enjoy a welcome drink of homemade rice wine (tuak). The Annah Rais hot springs are currently closed for maintenance, so we visit the riverside instead.',
        ],
        'highlights'       => ['Overnight homestay in a Bidayuh longhouse', 'Bamboo-cooked dinner with the host family', 'Village and skull-house visit', 'Riverside walk'],
        'itinerary'        => [
            ['Day 1 · 09:00', 'Pick-up in Kuching', 'Drive to Annah Rais via Padawan.'],
            ['Day 1 · 11:00', 'Welcome to the longhouse', 'Meet your host family and tour the village.'],
            ['Day 1 · 15:00', 'Riverside walk', 'Walk to the river and paddy fields with your guide.'],
            ['Day 1 · 19:00', 'Dinner', 'Traditional bamboo-cooked dinner with the family.'],
            ['Day 2 · 08:00', 'Breakfast and farewell', 'Morning walk in the village.'],
            ['Day 2 · 11:00', 'Return', 'Back in Kuching around 12:30.'],
        ],
        'includes'         => ['Return transport', 'Longhouse homestay (1 night)', 'All meals on the itinerary', 'Longhouse entrance (RM8)', 'Guide'],
        'excludes'         => ['Tips', 'Personal expenses'],
        'what_to_bring'    => ['Overnight bag', 'Towel', 'Insect repellent', 'Torchlight', 'Slippers'],
        'good_to_know'     => [
            'The Annah Rais hot springs are closed for maintenance until further notice. We will add them back when they reopen.',
            'Longhouse rooms are simple, with shared bathrooms.',
        ],
        'images'          => [
            'images/tours/annah-rais-longhouse-overnight-1.jpg',
            'images/tours/annah-rais-longhouse-overnight-2.jpg',
            'images/tours/annah-rais-longhouse-overnight-3.jpg',
        ],
        // Wikimedia Commons, free licences: [caption, author, licence, licence URL, source]
        'photo_credits'   => [
            ['Annah Rais longhouse veranda', 'Peter Gronemann from Switzerland', 'CC BY 2.0', 'https://creativecommons.org/licenses/by/2.0', 'https://commons.wikimedia.org/wiki/File:Ana_Rais_(3678660741).jpg'],
            ['Bridge at Annah Rais', 'Peter Gronemann from Switzerland', 'CC BY 2.0', 'https://creativecommons.org/licenses/by/2.0', 'https://commons.wikimedia.org/wiki/File:Ana_Rais_(3678669965).jpg'],
            ['River at Annah Rais', 'Peter Gronemann from Switzerland', 'CC BY 2.0', 'https://creativecommons.org/licenses/by/2.0', 'https://commons.wikimedia.org/wiki/File:Ana_Rais_(3679478378).jpg'],
        ],
    ],
];
