<?php

// SAMPLE homestays for the website until they are managed in rngcarrental (see App\Catalog\Contracts\HomestayCatalog).
//
// Built from real Kuching market data (October 2026): the developments, areas, unit sizes and nightly prices
// match homestay and rental listings on Mudah, PropertyGuru, Trovit, Traveloka and Airbnb. They are not
// copies of any one owner's listing, and they are marked is_sample so customers can see they are examples.
// Sources:
//   https://www.mudah.my/sarawak-kuching/accommodation-for-rent   (Kuching homestays RM200 - RM350 / night)
//   https://www.mudah.my/tabuan-residence-apartment-fully-furnished-2-bedrooms-115860756.htm   (770 sq ft, 2 bed 2 bath, pool + gym, ~5 min to Padungan)
//   https://www.traveloka.com/en-my/accommodation/homestay/malaysia/city/kuching-107978   (Samarahan ~RM77, Damai/Santubong ~RM198, Batu Kawa)
//   https://www.airbnb.com/rooms/1048373273424340863   (3-bedroom units at Kuching Riverine Resort, 6 guests, pool)
//   https://www.hotelsinkuching.com/en/type/homestays-32/   (homestays in MJC Batu Kawa and Tabuan Jaya)
// Photos (public/images/homestays) are freely licensed Wikimedia Commons photos of each area, credited in
// photo_credits; replace them with photos of the real units when the client has them.

