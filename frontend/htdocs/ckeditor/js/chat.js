$(document).ready(function() {
	$(function() {
	  var FADE_TIME = 150; // ms
	  var TYPING_TIMER_LENGTH = 400; // ms
	  var COLORS = [
	    '#e21400', '#91580f', '#f8a700', '#f78b00',
	    '#58dc00', '#287b00', '#a8f07a', '#4ae8c4',
	    '#3b88eb', '#3824aa', '#a700ff', '#d300e7'
	  ];
	
	  // Initialize varibles
	  var websocket = false;
	  var lastId = 0;
	  var $window = $('#chat-frame');
	  var $usernameInput = $('.usernameInput'); // Input for username
	  var $messages = $('.chat_messages'); // Messages area
	  var $inputMessage = $('.inputMessage'); // Input message input box
	  var $loginPage = $('.login.page'); // The login page
	  var $chatPage = $('.chat.page'); // The chatroom page
	
	  // Prompt for setting a username
	  var username = $('#chat_handle').val();
	  var connected = false;
	  var typing = false;
	  var lastTypingTime;
	  var $currentInput = $usernameInput.focus();
	  var url = $('#chat_baseurl').val();
	  var socket = io(url,{'reconnectionAttempts': 3});

	  function addParticipantsMessage (data) {
		  $('#num_online').html(data.numUsers);
	  }
	
	  // Sets the client's username
	  function setUsername () {
	    // If the username is valid
	    if (username) {
	      //$loginPage.fadeOut();
	      //$chatPage.show();
	      $loginPage.off('click');
	      $currentInput = $inputMessage.focus();
	
	      // Tell the server your username
	      if (websocket)
	    	  socket.emit('add user', username);
	      else
	    	  fallbackRead();
	    }
	  }
	
	  // Sends a chat message
	  function sendMessage () {
		  if (username == 'not-logged-in')
			  return false;
		  
	    var message = $inputMessage.val();
	    // Prevent markup from being injected into the message
	    message = cleanInput(message);
	    // if there is a non-empty message and a socket connection
	    if (message && connected) {
	      $inputMessage.val('');
	      addChatMessage({
	        username: username,
	        message: message
	      });
	      // tell server to execute 'new message' and send along one parameter
	      if (websocket)
	    	  socket.emit('new message', message);
	      else {
	    	  var post = {message:message,action:'new'};
	    	  $.post("includes/ajax.chat.php",post,function(data){
	    		  if (!data)
	    			  return false;
	    		  
	    		  var parsed = JSON.parse(data);
	    		  if (parsed.lastId)
	    			  lastId = parsed.lastId;
	    		  else
	    			  connected = false;
	    	  });
	      }
	    }
	  }
	
	  // Log a message
	  function log (message, options) {
	    var $el = $('<li>').addClass('log').text(message);
	    addMessageElement($el, options);
	  }
	
	  // Adds the visual chat message to the message list
	  function addChatMessage (data, options) {
	    // Don't fade the message in if there is an 'X was typing'
	    var $typingMessages = getTypingMessages(data);
	    options = options || {};
	    if ($typingMessages.length !== 0) {
	      options.fade = false;
	      $typingMessages.remove();
	    }
	
	    var $usernameDiv = $('<span class="username"/>')
	      .text(data.username)
	      .css('color', getUsernameColor(data.username));
	    var $messageBodyDiv = $('<span class="messageBody">')
	      .text(data.message);
	
	    var typingClass = data.typing ? 'typing' : '';
	    var $messageDiv = $('<li class="message"/>')
	      .data('username', data.username)
	      .addClass(typingClass)
	      .append($usernameDiv, $messageBodyDiv);
	
	    addMessageElement($messageDiv, options);
	  }
	
	  // Adds the visual chat typing message
	  function addChatTyping (data) {
	    data.typing = true;
	    data.message = 'is typing';
	    addChatMessage(data);
	  }
	
	  // Removes the visual chat typing message
	  function removeChatTyping (data) {
	    getTypingMessages(data).fadeOut(function () {
	      $(this).remove();
	    });
	  }
	
	  // Adds a message element to the messages and scrolls to the bottom
	  // el - The element to add as a message
	  // options.fade - If the element should fade-in (default = true)
	  // options.prepend - If the element should prepend
	  //   all other messages (default = false)
	  function addMessageElement (el, options) {
	    var $el = $(el);
	
	    // Setup default options
	    if (!options) {
	      options = {};
	    }
	    if (typeof options.fade === 'undefined') {
	      options.fade = true;
	    }
	    if (typeof options.prepend === 'undefined') {
	      options.prepend = false;
	    }
	
	    // Apply options
	    if (options.fade) {
	      $el.hide().fadeIn(FADE_TIME);
	    }
	    if (options.prepend) {
	      $messages.prepend($el);
	    } else {
	      $messages.append($el);
	    }
	    $messages[0].scrollTop = $messages[0].scrollHeight;
	  }
	
	  // Prevents input from having injected markup
	  function cleanInput (input) {
	    return $('<div/>').text(input).text();
	  }
	
	  // Updates the typing event
	  function updateTyping () {
	    if (connected) {
	      if (!typing) {
	        typing = true;
	        socket.emit('typing');
	      }
	      lastTypingTime = (new Date()).getTime();
	
	      setTimeout(function () {
	        var typingTimer = (new Date()).getTime();
	        var timeDiff = typingTimer - lastTypingTime;
	        if (timeDiff >= TYPING_TIMER_LENGTH && typing) {
	          socket.emit('stop typing');
	          typing = false;
	        }
	      }, TYPING_TIMER_LENGTH);
	    }
	  }
	
	  // Gets the 'X is typing' messages of a user
	  function getTypingMessages (data) {
	    return $('.typing.message').filter(function (i) {
	      return $(this).data('username') === data.username;
	    });
	  }
	
	  // Gets the color of a username through our hash function
	  function getUsernameColor (username) {
	    // Compute hash code
	    var hash = 7;
	    for (var i = 0; i < username.length; i++) {
	       hash = username.charCodeAt(i) + (hash << 5) - hash;
	    }
	    // Calculate color
	    var index = Math.abs(hash % COLORS.length);
	    return COLORS[index];
	  }
	  
	  // if fallback to ajax
	  function fallbackRead() {
		  if (websocket)
			  return false;
		  
		  var post = {action:'read',last_id:lastId};
		  $.post("includes/ajax.chat.php",post,function(data){
			  connected = true;
			  if (!data)
				  return false;
			  
			  var parsed = JSON.parse(data);
			  if (!parsed)
				  return false;
				  
			  if (parsed.numUsers)
				  addParticipantsMessage(parsed);
			  if (parsed.lastId)
				  lastId = parsed.lastId;
			  
			  if (!parsed.messages)
				  return false;
			  
			  parsed.messages.reverse();
			  for (i in parsed.messages) {
				  addChatMessage(parsed.messages[i]);
			  }
		  });
	  }
	
	  // Keyboard events
	
	  $window.keydown(function (event) {
	    // Auto-focus the current input when a key is typed
	    if (!(event.ctrlKey || event.metaKey || event.altKey)) {
	      $currentInput.focus();
	    }
	    // When the client hits ENTER on their keyboard
	    if (event.which === 13) {
	        sendMessage();
	        socket.emit('stop typing');
	        typing = false;
	    }
	  });
	
	  $inputMessage.on('input', function() {
	    updateTyping();
	  });
	
	  // Click events
	
	  // Focus input when clicking anywhere on login page
	  $loginPage.click(function () {
	    $currentInput.focus();
	  });
	
	  // Focus input when clicking on the message input's border
	  $inputMessage.click(function () {
	    $inputMessage.focus();
	  });
	  
	  $window.find('.contract').click(function(e){
		  e.preventDefault();
		  if ($window.find('.contain').height() == 196) {
			  $window.animate({height:'-=196px'},400);
			  $window.find('.contain').animate({height:'-=196px'},400);
			  $window.find('.inputMessage').hide(200);
			  $.get('includes/ajax.graph.php?action=chat_setting&height=1', function(){});
		  } 
		  else if ($window.find('.contain').height() > 200) {
			  $window.animate({height:'-=196px'},400);
			  $window.find('.contain').animate({height:'-=196px'},400);
			  $.get('includes/ajax.graph.php?action=chat_setting&height=2', function(){});
		  } 
	  });
	  $window.find('.expand').click(function(e){
		  e.preventDefault();
		  if ($window.find('.contain').height() == 0) {
			  $window.animate({height:'+=196px'},400);
			  $window.find('.contain').animate({height:'+=196px'},400);
			  $window.find('.inputMessage').show(200);
			  $.get('includes/ajax.graph.php?action=chat_setting&height=2', function(){});
		  } 
		  else if ($window.find('.contain').height() == 196) {
			  $window.animate({height:'+=196px'},400);
			  $window.find('.contain').animate({height:'+=196px'},400);
			  $.get('includes/ajax.graph.php?action=chat_setting&height=3', function(){});
		  } 
	  });
	
	  // Socket events
	
	  // Whenever the server emits 'login', log the login message
	  socket.on('login', function (data) {
	    connected = true;
	    websocket = true;
	    // Display the welcome message
	    var message = "Welcome to Chat – ";
	    log(message, {
	      prepend: true
	    });
	    addParticipantsMessage(data);
	  });
	
	  // Whenever the server emits 'new message', update the chat body
	  socket.on('new message', function (data) {
	    addChatMessage(data);
	  });
	
	  // Whenever the server emits 'user joined', log it in the chat body
	  socket.on('user joined', function (data) {
	    log(data.username + ' joined');
	    addParticipantsMessage(data);
	  });
	
	  // Whenever the server emits 'user left', log it in the chat body
	  socket.on('user left', function (data) {
	    log(data.username + ' left');
	    addParticipantsMessage(data);
	    removeChatTyping(data);
	  });
	
	  // Whenever the server emits 'typing', show the typing message
	  socket.on('typing', function (data) {
	    addChatTyping(data);
	  });
	
	  // Whenever the server emits 'stop typing', kill the typing message
	  socket.on('stop typing', function (data) {
	    removeChatTyping(data);
	  });
	  
	  setInterval(function(){
		  fallbackRead();
	  },3000);
	  
	  // start
	  setUsername();
	});
});
