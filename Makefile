copy-deps:
	rm -rf ext/meta-box
	rsync -r --exclude .git submodule/meta-box/ ext/meta-box

link-deps:
	rm -rf ext/meta-box
	cd ext; ln -s ../submodule/meta-box meta-box
