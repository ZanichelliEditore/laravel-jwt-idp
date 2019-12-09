<?php

namespace App\Listeners;


use App\Events\LogoutEvent;
use App\Models\Provider;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use App\Repositories\RepositoryInterface;

/**
 * Class LogoutProvidersListener
 *
 * This class handles the logout event {@see LogoutEvent}.
 *
 * @package App\Listeners
 */
class LogoutProvidersListener implements ShouldQueue 
{
    protected $providerRepository;

    /**
     * LogoutProvidersListener constructor.
     *
     * @param RepositoryInterface $providerRepository
     */
    public function __construct(RepositoryInterface $providerRepository) {
        $this->providerRepository = $providerRepository;
    }

    /**
     * Logout the user from every registered providers.
     *
     * @param LogoutEvent $event
     */
    public function handle(LogoutEvent $event) {
        
        $user = $event->getUser();

        $providers = $this->providerRepository->all();

        if (!count($providers)) {
            return;
        }

        $client = new Client();

        foreach ($providers as $provider) {
            try {
                $client->get($provider->logoutUrl, [
                    'query' => [
                        'id' => $user->id
                    ],
                    'auth' => [
                        decrypt($provider->username),
                        decrypt($provider->password)
                    ]
                ]);
            } catch (RequestException $e) {
                Log::error($e->getMessage());
            }
        }

    }
    
}

