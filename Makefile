default: clean package
	
clean:
	rm -Rf build
	mkdir build

package:
	rsync -rl --exclude-from=buildignore --delete . build/asm
	cd build && tar czf asm.tar.gz asm
