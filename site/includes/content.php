<?php
/**
 * Service content, taken from the company introduction letter and brochure.
 */

declare(strict_types=1);

function services(): array
{
    return [
        'swimming-pool-construction' => [
            'name'   => 'Swimming pool construction',
            'menu'   => 'Pool construction',
            'short'  => 'Villa, resort, hotel and apartment pools, designed and built end to end.',
            'lead'   => 'We design and build swimming pools for homes, villas, resorts, hotels and apartment projects across Kerala, from the first sketch to the day you swim.',
            'body'   => [
                'A good pool is more than a hole lined with tiles. The structure, the waterproofing, the way water moves through the filtration and the light that falls on it at night all have to work together for years. Our pool professionals and design engineers handle each part, so you deal with one team instead of five contractors.',
                'We build skimmer pools, overflow and infinity-edge pools, free-form lagoon pools, children\'s pools and plunge pools, finished in mosaic or tile of your choice. Every pool comes with a properly sized plant room, underwater lighting and the safety fittings it needs.',
                'Our in-house designers work to your budget. Share your ideas, or ask for ours. We take turnkey projects from the first plan to final handover, and we only use quality products from internationally reputed brands, with spares available when you need them.',
            ],
            'includes' => [
                'Turnkey delivery, from plan to handover',
                'Site study, layout and pool design',
                'Structure, waterproofing and tiling',
                'Skimmer, overflow and infinity-edge systems',
                'Filtration plant room and pipework',
                'Underwater lighting, ladders and handrails',
                'Heating and ventilation for indoor pools',
                'Testing, commissioning and handover',
            ],
            'suited' => ['Private villas and homes', 'Resorts and homestays', 'Hotels and clubs', 'Apartment and villa projects', 'Schools and institutions'],
            'faqs' => [
                ['How long does it take to build a swimming pool?', 'It depends on the size, the site and the finish you choose. We give you a clear timeline after the site visit, before any work starts.'],
                ['Can you work with our architect or builder?', 'Yes. We regularly work alongside architects, builders and pool contractors, and can take on just the pool, or just the filtration and equipment.'],
                ['Do you build pools outside Kochi?', 'Yes. With offices in Kochi, Kozhikode, Kasaragod and Thiruvananthapuram, we take on projects across Kerala.'],
            ],
        ],
        'pool-filtration-systems' => [
            'name'   => 'Pool & spa filtration systems',
            'menu'   => 'Filtration systems',
            'short'  => 'Plant rooms that keep water clear, safe and cheap to run.',
            'lead'   => 'Clear water comes from the plant room. We design, supply and install filtration systems for new pools and upgrade tired ones in existing pools.',
            'body'   => [
                'We size the pump, filter and pipework to the volume and use of your pool, so the water turns over properly without wasting power. For pools that already exist, we find out why the water is cloudy or green and fix the cause, not just the symptom.',
                'We work with sand filters, multiport valves, pumps, heaters and heat pumps, automatic chemical dosing, UV and ozone systems, as well as basin fittings such as skimmers, inlets, main drains and overflow grating.',
            ],
            'includes' => [
                'Pressure sand filters and multiport valves',
                'Circulation pumps',
                'Pool heaters and heat pumps',
                'Automatic chemical dosing',
                'UV sterilisers and ozone generators',
                'Skimmers, inlets, main drains and grating',
                'Upgrades of existing plant rooms',
            ],
            'suited' => ['New pools', 'Existing pools with water problems', 'Spas and jacuzzis', 'Pool contractors who need a filtration partner'],
            'faqs' => [
                ['My pool water keeps turning green. Can you help?', 'Usually yes. Green water is often a sign of an undersized filter, poor circulation or a dosing problem. We inspect the plant room and recommend a fix.'],
                ['Can you supply equipment only?', 'Yes. We supply filtration equipment, spares and chemicals to pool owners and contractors.'],
            ],
        ],
        'spa-sauna-steam' => [
            'name'   => 'Spa, jacuzzi, sauna & steam',
            'menu'   => 'Spa, sauna & steam',
            'short'  => 'Jacuzzis, saunas and steam rooms for homes, hotels and wellness centres.',
            'lead'   => 'We supply and install jacuzzis, spa equipment, saunas and steam generators, so you can bring the comfort of a resort spa home or into your hotel.',
            'body'   => [
                'A spa needs the same care as a pool, just in a smaller space and at a higher temperature. We plan the jets, blowers, heating and filtration so the water stays clean and the controls are simple to use.',
                'For saunas and steam rooms we take care of buying and installing the equipment. Several factors decide how efficiently it runs, so we also help you design the room it goes in.',
            ],
            'includes' => [
                'Built-in and portable jacuzzis',
                'Spa jets, blowers and controls',
                'Spa heating and filtration',
                'Sauna heaters and cabins',
                'Steam generators',
            ],
            'suited' => ['Homes and villas', 'Hotels and resorts', 'Wellness centres and clubs'],
            'faqs' => [
                ['Can a jacuzzi be added next to an existing pool?', 'Often, yes. It depends on space and the existing plant room. We check both during the site visit.'],
            ],
        ],
        'fountains-water-features' => [
            'name'   => 'Fountains & water features',
            'menu'   => 'Fountains & features',
            'short'  => 'Jet fountains, water curtains, cascades and lit features.',
            'lead'   => 'From a quiet bowl fountain in a courtyard to a lit jet fountain at a hotel entrance, we design and build water features that look good for years.',
            'body'   => [
                'Water features fail when the pump, nozzles and filtration are an afterthought. We plan the hydraulics first, then the look, so the water falls the way the design intended and the basin stays clean.',
                'We supply a wide range of fountain nozzles and fittings and install submersible underwater lights, so features look just as good after dark.',
            ],
            'includes' => [
                'Finger jet, umbrella, foam jet and fan jet nozzles',
                'Glass curtain and wall curtain fountains',
                'Water cascades and water sheets',
                'Portable fountain kits',
                'Submersible underwater lights',
                'Filtration systems and accessories',
            ],
            'suited' => ['Hotel and office entrances', 'Courtyards and gardens', 'Malls and public spaces', 'Homes and villas'],
            'faqs' => [
                ['Can you light the fountain at night?', 'Yes. We install submersible underwater lights in a range of colours.'],
            ],
        ],
        'water-treatment-plants' => [
            'name'   => 'Water treatment plants',
            'menu'   => 'Water treatment',
            'short'  => 'Drinking and non-drinking water plants, from filters to RO.',
            'lead'   => 'We supply and install water treatment plants for drinking and non-drinking use, chosen to suit the water at your site.',
            'body'   => [
                'Water across Kerala varies a lot: iron in one place, hardness or a bad smell in another. So we start in our own laboratory in Kochi, where our technicians test your water for physical, chemical and bacteriological quality. Then we recommend the simplest system that fixes it.',
                'Our range runs from pressure sand and carbon filters to softeners, iron removal, reverse osmosis and complete mineral water plants, plus pressure boosting systems where supply is weak.',
            ],
            'includes' => [
                'Water testing in our own laboratory',
                'Pressure sand filters',
                'Activated carbon filters',
                'Water softening plants',
                'Iron removal filters',
                'Reverse osmosis plants',
                'Micron filters',
                'UV sterilisers and ozone generators',
                'Automatic chemical dosing systems',
                'Mineral water plants',
                'Pressure boosting systems',
            ],
            'suited' => ['Homes and apartments', 'Hotels and hospitals', 'Factories and institutions', 'Packaged drinking water units'],
            'faqs' => [
                ['Which treatment does my water need?', 'That depends on what is in it. We test the water first and recommend a system based on the results.'],
                ['Do you test the water yourselves?', 'Yes. Our laboratory in Kochi tests water for physical, chemical and bacteriological quality.'],
            ],
        ],
        'sewage-effluent-treatment' => [
            'name'   => 'Sewage & effluent treatment plants',
            'menu'   => 'Sewage & effluent (STP / ETP)',
            'short'  => 'STP and ETP systems for buildings and industry.',
            'lead'   => 'We supply, install and maintain sewage and effluent treatment plants for residential, commercial and industrial buildings.',
            'body'   => [
                'A treatment plant has to work every day without fuss. Our experts design each plant around the actual effluent discharge of your building, using physical, chemical and biological treatment, and our laboratory checks that the treated water meets the mark.',
                'We set each plant up so that operation and maintenance are simple for your staff, or for us under a maintenance contract.',
                'Treated water can often be reused for gardening and flushing, which cuts your fresh water bill.',
            ],
            'includes' => [
                'Sewage treatment plants (STP)',
                'Effluent treatment plants (ETP)',
                'Pumps, blowers and dosing',
                'Upgrades and repairs of existing plants',
                'Operation and maintenance contracts',
            ],
            'suited' => ['Apartments and villa projects', 'Hotels and hospitals', 'Commercial buildings', 'Industrial units'],
            'faqs' => [
                ['Do you maintain plants installed by others?', 'Yes. We take on operation and maintenance contracts for existing plants after an inspection.'],
            ],
        ],
        'uv-ultrafiltration' => [
            'name'   => 'UV purifiers & ultrafiltration',
            'menu'   => 'UV & ultrafiltration',
            'short'  => 'Chemical-free disinfection for safe water.',
            'lead'   => 'UV-based water purifiers and ultrafiltration systems that make water safe without adding chemicals.',
            'body'   => [
                'UV light disinfects water as it flows through, killing bacteria and viruses without changing the taste. Ultrafiltration removes fine particles and microbes using a membrane.',
                'Both work well alongside the other filters in a treatment plant, or on their own for homes and small buildings.',
            ],
            'includes' => [
                'UV water purifiers',
                'Ultrafiltration (UF) systems',
                'Spare UV lamps and membranes',
            ],
            'suited' => ['Homes', 'Offices and schools', 'Hotels and restaurants'],
            'faqs' => [
                ['How often does a UV lamp need replacing?', 'UV lamps lose strength over time and need replacing periodically. We supply genuine spare lamps and can replace them for you.'],
            ],
        ],
        'fish-pond-filtration' => [
            'name'   => 'Fish pond bio filtration',
            'menu'   => 'Fish pond filtration',
            'short'  => 'Healthy, clear water for koi and garden ponds.',
            'lead'   => 'Bio filtration systems that keep garden and koi ponds clear and the fish healthy.',
            'body'   => [
                'Fish ponds need a different approach from pools. Instead of chemicals, a bio filter uses helpful bacteria to break down waste, while a pump and UV clarifier keep the water clear.',
                'We size the system to the pond and the number of fish, and can add lighting and water features.',
            ],
            'includes' => [
                'Pond bio filters',
                'Pond pumps',
                'UV clarifiers',
                'Underwater lighting',
            ],
            'suited' => ['Garden and koi ponds', 'Resort landscapes', 'Courtyard ponds'],
            'faqs' => [
                ['Will a filter make my pond water clear?', 'A correctly sized bio filter with a UV clarifier usually clears green water. We check the pond size and fish load first.'],
            ],
        ],
        'maintenance-amc' => [
            'name'   => 'Maintenance & AMC',
            'menu'   => 'Maintenance & AMC',
            'short'  => 'Operation and maintenance contracts and design consultancy.',
            'lead'   => 'Operation and maintenance contracts that keep pools, fountains and treatment plants running smoothly, all year.',
            'body'   => [
                'Most water problems start small. Regular servicing, water testing and timely spare parts keep a pool or plant from turning into an expensive repair. Under an annual maintenance contract (AMC), our team does exactly that, on a schedule agreed with you.',
                'If you prefer, we can place a permanent operator at your site, at a reasonable cost, to keep your pool or spa hygienic every day, backed by an annual maintenance contract.',
                'We also offer structural and design consultancy for architects, builders and owners planning a new pool or water system.',
            ],
            'includes' => [
                'Annual maintenance contracts (AMC)',
                'Permanent on-site operator',
                'Operation of pools and treatment plants',
                'Water testing and chemical balancing',
                'Repairs and equipment replacement',
                'Structural and design consultancy',
            ],
            'suited' => ['Hotels and resorts', 'Apartments and clubs', 'Private pool owners', 'Commercial buildings'],
            'faqs' => [
                ['Do you service pools you did not build?', 'Yes. We inspect the pool first, then propose a maintenance plan.'],
            ],
        ],
        'pool-equipment-spares' => [
            'name'   => 'Equipment, spares & chemicals',
            'menu'   => 'Equipment & spares',
            'short'  => 'Plant room, basin, lighting and cleaning equipment, plus chemicals.',
            'lead'   => 'Spares, consumables and chemicals for pools, water treatment and waste water treatment plants.',
            'body'   => [
                'We work with reputable equipment makers and keep a wide range of parts, so a broken pump or valve does not keep your pool closed for weeks.',
                'From plant room equipment to cleaning tools, we supply what pool owners, maintenance teams and contractors need.',
            ],
            'includes' => [
                'Plant room equipment: filters, valves, pumps, heaters, dosing',
                'Basin equipment: skimmers, inlets, drains, grating',
                'Underwater lights, ladders and handrails',
                'Safety equipment',
                'Cleaning and maintenance tools',
                'Pool and water treatment chemicals',
            ],
            'suited' => ['Pool owners', 'Maintenance teams', 'Pool contractors', 'Treatment plant operators'],
            'faqs' => [
                ['Do you supply contractors?', 'Yes. We supply equipment, spares and chemicals to pool contractors as well as owners.'],
            ],
        ],
    ];
}

function service(string $slug): ?array
{
    $all = services();
    return isset($all[$slug]) ? ['slug' => $slug] + $all[$slug] : null;
}

/** The main public pages, used for navigation and the sitemap. */
function nav_pages(): array
{
    return [
        '/'         => 'Home',
        '/about'    => 'About',
        '/services' => 'Services',
        '/projects' => 'Projects',
        '/gallery'  => 'Gallery',
        '/contact'  => 'Contact',
    ];
}
