#/bin/bash

set -e

f=$(basename $1 .yasm)

yasm -g dwarf2 -f elf64 $1 -o $f.o
ld $f.o -o $f
rm $f.o
