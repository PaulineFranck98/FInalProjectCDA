<?php

namespace App\Tests\Controller;

use App\Tests\TestTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    use TestTrait;

    public function testGetRequestToRegistrationPageReturnsSuccessfulResponse(): void 
    {
        $this->clientGoesOnRegisterPage();

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h1','');
    }

    public function testSpamBotsAreNotWelcome(): void
    {
        $client = $this->clientGoesOnRegisterPage();

        
    }
}
