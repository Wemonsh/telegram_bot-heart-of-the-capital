<?php

namespace App\Orchid\Screens;

use App\Models\Questionnaire;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\LaravelCache;
use BotMan\BotMan\Drivers\DriverManager;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class QuestionnaireShowScreen extends Screen
{
    public $questionnaire;

    /**
     * Query data.
     *
     * @return array
     */
    public function query(Questionnaire $questionnaire): iterable
    {
        return [
            'questionnaire' => $questionnaire
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Анкета';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Отправить приглашение')
                ->icon('paper-plane')
                ->method('send')
                ->canSee($this->questionnaire->exists),
        ];
    }

    public function send(Questionnaire $questionnaire)
    {
        if (in_array($questionnaire->getAttribute('campus'), ['34к2, Здание 1', '34к2, Здание 2', '34к2, Здание 3'])) {
            $botman = resolve('botman');
            $botman->say(url('/redirect' . '?type=1&id=' . $questionnaire->id), $questionnaire->getAttribute('telegram_id'), \BotMan\Drivers\Telegram\TelegramDriver::class);

            $questionnaire->status = 1;
            $questionnaire->save();

            Alert::info('Приглашение отправлено');
        } elseif (in_array($questionnaire->getAttribute('campus'), ['34к1, Здание 4', '34к1, Здание 5(АП)'])) {
            $botman = resolve('botman');
            $botman->say(url('/redirect' . '?type=2&id=' . $questionnaire->id), $questionnaire->getAttribute('telegram_id'), \BotMan\Drivers\Telegram\TelegramDriver::class);


            $questionnaire->status = 1;
            $questionnaire->save();

            Alert::info('Приглашение отправлено');
        } else {
            Alert::error('Ошибка некоректный кампус');
        }
        return redirect()->route('platform.questionnaire.list');
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::legend('questionnaire', [
                Sight::make('full_name', 'ФИО'),
                Sight::make('email', 'Email'),
                Sight::make('mobile_phone', 'Телефон'),
                Sight::make('campus', 'Корпус'),
                Sight::make('apartment', 'Квартира'),
                Sight::make('parking', 'Парковочное место'),
                Sight::make('created_at', 'Дата создания'),
                Sight::make('images', 'Вложения')->render(function ($questionnaire){
                    $html = '';
                    if ($questionnaire->images !== null) {
                        foreach ($questionnaire->images as $image){
                            $html.= "<img width='200px' src='/storage/" . $image . "'>";
                        }
                        return $html;
                    }
                    return 'Нет вложений';
                }),
                Sight::make('status', 'Статус')->render(function ($questionnaire){
                    switch ($questionnaire->status) {
                        case 0:
                        return 'Заявка создана';
                            break;
                        case 1:
                        return 'Отправлено приглашение';
                            break;
                        case 2:
                        return 'Приглашен';
                            break;
                    }
                    return 'Ошибка';
                }),
            ]),
        ];
    }
}
