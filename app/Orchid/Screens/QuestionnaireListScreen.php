<?php

namespace App\Orchid\Screens;

use App\Models\Questionnaire;
use App\Orchid\Layouts\QuestionnaireListLayout;
use App\Orchid\Layouts\QuestionnaireSelection;
use Orchid\Screen\Screen;

class QuestionnaireListScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'questionnaires' => Questionnaire::filtersApplySelection(QuestionnaireSelection::class)->paginate()
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Анкеты';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            QuestionnaireSelection::class,
            QuestionnaireListLayout::class
        ];
    }
}
