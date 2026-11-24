<?php
/**
 * Default content: contact details, image slots and the starter gallery.
 * Admin edits are saved to /data and override these values.
 */

declare(strict_types=1);

function unsplash(string $id, int $w = 1800): string
{
    // Temporary stock photos until the client's own photos are uploaded from /admin.
    return "https://images.unsplash.com/{$id}?auto=format&fit=crop&w={$w}&q=72";
}

function default_settings(): array
{
    return [
        'company'      => 'Mira Water Management',
        'brand'        => 'Mira Pools',
        'tagline'      => 'Swimming pools, spas & water solutions across Kerala',
        'phone'        => '+91 99958 55133',
        'phone_alt'    => '+91 97783 55133',
        'landline'     => '0484 3103171',
        'whatsapp'     => '919995855133',
        'email'        => 'info@mirapools.in',
        'email_alt'    => 'shaiju@mirapools.in',
        'address_line' => '36/546-K, 2nd Floor, Manath Ayyath Complex, Manath Ayyath Moola Road, Near Chithranjali Studio, NGO Quarters',
        'locality'     => 'Kakkanad',
        'city'         => 'Kochi',
        'region'       => 'Kerala',
        'postcode'     => '682021',
        'hours'        => '',
        'ceo'          => 'Shaiju Sebastian',
        'map_query'    => 'Manath Ayyath Complex, Kakkanad, Kochi, Kerala 682021',
        'facebook'     => '',
        'instagram'    => '',
        'youtube'      => '',
        'branches'     => [
            ['city' => 'Kochi (Head office)', 'district' => 'Kakkanad, Ernakulam', 'phone' => '+91 99958 55133'],
            ['city' => 'Kozhikode',           'district' => 'Calicut',             'phone' => '+91 81298 81762'],
            ['city' => 'Kasaragod',           'district' => 'Kasaragod',           'phone' => '+91 99954 34642'],
            ['city' => 'Thiruvananthapuram',  'district' => 'Trivandrum',          'phone' => '+91 97783 55133'],
        ],
    ];
}

/**
 * Every replaceable image on the public site.
 * key => [label, group, default src, hint]
 */
function image_slots(): array
{
    $t = '/assets/img/temp/';
    return [
        'home_hero'      => ['Home – big opening photo',       'Home',     unsplash('photo-1759372945658-1e9f56e751bd', 2200), 'Wide landscape, at least 2000 px'],
        'home_intro'     => ['Home – introduction photo',      'Home',     $t . 'project-villa-pool.jpg',            'Landscape'],
        'home_intro_2'   => ['Home – small overlapping photo', 'Home',     $t . 'spa-mosaic-jacuzzi.jpg',            'Square or portrait'],
        'home_quote'     => ['Home – promise / quote photo',   'Home',     $t . 'project-infinity-villa.jpg',        'Landscape'],
        'home_cta'       => ['Call-to-action band (all pages)', 'Shared',  unsplash('photo-1505236732171-72a5b19c4981', 2000), 'Water texture works best'],
        'about_hero'     => ['About – top photo',              'About',    $t . 'site-resort-sunset.jpg',            'Wide landscape'],
        'about_story'    => ['About – story photo',            'About',    $t . 'project-heritage-resort.jpg',       'Landscape'],
        'about_team'     => ['About – team / site work photo', 'About',    $t . 'project-overflow-edge.jpg',         'Portrait or square'],
        'services_hero'  => ['Services – top photo',           'Services', unsplash('photo-1564751531853-4eace2746ae9', 2000), 'Wide landscape'],
        'gallery_hero'   => ['Gallery – top photo',            'Gallery',  $t . 'site-project-12.jpg',               'Wide landscape'],
        'projects_hero'  => ['Projects – top photo',           'Projects', unsplash('photo-1694967832949-09984640b143', 2000), 'Wide landscape'],
        'contact_hero'   => ['Contact – top photo',            'Contact',  unsplash('photo-1650519876461-c516be8be76c', 2000), 'Wide landscape'],
        'svc_swimming-pool-construction' => ['Service – Swimming pool construction', 'Services', $t . 'site-project-6.jpg', 'Landscape'],
        'svc_pool-filtration-systems'    => ['Service – Pool & spa filtration',      'Services', $t . 'equip-sand-filter.jpg',      'Landscape'],
        'svc_spa-sauna-steam'            => ['Service – Spa, sauna & steam',         'Services', unsplash('photo-1773423386572-4ad451dc6c26'), 'Landscape'],
        'svc_fountains-water-features'   => ['Service – Fountains & water features', 'Services', $t . 'site-fountain-jets.jpg', 'Landscape'],
        'svc_water-treatment-plants'     => ['Service – Water treatment plants',     'Services', $t . 'treatment-ro-plant.jpg',     'Landscape'],
        'svc_sewage-effluent-treatment'  => ['Service – Sewage & effluent treatment', 'Services', unsplash('photo-1787150741378-ad5a5dee8501'), 'Landscape'],
        'svc_uv-ultrafiltration'         => ['Service – UV & ultrafiltration',       'Services', unsplash('photo-1526599256864-6bedb9d7dfb5'), 'Landscape'],
        'svc_fish-pond-filtration'       => ['Service – Fish pond filtration',       'Services', $t . 'site-project-7.jpg',         'Landscape'],
        'svc_maintenance-amc'            => ['Service – Maintenance & AMC',          'Services', $t . 'site-project-18.jpg',        'Landscape'],
        'svc_pool-equipment-spares'      => ['Service – Equipment, spares & chemicals', 'Services', $t . 'equip-pump.jpg',         'Landscape'],
    ];
}

