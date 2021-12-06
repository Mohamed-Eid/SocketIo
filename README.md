# SocketIo

Simple Socket Io Interface integrated with Laravel Event Broadcasting (like [pusher](https://pusher.com/))

# Installation :

First intsall the package using composer :

    composer require bluex/socket-io
    php artisan io:install
    npm install

The Add :

    Bluex\SocketIo\Providers\SocketIoServiceProvider::class,

To provider array in your app.php config file
In your broadcasting.php config file add next code to the connections array :

    'socket-io'  =>  [
        'driver'  =>  'socket-io',
    ],

in your .env file add

    BROADCAST_DRIVER=socket-io
    SOCKET_SERVER_URL=127.0.0.1
    SOCKET_SERVER_PORT=3000

# Usage :

#### Server Side :

first make a normal event ex:

    php artisan make:event TestEvent

in the event class just add the channel name \*(now only public channels are available) ex:

    public  function  broadcastOn()
    {
        return  ['testChannel'];
    }

Then fire your event from anywhere (don't forget to implement ShouldBroadcastNow interface if you have a queue worker already running )

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
