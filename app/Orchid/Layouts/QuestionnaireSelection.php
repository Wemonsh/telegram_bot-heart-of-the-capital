<?php

namespace App\Orchid\Layouts;

use App\Orchid\Filters\CreatedAtFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class QuestionnaireSelection extends Selection
{
    /**
     * @return Filter[]
     */
    public function filters(): iterable
    {
        return [CreatedAtFilter::class];
    }
}
