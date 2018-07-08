## esoTalk – AutoLink plugin

- When you post an URL, AutoLinks automatically embeds images, MP3 and videos.
- Currently supported are:
  * images
  * mp3
  * Youtube
  * Google Video
  * Facebook Video
  * Vimeo
  * Metacafe
  * Dailymotion
  * Myspace
  * SPIKE
  * Redlasso
  * OnSMASH
  * Tangle.com
  * LiveLeak


### Installation

Browse to your esoTalk plugin directory:
```
cd WEB_ROOT_DIR/addons/plugins/
```

Clone the AutoLink plugin repo into the plugin directory:
```
git clone git@github.com:esoTalk-plugins/AutoLink.git AutoLink
```

Chown the AutoLink plugin folder to the right web user:
```
chown -R apache:apache AutoLink/
```

Replace the `public function format()` code in **./core/libs/ETFormat.class.php**:
```php
public function format()
{
	// Trigger the "before format" event, which can be used to strip out code blocks.
	$this->trigger("beforeFormat");

	// Format links, mentions, and quotes.
	if (C("esoTalk.format.mentions")) $this->mentions();
	if (!$this->inline) $this->quotes();
 $this->links();

	// Format bullet and numbered lists.
	if (!$this->inline) $this->lists();

	// Trigger the "format" event, where all regular formatting can be applied (bold, italic, etc.)
	$this->trigger("format");

	// Format whitespace, adding in <br/> and <p> tags.
	if (!$this->inline) $this->whitespace();

	// Trigger the "after format" event, where code blocks can be put back in.
	$this->trigger("afterFormat");

	return $this;
}
```

with the following code:
```php
public function format($sticky = false)
{
	// Trigger the "before format" event, which can be used to strip out code blocks.
	$this->trigger("beforeFormat");

	// Format links, mentions, and quotes.
	if (C("esoTalk.format.mentions")) $this->mentions();
	if (!$this->inline) $this->quotes();
	if(array_search('AutoLink', C("esoTalk.enabledPlugins"))) {
        if ($sticky) $this->links();
    }
    else {
        $this->links();
    }

	// Format bullet and numbered lists.
	if (!$this->inline) $this->lists();

	// Trigger the "format" event, where all regular formatting can be applied (bold, italic, etc.)
	$this->trigger("format");

	// Format whitespace, adding in <br/> and <p> tags.
	if (!$this->inline) $this->whitespace();

	// Trigger the "after format" event, where code blocks can be put back in.
	$this->trigger("afterFormat");

	return $this;
}
```

Replace the the following line (51) in **./core/views/conversations/conversation.php**:
```php
echo "<div class='excerpt'>".ET::formatter()->init($conversation["firstPost"])->inline(true)->firstLine()->clip(200)->format()->get()."</div>";
```

with the following code: 
```php
echo "<div class='excerpt'>".ET::formatter()->init($conversation["firstPost"])->inline(true)->firstLine()->clip(200)->format(true)->get()."</div>";
```
