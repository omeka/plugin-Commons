<script type="text/javascript">

apply = function() {
    var url = "<?php echo WEB_ROOT . '/admin/commons/apply/apply'; ?>";
    jQuery.get(url);


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

