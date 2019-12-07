<?php

namespace App\Http\Middleware;

use Closure;
use League\OAuth2\Server\ResourceServer;
use Illuminate\Auth\AuthenticationException;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use App\Repositories\ClientRepositoryInterface;

class CheckClientRole
{

    /**
     * The Client Repository Interface.
     *
     * @var \App\Repositories\ClientRepositoryInterface
     */
    protected $clientRepository;
    /**
     * The Resource Server instance.
     *
     * @var \League\OAuth2\Server\ResourceServer
     */
    private $server;

    /**
     * Create a new middleware instance.
     *
     * @param  \League\OAuth2\Server\ResourceServer  $server
     * @param  \App\Repositories\ClientRepositoryInterface  $clientRepository
     * @return void
     */
    public function __construct(ResourceServer $server, ClientRepositoryInterface $clientRepository)
    {
        $this->server = $server;
        $this->clientRepository = $clientRepository;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param string $roles
     * @return mixed
     */
    public function handle($request, Closure $next, string $roles)
    {
        $psr = (new DiactorosFactory)->createRequest($request);

        try {
            $psr = $this->server->validateAuthenticatedRequest($psr);
            $clientReqId = $psr->getAttribute('oauth_client_id');
            $client = $this->clientRepository->find($clientReqId);
            $check = !empty($client->roles) && !array_diff([$roles], json_decode($client->roles));
        } catch (OAuthServerException $e) {
            throw new AuthenticationException;
        }

        if (!$check) {
            return response()->json([
                'message' => 'You are not authorized to use this resource'
            ], 403);
        }
        return $next($request);
    }
}
