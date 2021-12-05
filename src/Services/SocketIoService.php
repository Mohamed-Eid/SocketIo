<?php

namespace Bluex\SocketIo\Services;

use Bluex\SocketIo\Exceptions\SocketIoException;
use \GuzzleHttp\Client;

class SocketIoService
{
    public $serverUrl;
    public $broadCastUrl;
    public $broadCastToUserUrl;
    public $connectedUsersUrl;
    public $client;

    public function __construct()
    {
        $this->serverUrl = env('SOCKET_SERVER_URL') . ':' . env('SOCKET_SERVER_PORT');
        $this->broadCastUrl = $this->serverUrl . '/' . 'broadCastToChannel';
        $this->broadCastToUserUrl = $this->serverUrl . '/' . 'broadCastToUser';
        $this->connectedUsersUrl = $this->serverUrl . '/' . 'getConnectedUsers';
        $this->client = new Client();
    }


    public function broadCastTo(string $channel, $data)
    {
        $res = $this->client->post(
            $this->broadCastUrl,
            [
                'json' => [
                    'channel' => $channel,
                    'data'    => $data
                ]
            ]
        );
        return json_decode($res->getBody());
    }

    public function broadCastToUser(string $channel, $data, $userId)
    {
        $res = $this->client->post(
            $this->broadCastToUserUrl,
            [
                'json' => [
                    'channel' => $channel,
                    'data'    => $data,
                    'userId'  => $userId
                ]
            ]
        );
        return json_decode($res->getBody());
    }

    public function getOnlineSockets()
    {
        $res = $this->client->get($this->connectedUsersUrl);
        return json_decode($res->getBody());
    }


    /**
     * Validate number of channels and channel name format.
     *
     * @param string[] $channels An array of channel names to validate
     *
     * @throws SocketIoException If $channels is too big or any channel is invalid
     *
     * @return void
     */
    private function validate_channels($channels)
    {
        if (count($channels) > 100) {
            throw new SocketIoException('An event can be triggered on a maximum of 100 channels in a single call.');
        }

        foreach ($channels as $channel) {
            $this->validate_channel($channel);
        }
    }

    /**
     * Ensure a channel name is valid based on our spec.
     *
     * @param string $channel The channel name to validate
     *
     * @throws SocketIoException If $channel is invalid
     *
     * @return void
     */
    private function validate_channel($channel)
    {
        if (!preg_match('/\A[-a-zA-Z0-9_=@,.;]+\z/', $channel)) {
            throw new SocketIoException('Invalid channel name ' . $channel);
        }
    }


    /**
     * Trigger an event by providing event name and payload.
     * Optionally provide a socket ID to exclude a client (most likely the sender).
     *
     * @param array|string $channels        A channel name or an array of channel names to publish the event on.
     * @param string       $event
     * @param mixed        $data            Event data
     * @param array        $params          [optional]
     * @param bool         $already_encoded [optional]
     *
     * @throws PusherException   Throws PusherException if $channels is an array of size 101 or above or $socket_id is invalid
     * @throws ApiErrorException Throws ApiErrorException if the Channels HTTP API responds with an error
     *
     * @return object
     */
    public function trigger($channels, $event, $data, $params = array(), $already_encoded = false)
    {
        if (is_string($channels) === true) {
            $channels = array($channels);
        }


        $res = $this->client->post(
            $this->broadCastUrl,
            [
                'json' => [
                    'name'    => $event,
                    'channels' => array_values($channels),
                    'data'    => $data
                ]
            ]
        );
        return json_decode($res->getBody());
    }
}
