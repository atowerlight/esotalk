<?php
if (! defined ( "IN_ESOTALK" ))
	exit ();

$form = $data ["form"];
?>

<div class='area'>

	<h3>直接编辑config.php配置文件</h3>
	<p>谨慎修改，提交之前千万要注意有没有语法错误等，若配置文件出错将导致整个网站包括管理页面不能打开！！</p>
	<p>
		<a href='?loadbackup=1' class="button">加载前一次的备份</a>
	</p>
<?php echo $form->open(); ?>
<div>
<?php echo $form->input("content","textarea", array("rows" => "20", "tabindex" => 20, "style"=> 'width: 100%;')); ?>
</div>
	<p style="margin-top: 10px;"><?php echo $form->saveButton(); ?></p>

<?php echo $form->close(); ?>
</div>
