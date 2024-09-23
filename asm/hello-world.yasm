global _start

section .data

SYS_write equ 1
SYS_exit equ 60
STDOUT equ 1

msg db "hello, world", 10
MSG_LEN equ $-msg

section .text

_start:
    mov rax, SYS_write
    mov rdi, STDOUT
    mov rsi, msg
    mov rdx, MSG_LEN
    syscall

    mov rax, SYS_exit
    mov rdi, 0
    syscall
