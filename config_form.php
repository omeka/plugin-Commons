<?php
$site = array('site'=> array(
            'title' => get_option('site_title'),
            'admin_email' => get_option('administrator_email'),
            'description' => get_option('description'),
            'copyright_info' => get_option('copyright'),
            'author_info' => get_option('author'),
            'url' => WEB_ROOT,
            'version' => OMEKA_VERSION
            )
        );
$data = array('data' => $site)
?>

<script type="text/javascript">

apply = function() {
    data = {data: <?php echo json_encode($site); ?>};
    url = "<?php echo url('commons/index/apply') ?>";
    jQuery.post(url, data, function(response, status, jqXHR) {
        response = JSON.parse(response);
        //someday these should do something fancier and different based on response status
        switch(response.status) {
            case 'OK':
                alert(response.message);
            break;

            case 'ERROR':
                alert(response.message);
            break;

            case 'EXISTS':
                alert(response.message);
            break;
        }
    });


}

</script>

<div>
<p>Filler about the Commons yaddayaddayadda</p>
<a id="commons-approve" onclick="apply();">Click here to apply</a>


</div>

<div class='field'>
<label for='commons_key'>API Key</label>
    <div class='inputs'>
        <?php echo get_view()->formText('commons_key', option('commons_key'), array('size'=>'42')); ?>
        <?php if(get_option('commons_key')) :?>
        <p class='explanation'>Do not change this!</p>
        <?php else: ?>
        <p class='explanation'>You will receive instructions for obtaining your API key when your request has been approved.</p>
        <?php endif; ?>
    </div>
</div>

