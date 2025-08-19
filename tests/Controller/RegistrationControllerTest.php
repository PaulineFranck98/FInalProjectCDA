<?php

// namespace App\Tests\Controller;

// use App\Tests\TestTrait;
// use Symfony\Bundle\FrameworkBundle\KernelBrowser;
// use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

// class RegistrationControllerTest extends WebTestCase
// {
//     use TestTrait;

//     public function testGetRequestToRegistrationPageReturnsSuccessfulResponse(): void 
//     {
//         $this->clientGoesOnRegisterPage();

//         $this->assertResponseIsSuccessful();

//         $this->assertSelectorTextContains('h1','Créer un compte utilisateur');
//     }

    // public function testSpamBotsAreNotWelcome(): void
    // {
    //     $client = $this->clientGoesOnRegisterPage();


    //     $client->submitForm(
    //         "S'inscrire",
    //         [
    //             'registration_form[email]' => 'test@example.com',
    //             'registration_form[plainPassword][first]' => 'badpassword',
    //             'registration_form[plainPassword][second]' => 'badpassword',
    //             'registration_form[agreeTerms]' => true,
    //             'registration_form[phone]'=> "falsenumber",
    //             'registration_form[faxNumber]'=> "falsefaxnumber",
    //         ]
    //     );

        // debug
        // dump($client->getResponse()->headers->all());
        // dump($client->getResponse()->getStatusCode());


    //     $this->assertResponseStatusCodeSame(403, "Go away Dirty bot !");

    //     $this->assertRouteSame('app_register');
    // }

    // public function testMustBeRedirectedToTheLoginPageIfTheFormIsValid(): void 
    // {
    //     $client = $this->clientGoesOnRegisterPage();
    //     $this->truncateTableBeforeTest('user');

    //     $client->submitForm(
    //         "S'inscrire",
    //         [
    //             'registration_form[email]' => 'test@example.com',
    //             'registration_form[password][first]' => 'badpassword',
    //             'registration_form[password][second]' => 'badpassword',
    //             'registration_form[agreeTerms]' => true,
    //         ]
    //     );

    //     $this->assertResponseIsSuccessful();

    //     $this->assertRouteSame('app_login');
    // }

    // private function clientGoesOnRegisterPage(): KernelBrowser
    // {
    //     $client = $this->createClientAndFollowRedirects();

    //     $client->request('GET', '/register');

    //     return $client;
    // }
// }
