<?php

namespace Bluex\SocketIo\Services;

use \GuzzleHttp\Client;
use Illuminate\Broadcasting\Broadcasters\Broadcaster;

class SocketIo extends Broadcaster
{


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

        if (empty($request->channel_name) ||
            ($this->isGuardedChannel($request->channel_name) &&
            ! $this->retrieveUser($request, $channelName))) {
            throw new AccessDeniedHttpException;
        }

        return parent::verifyUserCanAccessChannel(
            $request, $channelName
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
            return $this->decodePusherResponse(
                $request, $this->pusher->socket_auth($request->channel_name, $request->socket_id)
            );
        }

        $channelName = $this->normalizeChannelName($request->channel_name);

        $user = $this->retrieveUser($request, $channelName);

        $broadcastIdentifier = method_exists($user, 'getAuthIdentifierForBroadcasting')
                        ? $user->getAuthIdentifierForBroadcasting()
                        : $user->getAuthIdentifier();

        return $this->decodePusherResponse(
            $request,
            $this->pusher->presence_auth(
                $request->channel_name, $request->socket_id,
                $broadcastIdentifier, $result
            )
        );
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

        if ($this->pusherServerIsVersionFiveOrGreater()) {
            $parameters = $socket !== null ? ['socket_id' => $socket] : [];

            try {
                $this->pusher->trigger(
                    $this->formatChannels($channels), $event, $payload, $parameters
                );
            } catch (ApiErrorException $e) {
                throw new BroadcastException(
                    sprintf('Pusher error: %s.', $e->getMessage())
                );
            }
        } else {
            $response = $this->pusher->trigger(
                $this->formatChannels($channels), $event, $payload, $socket, true
            );

            if ((is_array($response) && $response['status'] >= 200 && $response['status'] <= 299)
                || $response === true) {
                return;
            }

            throw new BroadcastException(
                ! empty($response['body'])
                    ? sprintf('Pusher error: %s.', $response['body'])
                    : 'Failed to connect to Pusher.'
            );
        }
    }
}
