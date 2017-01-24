<div class="repo-tracker-issue-list">
	<?php foreach ($issues as $issue) { ?>
		<div class="repo-tracker-issue-entry">
			Title: <?php echo $issue["title"]; ?>
		</div>
	<?php } ?>
</div>