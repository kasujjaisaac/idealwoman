<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DesignAndMetadataTest extends TestCase
{
    public static function publicPages(): array
    {
        return [['/'], ['/about'], ['/our-work'], ['/stories'], ['/get-involved'], ['/contact'], ['/stories/students-championing-peace'], ['/stories/learning-through-making']];
    }

    #[DataProvider('publicPages')]
    public function test_public_pages_have_one_heading_and_complete_share_metadata(string $path): void
    {
        $response = $this->get($path)->assertOk();
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML($response->getContent());
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $xpath = new DOMXPath($document);

        $this->assertSame(1, $xpath->query('//h1')->length);
        foreach (['description', 'twitter:title', 'twitter:description', 'twitter:image', 'twitter:image:alt'] as $name) {
            $this->assertNotSame('', $xpath->evaluate('string(//meta[@name="'.$name.'"]/@content)'), $path.' '.$name);
        }
        $this->assertSame(
            $xpath->evaluate('string(//meta[@property="og:image"]/@content)'),
            $xpath->evaluate('string(//meta[@name="twitter:image"]/@content)'),
        );
        $imagePath = parse_url($xpath->evaluate('string(//meta[@property="og:image"]/@content)'), PHP_URL_PATH);
        $this->assertFileExists(public_path(ltrim($imagePath, '/')));

        foreach ($xpath->query('//img') as $image) {
            $this->assertTrue($image->hasAttribute('alt'), $path.' image lacks alt attribute');
            $this->assertGreaterThan(0, (int) $image->getAttribute('width'));
            $this->assertGreaterThan(0, (int) $image->getAttribute('height'));
            $sourcePath = parse_url($image->getAttribute('src'), PHP_URL_PATH);
            if (str_starts_with($sourcePath, '/images/')) {
                $this->assertFileExists(public_path(ltrim($sourcePath, '/')));
            }
        }
    }

    public function test_story_breadcrumbs_connect_home_stories_and_the_current_article(): void
    {
        $response = $this->get('/stories/learning-through-making?utm_source=test')->assertOk();
        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $response->getContent(), $script);
        $graph = json_decode($script[1], true, flags: JSON_THROW_ON_ERROR)['@graph'];
        $types = array_column($graph, '@type');
        $this->assertContains('WebSite', $types);
        $breadcrumbs = $graph[array_search('BreadcrumbList', $types)]['itemListElement'];
        $this->assertSame(['Home', 'Stories', 'Learning through making'], array_column($breadcrumbs, 'name'));
        $this->assertSame([1, 2, 3], array_column($breadcrumbs, 'position'));
        $this->assertStringEndsWith('/stories/learning-through-making', $breadcrumbs[2]['item']);
        $this->assertStringNotContainsString('utm_source', $breadcrumbs[2]['item']);
    }

    public function test_home_uses_the_activity_photo_and_only_linked_social_profiles(): void
    {
        config(['idealwoman.social_links.linkedin' => null, 'idealwoman.social_links.tiktok' => '']);

        $this->get('/')->assertOk()->assertSee('images/together.jpg')
            ->assertSee('Pause slideshow')->assertDontSee('profile link coming soon')
            ->assertDontSee('class="card-number"', false);
    }

    public function test_sitemap_exposes_article_images_as_valid_xml(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk();
        $document = new DOMDocument;
        $this->assertTrue($document->loadXML($response->getContent()));
        $this->assertSame(2, $document->getElementsByTagNameNS('http://www.google.com/schemas/sitemap-image/1.1', 'image')->length);
    }
}
