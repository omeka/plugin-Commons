<?php 
$navArray = array(
  'site' => array('label' => 'Site Information', 'uri' => url('commons/index/site')),
  'share' => array('label' => 'Share', 'uri' => url('commons/index/share')),
  'settings' => array('label' => 'Settings', 'uri' => url('commons/index/settings')),
  'browse' => array('label' => 'Items Overview', 'uri' => url('commons/index/browse'))
);
?>

<ul id="section-nav" class="navigation">
<?php foreach($navArray as $navItem): ?>
    <?php $current = ''; ?>
    <?php if (is_current_url($navItem['uri'])) { $current = 'class="current"'; } ?>
    <li <?php echo $current; ?>><a href="<?php echo $navItem['uri']; ?>"><?php echo $navItem['label']; ?></a></li>
<?php endforeach; ?>
</ul>