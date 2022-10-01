<?php

namespace App\Orchid\Layouts\User\BigBrother;

use App\Models\User\BigBrother\BigBrother;
use App\Orchid\Layouts\AnikulturaListLayout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\TD;

class BigBrotherListLayout extends AnikulturaListLayout
{
    protected $target = 'big_brothers';

    protected function columns(): iterable
    {
        return [
            TD::make('firstname', __('First Name'))
                ->render(function (BigBrother $bigBrother) {
                    return Link::make($bigBrother->first_name)
                        ->route('platform.big-brothers.edit', [$bigBrother->id]);
                }),

            TD::make('middlename', __('Middle Name'))
                ->render(function (BigBrother $bigBrother) {
                    return Link::make($bigBrother->middle_name)
                        ->route('platform.big-brothers.edit', [$bigBrother->id]);
                }),

            TD::make('lastname', __('Last Name'))
                ->cantHide()
                ->render(function (BigBrother $bigBrother) {
                    return Link::make($bigBrother->last_name)
                        ->route('platform.big-brothers.edit', [$bigBrother->id]);
                }),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->cantHide()
                ->width('100px')
                ->render(function (BigBrother $bigBrother) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([
                            Link::make(__('Edit'))
                                ->route('platform.farmers.edit', [$bigBrother->id])
                                ->icon('pencil'),

                            Button::make(__('Delete'))
                                ->icon('trash')
                                ->method('remove')
                                ->confirm(__('Once the big brother is deleted, all of its resources and data will be permanently deleted.'))
                                ->parameters([
                                    'id' => $bigBrother->id,
                                ]),
                        ]);
                }),
        ];
    }
}
