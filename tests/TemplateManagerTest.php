<?php

require_once __DIR__ . '/../src/Entity/Destination.php';
require_once __DIR__ . '/../src/Entity/Quote.php';
require_once __DIR__ . '/../src/Entity/Site.php';
require_once __DIR__ . '/../src/Entity/Template.php';
require_once __DIR__ . '/../src/Entity/User.php';
require_once __DIR__ . '/../src/Helper/SingletonTrait.php';
require_once __DIR__ . '/../src/Context/ApplicationContext.php';
require_once __DIR__ . '/../src/Repository/Repository.php';
require_once __DIR__ . '/../src/Repository/DestinationRepository.php';
require_once __DIR__ . '/../src/Repository/QuoteRepository.php';
require_once __DIR__ . '/../src/Repository/SiteRepository.php';
require_once __DIR__ . '/../src/TemplateManager.php';
require_once __DIR__ . '/../src/TemplateBuilder.php';
require_once __DIR__ . '/../tests/TypeTestCase.php';

class TemplateManagerTest extends \App\Tests\TypeTestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var Quote
     */
    protected $quote;


    /**
     * Init the mocks
     */
    public function setUp(): void
    {
        $this->faker = \Faker\Factory::create();
        $this->quote = new Quote($this->faker->randomNumber(), $this->faker->randomNumber(), $this->faker->randomNumber(), $this->faker->dateTime);
    }

    /**
     * Closes the mocks
     */
    public function tearDown(): void
    {
    }

    public function testGetTemplateComputed()
    {
        $expectedDestination = DestinationRepository::getInstance()->getById($this->faker->randomNumber());
        $expectedUser = ApplicationContext::getInstance()->getCurrentUser();
        $expectedSite = SiteRepository::getInstance()->getById($this->quote->getSiteId());

        $template = new Template(
            1,
            'Votre voyage avec une agence locale [quote:destination_name]',
            "
Bonjour [user:first_name],

Merci d'avoir contacté un agent local pour votre voyage [quote:destination_name].

Le lien vers votre destination est: [quote:destination_link].

Le résumé html est: [quote:summary_html].
Le résumé non html est: [quote:summary].

Bien cordialement,

L'équipe Evaneos.com
www.evaneos.com
");
        $templateManager = new TemplateManager();

        $message = $templateManager->getTemplateComputed(
            $template,
            [
                'quote' => $this->quote
            ]
        );

        $this->assertEquals('Votre voyage avec une agence locale ' . $expectedDestination->getCountryName(), $message->getSubject());
        $this->assertEquals(
            sprintf("
Bonjour %s,

Merci d'avoir contacté un agent local pour votre voyage %s.

Le lien vers votre destination est: %s.

Le résumé html est: %s.
Le résumé non html est: %s.

Bien cordialement,

L'équipe Evaneos.com
www.evaneos.com
",
                $expectedUser->getFirstname(),
                $expectedDestination->getCountryName(),
                $expectedSite->getUrl() . '/' . $expectedDestination->getCountryName() . '/quote/' . $this->quote->getId(),
                sprintf('<p>%d</p>', $this->quote->getId()),
                $this->quote->getId()
            ),
            $message->getContent()
        );
    }

    /**
     * @param Quote|null $expected
     * @param array $data
     *
     * @dataProvider getQuoteProvider
     */
    public function testGetQuote(?Quote $expected, array $data)
    {
        $templateManager = new TemplateManager();

        $this->assertEquals($expected, $this->getResultFromMethod($templateManager, 'getQuote', [$data]));
    }

    /**
     * @return array
     */
    public function getQuoteProvider()
    {
        return [
            [null, []],
            [$this->quote, ['quote' => $this->quote]],
        ];
    }

    /**
     * @param User $expected
     * @param array $data
     *
     * @dataProvider getUserProvider
     */
    public function testGetUser(User $expected, array $data)
    {
        $templateManager = new TemplateManager();

        $this->assertEquals($expected, $this->getResultFromMethod($templateManager, 'getUser', [$data]));
    }

    /**
     * @return array
     */
    public function getUserProvider()
    {
        $user = new User(1, 'firstName', 'lastName', 'email');
        $userApplication = ApplicationContext::getInstance()->getCurrentUser();

        return [
            [$user, ['user' => $user]],
            [$userApplication, ['user' => null]],
            [$userApplication, []],
        ];
    }

    /**
     * @param string $expected
     * @param string $text
     *
     * @dataProvider hydrateTextWithUserProvider
     */
    public function testHydrateTextWithUser(string $expected, string $text)
    {
        $templateManager = new TemplateManager();
        $user = new User(1, 'firstName', 'lastName', 'email');

        $this->getResultFromMethod($templateManager, 'hydrateTextWithUser', [&$text, $user]);
        $this->assertStringContainsString($expected, $text);
    }

    /**
     * @return array
     */
    public function hydrateTextWithUserProvider()
    {
        return [
            ['Firstname', '[user:first_name]'],
            ['[user:firstname]', '[user:firstname]'],
        ];
    }

    /**
     * @param string $expected
     * @param string $text
     *
     * @dataProvider fillSummaryHtmlProvider
     */
    public function testFillSummaryHtml(string $expected, string $text)
    {
        $templateManager = new TemplateManager();
        $quote = new Quote(1, 2, 3, new \DateTime());

        $this->getResultFromMethod($templateManager, 'fillSummaryHtml', [&$text, $quote]);
        $this->assertStringContainsString($expected, $text);
    }

    /**
     * @return array
     */
    public function fillSummaryHtmlProvider()
    {
        return [
            ['<p>1</p>', '[quote:summary_html]'],
            ['[quote:summaryhtml]', '[quote:summaryhtml]'],
        ];
    }

    /**
     * @param string $expected
     * @param string $text
     *
     * @dataProvider fillSummaryProvider
     */
    public function testFillSummary(string $expected, string $text)
    {
        $templateManager = new TemplateManager();
        $quote = new Quote(1, 2, 3, new \DateTime());

        $this->getResultFromMethod($templateManager, 'fillSummary', [&$text, $quote]);
        $this->assertStringContainsString($expected, $text);
    }

    /**
     * @return array
     */
    public function fillSummaryProvider()
    {
        return [
            ['1', '[quote:summary]'],
            ['[quote:sum]', '[quote:sum]'],
        ];
    }

    /**
     * @param string $expected
     * @param string $text
     *
     * @dataProvider fillDestinationNameProvider
     */
    public function testFillDestinationName(string $expected, string $text)
    {
        $templateManager = new TemplateManager();
        $destination = new Destination(1, 'countryName', 'conjunction', 'computerName');

        $this->getResultFromMethod($templateManager, 'fillDestinationName', [&$text, $destination]);
        $this->assertStringContainsString($expected, $text);
    }

    /**
     * @return array
     */
    public function fillDestinationNameProvider()
    {
        return [
            ['countryName', '[quote:destination_name]'],
            ['[quote:destination]', '[quote:destination]'],
        ];
    }

    /**
     * @param string $expected
     * @param string $text
     *
     * @dataProvider fillDestinationLinkProvider
     */
    public function testFillDestinationLink(string $expected, string $text)
    {
        $templateManager = new TemplateManager();
        $site = new Site(1, 'url');
        $destination = new Destination(1, 'countryName', 'conjunction', 'computerName');
        $quote = new Quote(1, 2, 3, new \DateTime());

        $this->getResultFromMethod($templateManager, 'fillDestinationLink', [&$text, $site, $destination, $quote]);
        $this->assertStringContainsString($expected, $text);
    }

    /**
     * @return array
     */
    public function fillDestinationLinkProvider()
    {
        return [
            ['url/countryName/quote/1', '[quote:destination_link]'],
            ['[quote:destination]', '[quote:destination]'],
        ];
    }
}
