<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setup;

class SetupSeeder extends Seeder
{
    public function run(): void
    {
        if (Setup::count() === 0) {
            $setups = [
                [
                    'seo_header' => 
                    '   <!-- Basic SEO -->
                        <title>Jomfun – Latest News & Updates</title>
                        <meta name="description" content="Stay updated with Jomfun. Discover the latest news, updates, and stories about Jomfun and related topics.">
                        <meta name="keywords" content="Jomfun, news, updates, articles, lifestyle, technology">
                        <meta name="robots" content="index, follow">
                        <meta name="author" content="Jomfun">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <meta charset="UTF-8">

                        <!-- Canonical URL -->
                        <link rel="canonical" href="https://jomfun.com">

                        <!-- Open Graph / Facebook -->
                        <meta property="og:title" content="Jomfun – Latest News & Updates">
                        <meta property="og:description" content="Read the latest news, stories, and updates from Jomfun. Stay informed and entertained.">
                        <meta property="og:type" content="website">
                        <meta property="og:url" content="https://jomfun.com">
                        <meta property="og:image" content="https://jomfun.com/images/jomfun-banner.jpg">
                        <meta property="og:site_name" content="Jomfun">

                        <!-- Twitter Card -->
                        <meta name="twitter:card" content="summary_large_image">
                        <meta name="twitter:title" content="Jomfun – Latest News & Updates">
                        <meta name="twitter:description" content="Stay updated with Jomfun. Discover articles, insights, and the latest news.">
                        <meta name="twitter:image" content="https://jomfun.com/images/jomfun-banner.jpg">
                        <meta name="twitter:site" content="@Jomfun">

                        <!-- Schema.org / JSON-LD for structured data -->
                        <script type="application/ld+json">
                            {
                            "@context": "https://schema.org",
                            "@type": "WebPage",
                            "name": "Jomfun – Latest News & Updates",
                            "url": "https://jomfun.com",
                            "description": "Stay updated with Jomfun. Discover the latest news, updates, and stories about Jomfun and related topics.",
                            "publisher": {
                                "@type": "Organization",
                                "name": "Jomfun",
                                "logo": {
                                "@type": "ImageObject",
                                "url": "https://jomfun.com/images/logo.png"
                                }
                            }
                            }
                        </script>
                    '
                ],
            ];

            foreach ($setups as $setup) {
                Setup::create($setup);
            }
        }
    }
}