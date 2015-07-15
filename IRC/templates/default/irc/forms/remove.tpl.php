<?php
$account = $vars['account'];
?>

<form
    action="<?= \Idno\Core\site()->config()->getDisplayURL() ?>account/irc/"
    class="form-horizontal" method="post">
    <p>
	<input type="hidden" name="remove"
	       value="<?= $account['id'] ?>"/>
	<button type="submit"
		class="connect lkin connected"><i class="fa fa-irc"></i>
	    <?= $account['username'] ?> in <?= $account['server'] ?><?= $account['channel'] ?>
	    (Disconnect)
	</button>
	<?= \Idno\Core\site()->actions()->signForm('/account/irc/') ?>
    </p>
</form>