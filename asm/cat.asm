global _start

section .data

SYS_read equ 0
SYS_write equ 1
SYS_open equ 2
SYS_close equ 3
SYS_exit equ 60
O_RDONLY equ 0
STDOUT equ 0

fd dq 0
char db 0
argc_err_msg db "Provide file to print", 10
ARGC_ERR_MSG_LEN equ $ - argc_err_msg
open_err_msg db "Failed to open file", 10
OPEN_ERR_MSG_LEN equ $ - open_err_msg

section .text

_start:
    pop rax
    dec rax
    cmp rax, 0
    je argc_err

    add rsp, 8 ; skip program name
    pop rdi

    mov rax, SYS_open
    mov rsi, O_RDONLY
    syscall

    cmp rax, 0
    jl open_err

    mov [fd], rax

print_char:
    mov rax, SYS_read
    mov rdi, [fd]
    mov rsi, char
    mov rdx, 1
    syscall

    cmp rax, 0
    je print_char_done

    mov rax, SYS_write
    mov rdi, STDOUT
    mov rsi, char
    mov rdx, 1
    syscall
    jmp print_char

print_char_done:
    mov rax, SYS_close
    mov rdi, [fd]
    syscall

    mov rax, SYS_exit
    mov rdi, 0
    syscall

argc_err:
    mov rax, SYS_write
    mov rdi, STDOUT
    mov rsi, argc_err_msg
    mov rdx, ARGC_ERR_MSG_LEN
    syscall
    jmp exit

open_err:
    mov rax, SYS_write
    mov rdi, STDOUT
    mov rsi, open_err_msg
    mov rdx, OPEN_ERR_MSG_LEN
    syscall

exit:
    mov rax, SYS_exit
    mov rdi, 1
    syscall
