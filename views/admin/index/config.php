<?php
head();

?>

<div id='primary'>

<form enctype="multipart/form-data" action="" method="post">

    <div class='field'>
        <label for='commons_title_color'>Pick a color for the title of your site on Omeka Commons</label>
        <div class='inputs'>
            <input name='commons_title_color' type='text' />
        </div>
    </div>

    <div class='field'>
    <label for='commons_logo'>Upload a logo</label>
        <div class='inputs'>
            <input name='commons_logo' type='file' />
        </div>
    </div>
     <input id="submit" class=" submit" type="submit" value="Save Changes" name="submit">
</form>

</div>




<?php foot();?>