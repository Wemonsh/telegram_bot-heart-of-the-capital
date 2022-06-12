<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\DateRange;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;

class CreatedAtFilter extends Filter
{
    /**
     * The displayable name of the filter.
     *
     * @return string
     */
    public function name(): string
    {
        return 'filter';
    }

    /**
     * The array of matched parameters.
     *
     * @return array|null
     */
    public function parameters(): ?array
    {
        return ['full_name', 'created_at_period', 'status'];
    }

    /**
     * Apply to a given Eloquent query builder.
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function run(Builder $builder): Builder
    {

        if ($this->request->has('created_at_period'))
        {
            $builder->whereBetween('created_at', [$this->request->get('created_at_period')['start'],
                $this->request->get('created_at_period')['end']]);
        }

        if ($this->request->has('full_name')) {
            $builder->where('full_name', 'LIKE', '%'.$this->request->get('full_name').'%');
        }

        if ($this->request->has('status')) {
            $builder->where('status', '=', $this->request->get('status'));
        }

        return $builder;
    }

    /**
     * Get the display fields.
     *
     * @return Field[]
     */
    public function display(): iterable
    {
        return [
            Input::make('full_name')
                ->title('ФИО')
                ->name('full_name'),
            DateRange::make('created_at_period')
                ->title('Период создания заявки'),
            Select::make('status')
                ->title('Статус')
                ->options([
                    '0'   => 'Заявка создана',
                    '1' => 'Отправлено приглашение',
                    '2' => 'Приглашен',
                ])
        ];
    }
}
