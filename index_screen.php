<!DOCTYPE html>
<html>
<head>
    <title><?php echo core::$page_config['title_tag'] ?></title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
    <?php echo $this->get_css() ?>
</head>

<body>
<?php echo core::$page_config['pre_content']; ?>
<div id="content" class="<?php core::$page_config['body_class'];?>">
    <?php echo $this->body; ?>
</div>
<?php echo  core::$page_config['post_content']; ?>
<?php echo $this->get_js();?>
</body>
</html>