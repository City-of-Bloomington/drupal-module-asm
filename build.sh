#!/bin/bash
APPNAME=asm
DIR=`pwd`
BUILD=$DIR/build

if [ ! -d $BUILD ]
	then mkdir $BUILD
fi

# The PHP code does not need to actually build anything.
# Just copy all the files into the build
echo "Preparing release"
rsync -rl --exclude-from=$DIR/buildignore --delete $DIR/ $BUILD/$APPNAME
cd $BUILD
tar czf $APPNAME.tar.gz $APPNAME
