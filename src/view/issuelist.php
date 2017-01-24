<div class="repo-tracker-issue-list">
	Number of issues: <?php echo $numIssues; ?>
	<?php foreach ($issues as $issue) { ?>
		<div class="repo-tracker-issue-entry">
			Title: <?php echo $issue["title"]; ?>
		</div>
	<?php } ?>
</div>