function gallery_categories(): array
{
    return [
        'pools'     => 'Swimming pools',
        'spas'      => 'Spas & jacuzzis',
        'fountains' => 'Fountains & features',
        'ponds'     => 'Fish ponds',
        'treatment' => 'Water treatment',
        'equipment' => 'Equipment',
    ];
}

/** Starter gallery, taken from the old website and the company brochure. Replace from /admin. */
function default_gallery(): array
{
    $t = '/assets/img/temp/';
    $rows = [
        ['site-project-12.jpg',          'Seaside pool with coconut palms',        'pools',     true],
        ['site-project-5.jpg',           'Mohanlal Residence',                     'pools',     true],
        ['site-project-18.jpg',          'Indoor pool with wall cascades',         'pools',     true],
        ['site-project-19.jpg',          'Lit fountain jets at dusk',              'fountains', true],
        ['project-resort-gazebo.jpg',    'Resort pool with island gazebo',         'pools',     true],
        ['site-project-2.jpg',           'Fish pond with stone cascade wall',      'ponds',     true],
        ['site-project-6.jpg',           'Hotel pool with mosaic artwork',         'pools',     true],
        ['project-infinity-villa.jpg',   'Overflow-edge pool for a private villa', 'pools',     true],
        ['site-project-3.jpg',           'Illuminated jet fountain',               'fountains', true],
        ['site-project-1.jpg',           'Resort pool beside a traditional building', 'pools',  false],
        ['site-project-7.jpg',           'Koi pond with bio filtration',           'ponds',     false],
        ['site-project-8.jpg',           'Glass vortex water feature',             'fountains', false],
        ['site-project-10.jpg',          'Mosaic-tiled pool with built-in steps',  'pools',     false],
        ['site-project-4.jpg',           'Illuminated bubble wall',                'fountains', false],
        ['site-project-17.jpg',          'Rain curtain water feature',             'fountains', false],
        ['site-project-14.jpg',          'Lap pool with green mosaic',             'pools',     false],
        ['site-project-11.jpg',          'Dandelion fountain',                     'fountains', false],
        ['site-project-16.jpg',          'Indoor pool with mosaic artwork',        'pools',     false],
        ['site-project-13.jpg',          'Indoor water wall',                      'fountains', false],
        ['site-project-20.jpg',          'Garden pool built around trees',         'pools',     false],
        ['site-project-15.jpg',          'Stone bowl fountain',                    'fountains', false],
        ['site-project-9.jpg',           'Row of lit jet fountains',               'fountains', false],
        ['project-beach-resort.jpg',     'Beachside resort pool',                  'pools',     false],
        ['project-heritage-resort.jpg',  'Pool for a heritage-style resort',       'pools',     false],
        ['project-villa-pool.jpg',       'Villa pool with granite coping',         'pools',     false],
        ['project-freeform-dolphin.jpg', 'Free-form pool with dolphin mosaic',     'pools',     false],
        ['project-lagoon-pool.jpg',      'Lagoon-style pool with rock edges',      'pools',     false],
        ['project-overflow-edge.jpg',    'Raised overflow pool',                   'pools',     false],
        ['project-pool-pavilion.jpg',    'Pool and pavilion lit at night',         'pools',     true],
        ['project-kids-splash.jpg',      'Children\'s splash pool',                'pools',     false],
        ['pool-dolphin-mosaic.jpg',      'Custom mosaic tile artwork',             'pools',     false],
        ['spa-mosaic-jacuzzi.jpg',       'Mosaic-tiled jacuzzi',                   'spas',      true],
        ['spa-jacuzzi.jpg',              'Family jacuzzi',                         'spas',      false],
        ['spa-hot-tub.jpg',              'Outdoor hot tub',                        'spas',      false],
        ['feature-courtyard-jets.jpg',   'Courtyard jet fountains',                'fountains', true],
        ['feature-reflecting-pool.jpg',  'Reflecting pool with underwater lights', 'fountains', false],
        ['feature-water-curtain.jpg',    'Water curtain wall',                     'fountains', false],
        ['feature-stone-cascade.jpg',    'Stone cascade wall',                     'fountains', false],
        ['feature-bowl-fountains.jpg',   'Bowl fountains in a garden',             'fountains', false],
        ['feature-garden-fountain.jpg',  'Garden fountain',                        'fountains', false],
        ['feature-glass-domes.jpg',      'Glass dome fountain nozzles',            'fountains', false],
        ['treatment-ro-plant.jpg',       'Reverse osmosis water treatment plant',  'treatment', false],
        ['equip-sand-filter.jpg',        'Pressure sand filter',                   'equipment', false],
        ['equip-pump.jpg',               'Pool circulation pump',                  'equipment', false],
    ];
    $items = [];
    foreach ($rows as $i => [$file, $title, $cat, $featured]) {
        $items[] = [
            'id'       => 'seed' . str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT),
            'src'      => $t . $file,
            'thumb'    => $t . $file,
            'title'    => $title,
            'alt'      => $title . ' by Mira Pools, Kerala',
            'category' => $cat,
            'location' => '',
            'featured' => $featured,
            'width'    => 0,
            'height'   => 0,
            'created'  => '2026-11-14T00:00:00+05:30',
        ];
    }
    return $items;
}
