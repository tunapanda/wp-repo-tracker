<div class="repo-tracker-postit-list">
	<?php if ($lastError) { ?>
		<div class="repo-tracker-error">
			There was an error fetching issues from GitHub. Error information follows.
			<pre><?php echo $lastError; ?></pre>
		</div>
	<?php } ?>
	<?php foreach ($issues as $index=>$issue) { ?>
		<div class="repo-tracker-postit-entry">
			<img src="<?php echo REPOTRACKER_URL; ?>/img/postit-<?php echo rand(1,4);?>.png"/>
			<div class="repo-tracker-postit-label">
				<?php echo esc_html($issue["title"]); ?>
			</div>
		</div>
	<?php } ?>
</div>