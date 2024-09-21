#/bin/bash

yasm -g dwarf2 -f elf64 $1.asm -o $1.o && ld $1.o -o $1.out
