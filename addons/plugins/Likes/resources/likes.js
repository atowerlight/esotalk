$(function() {
	
	$(document).on("click", ".likes .showMore", function(e) {
		e.preventDefault();
		ETSheet.loadSheet("onlineSheet", "conversation/liked.view/"+$(this).parents(".post").data("id"));
	});

	$(document).on("click", ".likes .like-button", function(e) {
		e.preventDefault();
		var area = $(this).parents(".likes");
		area.find(".like-button").html(area.hasClass("liked") ? T("Like") : T("Unlike"));
		
		$.ETAjax({
			url: "conversation/"+(area.hasClass("liked") ? "unlike" : "like")+".json/"+area.parents(".post").data("id"),
			success: function(data) {
				area.find(".like-members").html(data.names);
				area.find(".like-separator").toggle(!!data.names);
				area.toggleClass("liked");
			}
		});
	});

});
