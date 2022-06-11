<?php

namespace App\Conversations;

use App\Models\Questionnaire;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Support\Facades\Storage;

class RegistrationConversation extends Conversation
{
    protected $fullName;
    protected $email;
    protected $mobilePhone;
    protected $campus;
    protected $apartment;
    protected $parking;
    protected $images;
    protected $telegram_id;

    public function run()
    {
        $this->say(__('botman.register.welcome'));
        $this->askForFullName();
    }

    public function askForFullName() {
        $this->bot->typesAndWaits(1);
        $this->ask(__('botman.register.full_name.question'), function(Answer $answer) {
            $regular = '/^[А-ЯЁ][а-яё]*([-][А-ЯЁ][а-яё]*)?\s[А-ЯЁ][а-яё]*\s[А-ЯЁ][а-яё]*$/mu';
            if (preg_match($regular, $answer->getText())) {
                $this->fullName = $answer->getText();
                $this->say('✏' . $this->fullName, ['reply_markup' => json_encode(['remove_keyboard' => true])]);
                $this->askForEmail();
            } else {
                $this->say(__('botman.register.full_name.error'));
                $this->askForFullName();
            }
        });
    }

    public function askForEmail() {
        $this->bot->typesAndWaits(2);
        $this->ask(__('botman.register.email.question'), function(Answer $answer) {
            $regular = '/^((([0-9A-Za-z]{1}[-0-9A-z\.]{1,}[0-9A-Za-z]{1})|([0-9А-Яа-я]{1}[-0-9А-я\.]{1,}[0-9А-Яа-я]{1}))@([-A-Za-z]{1,}\.){1,2}[-A-Za-z]{2,})$/u';
            if (preg_match($regular, $answer->getText())) {
                $this->email = $answer->getText();
                $this->say('✏' . $this->email, ['reply_markup' => json_encode(['remove_keyboard' => true])]);
                $this->askForMobilePhone();
            } else {
                $this->say(__('botman.register.email.error'));
                $this->askForEmail();
            }
        });
    }

