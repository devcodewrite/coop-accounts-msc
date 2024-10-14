<?php

namespace App\Filters;

use App\Models\AppModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\Exceptions\HTTPException;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Codewrite\CoopAuth\GuardReponse;

class AppFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Get the key from the header
        $apiKey = $request->getHeaderLine('Api-Key');
        if (!$apiKey)
            return (new GuardReponse(false, GuardReponse::TOKEN_NOT_PROVIDED, 'Api key not provided'))->responsed();

        //Retrive active app data with api key if no data return null
        $appModel = new AppModel();
        $app = $appModel->findActiveApp($apiKey);

        // if api key is not avaliable
        if (!$app)
            return (new GuardReponse(false, GuardReponse::UNAUTHORIZED, 'Unauthorized Api key!'))->responsed();

        // checking scopes
        if (!$this->hasScope($request, $app))
            return (new GuardReponse(false, GuardReponse::INSUFFICIENT_SCOPE))->responsed();
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }

    private function getResourceId(RequestInterface $request): string | null
    {
        // get ResourceID From Uri
        return $request->getUri()->getSegment(1);
    }

    private function hasScope(RequestInterface $request, $app): bool
    {
        $resourceId = $this->getResourceId($request);

        // Map HTTP Method to Action
        $httpToActionMap = [
            'GET' => 'view',
            'POST' => 'create',
            'PUT' => 'update',
            'PATCH' => 'update',
            'DELETE' => 'delete'
        ];
        $requestedAction = $httpToActionMap[$request->getMethod()] ?? null;

        // get actions by resourceId
        $actions = $app->scopes[$resourceId] ?? [];

        return in_array($requestedAction, $actions);
    }
}
