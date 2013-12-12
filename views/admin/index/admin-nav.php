<?php 
$navArray = array(
  'site' => array('label' => __('Site Information'), 'uri' => url('commons/index/site')),
  'share' => array('label' => __('Share'), 'uri' => url('commons/index/share')),
  'settings' => array('label' => __('Settings'), 'uri' => url('commons/index/settings')),
  'browse' => array('label' => __('Items Overview'), 'uri' => url('commons/index/browse')),
  'tos' => array('label' => __("Terms of Service"), 'uri' => url('commons/index/tos'))
);
?>

<ul id="section-nav" class="navigation">
<?php foreach($navArray as $navItem): ?>
    <?php $current = ''; ?>
    <?php if (is_current_url($navItem['uri'])) { $current = 'class="current"'; } ?>
    <li <?php echo $current; ?>><a href="<?php echo $navItem['uri']; ?>"><?php echo $navItem['label']; ?></a></li>
<?php endforeach; ?>
</ul>