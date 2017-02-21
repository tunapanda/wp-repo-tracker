<div class="repo-tracker-issue-list">
	<?php if ($lastError) { ?>
		<div class="repo-tracker-error">
			There was an error fetching issues from GitHub. Error information follows.
			<pre><?php echo $lastError; ?></pre>
		</div>
	<?php } ?>
	<?php foreach ($issues as $issue) { ?>
		<div class="repo-tracker-issue-entry">
			<span class="title">
				<a href="<?php echo esc_attr($issue["url"]); ?>" target="_blank">
					<?php echo esc_html($issue["title"]); ?>
				</a>
			</span>
			<?php foreach ($issue["labels"] as $label) { ?>
				<span class="label"><?php echo esc_html($label); ?></span>
			<?php } ?>

			<?php if ($issue["numComments"]) { ?>
				<div class="num-comments">
					<img src="<?php echo REPOTRACKER_URL; ?>/img/bubble.png" />
					<?php echo $issue["numComments"]; ?>
				</div>
			<?php } ?>

			<div class="description">
				<?php if ($issue["state"]=="open") { ?>
					Opened <?php echo human_time_diff($issue["opened"],time()); ?> ago,
				<?php } else if ($issue["state"]=="closed") { ?>
					Closed <?php echo human_time_diff($issue["closed"],time()); ?> ago,
				<?php } ?>

				<?php if (!$issue["numAssigned"]) { ?>
					unassigned.
				<?php } else if ($issue["numAssigned"]==1) { ?>
					assigned to 1 person.
				<?php } else { ?>
					assigned to <?php echo $issue["numAssigned"]; ?> persons.
				<?php } ?>
			</div>
		</div>
	<?php } ?>
</div>