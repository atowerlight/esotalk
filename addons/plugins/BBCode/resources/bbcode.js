function hotkey()  
{  
    var a=window.event.keyCode;  
    if((a==66)&&(event.altKey)){ETConversation.wrapText($("textarea"), "[b]", "[/b]")}
    if((a==73)&&(event.altKey)){ETConversation.wrapText($("textarea"), "[i]", "[/i]")}
    if((a==83)&&(event.altKey)){ETConversation.wrapText($("textarea"), "[s]", "[/s]")}
    if((a==72)&&(event.altKey)){ETConversation.wrapText($("textarea"), "[h]", "[/h]")}
    if((a==76)&&(event.altKey)){ETConversation.wrapText($("textarea"), "[url=http://example.com]", "[/url]", "http://example.com", "link text")}
    if((a==71)&&(event.altKey)){ETConversation.wrapText($("textarea"), "[img]", "[/img]", "", "http://example.com/image.jpg")}
    if((a==67)&&(event.altKey)){ETConversation.wrapText($("textarea"), "[code]", "[/code]")}
}
document.onkeydown = hotkey;

var BBCode = {

    bold: function(id) {ETConversation.wrapText($("#"+id+" textarea"), "[b]", "[/b]");},
    italic: function(id) {ETConversation.wrapText($("#"+id+" textarea"), "[i]", "[/i]");},
    strikethrough: function(id) {ETConversation.wrapText($("#"+id+" textarea"), "[s]", "[/s]");},
    header: function(id) {ETConversation.wrapText($("#"+id+" textarea"), "[h]", "[/h]");},
    link: function(id) {ETConversation.wrapText($("#"+id+" textarea"), "[url=http://example.com]", "[/url]", "http://example.com", "link text");},
    image: function(id) {ETConversation.wrapText($("#"+id+" textarea"), "[img]", "[/img]", "", "http://example.com/image.jpg");},
    fixed: function(id) {ETConversation.wrapText($("#"+id+" textarea"), "[code]", "[/code]");},
    
    };