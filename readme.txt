=== wp-repo-tracker ===
Contributors: Tunapanda
Donate link: http://www.tunapanda.org/contribute
Tags: github, integration, issues
Requires at least: 3.8.1
Tested up to: 4.7.1
Stable tag: trunk
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Track issues from a code repository, and show them on your WordPress site.

== Description ==
This plugin makes it possible to show a list of issues from [GitHub](https://www.github.com)
on a WordPress page. It has a flexible filtering system so you can specify 
which issues from which projects that should be taken into account. 

This plugin can also make data available for other services to use, at this time
[Dasheroo](http://www.dasheroo.com/) is supported.

You can use this plugin on an intranet or extranet, to show the status of a team
or project. For example, you can use it to show KPI:s such as how many issues that 
were closed last week, plot a graph over the current number of open issues in the 
current sprint (a sprint burndown chart), or show all the issues with a "help-wanted"
label in order to engage with the open source community.

Here are a few examples of things you can do:

= Measure number of closed issues last week =

Hello.

= List issues in current sprint, and show a burn down chart = 

Test.

= Engage open source hackers = 

= Hacking =
Some small things to think about if you want to contribute to this plugin:
* Don't update the README.md file. Update the readme.txt file, then build the
  README.md file using `make readme`. This in turn uses the
  [wp2md](https://github.com/wpreadme2markdown/wp-readme-to-markdown) command,
  so this needs to be installed on your system.