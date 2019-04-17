// Setup basic express server
var express = require('express');
var app = express();
var cors = require('cors');
var server = require('http').createServer(app);
var io = require('socket.io')(server);
io.origins('*');
var Datastore = require('nedb');
var db = new Datastore({ filename: './chat.db',autoload:true });
db.persistence.setAutocompactionInterval(60000);
var port = 3000;


server.listen(port, function () {
  console.log('Server listening at port %d', port);
});

app.use(express.static(__dirname + '/public'));
app.get('*',cors({origin:'*'}), function(req, res, next){});


// Chatroom

// usernames which are currently connected to the chat
var usernames = {};
var numUsers = 0;

io.on('connection', function (socket) {
  var addedUser = false;

  // when the client emits 'new message', this listens and executes
  socket.on('new message', function (data) {
    // we tell the client to execute 'new message'
    socket.broadcast.emit('new message', {
      username: socket.username,
      message: data
    });
    
    db.insert({username:socket.username,message:data,timestamp:Date.now()},function(){
    	db.count({}, function (err, count) {
    		if (count > 30) {
    			var elim = count - 30;
    			db.find().sort({timestamp:1}).exec(function (err,docs) {
    				if (docs) {
    					db.remove({ _id: docs[i]['_id']});
    				}
    			});
    		}
    	});
    });
  });

  // when the client emits 'add user', this listens and executes
  socket.on('add user', function (username) {
    // we store the username in the socket session for this client
    socket.username = username;
    // add the client's username to the global list
    usernames[username] = username;
    ++numUsers;
    addedUser = true;
    socket.emit('login', {
      numUsers: numUsers
    });
    // echo globally (all clients) that a person has connected
    socket.broadcast.emit('user joined', {
      username: socket.username,
      numUsers: numUsers
    });
    
    db.find().sort({timestamp:1}).exec(function (err,docs) {
		for (i in docs) {
			socket.emit('new message',{
		      username: docs[i].username,
		      message: docs[i].message
		    });
		}
	});
  });

  // when the client emits 'typing', we broadcast it to others
  socket.on('typing', function () {
    socket.broadcast.emit('typing', {
      username: socket.username
    });
  });

  // when the client emits 'stop typing', we broadcast it to others
  socket.on('stop typing', function () {
    socket.broadcast.emit('stop typing', {
      username: socket.username
    });
  });

  // when the user disconnects.. perform this
  socket.on('disconnect', function () {
    // remove the username from global usernames list
    if (addedUser) {
      delete usernames[socket.username];
      --numUsers;

      // echo globally that this client has left
      socket.broadcast.emit('user left', {
        username: socket.username,
        numUsers: numUsers
      });
    }
  });
});
