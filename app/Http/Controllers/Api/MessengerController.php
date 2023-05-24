<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\sendMessageToChannelRequest;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Attachments\Image as TgImage;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MessengerController extends Controller
{
    public function sendMessageToChannel(sendMessageToChannelRequest $request)
    {
        $extension = explode('/', mime_content_type($request->input('photo')))[1];
        $png_url = "product-".time().".".$extension;
        $path = '/send-files/' . $png_url;

        $file = base64_decode(preg_replace('#^data:image/\w+;base64,#i','',$request->input('photo')));

        Storage::disk('public')->put($path, $file);

        $botman = resolve('botman');

        $attachment = new TgImage(Storage::disk('public')->url($path), [
            'custom_payload' => true,
        ]);

        $message = OutgoingMessage::create('123123123')->withAttachment($attachment);

        $botman->say($message, '168048474',
            \BotMan\Drivers\Telegram\TelegramDriver::class);
    }
}
