<?php

namespace App\Http\Controllers;

use App\Conversations\RegistrationConversation;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\LaravelCache;
use BotMan\BotMan\Drivers\DriverManager;

class BotManController extends Controller
{
    public function __invoke()
    {
        $config = [
            // Your driver-specific configuration
            "telegram" => [
                "token" => "5375693593:AAFG-XEAJXm3Uqy0Y0DI_Gau3LG92rbkKSY"
            ]
        ];

        // Load the driver(s) you want to use
        DriverManager::loadDriver(\BotMan\Drivers\Telegram\TelegramDriver::class);

        // Create an instance
        $botman = BotManFactory::create($config, new LaravelCache());

        $botman->hears('/register', function (BotMan $bot) {
            $bot->startConversation(new RegistrationConversation);
        });

        // Give the bot something to listen for.
        $botman->hears('hello', function (BotMan $bot) {
            $bot->reply('Hello yourself.');
        });

        // Start listening
        $botman->listen();
    }
}
