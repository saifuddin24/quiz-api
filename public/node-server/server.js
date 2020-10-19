const express = require( 'express' );
const app = express();
const http = require( 'http' ).Server( app );
const io = require('socket.io')(http);

app.get( "/", (req, res) => res.sendFile( __dirname + "/index.html") );

io.on('connection', socket => {

});

http.listen(3000, console.log("App is running on port 3000"))
