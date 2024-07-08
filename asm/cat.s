    .global _start

    .equ SYS_read, 0
    .equ SYS_write, 1
    .equ SYS_open, 2
    .equ SYS_close, 3
    .equ SYS_exit, 60
    .equ O_RDONLY, 0
    .equ STDOUT, 0

    .data
fd:
    .quad 0
ch:
    .byte 0
argc_err_msg:
    .ascii "Provide file to print\n"
argc_err_msg_len = . - argc_err_msg

    .text
_start:
    pop %rax
    decq %rax
    cmpq $0, %rax
    je argc_err

    addq $8, %rsp # skip program name
    pop %rdi

    movq $SYS_open, %rax
    movq $O_RDONLY, %rsi
    syscall

    movq %rax, fd

print_char:
    movq $SYS_read, %rax
    movq fd, %rdi
    movq $ch, %rsi
    movq $1, %rdx
    syscall

    cmpq $0, %rax
    je print_char_done

    movq $SYS_write, %rax
    movq $STDOUT, %rdi
    movq $ch, %rsi
    movq $1, %rdx
    syscall
    jmp print_char

print_char_done:
    movq $SYS_close, %rax
    movq fd, %rdi
    syscall

    movq $SYS_exit, %rax
    movq $0, %rdi
    syscall

argc_err:
    movq $SYS_write, %rax
    movq $STDOUT, %rdi
    movq $argc_err_msg, %rsi
    movq $argc_err_msg_len, %rdx
    syscall

    movq $SYS_exit, %rax
    movq $1, %rdi
    syscall
