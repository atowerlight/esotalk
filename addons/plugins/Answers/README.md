# Answers Plugin

Allow posters to mark a reply as having answered their question.

## Installation

[Download](https://github.com/esotalk/Answers/archive/master.zip) or clone the Answers plugin repo into your esoTalk plugin directory:

	cd ESOTALK_DIR/addons/plugins/
	git clone git@github.com:esotalk/Answers.git Answers

Navigate to the the admin/plugins page and activate the Answers plugin.

## Translation

Create `definitions.Answers.php` in your language pack with the following definitions:

	$definitions["Answer"] = "Answer";
	$definitions["Answered"] = "Answered";
	$definitions["Answered by %s"] = "Answered by %s";
	$definitions["label.answered"] = "Answered";
	$definitions["Remove answer"] = "Remove answer";
	$definitions["See post in context"] = "See post in context";
	$definitions["This post answered my question"] = "This post answered my question";
