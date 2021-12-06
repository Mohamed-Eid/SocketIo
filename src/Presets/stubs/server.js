const express = require('express');

const app = express();

const server = require('http').createServer(app);
const bodyParser = require("body-parser");

require('dotenv').config();


app.use(bodyParser.urlencoded({ extended: false }));
app.use(bodyParser.json());



const io = require('socket.io')(server, {
    cors: { origin: "*"}
});


function sendDataToChannel(channel, data, io ){
    io.emit(channel, data);
}

function sendDataToUser(channel, data, user ){
    io.to(user.id).emit(channel, data);
}

function getUser(users , id){
    return users.filter( (user) => {
        return user.handshake.auth.id == id;
    })[0];
}

var users = [];

function parseUsers(users){
    newUsers = [];
    users.forEach(socket => {
        newUsers.push({
            socketId: socket.id,
            handshake: socket.handshake,
            connected : socket.connected
        });
    });

    return newUsers;
}

async function getSockets(){
    return await io.fetchSockets();
    // // return all Socket instances
    // const sockets = await io.fetchSockets();
    // const socketCount = io.of("/admin").sockets.size;
    // const sockets = await io.fetchSockets();
    // console.log(sockets[0].data.username);
    // // return all Socket instances in the "room1" room of the main namespace
    // const sockets = await io.in("room1").fetchSockets();
}

//TODO:: handle on close
io.on('connection', (socket) => {
    console.log('device connected');
    users.push(socket); 
});

app.post('/broadCastToChannel', function (req, res) {
    if(req.body.channels.length > 0){
        req.body.channels.forEach(channel => {
            sendDataToChannel(channel , req.body.data , io);
        });
    }

    
    sendDataToChannel(req.body.channel , req.body.data , io);
    
    res.send(req.body)
});

app.post('/broadCastToUser', function (req, res) {
    let user = getUser(users, req.body.userId);
    if(user !== undefined ){
        sendDataToUser(req.body.channel , req.body.data , user)
    }
    res.send(req.body)
});

app.get('/getConnectedUsers', function (req, res) {
    res.send(parseUsers(users));
});

server.listen(process.env.SOCKET_SERVER_PORT || 3000, () => {
    console.log('Server is running on port : '+ process.env.SOCKET_SERVER_PORT);
});