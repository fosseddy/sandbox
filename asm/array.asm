global _start

section .data

SYS_exit equ 60

avrg dq 0
sum dq 0
max dq 0
min dq 0
arr dq 25, 69, 41, -1, 420, 8, 69
ARR_LEN equ 7

section .text

_start:
    mov r8, [arr]
    mov r9, [arr]
    mov r10, ARR_LEN

    mov rsi, 0
    mov rcx, ARR_LEN
.arr_loop:
    mov rax, [arr + rsi * 8]

    add [sum], rax

    cmp rax, r8
    cmovg r8, rax

    cmp rax, r9
    cmovl r9, rax

    inc rsi
    loop .arr_loop

    mov [max], r8
    mov [min], r9

    mov rax, [sum]
    cqo
    idiv r10
    mov [avrg], rax

.exit:
    mov rax, SYS_exit
    mov rdi, 0
    syscall
