<div class="row">

    <div class="col-md-10 col-md-offset-1">
	<?= $this->draw('account/menu') ?>
        <h1>IRC</h1>
    </div>

</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">
	<?php
	if (empty(\Idno\Core\site()->session()->currentUser()->irc)) {
	    ?>
    	<div class="control-group">
    	    <div class="controls-config">
    		<div class="row">
    		    <div class="col-md-7">
    			<p>
    			    Easily share updates to IRC.</p>

    			<p>
    			    With IRC connected, you can cross-post updates, pictures, and posts that you
    			    publish publicly on your site.
    			</p>

    		    </div>

    		</div>
    	    </div>
    	</div>
	    <?php
	} else if (!\Idno\Core\site()->config()->multipleSyndicationAccounts()) {
	    ?>
    	<div class="control-group">
    	    <div class="controls-config">
    		<div class="row">
    		    <div class="col-md-7">
    			<p>
    			    Your account is currently connected to IRC. Public updates, pictures, and posts
    			    that you publish here
    			    can be cross-posted to IRC.
    			</p>

    			<div class="social">
				<?php
				
				if ($accounts = \Idno\Core\site()->session()->currentUser()->irc) {
				    foreach ($accounts as $account) {

					echo $this->__(['account' => $account])->draw('irc/forms/remove');
				    }
				}
				?>
    			</div>
    		    </div>
    		</div>
    	    </div>
    	</div>

	    <?php
	} else {
	    ?>
    	<div class="control-group">
    	    <div class="controls-config">
    		<div class="row">
    		    <div class="col-md-7">
    			<p>
    			    Your account is currently connected to IRC. Public updates, pictures, and posts
    			    that you publish here
    			    can be cross-posted to IRC.
    			</p>

    			<div class="social">
				<?php 
				if ($accounts = \Idno\Core\site()->session()->currentUser()->irc) {
				    foreach ($accounts as $account) {

					echo $this->__(['account' => $account])->draw('irc/forms/remove');
				    }
				}
				?>
    			</div>

    		    </div>
    		</div>
    	    </div>
    	</div>
	    <?php
	}
	?>
	
	
	<div class="control-group">
    	    <div class="controls-config">
    		<div class="row">
    		    <div class="col-md-7">


    			<div class="social">

    			    <p>
    			    <form
    				action="<?= \Idno\Core\site()->config()->getDisplayURL() ?>account/irc/"
    				class="form-horizontal" method="post">
    				<input type="text" name="server" placeholder="Network, e.g. irc.freenode.net" />
    				<input type="text" name="channel" placeholder="Channel, e.g. #knownchat" />
    				<input type="text" name="username" placeholder="Username to use" />
    				<input type="submit" value="Add channel..." class="btn btn-default" />
				<?= \Idno\Core\site()->actions()->signForm('/account/irc/') ?>
    			    </form>
    			    </p>
    			</div>
    		    </div>

    		</div>
    	    </div>
    	</div>
    </div>
</div>