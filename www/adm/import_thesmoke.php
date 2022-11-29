<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$content = $_REQUEST['content'];

$result = [];

if ($content) {
    if (preg_match('_^\s*(.*?)\n_', $content, $match)) {
        $result['Title'] = $match[1];
    }
    if (preg_match('_About the larp\s+(.*?)Parameters_s', $content, $match)) {
        $result['Description'] = str_replace("Presented by\r\n","\r\nPresented by", $match[1]);
    }
    if (preg_match('_Number of participants: (.*)_', $content, $match)) {
        $result['Participants'] = str_replace("–","-",$match[1]);
    }
    if (preg_match('_Designers?: (.*)_', $content, $match)) {
        $result['Designer'] = $match[1];
    }
}

function res ($label, $value) {
    print '<p>';
    print $label . ': <span onclick="navigator.clipboard.writeText(this.innerHTML); $(this).fadeOut(100).fadeIn(100);" style="cursor: copy;">' . htmlspecialchars($value) . '</span>';
    print '</p>';

}

htmladmstart("The Smoke 2020 description parser");
?>
<form action="import_thesmoke.php">
<p>
Full description text:<br>
<textarea cols="100" rows="10" name="content">
<?php print htmlspecialchars($content); ?>
</textarea><br>
<input type="submit">
</p>
</form>

<?php
foreach($result as $k => $v) {
    res($k,$v);
}
?>

</body>
</html>