## esoTalk – Conversation Warning plugin

- Define the rules of conversation before replying.

### Installation

Browse to your esoTalk plugin directory:
```
cd WEB_ROOT_DIR/addons/plugins/
```

Clone the ConversationWarning plugin repo into the plugin directory:
```
git clone git@github.com:tvb/ConversationWarning.git ConversationWarning
```

Chown the ConversationWarning plugin folder to the right web user:
```
chown -R apache:apache ConversationWarning/
```

### Translation

Create `definitions.ConversationWarning.php` in your language pack with the following definitions:

```
$definitions["Add a Converstation Warning"] = "Add a Converstation Warning";
$definitions["Define the rules of a Conversation"] = "Define the rules of a Conversation";
$definitions["Edit Warning"] = "Edit Warning";
$definitions["Remove Warning"] = "Remove Warning";
$definitions["Warning!"] = "Warning";
```