return [
    [
        'slug'            => 'riverine-resort-3-bedroom-condo',
        'name'            => '3-Bedroom Condo at Riverine Resort',
        'is_sample'       => true,
        'type'            => 'Condominium',
        'area'            => 'Petanak',
        'address'         => 'Kuching Riverine Resort, Jalan Petanak, 93100 Kuching, Sarawak',
        'map_query'       => 'Kuching Riverine Resort, Kuching, Sarawak',
        'price_per_night' => 280,
        'guests'          => 6,
        'bedrooms'        => 3,
        'beds'            => 4,
        'bathrooms'       => 2,
        'size_sqft'       => 1100,
        'check_in'        => '15:00',
        'check_out'       => '12:00',
        'min_nights'      => 1,
        'summary'         => 'Riverside condo for families and groups of up to six, with a swimming pool and close to the Kuching Waterfront.',
        'description'     => [
            'Kuching Riverine Resort is one of the most popular homestay addresses in the city: a riverside condominium a short ride from the Kuching Waterfront, Main Bazaar and the Darul Hana Bridge.',
            'This three-bedroom unit sleeps six, with air-conditioned bedrooms, a living room with TV and an equipped kitchen for simple meals. Guests can use the condominium swimming pool.',
            'Add one of our rental cars to reach Satok market, Semenggoh and the Damai beaches easily.',
        ],
        'amenities'       => ['Wi-Fi', 'Air conditioning', 'Kitchen', 'Swimming pool', 'Smart TV', 'Lift', 'Free parking', 'Security'],
        'house_rules'     => ['No smoking indoors', 'No pets', 'No parties or events', 'Pool rules set by the condominium management'],
        'images'          => [
            'images/homestays/riverine-resort-3-bedroom-condo-1.jpg',
            'images/homestays/riverine-resort-3-bedroom-condo-2.jpg',
            'images/homestays/riverine-resort-3-bedroom-condo-3.jpg',
        ],
        // Wikimedia Commons, free licences: [caption, author, licence, licence URL, source]
        'photo_credits'   => [
            ['Area: Kuching Waterfront', 'Marufish from Alor Setar, Malaysia', 'CC BY-SA 2.0', 'https://creativecommons.org/licenses/by-sa/2.0', 'https://commons.wikimedia.org/wiki/File:Kuching_Waterfront_(17587104128).jpg'],
            ['Area: Darul Hana Bridge', 'Fabio Achilli from Milano, Italy', 'CC BY 2.0', 'https://creativecommons.org/licenses/by/2.0', 'https://commons.wikimedia.org/wiki/File:Darul_Hana_Bridge,_Kuching,_Sarawak.jpg'],
            ['Area: Waterfront promenade', 'Jin', 'CC BY 2.0', 'https://creativecommons.org/licenses/by/2.0', 'https://commons.wikimedia.org/wiki/File:Kuching_Waterfront_Panorama.jpg'],
        ],
        'listed_at'       => '2026-10-10',
    ],
    [
        'slug'            => 'tabuan-residence-2-bedroom-apartment',
        'name'            => '2-Bedroom Apartment at Tabuan Residence',
        'is_sample'       => true,
        'type'            => 'Apartment',
        'area'            => 'Tabuan',
        'address'         => 'Tabuan Residence, Tabuan, 93350 Kuching, Sarawak',
        'map_query'       => 'Tabuan Residence, Kuching, Sarawak',
        'price_per_night' => 220,
        'guests'          => 4,
        'bedrooms'        => 2,
        'beds'            => 2,
        'bathrooms'       => 2,
        'size_sqft'       => 770,
        'check_in'        => '15:00',
        'check_out'       => '12:00',
        'min_nights'      => 1,
        'summary'         => 'Fully furnished 770 sq ft apartment with a pool and gym, about 5 minutes\' drive to Padungan.',
        'description'     => [
            'A modern, fully furnished two-bedroom, two-bathroom apartment in Tabuan Residence. Each bedroom has its own bathroom, which suits two couples or a small family.',
            'Residents\' facilities include a swimming pool and gym. Padungan\'s cafés and the Great Cat of Kuching are about 5 minutes away by car, and the airport about 15 minutes.',
        ],
        'amenities'       => ['Wi-Fi', 'Air conditioning', 'Kitchen', 'Washing machine', 'Swimming pool', 'Gym', 'Free parking', 'Security'],
        'house_rules'     => ['No smoking indoors', 'No pets', 'No parties or events', 'Quiet hours 10pm – 7am'],
        'images'          => [
            'images/homestays/tabuan-residence-2-bedroom-apartment-1.jpg',
            'images/homestays/tabuan-residence-2-bedroom-apartment-2.jpg',
            'images/homestays/tabuan-residence-2-bedroom-apartment-3.jpg',
        ],
        // Wikimedia Commons, free licences: [caption, author, licence, licence URL, source]
        'photo_credits'   => [
            ['Nearby: Great Cat of Kuching, Padungan', 'wanghongliu', 'CC BY-SA 3.0', 'https://creativecommons.org/licenses/by-sa/3.0', 'https://commons.wikimedia.org/wiki/File:%E5%8F%A4%E6%99%8B%E5%A4%A7%E7%99%BD%E7%8C%AB%E3%80%81%E5%A4%A7%E5%94%90%E7%89%8C%E5%9D%8A_Great_Cat_%5E_Selamat_Datang_-_panoramio.jpg'],
            ['Nearby: Chinatown gate, Jalan Padungan', 'Stefan Krasowski', 'CC BY 2.0', 'https://creativecommons.org/licenses/by/2.0', 'https://commons.wikimedia.org/wiki/File:Chinatown_welcome_gate_at_Jalan_Padungan,_Kuching,_Sarawak,_Malaysia.jpg'],
            ['Nearby: Kuching Waterfront', 'Marufish from Alor Setar, Malaysia', 'CC BY-SA 2.0', 'https://creativecommons.org/licenses/by-sa/2.0', 'https://commons.wikimedia.org/wiki/File:Kuching_Waterfront_(17154525953).jpg'],
        ],
        'listed_at'       => '2026-10-02',
    ],
    [
        'slug'            => 'bandar-baru-semariang-3-bedroom-house',
        'name'            => '3-Bedroom House in Bandar Baru Semariang',
        'is_sample'       => true,
        'type'            => 'Landed House',
        'area'            => 'Petra Jaya',
        'address'         => 'Bandar Baru Semariang, Petra Jaya, 93050 Kuching, Sarawak',
        'map_query'       => 'Bandar Baru Semariang, Kuching, Sarawak',
        'price_per_night' => 280,
        'guests'          => 8,
        'bedrooms'        => 3,
        'beds'            => 4,
        'bathrooms'       => 2,
        'size_sqft'       => 1400,
        'check_in'        => '14:00',
        'check_out'       => '12:00',
        'min_nights'      => 1,
        'summary'         => 'Air-conditioned family house with parking for 2–3 cars, on the Petra Jaya side of Kuching.',
        'description'     => [
            'A whole house for your group in Bandar Baru Semariang, a residential township in Petra Jaya. All three bedrooms are air-conditioned, and the porch fits two to three cars.',
            'Petra Jaya puts you on the way to Santubong, Damai Beach and the Bako National Park jetty, while the city centre is about 20 minutes away.',
        ],
        'amenities'       => ['Wi-Fi', 'Air conditioning', 'Full kitchen', 'Washing machine', 'Smart TV', 'Free parking', 'Family friendly', 'Water heater'],
        'house_rules'     => ['No smoking indoors', 'No pets', 'Quiet hours 10pm – 7am'],
        'images'          => [
            'images/homestays/bandar-baru-semariang-3-bedroom-house-1.jpg',
            'images/homestays/bandar-baru-semariang-3-bedroom-house-2.jpg',
            'images/homestays/bandar-baru-semariang-3-bedroom-house-3.jpg',
        ],
        // Wikimedia Commons, free licences: [caption, author, licence, licence URL, source]
        'photo_credits'   => [
            ['Area: Bandar Baru Semariang and Mount Santubong', 'Cerevisae', 'CC BY-SA 4.0', 'https://creativecommons.org/licenses/by-sa/4.0', 'https://commons.wikimedia.org/wiki/File:Part_of_Petra_Jaya,_Bandar_Baru_Samariang,_Santubong_River,_and_Mount_Santubong.jpg'],
            ['Nearby: Baitul Makmur II, Petra Jaya', 'Cerevisae', 'CC BY-SA 4.0', 'https://creativecommons.org/licenses/by-sa/4.0', 'https://commons.wikimedia.org/wiki/File:Baitul_Makmur_II,_Petra_Jaya,_Kuching.jpg'],
            ['Nearby: Sarawak State Assembly building', 'CEphoto, Uwe Aranas', 'CC BY-SA 4.0', 'https://creativecommons.org/licenses/by-sa/4.0', 'https://commons.wikimedia.org/wiki/File:Kuching_Sarawak_Dewan-Undangan-Negeri-Sarawak-01.jpg'],
        ],
        'listed_at'       => '2026-09-20',
    ],
    [
        'slug'            => 'mjc-batu-kawa-family-house',
        'name'            => 'Family House near MJC Batu Kawa',
        'is_sample'       => true,
        'type'            => 'Landed House',
        'area'            => 'Batu Kawa',
        'address'         => 'MJC Batu Kawa, 93250 Kuching, Sarawak',
        'map_query'       => 'MJC Batu Kawa, Kuching, Sarawak',
        'price_per_night' => 200,
        'guests'          => 8,
        'bedrooms'        => 4,
        'beds'            => 5,
        'bathrooms'       => 3,
        'size_sqft'       => 1800,
        'check_in'        => '15:00',
        'check_out'       => '11:00',
        'min_nights'      => 2,
        'summary'         => 'Spacious double-storey house in the MJC Batu Kawa township, close to shops, eateries and Emporium.',
        'description'     => [
            'Room for big families: four bedrooms, three bathrooms and a large living and dining area. MJC Batu Kawa\'s shops, food courts and supermarkets are within a few minutes.',
            'Batu Kawa is on the road to Semenggoh, Bau and the Fairy and Wind Caves, so it is a convenient base for day trips with a rental MPV.',
        ],
        'amenities'       => ['Wi-Fi', 'Air conditioning', 'Full kitchen', 'Washing machine', 'Smart TV', 'Free parking', 'Family friendly', 'Water heater'],
        'house_rules'     => ['No smoking indoors', 'No pets', 'Minimum stay 2 nights', 'Quiet hours 10pm – 7am'],
        'images'          => [
            'images/homestays/mjc-batu-kawa-family-house-1.jpg',
            'images/homestays/mjc-batu-kawa-family-house-2.jpg',
            'images/homestays/mjc-batu-kawa-family-house-3.jpg',
        ],
        // Wikimedia Commons, free licences: [caption, author, licence, licence URL, source]
        'photo_credits'   => [
            ['Nearby: Batu Kawa Riverbank Park', 'Esther Siaw', 'CC BY 4.0', 'https://creativecommons.org/licenses/by/4.0', 'https://commons.wikimedia.org/wiki/File:2025_Batu_Kawa_Old_Town_Mid_Autumn_Festival,_Batu_Kawa_Riverbank_Park_-_9.jpg'],
            ['Nearby: Riverbank Park entrance', 'Cerevisae', 'CC BY-SA 4.0', 'https://creativecommons.org/licenses/by-sa/4.0', 'https://commons.wikimedia.org/wiki/File:Batu_Kawa_Riverbank_park_entrance.jpg'],
            ['Nearby: Gazebo, Riverbank Park', 'Cerevisae', 'CC BY-SA 4.0', 'https://creativecommons.org/licenses/by-sa/4.0', 'https://commons.wikimedia.org/wiki/File:Gazebo_at_Batu_Kawa_Riverbank_Park.jpg'],
        ],
        'listed_at'       => '2026-08-28',
    ],
    [
        'slug'            => 'damai-santubong-holiday-house',
        'name'            => 'Holiday House near Damai Beach',
        'is_sample'       => true,
        'type'            => 'Holiday House',
        'area'            => 'Santubong',
        'address'         => 'Jalan Santubong, Damai, 93050 Kuching, Sarawak',
        'map_query'       => 'Damai Beach, Santubong, Sarawak',
        'price_per_night' => 200,
        'guests'          => 6,
        'bedrooms'        => 3,
        'beds'            => 3,
        'bathrooms'       => 2,
        'size_sqft'       => 1300,
        'check_in'        => '15:00',
        'check_out'       => '11:00',
        'min_nights'      => 1,
        'summary'         => 'Beach getaway at the foot of Mount Santubong, minutes from Damai Beach and the Sarawak Cultural Village.',
        'description'     => [
            'A relaxed holiday house about 40 minutes from the city, with a veranda and barbecue area facing the jungle-covered slopes of Mount Santubong.',
            'Damai Beach, the Sarawak Cultural Village and the Santubong fishing village seafood restaurants are a short drive away, and dolphin and firefly cruises leave from the nearby jetty.',
        ],
        'amenities'       => ['Wi-Fi', 'Air conditioning', 'Kitchen', 'BBQ pit', 'Garden', 'Free parking', 'Near beach', 'Water heater'],
        'house_rules'     => ['No smoking indoors', 'BBQ until 10pm', 'No parties or events'],
        'images'          => [
            'images/homestays/damai-santubong-holiday-house-1.jpg',
            'images/homestays/damai-santubong-holiday-house-2.jpg',
            'images/homestays/damai-santubong-holiday-house-3.jpg',
        ],
        // Wikimedia Commons, free licences: [caption, author, licence, licence URL, source]
        'photo_credits'   => [
            ['Nearby: Damai Beach', 'Bernard DUPONT from FRANCE', 'CC BY-SA 2.0', 'https://creativecommons.org/licenses/by-sa/2.0', 'https://commons.wikimedia.org/wiki/File:Beach_and_Jungle_(23847471645).jpg'],
            ['Nearby: Mount Santubong', '© 2005 Matthew Anderson Lockhart', 'CC BY-SA 2.5', 'https://creativecommons.org/licenses/by-sa/2.5', 'https://commons.wikimedia.org/wiki/File:Mount_Santubong_from_Santubong_Bridge.jpg'],
            ['Nearby: Santubong River', 'Bernard DUPONT from FRANCE', 'CC BY-SA 2.0', 'https://creativecommons.org/licenses/by-sa/2.0', 'https://commons.wikimedia.org/wiki/File:Santubong_Peninsula_seen_from_Santubong_River_(15659139549).jpg'],
        ],
        'listed_at'       => '2026-07-15',
    ],
    [
        'slug'            => 'kota-samarahan-loft-studio',
        'name'            => 'Loft Studio in Kota Samarahan',
        'is_sample'       => true,
        'type'            => 'Studio',
        'area'            => 'Kota Samarahan',
        'address'         => 'Jalan Datuk Mohammad Musa, 94300 Kota Samarahan, Sarawak',
        'map_query'       => 'Kota Samarahan, Sarawak',
        'price_per_night' => 80,
        'guests'          => 2,
        'bedrooms'        => 1,
        'beds'            => 1,
        'bathrooms'       => 1,
        'size_sqft'       => 450,
        'check_in'        => '14:00',
        'check_out'       => '12:00',
        'min_nights'      => 1,
        'summary'         => 'Budget loft studio near UNIMAS, popular with visiting parents, students and business travellers.',
        'description'     => [
            'A compact loft with the bed upstairs and a small living area and kitchenette below. It is close to UNIMAS, UiTM Samarahan and the Samarahan shops and eateries.',
            'Kuching city is about 30 minutes away by car, which makes this a good value base if you are renting a car for your stay.',
        ],
        'amenities'       => ['Wi-Fi', 'Air conditioning', 'Kitchenette', 'Work desk', 'Smart TV', 'Free parking', 'Security'],
        'house_rules'     => ['No smoking indoors', 'No pets', 'No parties or events'],
        'images'          => [
            'images/homestays/kota-samarahan-loft-studio-1.jpg',
            'images/homestays/kota-samarahan-loft-studio-2.jpg',
            'images/homestays/kota-samarahan-loft-studio-3.jpg',
        ],
        // Wikimedia Commons, free licences: [caption, author, licence, licence URL, source]
        'photo_credits'   => [
            ['Area: Kota Samarahan and UNIMAS', 'Cerevisae', 'CC BY-SA 4.0', 'https://creativecommons.org/licenses/by-sa/4.0', 'https://commons.wikimedia.org/wiki/File:UNIMAS_campus_and_Wisma_SALCRA_in_Kota_Samarahan.jpg'],
            ['Nearby: UNIMAS chancellery', 'Tabdulla', 'CC BY-SA 3.0', 'http://creativecommons.org/licenses/by-sa/3.0/', 'https://commons.wikimedia.org/wiki/File:Chancellory_Universiti_Malaysia_Sarawak.JPG'],
            ['Nearby: Sarawak Heart Centre', 'Cerevisae', 'CC BY-SA 4.0', 'https://creativecommons.org/licenses/by-sa/4.0', 'https://commons.wikimedia.org/wiki/File:Sarawak_heart_Centre,_Kota_Samarahan_main_building.jpg'],
        ],
        'listed_at'       => '2026-10-05',
    ],
];
