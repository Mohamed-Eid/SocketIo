<?php

namespace Bluex\SocketIo\Services;

use Bluex\SocketIo\Exceptions\ApiErrorException;
use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SocketIo extends Broadcaster
{

    public $socketService;

    public function __construct()
    {
        $this->socketService = new SocketIoService();
    }

    /**
     * Authenticate the incoming request for a given channel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function auth($request)
    {
        $channelName = $this->normalizeChannelName($request->channel_name);

        if (
            empty($request->channel_name) ||
            ($this->isGuardedChannel($request->channel_name) &&
                !$this->retrieveUser($request, $channelName))
        ) {
            throw new AccessDeniedHttpException();
        }

        return parent::verifyUserCanAccessChannel(
            $request,
            $channelName
        );
    }

    /**
     * Return the valid authentication response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $result
     * @return mixed
     */
    public function validAuthenticationResponse($request, $result)
    {
        if (Str::startsWith($request->channel_name, 'private')) {
        }

        $channelName = $this->normalizeChannelName($request->channel_name);

        $user = $this->retrieveUser($request, $channelName);

        $broadcastIdentifier = method_exists($user, 'getAuthIdentifierForBroadcasting')
            ? $user->getAuthIdentifierForBroadcasting()
            : $user->getAuthIdentifier();
    }


    /**
     * Broadcast the given event.
     *
     * @param  array  $channels
     * @param  string  $event
     * @param  array  $payload
     * @return void
     *
     * @throws \Illuminate\Broadcasting\BroadcastException
     */
    public function broadcast(array $channels, $event, array $payload = [])
    {
        $socket = Arr::pull($payload, 'socket');

        if (true) {
            $parameters = $socket !== null ? ['socket_id' => $socket] : [];
            try {
                $this->socketService->trigger(
                    $this->formatChannels($channels),
                    $event,
                    $payload,
                    $parameters
                );
            } catch (ApiErrorException $e) {
                throw new BroadcastException(
                    sprintf('Pusher error: %s.', $e->getMessage())
                );
            }
        } else {
            $response = $this->socketService->trigger(
                $this->formatChannels($channels),
                $event,
                $payload,
                $socket,
                true
            );

            if ((is_array($response) && $response['status'] >= 200 && $response['status'] <= 299)
                || $response === true
            ) {
                return;
            }

            throw new BroadcastException(
                !empty($response['body'])
                    ? sprintf('Pusher error: %s.', $response['body'])
                    : 'Failed to connect to Pusher.'
            );
        }
    }


    /**
     * Return true if the channel is protected by authentication.
     *
     * @param  string  $channel
     * @return bool
     */
    public function isGuardedChannel($channel)
    {
        return Str::startsWith($channel, ['private-', 'presence-']);
    }

    /**
     * Remove prefix from channel name.
     *
     * @param  string  $channel
     * @return string
     */
    public function normalizeChannelName($channel)
    {
        foreach (['private-encrypted-', 'private-', 'presence-'] as $prefix) {
            if (Str::startsWith($channel, $prefix)) {
                return Str::replaceFirst($prefix, '', $channel);
            }
        }

        return $channel;
    }
}


$s = new SocketIo(new SocketIoService());

print_r($s);
