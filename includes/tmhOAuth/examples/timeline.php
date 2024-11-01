<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
twitterWidgets.onload = _ga.trackTwitter;
</script>
<?php
require '../tmhOAuth.php';
require '../tmhUtilities.php';
$tmhOAuth = new tmhOAuth(array(
	'consumer_key'    => 'zUuqGO9xrLE4rVXctP8hKQ',
	'consumer_secret' => 'AtferfgKSYJG6Z0vnOI0dITimPFqAVvCOBs71P6mm4',
	'user_token'      => '108306523-n8E6MfAPAgdMxzfDZQNwk4WQDl2vDhL0yQN2E9bO',
	'user_secret'     => 'K8PdxAvXTlduNOtIhDaYkGYvX2ZV15QMdSreEEuY',
));



echo '<h3>Timeline:</h3>';
$code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/statuses/user_timeline'), array(
	'screen_name' => '3doordigital',
	'count'=>'2'
));
$output = json_decode($tmhOAuth->response['response'],true);
echo '<ul class="twitterfeed" style="list-style-type: none;">';
foreach($output as $tweet){ 
    echo '<li class="tweet" style="clear:both;"><div ><a href="https://twitter.com/intent/user?screen_name=' . $tweet['user']['screen_name'] . '" rel="nofollow"><img src="' . $tweet['user']['profile_image_url'] . '" height="48" width="48" alt="' . $tweet['user']['screen_name'] . '" title="' . $tweet['user']['screen_name'] . '" style="float: left;"></a></div>' . $tweet['text'] . '<br /><span><a href="http://twitter.com/intent/retweet?related=' . $tweet['user']['screen_name'] . '&amp;tweet_id=' . $tweet['id'] . '" rel="nofollow"><img src="http://si0.twimg.com/images/dev/cms/intents/icons/retweet.png" alt="ReTweet"></a>
<a href="http://twitter.com/intent/tweet?related=' . $tweet['user']['screen_name'] . '&amp;in_reply_to=' . $tweet['id'] . '" rel="nofollow"><img src="http://si0.twimg.com/images/dev/cms/intents/icons/reply.png" alt="Reply"></a>
<a href="http://twitter.com/intent/favorite?related=' . $tweet['user']['screen_name'] . '&amp;tweet_id=' . $tweet['id'] . '" rel="nofollow"><img src="http://si0.twimg.com/images/dev/cms/intents/icons/favorite.png" alt="Favorite"></a></span><a href="http://twitter.com/3doordigital/statuses/' . $tweet['id'] . '" rel="nofollow" target="_blank">permalink</a> || Image URL HTTPS: ' . $tweet['user']['profile_image_url_https'] . '<br /></li>';
}
echo '</ul>';

echo '<h3>Favourites:</h3>';
$code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/favorites/list'), array(
	'screen_name' => 'twitter',
	'count'=>'2'
));

$output = json_decode($tmhOAuth->response['response'],true);
echo '<ul class="twitterfeed" style="list-style-type: none;">';
foreach($output as $tweet){ 
    echo '<li class="tweet" style="clear:both;"><div ><a href="https://twitter.com/intent/user?screen_name=' . $tweet['user']['screen_name'] . '" rel="nofollow"><img src="' . $tweet['user']['profile_image_url'] . '" height="48" width="48" alt="' . $tweet['user']['screen_name'] . '" title="' . $tweet['user']['screen_name'] . '" style="float: left;"></a></div>' . $tweet['text'] . '<br /><span><a href="http://twitter.com/intent/retweet?related=' . $tweet['user']['screen_name'] . '&amp;tweet_id=' . $tweet['id'] . '" rel="nofollow"><img src="http://si0.twimg.com/images/dev/cms/intents/icons/retweet.png" alt="ReTweet"></a>
<a href="http://twitter.com/intent/tweet?related=' . $tweet['user']['screen_name'] . '&amp;in_reply_to=' . $tweet['id'] . '" rel="nofollow"><img src="http://si0.twimg.com/images/dev/cms/intents/icons/reply.png" alt="Reply"></a>
<a href="http://twitter.com/intent/favorite?related=' . $tweet['user']['screen_name'] . '&amp;tweet_id=' . $tweet['id'] . '" rel="nofollow"><img src="http://si0.twimg.com/images/dev/cms/intents/icons/favorite.png" alt="Favorite"></a></span><a href="http://twitter.com/3doordigital/statuses/' . $tweet['id'] . '" rel="nofollow" target="_blank">permalink</a> || Image URL HTTPS: ' . $tweet['user']['profile_image_url_https'] . '<br /></li>';
}
echo '</ul>';



echo '<h3>Search:</h3>';
$code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/search/tweets'), array(
	'q' => 'bbc',
	'count'=>'2'
));

$output = json_decode($tmhOAuth->response['response'],true);

echo '<ul class="twitterfeed" style="list-style-type: none;">';
foreach($output['statuses'] as $tweet){ // added statuses here
    echo '<li class="tweet" style="clear:both;"><div ><a href="https://twitter.com/intent/user?screen_name=' . $tweet['user']['screen_name'] . '" rel="nofollow"><img src="' . $tweet['user']['profile_image_url'] . '" height="48" width="48" alt="' . $tweet['user']['screen_name'] . '" title="' . $tweet['user']['screen_name'] . '" style="float: left;"></a></div>' . $tweet['text'] . '<br /><span><a href="http://twitter.com/intent/retweet?related=' . $tweet['user']['screen_name'] . '&amp;tweet_id=' . $tweet['id'] . '" rel="nofollow"><img src="http://si0.twimg.com/images/dev/cms/intents/icons/retweet.png" alt="ReTweet"></a>
<a href="http://twitter.com/intent/tweet?related=' . $tweet['user']['screen_name'] . '&amp;in_reply_to=' . $tweet['id'] . '" rel="nofollow"><img src="http://si0.twimg.com/images/dev/cms/intents/icons/reply.png" alt="Reply"></a>
<a href="http://twitter.com/intent/favorite?related=' . $tweet['user']['screen_name'] . '&amp;tweet_id=' . $tweet['id'] . '" rel="nofollow"><img src="http://si0.twimg.com/images/dev/cms/intents/icons/favorite.png" alt="Favorite"></a></span><a href="http://twitter.com/3doordigital/statuses/' . $tweet['id'] . '" rel="nofollow" target="_blank">permalink</a> || Image URL HTTPS: ' . $tweet['user']['profile_image_url_https'] . '<br /></li>';
}
echo '</ul>';






?>