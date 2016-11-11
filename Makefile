
copy-deps:
	rm -rf ext/wprecord
	rsync -r --exclude .git submodule/wprecord/ ext/wprecord

link-deps:
	rm -rf ext/wprecord
	cd ext; ln -s ../submodule/wprecord wprecord

