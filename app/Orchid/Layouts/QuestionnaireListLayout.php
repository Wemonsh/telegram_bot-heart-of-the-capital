<?php

namespace App\Orchid\Layouts;

use App\Models\Questionnaire;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class QuestionnaireListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'questionnaires';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('id', '#'),
            TD::make('full_name', 'ФИО')
                ->render(function (Questionnaire $questionnaire) {
                    return Link::make($questionnaire->full_name)
                        ->route('platform.questionnaire.show', $questionnaire);
                }),
            TD::make('status', 'Статус')
                ->render(function (Questionnaire $questionnaire) {
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
            TD::make('email', 'Email'),
            TD::make('mobile_phone', 'Телефон'),
            TD::make('campus', 'Корпус'),
            TD::make('apartment', 'Квартира'),
            TD::make('parking', 'Парковочное место'),
            TD::make('created_at', 'Дата создания'),
            TD::make('updated_at', 'Дата изменения'),
        ];
    }
}
