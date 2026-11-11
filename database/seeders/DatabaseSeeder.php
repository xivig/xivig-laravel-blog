<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\AdminEmail;
use App\Models\Author;
use App\Models\Category;
use App\Models\ChatMessage;
use App\Models\Comment;
use App\Models\ContactMessage;
use App\Models\FAQ;
use App\Models\MediaAsset;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\NewsletterSubscriber;
use App\Models\Page;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Redirect;
use App\Models\Role;
use App\Models\Service;
use App\Models\ServiceInquiry;
use App\Models\Setting;
use App\Models\SocialLink;
use App\Models\Tag;
use App\Models\User;
use App\Models\Widget;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        // 1. Settings
        Setting::set('site_title', 'XIVIG Maison Gazette', 'text', 'general');
        Setting::set('site_tagline', 'The Haute Couture & Horology Journal', 'text', 'general');
        Setting::set('site_description', 'Chronicles of bespoke craftsmanship, luxury timepieces, and contemporary aesthetics.', 'textarea', 'general');
        Setting::set('contact_email', 'gazette@xivig.com', 'text', 'general');
        Setting::set('posts_per_page', '9', 'text', 'general');
        Setting::set('comment_auto_approve', '1', 'boolean', 'general');
        Setting::set('timezone', 'Europe/Paris', 'text', 'general');

        // 2. Secure Admin User Provisioning
        $adminEmail = env('INITIAL_ADMIN_EMAIL', 'admin@xivig.com');
        $adminName = env('INITIAL_ADMIN_NAME', 'Maison Administrator');
        $envPassword = env('INITIAL_ADMIN_PASSWORD');

        if (!empty($envPassword)) {
            $adminPassword = $envPassword;
        } elseif (app()->environment('testing')) {
            // Predictable test harness password for automated testing suite
            $adminPassword = 'admin123456';
        } elseif (app()->environment('production')) {
            // Cryptographically secure 24-character generated password for production installations
            $adminPassword = Str::password(24, true, true, true, false);
            if ($this->command) {
                $this->command->warn('========================================================================');
                $this->command->warn(' PRODUCTION SECURITY NOTICE: Initial Administrator Provisioned');
                $this->command->line(" Email:    {$adminEmail}");
                $this->command->line(" Password: {$adminPassword}");
                $this->command->warn(' Store these credentials in a secure credential vault immediately.');
                $this->command->warn('========================================================================');
            }
        } else {
            // Local development fallback with warning
            $adminPassword = 'admin123456';
            if ($this->command) {
                $this->command->comment("Notice: Using development admin credentials ({$adminEmail}). Set INITIAL_ADMIN_PASSWORD in .env for production.");
            }
        }

        $admin = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name'        => $adminName,
                'password'    => Hash::make($adminPassword),
                'role'        => 'admin',
                'designation' => 'Executive Editor-in-Chief',
                'timezone'    => 'Europe/Paris',
                'bio'         => 'Chief Executive Editor of the Maison Gazette.',
            ]
        );

        // 3. Editorial Authors
        $author1 = Author::firstOrCreate(
            ['slug' => 'eleonore-saint-germain'],
            [
                'user_id'     => $admin->id,
                'name'        => 'Éléonore de Saint-Germain',
                'email'       => 'eleonore@xivig.com',
                'designation' => 'Senior Haute Couture Critic',
                'bio'         => 'Former Paris fashion editor with over fifteen years dissecting atelier draping, rare textiles, and Milanese runway presentations.',
                'twitter'     => 'https://twitter.com',
                'linkedin'    => 'https://linkedin.com',
                'instagram'   => 'https://instagram.com',
                'is_featured' => true,
            ]
        );

        $author2 = Author::firstOrCreate(
            ['slug' => 'marcello-vieri'],
            [
                'name'        => 'Marcello Vieri',
                'email'       => 'marcello@xivig.com',
                'designation' => 'Master Horologist & Watchmaker',
                'bio'         => 'Geneva-trained watchmaker and horological historian dedicated to the mechanical purity of perpetual calendars and deadbeat seconds.',
                'twitter'     => 'https://twitter.com',
                'is_featured' => true,
            ]
        );

        $author3 = Author::firstOrCreate(
            ['slug' => 'sophia-thorne'],
            [
                'name'        => 'Sophia Thorne',
                'email'       => 'sophia@xivig.com',
                'designation' => 'Artisan Leathercraft Historian',
                'bio'         => 'Specializing in Florentine saddlery, saddle-stitching methodologies, and vegetable-tanned French calfskins.',
                'is_featured' => true,
            ]
        );

        // 4. Categories
        $categories = [
            [
                'name'        => 'Haute Couture',
                'slug'        => 'haute-couture',
                'description' => 'Architectural silhouettes, silk velvets, and hand-embroidered bespoke tailoring.',
                'color'       => '#cba358',
                'is_featured' => true,
            ],
            [
                'name'        => 'Horology & Timepieces',
                'slug'        => 'horology-timepieces',
                'description' => 'Precision balance wheels, grand complications, and the mechanical philosophy of watchmaking.',
                'color'       => '#6366f1',
                'is_featured' => true,
            ],
            [
                'name'        => 'Artisan Savoir-Faire',
                'slug'        => 'artisan-savoir-faire',
                'description' => 'Master craftsmen preserving centuries-old artisanal traditions across Europe.',
                'color'       => '#10b981',
                'is_featured' => true,
            ],
            [
                'name'        => 'Runway Reports',
                'slug'        => 'runway-reports',
                'description' => 'Front-row dispatches from Paris, Milan, London, and Tokyo fashion weeks.',
                'color'       => '#f43f5e',
                'is_featured' => false,
            ],
            [
                'name'        => 'Architectural Living',
                'slug'        => 'architectural-living',
                'description' => 'Minimalist interiors, bespoke marble finishes, and spaces crafted for contemplation.',
                'color'       => '#06b6d4',
                'is_featured' => false,
            ],
        ];

        $catModels = [];
        foreach ($categories as $cat) {
            $catModels[$cat['slug']] = Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 5. Tags
        $tagNames = [
            'Bespoke Tailoring', 'Tourbillons', 'Italian Leather', 'Paris Fashion Week',
            'Swiss Mechanics', 'Minimalism', 'Craftsmanship', 'Sustainable Luxury', 'Vintage Horology'
        ];

        $tagModels = [];
        foreach ($tagNames as $tn) {
            $slug = Str::slug($tn);
            $tagModels[$slug] = Tag::firstOrCreate(['slug' => $slug], ['name' => $tn]);
        }

        // Check available media images
        $sampleImages = glob(storage_path('app/public/media/*.{jpg,png,webp,avif}'), GLOB_BRACE);
        $imageFilenames = array_map(fn($p) => 'media/' . basename($p), $sampleImages);

        $img1 = !empty($imageFilenames) ? $imageFilenames[0] : null;
        $img2 = count($imageFilenames) > 1 ? $imageFilenames[1] : $img1;
        $img3 = count($imageFilenames) > 2 ? $imageFilenames[2] : $img1;
        $img4 = count($imageFilenames) > 3 ? $imageFilenames[3] : $img1;
        $img5 = count($imageFilenames) > 4 ? $imageFilenames[4] : $img1;

        // 6. Editorial Stories
        $articles = [
            [
                'title'                  => 'The Geometries of Haute Couture: Inside the Parisian Atelier',
                'slug'                   => 'geometries-of-haute-couture-paris-atelier',
                'category_id'            => $catModels['haute-couture']->id,
                'author_id'              => $author1->id,
                'is_featured'            => true,
                'is_editor_pick'         => false,
                'views_count'            => 4820,
                'likes_count'            => 312,
                'reading_time'           => 6,
                'featured_image'         => $img1,
                'featured_image_caption' => 'The cutting tables of Rue du Faubourg Saint-Honoré, Paris.',
                'published_at'           => now()->subDays(2),
                'excerpt'                => 'An intimate exploration of silk radzimir, double-faced cashmere draping, and how traditional silhouettes are being quietly re-engineered for the 21st century.',
                'content'                => '<p>In an era where velocity often eclipses intention, the secluded ateliers of Paris remain sanctuaries of deliberate creation. Here, tucked behind unmarked limestone facades, master tailors manipulate textiles not as seasonal commodity, but as architectural sculpture.</p>
<h2>The Architecture of the Silhouette</h2>
<p>Draping on the bias is an art form that demands complete surrender to the cloth. A millimeter of tension will transform how a wool crepe falls across the scapula, shifting the garment from a costume into an extension of the wearer’s spine.</p>
<blockquote>"The true luxury of a garment lies in the silent conversations between the tailor’s chalk and the client’s posture. It is a dialogue that machine algorithms cannot decipher."</blockquote>
<p>Each season presents an opportunity to re-examine classical proportions. We observe an intentional return to structure: elongated lapels hand-padded with horsehair canvas, suppressed waists engineered with invisible darting, and trousers that cascade with mathematical precision.</p>
<h2>Material Integrity and Provenance</h2>
<p>The fabrics chosen for this collection represent centuries of agrarian refinement. Extra-long staple sea-island cottons spun in Lancashire, raw silks harvested in Como, and Scottish merinos woven on vintage looms.</p>',
                'tags'                   => ['bespoke-tailoring', 'paris-fashion-week', 'craftsmanship'],
            ],
            [
                'title'                  => 'The Perpetual Motion: Reflections on Horological Purity',
                'slug'                   => 'perpetual-motion-horological-purity',
                'category_id'            => $catModels['horology-timepieces']->id,
                'author_id'              => $author2->id,
                'is_featured'            => false,
                'is_editor_pick'         => true,
                'views_count'            => 3420,
                'likes_count'            => 194,
                'reading_time'           => 5,
                'featured_image'         => $img2,
                'featured_image_caption' => 'Hand-finishing the bevels of a free-sprung balance wheel.',
                'published_at'           => now()->subDays(4),
                'excerpt'                => 'Why the modern mechanical watch remains the ultimate philosophical triumph against digital ephemerality.',
                'content'                => '<p>To wear a mechanical complication is to carry a kinetic micro-universe upon one’s wrist. As the escapement sings its rapid cadence of 28,800 vibrations per hour, it serves as a tactile memento mori—a gentle, ceaseless reminder of time slipping through our grasp.</p>
<h2>The Art of Anglage and Black Polish</h2>
<p>Before a bridge is sealed within its sapphire sanctuary, an artisan spends up to twelve hours with gentian wood and diamond paste. Anglage—the delicate chamfering of right angles into mirror-bright 45-degree bevels—is a craft where a single twitch of the hand ruins a month of precision milling.</p>
<blockquote>"A quartz crystal calculates time through electricity; a mechanical timepiece breathes through human interaction."</blockquote>
<p>When you wind a crown, your kinetic energy is stored within the mainspring. There is an unmistakable romance in realizing that this machine will tick in two centuries precisely as it does today, requiring neither firmware nor battery.</p>',
                'tags'                   => ['tourbillons', 'swiss-mechanics', 'vintage-horology'],
            ],
            [
                'title'                  => 'Florentine Saddlery: The Enduring Mastery of Vegetable-Tanned Calfskin',
                'slug'                   => 'florentine-saddlery-vegetable-tanned-calfskin',
                'category_id'            => $catModels['artisan-savoir-faire']->id,
                'author_id'              => $author3->id,
                'is_featured'            => false,
                'is_editor_pick'         => true,
                'views_count'            => 2890,
                'likes_count'            => 145,
                'reading_time'           => 4,
                'featured_image'         => $img3,
                'featured_image_caption' => 'Two-needle saddle stitching using beeswaxed linen thread.',
                'published_at'           => now()->subDays(7),
                'excerpt'                => 'Tracing the aroma of chestnut tannins and two-needle hand stitching along the banks of the Arno River.',
                'content'                => '<p>Step into an authentic Florentine leather workshop and the air is immediately heavy with the intoxicating scent of mimosa bark, chestnut tannins, and raw beeswax. Unlike modern chrome tanning that yields instant uniformity, natural vegetable tanning is an agonizingly slow ritual taking up to sixty days in submerged wooden vats.</p>
<h2>The Immortality of the Saddle Stitch</h2>
<p>A sewing machine employs two distinct threads that interlock in the middle; if one loop breaks, the entire seam unravels. The traditional saddle stitch utilizes a single length of linen thread with a harness needle at each end, crossing continuously through hand-punched pricking irons.</p>
<p>Even if one stitch were severed by a scalpel, the structural integrity of the surrounding seam remains rock-solid. It is why our bespoke weekender bags outlive their original custodians.</p>',
                'tags'                   => ['italian-leather', 'craftsmanship', 'sustainable-luxury'],
            ],
            [
                'title'                  => 'Monochrome & Restraint: Redefining the Modern Luxury Wardrobe',
                'slug'                   => 'monochrome-restraint-modern-luxury-wardrobe',
                'category_id'            => $catModels['haute-couture']->id,
                'author_id'              => $author1->id,
                'is_featured'            => false,
                'is_editor_pick'         => true,
                'views_count'            => 1940,
                'likes_count'            => 98,
                'reading_time'           => 4,
                'featured_image'         => $img4,
                'featured_image_caption' => 'Textural exploration in charcoal wool and matte silk.',
                'published_at'           => now()->subDays(10),
                'excerpt'                => 'True sophistication speaks in hushed tones. How understated silhouettes and texture contrast dominate the contemporary Maison ethos.',
                'content'                => '<p>Loud branding and ostentatious emblems are the antithesis of discernment. The connoisseur understands that authority is communicated through the drape of raw-edged linen and the subtle gleam of horn buttons carved in the Scottish Highlands.</p>
<h2>The Nuances of Black and Charcoal</h2>
<p>Black is never merely black. In the hands of a knowledgeable designer, it encompasses a symphony of textures: the dry matte grain of fresco wool, the deep light-absorbing depth of Japanese velvet, and the fluid sheen of washed silk charmeuse.</p>',
                'tags'                   => ['minimalism', 'bespoke-tailoring'],
            ],
            [
                'title'                  => 'The Living Atelier: Designing Spaces for Contemplation and Creation',
                'slug'                   => 'living-atelier-spaces-for-contemplation',
                'category_id'            => $catModels['architectural-living']->id,
                'author_id'              => $author1->id,
                'is_featured'            => false,
                'is_editor_pick'         => false,
                'views_count'            => 1510,
                'likes_count'            => 82,
                'reading_time'           => 5,
                'featured_image'         => $img5,
                'featured_image_caption' => 'Sunlight illuminating hand-planed walnut and honed travertine.',
                'published_at'           => now()->subDays(14),
                'excerpt'                => 'Examining how architectural harmony, unpolished travertine, and curated acoustics enhance creative lucidity.',
                'content'                => '<p>Architecture is the ultimate frame of human experience. When an interior strips away visual noise, the mind naturally settles into deep equilibrium.</p>
<h2>Travertine, Walnut, and Natural Patina</h2>
<p>Materials that age with dignity are fundamental to the Maison lifestyle. Honed travertine with its porous cavities tells of ancient mineral deposits; solid European walnut oxidizes gently over decades under the kiss of morning sunlight.</p>',
                'tags'                   => ['minimalism', 'sustainable-luxury'],
            ],
        ];

        foreach ($articles as $artData) {
            $tagSlugs = $artData['tags'];
            unset($artData['tags']);

            $post = Post::firstOrCreate(['slug' => $artData['slug']], $artData);

            $tagIds = [];
            foreach ($tagSlugs as $ts) {
                if (isset($tagModels[$ts])) {
                    $tagIds[] = $tagModels[$ts]->id;
                }
            }
            $post->tags()->sync($tagIds);

            // Add sample reader comments
            Comment::firstOrCreate(
                ['post_id' => $post->id, 'author_email' => 'laurent@vogue.fr'],
                [
                    'author_name' => 'Laurent de Valois',
                    'content'     => 'A truly magnificent essay. The comparison between architectural form and drapery captures precisely what is missing in modern industrial pret-a-porter.',
                    'status'      => 'approved',
                    'created_at'  => now()->subDays(1),
                ]
            );

            Comment::firstOrCreate(
                ['post_id' => $post->id, 'author_email' => 'geneve.horloger@swiss.ch'],
                [
                    'author_name' => 'Jean-Luc Picard',
                    'content'     => 'Extremely well articulated. It is rare to see general publications appreciate the grueling patience of manual anglage.',
                    'status'      => 'approved',
                    'created_at'  => now()->subHours(12),
                ]
            );
        }

        // 7. Sample Subscribers & Patrons
        $sampleSubscribers = [
            [
                'email'  => 'collector@hauteluxury.com',
                'name'   => 'Baron Laurent de Valois',
                'tier'   => 'vip',
                'status' => 'subscribed',
                'notes'  => 'Collector of Patek Philippe & Vacheron vintage complications.',
            ],
            [
                'email'  => 'atelier@paris-couture.fr',
                'name'   => 'Madame Camille Delacroix',
                'tier'   => 'vip',
                'status' => 'subscribed',
                'notes'  => 'Creative Director, Rue Saint-Honoré Paris atelier.',
            ],
            [
                'email'  => 'curator@florence-leather.it',
                'name'   => 'Alessandro Rossi',
                'tier'   => 'standard',
                'status' => 'subscribed',
                'notes'  => 'Master artisan in vegetable-tanned saddlery.',
            ],
            [
                'email'  => 'editor@british-style.co.uk',
                'name'   => 'Eleanor Vance',
                'tier'   => 'press',
                'status' => 'subscribed',
                'notes'  => 'London correspondent for Savile Row & luxury tailoring.',
            ],
            [
                'email'  => 'patron@swiss-horloger.ch',
                'name'   => 'Jean-Luc Picard',
                'tier'   => 'partner',
                'status' => 'subscribed',
                'notes'  => 'Geneva independent watchmaker association representative.',
            ],
        ];

        foreach ($sampleSubscribers as $subData) {
            NewsletterSubscriber::updateOrCreate(
                ['email' => $subData['email']],
                array_merge($subData, [
                    'source'     => 'web_portal',
                    'created_at' => now()->subDays(rand(2, 45)),
                ])
            );
        }

        // 7b. Sample Atelier Services & Client Inquiries
        $sampleServices = [
            [
                'title'           => 'Haute Horlogerie & Complications Advisory',
                'slug'            => 'haute-horlogerie-complications-advisory',
                'category'        => 'Horology Advisory',
                'tagline'         => 'Curatorial provenance verification, independent watchmaker acquisitions, and private collection strategy.',
                'description'     => "Our horological advisory service provides bespoke guidance for discerning collectors and institutions seeking high-complication timepieces. We leverage an international network of independent artisans, auction specialists, and archival masters.\n\nEvery engagement includes rigorous microscopic condition assessment, movement finishing provenance, and long-term liquidity analysis.",
                'icon'            => 'bi-clock-history',
                'badge'           => 'Signature Atelier',
                'pricing_type'    => 'fixed',
                'price'           => 3500.00,
                'price_unit'      => '/engagement',
                'delivery_time'   => '2 - 3 Weeks',
                'features'        => [
                    'Comprehensive 40-point movement & finishing inspection',
                    'Historical provenance & archive extract verification',
                    'Direct liaison with independent Swiss ateliers',
                    'Confidential acquisition negotiation & escrow structuring',
                    'Bound leather dossier with archival macro-photography'
                ],
                'target_audience' => 'Private Collectors, Family Offices, Luxury Maisons',
                'is_featured'     => true,
                'status'          => 'active',
                'sort_order'      => 1,
            ],
            [
                'title'           => 'Investigative Editorial Journalism & Bespoke Monographs',
                'slug'            => 'investigative-editorial-journalism-bespoke-monographs',
                'category'        => 'Editorial & Press',
                'tagline'         => 'Exquisite long-form cultural essays, artisan profiles, and museum-grade heritage monographs.',
                'description'     => "In an era of disposable digital copy, we practice slow, authoritative journalism. We craft immersive narrative essays exploring the intersections of horology, architecture, bespoke tailoring, and heritage craft.\n\nFrom investigative exposés on endangered European leather tanning to deeply moving artisan biographies, our chronicles are published in the Maison Gazette and syndicated globally.",
                'icon'            => 'bi-pen-fill',
                'badge'           => 'Most Requested',
                'pricing_type'    => 'starting_at',
                'price'           => 2200.00,
                'price_unit'      => '/feature',
                'delivery_time'   => '7 - 10 Business Days',
                'features'        => [
                    '2,500 - 4,500 word deeply researched investigative narrative',
                    'On-site artisan interview or atelier documentary visit',
                    'Syndicated publication on XIVIG Maison Gazette front cover',
                    'High-resolution photographic art direction & layout styling',
                    'Perpetual worldwide commercial reproduction & PR syndication rights'
                ],
                'target_audience' => 'Prestige Brands, Cultural Institutions, Historic Maisons',
                'is_featured'     => true,
                'status'          => 'active',
                'sort_order'      => 2,
            ],
            [
                'title'           => 'Creative Direction & Visual Brand Scenography',
                'slug'            => 'creative-direction-visual-brand-scenography',
                'category'        => 'Creative Direction',
                'tagline'         => 'Sensory aesthetic direction, lookbook storytelling, and cinematic campaign visual systems.',
                'description'     => "We define the visual and poetic vernacular of ultra-luxury houses. From seasonal campaigns and product launch scenography to brand identity restyling, our studio merges neoclassical European grandeur with cutting-edge visual composition.\n\nWe oversee lighting, model casting, materials curation, and typographic discipline.",
                'icon'            => 'bi-palette-fill',
                'badge'           => 'Atelier Exclusive',
                'pricing_type'    => 'retainer',
                'price'           => 6500.00,
                'price_unit'      => '/month',
                'delivery_time'   => 'Quarterly Retainer',
                'features'        => [
                    'Complete seasonal campaign visual strategy & moodboard architecture',
                    'Art direction for digital, print, and physical atelier spaces',
                    'Typography & brand manifesto curation',
                    'Bimonthly curatorial review sessions with senior executives',
                    'Full production team oversight & post-production color grading'
                ],
                'target_audience' => 'Haute Couture Houses, Niche Perfumeries, Watch Ateliers',
                'is_featured'     => true,
                'status'          => 'active',
                'sort_order'      => 3,
            ],
            [
                'title'           => 'Bespoke Digital Platform Architecture & High-End Engineering',
                'slug'            => 'bespoke-digital-platform-architecture',
                'category'        => 'Digital Architecture',
                'tagline'         => 'Bespoke luxury web applications, editorial portals, and ultra-fast digital showrooms.',
                'description'     => "Digital experiences for luxury brands require zero-compromise execution: ultra-fast micro-interactions, dark/light theme perfection, cryptographic security, and flawless typography. We build custom Laravel, Vue, and Tailwind architectural solutions engineered for extreme performance.",
                'icon'            => 'bi-code-slash',
                'badge'           => null,
                'pricing_type'    => 'quote',
                'price'           => null,
                'price_unit'      => 'Custom Commission',
                'delivery_time'   => '4 - 8 Weeks',
                'features'        => [
                    'Tailored Laravel / modern PHP enterprise architecture',
                    'Zero-flash luxury theme engine & responsive micro-animations',
                    'Cryptographic 2FA & RBAC clearance security models',
                    'Global CDN distribution & sub-second page performance',
                    'Dedicated post-launch maintenance & curatorial support'
                ],
                'target_audience' => 'Luxury Brands, High-Volume Publications, Private Enterprises',
                'is_featured'     => false,
                'status'          => 'active',
                'sort_order'      => 4,
            ]
        ];

        foreach ($sampleServices as $svcData) {
            $createdSvc = Service::updateOrCreate(
                ['slug' => $svcData['slug']],
                $svcData
            );

            // Add sample client inquiries
            if ($createdSvc->slug === 'haute-horlogerie-complications-advisory') {
                ServiceInquiry::firstOrCreate(
                    ['email' => 'marquis.fontaine@geneva-cap.ch', 'service_id' => $createdSvc->id],
                    [
                        'name'      => 'Marquis Henri de Fontaine',
                        'company'   => 'Fontaine Private Trust',
                        'phone'     => '+41 22 819 00 12',
                        'budget'    => '$7,500 - $20,000',
                        'timeline'  => 'Immediate',
                        'message'   => 'Seeking curatorial advisory for an upcoming private auction featuring an unrecorded Philippe Dufour Simplicity 34mm white gold. We require verification of movement anglage and provenance.',
                        'status'    => 'pending',
                        'notes'     => 'High priority client. Reached out via private recommendation.',
                    ]
                );
            }
        }

        // 8. Register Media Assets in database
        if (!empty($imageFilenames)) {
            foreach ($imageFilenames as $imgRel) {
                $base = basename($imgRel);
                MediaAsset::firstOrCreate(
                    ['file_path' => $imgRel],
                    [
                        'original_name' => $base,
                        'file_name'     => $base,
                        'file_size'     => 124000,
                        'mime_type'     => 'image/jpeg',
                        'dimensions'    => '1200x800',
                        'alt_text'      => 'Editorial luxury showcase',
                    ]
                );
            }
        }

        // 9. Static CMS Pages
        Page::firstOrCreate(
            ['slug' => 'about-us'],
            [
                'title' => 'About XIVIG Maison Gazette',
                'content' => '<h2>An Architectural Manifesto on Sartorial & Horological Mastery</h2><p>Founded at the intersection of classical European couture ateliers and avant-garde horology, XIVIG Maison Gazette operates as an independent critical journal. Our editors and master craftsmen document the deliberate, unhurried pursuit of perfection.</p><blockquote>"True luxury does not clamor for attention; it commands stillness through precision, material authenticity, and timeless poise."</blockquote><p>From the secret workshops of Place Vendôme to the bespoke shoemakers of Florence, our correspondents explore the cultural resonance and enduring heritage of tactile art forms.</p>',
                'meta_title' => 'About XIVIG Maison Gazette | Haute Couture & Horology Journal',
                'meta_description' => 'Discover the founding philosophy, editorial principles, and atelier pedigree of XIVIG Maison Gazette.',
                'meta_keywords' => 'luxury, haute couture, horology, craftsmanship, atelier, editorial',
                'template' => 'default',
                'is_published' => true,
            ]
        );

        Page::firstOrCreate(
            ['slug' => 'privacy-policy'],
            [
                'title' => 'Privacy & Confidentiality Codex',
                'content' => '<h2>Discretion & Digital Stewardship</h2><p>At XIVIG Maison, we hold patron confidentiality in the same reverence as our physical archives. We operate with minimal digital footprinting and uncompromising cryptographic integrity.</p><h3>Data Collection & Purpose</h3><p>We only collect information voluntarily provided when requesting editorial correspondence, dispatching inquiries through the Salon Desk, or subscribing to our weekly Gazette editions.</p>',
                'meta_title' => 'Patron Privacy & Data Codex | XIVIG Maison Gazette',
                'meta_description' => 'Our comprehensive pledge to reader privacy, cryptographic stewardship, and editorial discretion.',
                'template' => 'default',
                'is_published' => true,
            ]
        );

        Page::firstOrCreate(
            ['slug' => 'terms-of-dispatch'],
            [
                'title' => 'Terms of Gazette Dispatch & Editorial Syndication',
                'content' => '<h2>Editorial Integrity & Syndication Rights</h2><p>All photography, critical essays, horological diagrams, and monographs published under the XIVIG imprint remain the intellectual and artistic property of the Maison.</p><p>Reprints and translations are granted exclusively upon written consent from the executive editorial board.</p>',
                'meta_title' => 'Editorial Terms of Dispatch | XIVIG Maison Gazette',
                'meta_description' => 'Review terms governing readership, syndication rights, and intellectual property across the XIVIG portfolio.',
                'template' => 'default',
                'is_published' => true,
            ]
        );

        Page::firstOrCreate(
            ['slug' => 'editorial-ethics'],
            [
                'title' => 'Editorial Ethics & Maison Standards Codex',
                'content' => '<h2>Uncompromised Autonomy &amp; The Maison Standards Codex</h2><p>In an era of commercial convergence and algorithmic curation, XIVIG Maison Gazette maintains a sacred covenant with our readership: absolute, uncompromised editorial independence. Every critique, historical monograph, and horological deconstruction published under our imprint is forged under strict ethical safeguards.</p><h3>1. Independence &amp; Non-Endorsement Doctrine</h3><p>We do not accept paid editorial placements, compensated product reviews, or undisclosed brand sponsorships. Our evaluations of timepieces, bespoke tailoring, and leathercraft are conducted with rigorous objectivity, free from commercial influence or atelier pressure.</p><h3>2. Provenance &amp; Material Authenticity Verification</h3><p>Every historical reference, atelier lineage, and horological movement mentioned in our dispatches undergoes rigorous provenance verification by our scholarly research board. We demand documented authenticity before chronicling rare archives and vintage acquisitions.</p><h3>3. Conflict of Interest Disclosures</h3><p>Should any contributing editor, horologist, or critic possess any direct, advisory, or familial connection to an artisan, manufacture, or auction house featured in a chronicle, full and conspicuous disclosure is mandated at the vanguard of the publication.</p><h3>4. Rectification &amp; Archival Record</h3><p>We hold ourselves to the highest standards of intellectual humility. In the rare event of factual discrepancy, corrections are promptly issued with transparent editorial notations preserving the historical record.</p>',
                'meta_title' => 'Editorial Ethics & Maison Standards Codex | XIVIG Maison Gazette',
                'meta_description' => 'Our uncompromising code of editorial autonomy, provenance verification, conflict disclosures, and journalistic integrity.',
                'meta_keywords' => 'editorial ethics, journalistic integrity, maison standards, provenance, luxury critique, conflict of interest, authenticity',
                'template' => 'default',
                'is_published' => true,
            ]
        );

        // 10. Sample Redirects
        Redirect::firstOrCreate(
            ['source_url' => '/legacy-editorial/couture-origins'],
            [
                'target_url' => '/chronicles',
                'status_code' => 301,
                'hits' => 14,
                'is_active' => true,
                'last_accessed_at' => now()->subDays(2),
            ]
        );

        Redirect::firstOrCreate(
            ['source_url' => '/feed'],
            [
                'target_url' => '/feed.xml',
                'status_code' => 301,
                'hits' => 42,
                'is_active' => true,
                'last_accessed_at' => now()->subHours(6),
            ]
        );

        // 11. Sample Contact Message
        ContactMessage::firstOrCreate(
            ['email' => 'concierge@le-meurice.com'],
            [
                'name' => 'Jean-Luc Moreau',
                'subject' => 'Private Atelier Exhibition Inquiry',
                'message' => 'Greetings to the Editors. We would be honored to host a private salon viewing for your horological patrons during Paris Autumn Couture Week.',
                'ip_address' => '127.0.0.1',
                'is_read' => false,
                'created_at' => now()->subDay(),
            ]
        );

        // 12. Roles & Permissions (Spatie Schema Compatible)
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator', 'description' => 'Unrestricted master governance across the entire Maison digital platform.']);
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Administrator', 'description' => 'Full administrative command of content, taxonomies, SEO, and operations.']);
        $editorRole = Role::firstOrCreate(['name' => 'editor'], ['display_name' => 'Executive Editor', 'description' => 'Reviews, schedules, approves, and polishes critical editorial monographs.']);
        $authorRole = Role::firstOrCreate(['name' => 'author'], ['display_name' => 'Staff Author & Critic', 'description' => 'Composes and drafts chronicles, essays, and critiques.']);
        $contributorRole = Role::firstOrCreate(['name' => 'contributor'], ['display_name' => 'Guest Contributor', 'description' => 'Submits manuscripts for atelier review.']);

        $permissions = [
            'view_posts' => 'content',
            'create_posts' => 'content',
            'edit_posts' => 'content',
            'delete_posts' => 'content',
            'publish_posts' => 'content',
            'view_pages' => 'pages',
            'create_pages' => 'pages',
            'edit_pages' => 'pages',
            'delete_pages' => 'pages',
            'manage_categories' => 'taxonomies',
            'manage_tags' => 'taxonomies',
            'manage_media' => 'media',
            'manage_comments' => 'discourse',
            'manage_seo' => 'seo',
            'manage_redirects' => 'seo',
            'manage_menus' => 'layout',
            'manage_widgets' => 'layout',
            'manage_settings' => 'system',
            'manage_users' => 'system',
            'manage_backups' => 'system',
        ];

        foreach ($permissions as $name => $group) {
            $perm = Permission::firstOrCreate(['name' => $name], ['group' => $group]);
            $adminRole->givePermissionTo($perm);
            if (str_contains($name, 'post') || str_contains($name, 'categor') || str_contains($name, 'media')) {
                $editorRole->givePermissionTo($perm);
            }
        }

        $admin->assignRole('super_admin');
        $admin->assignRole('admin');

        // 13. Dynamic Navigation Menus
        $headerMenu = Menu::firstOrCreate(['location' => 'header'], ['name' => 'Primary Masthead Menu', 'is_active' => true]);
        $headerItems = [
            ['title' => 'Chronicles', 'url' => '/', 'order' => 1],
            ['title' => 'Haute Couture', 'url' => '/category/haute-couture', 'order' => 2],
            ['title' => 'Horology & Timepieces', 'url' => '/category/horology-timepieces', 'order' => 3],
            ['title' => 'Artisan Savoir-Faire', 'url' => '/category/artisan-savoir-faire', 'order' => 4],
            ['title' => 'Architectural Living', 'url' => '/category/architectural-living', 'order' => 5],
            ['title' => 'About Atelier', 'url' => '/page/about-us', 'order' => 6],
            ['title' => 'Salon Desk', 'url' => '/contact', 'order' => 7],
        ];
        foreach ($headerItems as $item) {
            MenuItem::firstOrCreate(['menu_id' => $headerMenu->id, 'url' => $item['url']], $item);
        }

        $footerMenu = Menu::firstOrCreate(['location' => 'footer'], ['name' => 'Footer Directory', 'is_active' => true]);
        $footerItems = [
            ['title' => 'About Maison XIVIG', 'url' => '/page/about-us', 'order' => 1],
            ['title' => 'Editorial Salon Desk', 'url' => '/contact', 'order' => 2],
            ['title' => 'Bespoke Services', 'url' => '/services', 'order' => 3],
            ['title' => 'Editorial Sitemap', 'url' => '/sitemap', 'order' => 4],
        ];
        foreach ($footerItems as $fitem) {
            MenuItem::updateOrCreate(['menu_id' => $footerMenu->id, 'url' => $fitem['url']], $fitem);
        }
        // Remove legacy XML and RSS syndication links from footer menu
        MenuItem::where('menu_id', $footerMenu->id)
            ->whereIn('url', ['/sitemap.xml', '/feed.xml', '/rss.xml', '/feed'])
            ->delete();

        // 14. Dynamic Widgets
        Widget::firstOrCreate(
            ['name' => 'Trending In Atelier Sidebar'],
            [
                'type' => 'trending',
                'area' => 'sidebar',
                'title' => 'Trending Chronicles',
                'order' => 1,
                'is_active' => true,
            ]
        );

        Widget::firstOrCreate(
            ['name' => 'Maison VIP Newsletter Sidebar'],
            [
                'type' => 'newsletter',
                'area' => 'sidebar',
                'title' => 'VIP Atelier Dispatch',
                'order' => 2,
                'is_active' => true,
            ]
        );

        Widget::firstOrCreate(
            ['name' => 'Curated Categories Index'],
            [
                'type' => 'categories',
                'area' => 'sidebar',
                'title' => 'Maison Collections',
                'order' => 3,
                'is_active' => true,
            ]
        );

        // 15. Social Links
        $socials = [
            ['platform' => 'instagram', 'url' => 'https://instagram.com', 'icon' => 'bi-instagram', 'order' => 1],
            ['platform' => 'twitter', 'url' => 'https://x.com', 'icon' => 'bi-twitter-x', 'order' => 2],
            ['platform' => 'linkedin', 'url' => 'https://linkedin.com', 'icon' => 'bi-linkedin', 'order' => 3],
            ['platform' => 'pinterest', 'url' => 'https://pinterest.com', 'icon' => 'bi-pinterest', 'order' => 4],
        ];
        foreach ($socials as $soc) {
            SocialLink::firstOrCreate(['platform' => $soc['platform']], $soc);
        }

        // 16. FAQs for Schema.org
        $faqs = [
            [
                'question' => 'What is the editorial philosophy of Maison XIVIG Gazette?',
                'answer' => 'Maison XIVIG Gazette serves as an independent critical journal documenting haute couture craftsmanship, precision horology, and architectural aesthetics with unhurried rigor.',
                'order' => 1,
            ],
            [
                'question' => 'How frequently are new chronicles and monographs released?',
                'answer' => 'Curated dispatches are published weekly following meticulous atelier verification and fact-checking by our senior horologists and fashion critics.',
                'order' => 2,
            ],
            [
                'question' => 'Can patrons subscribe to private salon viewings or print editions?',
                'answer' => 'Yes. Patrons may join the Maison Circle via our VIP Newsletter or submit inquiries directly to our Paris Salon Concierge Desk.',
                'order' => 3,
            ],
        ];
        foreach ($faqs as $faq) {
            FAQ::firstOrCreate(['question' => $faq['question']], $faq);
        }

        // 17. Seed Initial Editorial Dispatches / Emails
        if (AdminEmail::count() === 0) {
            AdminEmail::create([
                'sender_name'     => 'Éléonore de Saint-Germain',
                'sender_email'    => 'eleonore@xivig.com',
                'recipient_email' => 'admin@xivig.com',
                'recipient_name'  => 'Maison Administrator',
                'subject'         => 'Atelier Dispatch: Spring 2027 Couture Textile Selection',
                'body'            => "<p>Dear Executive Editor,</p><p>We have finalized the swatch portfolio for our upcoming feature on double-faced cashmere and Florentine silk radzimir. The photographic plates have been captured in the high atelier on Rue Cambon.</p><p>Kindly review the attached proofs when convenient.</p><p>Warmest regards,<br><strong>Éléonore de Saint-Germain</strong></p>",
                'folder'          => 'inbox',
                'is_read'         => false,
                'is_starred'      => true,
            ]);

            AdminEmail::create([
                'sender_name'     => 'Marcello Vieri',
                'sender_email'    => 'vieri@genevawatches.ch',
                'recipient_email' => 'admin@xivig.com',
                'recipient_name'  => 'Maison Administrator',
                'subject'         => 'Private Viewing Invitation: Vallée de Joux Horological Grand Complication',
                'body'            => "<p>Distinguished Editors,</p><p>The master watchmakers at Vallée de Joux cordially invite the Maison Gazette for an exclusive embargoed preview of our deadbeat seconds tourbillon before SIHH.</p><p>We can accommodate your horology photography unit next Thursday.</p>",
                'folder'          => 'inbox',
                'is_read'         => true,
                'is_starred'      => false,
            ]);
        }

        // 18. Seed Initial Live Salon Chat Messages
        if (ChatMessage::count() === 0) {
            ChatMessage::create([
                'sender_id'   => $admin->id,
                'channel'     => 'editorial_salon',
                'message'     => "Good morning team. Welcome to the Maison Gazette Editorial Salon. Today's priority is curating the haute couture draping monograph and finalizing watch reviews.",
                'is_read'     => true,
                'created_at'  => now()->subHours(3),
            ]);

            ChatMessage::create([
                'sender_id'   => $admin->id,
                'channel'     => 'haute_couture',
                'message'     => 'Textile plates from Milan have arrived and are awaiting colour grading.',
                'is_read'     => true,
                'created_at'  => now()->subHours(2),
            ]);
        }

        // 19. Seed Initial Activity Logs
        if (ActivityLog::count() === 0) {
            ActivityLog::create([
                'user_id'     => $admin->id,
                'action'      => 'login',
                'description' => 'User Maison Administrator authenticated into administration suite',
                'ip_address'  => '127.0.0.1',
                'created_at'  => now()->subHours(4),
            ]);

            ActivityLog::create([
                'user_id'     => $admin->id,
                'action'      => 'updated',
                'description' => 'Updated atelier & system settings',
                'ip_address'  => '127.0.0.1',
                'created_at'  => now()->subHours(2),
            ]);
        }
    }
}
