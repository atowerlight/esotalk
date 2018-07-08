$(function() {
	function activateHighlights() {
		$('pre').each(function(i, block) {
			hljs.highlightBlock(block);
		});
	}
	var initPost = ETConversation.initPost;
	ETConversation.initPost = function(postId, html) {
		initPost(postId, html);
		activateHighlights();
	};
	
	var restorePost = ETConversation.restorePost;
	ETConversation.restorePost = function(postId) {
		var redisplayAvatars = ETConversation.redisplayAvatars;
		ETConversation.redisplayAvatars = function() {
			redisplayAvatars();
			activateHighlights();
			ETConversation.redisplayAvatars = redisplayAvatars;
		};
		restorePost(postId);
	};
});