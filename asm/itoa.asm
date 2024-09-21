global _start

section .data

TRUE equ 1
FALSE equ 0

SYS_write equ 1
SYS_exit equ 60

STDOUT equ 1

num dq 48

section .bss

buf resb 32 ; should be enough

section .text

_start:
    mov rdi, [num]
    mov rsi, buf
    call itoa

    mov rdi, STDOUT
    mov rsi, buf
    mov rdx, rax
    mov rax, SYS_write
    syscall

exit:
    mov rax, SYS_exit
    mov rdi, 0
    syscall

; (rdi num q, rsi buf *b) rax buf_len q
itoa:
    mov r8, 0 ; buf_len
    mov r9, FALSE ; is_negative

    mov rax, rdi

    mov rcx, 10
    cmp rax, 0
    jge .slice_number

    mov r9, TRUE
    neg rax
.slice_number:
    cqo
    idiv rcx

    push rdx
    inc r8

    cmp rax, 0
    jne .slice_number

    mov rcx, 0
    cmp r9, TRUE
    jne .to_char

    mov [rsi+rcx], byte '-'
    inc rcx
    inc r8
.to_char:
    pop rax
    add al, '0'
    mov [rsi+rcx], al

    inc rcx
    cmp rcx, r8
    jl .to_char

    mov rax, r8
    ret
