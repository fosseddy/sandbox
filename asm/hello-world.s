    .global _start

    .equ SYS_write, 1
    .equ SYS_exit, 60

    .equ STDOUT, 1

    .equ MSG_LEN, 13

    .data
msg:
    .ascii "hello, world\n"

    .text
_start:
    movq $STDOUT, %rdi
    movq $msg, %rsi
    movq $MSG_LEN, %rdx
    movq $SYS_write, %rax
    syscall

    movq $SYS_exit, %rax
    movq $0, %rdi
    syscall
