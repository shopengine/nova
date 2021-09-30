<?php

namespace ShopEngine\Nova\Actions;

use Laravel\Nova\Actions\Action;
use Laravel\Nova\Http\Requests\ActionRequest;
use ShopEngine\Nova\Api\ListRequestBuilder;
use ShopEngine\Nova\Api\UpdateRequestBuilder;
use ShopEngine\Nova\Fields\ShopEngineModel as ShopEngineModelField;
use ShopEngine\Nova\Models\ShopEngineModel;
use ShopEngine\Nova\Resources\Codepool;
use ShopEngine\Nova\Resources\ConditionSet;
use ShopEngine\Nova\Structs\Api\ListRequestStruct;
use ShopEngine\Nova\Structs\Api\RequestFilterStruct;

class CodeMassEdit extends Action
{
    public function __construct()
    {
    }

    public function name()
    {
        return __('se.actions.massedit');
    }

    /**
     * Perform the action on the given models.
     *
     * @param \Laravel\Nova\Http\Requests\ActionRequest $request
     *
     * @return mixed
     */
    public function handleRequest(ActionRequest $request)
    {
        $resource = $request->resource();
        $loadRequest = ListRequestBuilder::fromResource($resource);

        $ids = explode(',', $request->get('resources'));

        $requestStruct = (new ListRequestStruct())->withAllEntries();
        $requestStruct->addFilter(new RequestFilterStruct(
            'aggregateId',
            join('|', $ids),
            'eq'
        ));

        $results = $loadRequest->loadItems($requestStruct);
        $updateRequest = UpdateRequestBuilder::fromResource($resource);

        /** @var ShopEngineModel $model */
        foreach ($results as $model) {
            $hasChanges = false;

            if ($request->has("codepoolId")) {
                $model->setAttribute('codepoolId', $request->get("codepoolId"));
                $hasChanges = true;
            }

            if ($request->has("conditionSetVersionId")) {
                $model->setAttribute('conditionSetVersionId', $request->get("conditionSetVersionId"));
                $hasChanges = true;
            }

            if ($hasChanges) {
                $updateRequest->save($model);
            };
        }

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
            ShopEngineModelField::make('Kondition', 'conditionSetVersionId')
                ->model(ConditionSet::class)
                ->valueFieldName('versionId')
                ->labelFieldName('name')
                ->onlyOnForms(),
            ShopEngineModelField::make('Codepool', 'codepoolId')
                ->model(Codepool::class)
                ->labelFieldName('name')
                ->onlyOnForms(),
        ];
    }
}
