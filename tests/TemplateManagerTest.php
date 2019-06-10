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
        $this->quote = new Quote($this->faker->randomNumber(), $this->faker->randomNumber(), $this->faker->randomNumber(), $this->faker->date());
    }

    /**
     * Closes the mocks
     */
    public function tearDown(): void
    {
    }

    /**
     * @test
     */
    public function test()
    {
        $expectedDestination = DestinationRepository::getInstance()->getById($this->faker->randomNumber());
        $expectedUser = ApplicationContext::getInstance()->getCurrentUser();

        $template = new Template(
            1,
            'Votre voyage avec une agence locale [quote:destination_name]',
            "
Bonjour [user:first_name],

Merci d'avoir contacté un agent local pour votre voyage [quote:destination_name].

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

        $this->assertEquals('Votre voyage avec une agence locale ' . $expectedDestination->countryName, $message->getSubject());
        $this->assertEquals("
Bonjour " . $expectedUser->getFirstname() . ",

Merci d'avoir contacté un agent local pour votre voyage " . $expectedDestination->countryName . ".

Bien cordialement,

L'équipe Evaneos.com
www.evaneos.com
", $message->getContent());
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
}
