<?php
$site = array('site'=> array(
            'title' => get_option('site_title'),
            'admin_email' => get_option('administrator_email'),
            'description' => get_option('description'),
            'copyright_info' => get_option('copyright'),
            'author_info' => get_option('author'),
            'url' => WEB_ROOT
            )
        );
$data = array('data' => $site)
?>

<script type="text/javascript">

apply = function() {
    data = {data: <?php echo json_encode($site); ?>};
    console.log(data);
    url = "<?php echo COMMONS_API_APPLY_URL; ?>";
    jQuery.post(url, data, function(response, status, jqXHR) {
        switch(response.status) {
            case 'OK':
                alert("Thanks! You should receive an email shortly with a link to follow with more info.");
            break;

            case 'EXISTS':
                alert("Looks like you're already registered. this message should go away, because you should not have seen the link in the first place. Blame Patrick");
            break;
        }
    });


}

</script>

<div>
<p>Filler about the Commons yaddayaddayadda</p>
<a onclick="apply();">Click here to apply</a>


</div>

<div class='field'>
<label for='commons_key'>API Key</label>
    <div class='inputs'>
        <?php echo __v()->formText('commons_key', get_option('commons_key'), array('size'=>'42')); ?>
        <?php if(get_option('commons_key')) :?>
        <p class='explanation'>Do not change this!</p>
        <?php else: ?>
        <p class='explanation'>You will receive instructions for obtaining your API key when your request has been approved.</p>
        <?php endif; ?>
    </div>
</div>

