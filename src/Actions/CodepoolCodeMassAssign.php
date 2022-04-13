<?php

namespace ShopEngine\Nova\Actions;

use App\Facades\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\ActionRequest;
use ShopEngine\Nova\Resources\Codepool;
use SSB\Api\Client;
use SSB\Api\Model\Code;

class CodepoolCodeMassAssign extends Action
{
    use InteractsWithQueue;
    use Queueable;

    /**
     * The displayable name of the action.
     *
     * @var string
     */
    public $name = 'Codes verschieben';

    public Client $client;

    public function __construct()
    {
        $this->client = Shop::shopEngineClient();
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
        $codepoolId =  $request->input('resources');

        if ($resource !== Codepool::class) {
            return Action::danger($resource . ' is not ' . Codepool::class);
        }

        if (!is_numeric($codepoolId)) {
            return Action::danger('Resource id is not numeric');
        }

        $codes = $this->getCodes($request);

        if (empty($codes)) {
            return Action::danger('No valid codes given');
        }

        $message = [
            'Entered ' . count($codes) . ' codes'
        ];

        $responseMessage = $this->updateCodes($codes, $codepoolId);
        $message = array_merge($message, $responseMessage);

        return Action::message(implode(' | ', $message));
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Textarea::make('Codes', 'codes')
                ->rules('required')
                ->help('Codes durch Komma trennen')
        ];
    }

    protected function getCodes(ActionRequest $request): array
    {
        $codes = explode(',', $request->input('codes'));
        $codes = array_map('trim', $codes);
        $codes = array_filter($codes, fn(string $code) => ! Str::contains($code, ' '));

        return array_filter($codes);
    }

    protected function updateCodes(array $codes, int $codepoolId): array
    {
        $responseMessage = [];
        $codeAggregateIds = [];

        foreach (array_chunk($codes, 150) as $chunk) {
            /** @var Code[] $shopEngineCodes */
            $shopEngineCodes = $this->client->get('code', [
                'code-eq' => implode('|', $chunk),
                'codepoolId-ne' => $codepoolId,
                'properties' => 'aggregateId'
            ]);

            foreach ($shopEngineCodes as $code) {
                $codeAggregateIds[] = $code->getAggregateId();
            }
        }

        $responseMessage[] = 'Requested ' . count($codeAggregateIds) . ' codes';

        if (!empty($codeAggregateIds)) {
            $this->client->patch('code/updateBatch', [
                'aggregateIds' => implode('|', $codeAggregateIds),
                'data' => [
                    'codepoolId' => $codepoolId,
                ],
            ]);
        }

        return $responseMessage;
    }
}
