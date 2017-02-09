<?php $dir = plugins_url(); ?>
<p>First of all, you need to create a channel. To create a channel, just click the button at the right of the search line. In the pop-up window, you will be offered to create a group or a channel. Choose “New Channel” and click “Next”.<p>

<div align="center">
<?php
echo '<img src="' . plugins_url( 'images/new_channel.jpg', dirname(__FILE__) ) . '" > ';
?>
</div>
 
<p>On the next step, you will be offered to write a name, description and set up the main picture.On the next step, you can also choose whether it will be a Private or Public channel. To be able to build community and schedule content, please choose the Public channel option. Important: Choose and write a bundle for your link (it can be shared with other Telegram users as an invite link).</p>


<div align="center">
<?php
echo '<img src="' . plugins_url( 'images/public.jpg', dirname(__FILE__) ) . '" > ';
?>
</div>

<p> Brilliant! On the next step, you can invite people from your contact list. Your channel is created. Now it is high time to create a bot, and connect it to Telegram.<br />

 You don’t need to write any code for this. In fact, you don’t even need your computer! Go to the telegram app on your phone and…<br />

Search for the “botfather” telegram bot (he’s the one that’ll assist you with creating and managing your bot)
Type /help to see all possible commands the botfather can handle<br />

Click on or type /newbot to create a new bot.</p>

<div align="center">
<?php
echo '<img src="' . plugins_url( 'images/newbot.jpg', dirname(__FILE__) ) . '" > ';
?>
</div>


<p>Follow instructions and make a new name for your bot. If you are making a bot just for experimentation, it can be useful to namespace your bot by placing your name before it in its username, since it has to be a unique name. Although, its screen name can be whatever you like. You should see a new API token generated for it. Copy this Token and paste into newsgram settings. After setting up your bot, add it as the channel admin by clicking on the channel name and choosing channel admin. 
</p>
<div align="center">
<?php
echo '<img src="' . plugins_url( 'images/administrator.jpg', dirname(__FILE__) ) . '" > ';
?>
</div>


