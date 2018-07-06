## esoTalk – Signature plugin

- Let users add their signature to posts.
- Works together or individually with the Likes plugin. You can enable them both, or either one.

### Installation

Browse to your esoTalk plugin directory:
```
cd WEB_ROOT_DIR/addons/plugins/
```

Clone the Signature plugin repo into the plugin directory:
```
git clone git@github.com:esoTalk-plugins/Signature.git Signature
```

Chown the Signature plugin folder to the right web user:
```
chown -R apache:apache Signature/
```

Navigate to the esoTalk /admin/plugins page and activate the Signature plugin!
And lastly, edit your signature in your profile settings page :smile:


### Translation

Add the following definitions to your translation file (or create a seperate definitions.Signature.php file):

```
$definitions["BBCode Allowed"] = "BBCode Allowed";
$definitions["Characters"] = "Characters";
$definitions["Enter the amount of signature characters allowed."] = "Enter the amount of signature characters allowed.";
$definitions["Max characters:"] = "Max characters:";
$definitions["Signature"] = "Signature";
```
