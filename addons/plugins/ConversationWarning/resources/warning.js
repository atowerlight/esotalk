// Copyright 2014 Tristan van Bokkem

// function to load a sheet and formatting the add warning form.
function loadWarningSheet(formData) {
	if (!formData) {
		ETSheet.loadSheet("warningSheet", "warning/" + ETConversation.id, function() {
		});
	} else {
		ETSheet.hideSheet("warningSheet");
	}
};

// function for removing a conversation warning.
function removeWarning() {
		$.ETAjax({
	    url: "warning/remove/" + ETConversation.id,
	    type: "post",
	    success: function(data) {
	        $(".modbreak").remove();
			ETMessages.showMessage(T("Warning successfully removed."), {className: "success autoDismiss"});
		}
	});
};

// click trigger to remove a warning while editting a post.
$(".control-remove-warning").live("click", function(e) {
	e.preventDefault();
	removeWarning();
});

// click trigger to edit a warning while editting a post.
$(".control-edit-warning").live("click", function(e) {
	e.preventDefault();
	loadWarningSheet();
});

$(function() {
	// click trigger to remove a warning while initiating a new reply.
	$(".control-remove-warning").on("click", function(e) {
		e.preventDefault();
		removeWarning();
	});

	// click trigger to edit a warning while editting a post.
	$(".control-edit-warning").on("click", function(e) {
		e.preventDefault();
		loadWarningSheet();
	});

	// click trigger to add a warning.
	$("#addWarning").live("click", function(e) {
		e.preventDefault();
		loadWarningSheet();
	});

});