    public function askForMobilePhone() {
        $this->bot->typesAndWaits(1);
        $keyboard = [[['text' => __('botman.register.mobile_phone.default_answer'), 'request_contact' => true]]];
        $this->ask(__('botman.register.mobile_phone.question'), function(Answer $answer) {
            $regular = '/^(\+)?(\(\d{2,3}\) ?\d|\d)(([ \-]?\d)|( ?\(\d{2,3}\) ?)){5,12}\d$/';
            if ($answer->getText() === "%%%_CONTACT_%%%") {
                $this->mobilePhone = $answer->getMessage()->getPayload()['contact']['phone_number'];
                $this->say('✏' . $this->mobilePhone, ['reply_markup' => json_encode(['remove_keyboard' => true])]);
                $this->askForCampus();
            } elseif(preg_match($regular, $answer->getText())) {
                $this->mobilePhone = $answer->getText();
                $this->say('✏' . $this->mobilePhone, ['reply_markup' => json_encode(['remove_keyboard' => true])]);
                $this->askForCampus();
            } else {
                $this->say(__('botman.register.mobile_phone.error'));
                $this->askForMobilePhone();
            }
        },['reply_markup' => json_encode(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => true])]);
    }

    public function askForCampus(){
        $this->bot->typesAndWaits(1);
        $question = Question::create(__('botman.register.campus.question'))
            ->fallback('Unable to create a new database')
            ->callbackId('create_database')
            ->addButtons([
                Button::create(__('botman.register.campus.data.q1'))->value(__('botman.register.campus.data.q1')),
                Button::create(__('botman.register.campus.data.q2'))->value(__('botman.register.campus.data.q2')),
                Button::create(__('botman.register.campus.data.q3'))->value(__('botman.register.campus.data.q3')),
                Button::create(__('botman.register.campus.data.q4'))->value(__('botman.register.campus.data.q4')),
                Button::create(__('botman.register.campus.data.q5'))->value(__('botman.register.campus.data.q5')),
            ]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->campus = $answer->getText();
                $this->say('✏' . $this->campus);
                $this->askForApartment();
            } else {
                $this->askForCampus();
            }
        }, ['reply_markup' => json_encode(['remove_keyboard' => true])]);
    }

    public function askForApartment() {
        $this->bot->typesAndWaits(1);
        $keyboard = [[__('botman.register.apartment.default_answer')]];
        $this->ask(__('botman.register.apartment.question'), function (Answer $answer) {
            $this->apartment = $answer->getText();
            $this->askForParking();
        }, ['reply_markup' => json_encode(['keyboard' => $keyboard, 'one_time_keyboard' => true, 'resize_keyboard' => true])]);
    }

    public function askForParking() {
        $this->bot->typesAndWaits(1);
        $keyboard = [[__('botman.register.parking.default_answer')]];
        $this->ask(__('botman.register.parking.question'), function (Answer $answer) {
            $this->parking = $answer->getText();
            $this->askForDocumentsConfirmingOwnership();
        }, ['reply_markup' => json_encode(['keyboard' => $keyboard, 'one_time_keyboard' => true, 'resize_keyboard' => true])]);
    }

    public function askForDocumentsConfirmingOwnership() {
        $this->bot->typesAndWaits(1);
        $keyboard = [[__('botman.register.document.default_answer')]];

        $this->askForImages(__('botman.register.document.question'), function ($images) {
            foreach ($images as $image) {
                $url = $image->getUrl();
                $contents = file_get_contents($url);
                $name = uniqid() . '_' . substr($url, strrpos($url, '/') + 1);
                Storage::disk('public')->put($name, $contents);
                $this->images[] = $name;
            }
            $this->askConfirm();
        }, function(Answer $answer) {
            if ($answer->getText() === __('botman.register.document.default_answer')) {
                $this->askConfirm();
            } else {
                $this->say('Ошибка, необходимо загрузить фото');
                $this->askForDocumentsConfirmingOwnership();
            }
        }, ['reply_markup' => json_encode(['keyboard' => $keyboard, 'one_time_keyboard' => true, 'resize_keyboard' => true])]);
    }

    public function askConfirm() {
        $this->bot->typesAndWaits(1);
        $text = '😊 ' . $this->fullName . PHP_EOL .
            '☎️ ' . $this->mobilePhone . PHP_EOL .
            '📧 ' . $this->email . PHP_EOL .
            '🏢 ' . $this->campus . PHP_EOL .
            '🏠  ' . $this->apartment . PHP_EOL .
            '🚘 ' . $this->parking;
        $question = Question::create($text)
            ->fallback('Unable to create a new database')
            ->callbackId('confirm_form')
            ->addButtons([
                Button::create('Отправить анкету')->value('1'),
                Button::create('Заполнить повторно')->value('2'),
            ]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getText() == 1) {

                    $this->say('Правовая информация:', ['reply_markup' => json_encode(['inline_keyboard' => [
                        [['text' => 'Политика конфиденциальности', 'url' => 'https://www.serdtse.su/privacy'],
                            ['text' => 'Обработка персональных данных', 'url' => 'https://www.serdtse.su/agreement']]
                    ], 'one_time_keyboard' => true, 'resize_keyboard' => true])]);

                    $this->askPrivacy();
                } elseif($answer->getText() == 2) {
                    $this->askForFullName();
                } else {
                    $this->askConfirm();
                }
            } else {
                $this->askConfirm();
            }
        }, ['reply_markup' => json_encode(['remove_keyboard' => true])]);
    }

    public function askPrivacy()
    {
        $this->bot->typesAndWaits(1);
        $text = 'Я ознакомлен/ознакомлена с Политикой конфиденциальности и согласен/согласна на обработку персональных данных';
        $questionPrivacy = Question::create($text)
            ->fallback('Unable to create a new database')
            ->callbackId('confirm_form')
            ->addButtons([
                Button::create('Подтверждаю')->value('1'),
                Button::create('Не подтверждаю')->value('2'),
            ]);

        $this->ask($questionPrivacy, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getText() == 1) {
                    $this->end();
                } elseif($answer->getText() == 2) {
                    $this->say('Благодарим Вас за интерес к Инициативной Группе. Создание заявки на добавление в закрытый чат Соседи остановлено. Внесённая информация удалена');
                    $this->say(__('botman.register.welcome'));
                } else {
                    $this->askPrivacy();
                }
            } else {
                $this->askPrivacy();
            }
        }, ['reply_markup' => json_encode(['remove_keyboard' => true])]);
    }

    public function end() {
        $this->bot->typesAndWaits(1);
        $user = $this->bot->getUser();
        $this->telegram_id = $user->getId();

        $questionnaire = Questionnaire::create([
            'full_name' => $this->fullName,
            'email' => $this->email,
            'mobile_phone' => $this->mobilePhone,
            'campus' => $this->campus,
            'apartment' => $this->apartment,
            'parking' => $this->parking,
            'images' => $this->images,
            'telegram_id' => $this->telegram_id,
        ]);

        $this->say(__('botman.register.end.answer'), ['reply_markup' => json_encode(['remove_keyboard' => true])]);
    }
}
