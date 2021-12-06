# SocketIo

Simple Socket Io Interface integrated with Laravel Event Broadcasting (like [pusher](https://pusher.com/))

# Installation :

We’re going to use Composer’s require command to download and install our package

    composer require bluex/socket-io

Then we use this artisan command to install our socket server (written in nodejs-express)

    php artisan io:install

Then install our socket server dependencies using

    npm install

### Switch the broadcast driver to socket io

We need to tell Laravel to use Socket Io Channels for realtime updates. Register the Broadcast application service by opening `config/app.php` and uncommenting this line:

    // App\Providers\BroadcastServiceProvider::class,

and add this line to the `providers array` in same file :

    Bluex\SocketIo\Providers\SocketIoServiceProvider::class,

In your project root’s `.env` file, change the broadcast driver from its default “log” value to “socket-io”.

    BROADCAST_DRIVER=socket-io

scroll further down this file and add your socket server configrations

    SOCKET_SERVER_URL=127.0.0.1
    SOCKET_SERVER_PORT=3000

In your `config/broadcasting.php ` file add next code to the `connections array` :

    'socket-io'  =>  [
        'driver'  =>  'socket-io',
    ],

# Usage :

### Server Side :

first make a normal event ex:

    php artisan make:event TestEvent

in the event class just add the channel name \*(now only public channels are available) ex:

    public  function  broadcastOn()
    {
        return  ['testChannel'];
    }

Then fire your event from anywhere (don't forget to implement `ShouldBroadcastNow` interface if you have a queue worker already running )

#### Client Side :

##### Js Example:

    let  ip_address  =  "{{ env('SOCKET_SERVER_URL') }}";
    let  socket_port  =  '3000';
    let  socket  =  io(ip_address  +  ':'  +  socket_port,  {
        auth:  {
    	    token:  "123",
        }
    });
    socket.on('testChannel',  (data)  =>  {
    	console.log('testChannel : ');
    	console.log(data);
    });

# TODO :

- [ ] Unit Test
- [ ] Support Private Channels
- [ ] Write a documentation
