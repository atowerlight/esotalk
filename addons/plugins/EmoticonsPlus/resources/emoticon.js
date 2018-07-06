var EmoticonAdv = {
	activeTextAreaId:"",
	showDropDown: function(id){
		this.activeTextAreaId = id;
		buttonPosition = $("#"+id+" .formattingButtons .control-smile").offset();
		$("#emoticonDropDown").css( { 
					left: buttonPosition.left, 
					top: buttonPosition.top+6
			} );

		$("#emoticonDropDown").fadeIn();
	},
	hideDropDown: function(){
		$("#emoticonDropDown").fadeOut();

	},
	insertSmiley : function(smiley){
		ETConversation.wrapText($("#"+this.activeTextAreaId+" textarea"), " "+smiley+" ", "");
		//ETConversation.insertText($("#"+this.activeTextAreaId+" textarea"), " "+smiley+" ");

	}
};

//bind mouse out event to automatically close the menu
$(function(){
		$("#emoticonDropDown").bind("mouseleave",function(){
			EmoticonAdv.hideDropDown();
		});
		$("#emoticonDropDown *").click(function(event){
			event.stopPropagation(); //this is needed to prevent the reply area to collapse on click outside it
		});
});
