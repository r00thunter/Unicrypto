<?php 
$chat_height = (!empty($_SESSION['chat_height'])) ? $_SESSION['chat_height'] : false;
$inner = 196;
$outer = 222;

if ($chat_height == 1) {
	$inner = 0;
	$outer = 26;
}
else if ($chat_height == 3) {
	$inner = 392;
	$outer = 418;
}
?>

<div id="chat-frame" style="height:<?= $outer ?>px;">
	<div class="chat-head">
		<span id="num_online">0</span> online.
		<a href="#" class="chat-control contract fa fa-minus"></a>
		<a href="#" class="chat-control expand fa fa-plus"></a>
		<div class="clear"></div>
	</div>
	<div class="contain" style="height:<?= $inner ?>px;">
		<ul class="pages">
			<li class="chat page">
				<div class="chatArea">
					<ul class="chat_messages"></ul>
				</div>
				<input class="inputMessage" placeholder="<?= (User::isLoggedIn()) ? 'Type here...' : 'Log in to comment!' ?>" <?= (User::isLoggedIn()) ? '' : 'disabled="disabled"' ?> style="<?= ($chat_height == 1) ? 'display:none;' : '' ?>"/>
			</li>
			<li class="login page">
				<div class="form">
					<h3 class="title">What's your nickname?</h3>
					<input class="usernameInput" type="text" maxlength="14" />
				</div>
			</li>
		</ul>
	</div>
</div>