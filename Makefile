copy-deps:
	rm -rf ext/meta-box
	rsync -r --exclude .git submodule/meta-box/ ext/meta-box

	rm -rf ext/wprecord
	rsync -r --exclude .git submodule/wprecord/ ext/wprecord

link-deps:
	rm -rf ext/meta-box
	cd ext; ln -s ../submodule/meta-box meta-box

	rm -rf ext/wprecord
	cd ext; ln -s ../submodule/wprecord wprecord

readme:
	wp2md convert readme.txt README.md