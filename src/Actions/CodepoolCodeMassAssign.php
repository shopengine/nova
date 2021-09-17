<?php

namespace ShopEngine\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\ActionRequest;

class CodepoolCodeMassAssign extends Action
{
    use InteractsWithQueue, Queueable;

    public $component = 'codepool-code-mass-assign';


    /**
     * Perform the action on the given models.
     *
     * @param \Laravel\Nova\Http\Requests\ActionRequest $request
     *
     * @return mixed
     */
    public function handleRequest(ActionRequest $request)
    {
        // @todo @dave add api

        dd(
            $request->input('resource'),
            $request->input('codes'),
        );

        return true;
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Textarea::make('Codes','codes')
                ->required(true)
                ->rules('required'),
        ];
    }
}