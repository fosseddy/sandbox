global _start

section .data

SYS_read equ 0
SYS_write equ 1
SYS_exit equ 60
STDIN equ 0
STDOUT equ 1
LF equ 10

prompt db "Enter your name: "
PROMPT_LEN equ $ - prompt

msg db "Hello, "
MSG_LEN equ $ - msg

end_msg db "!", LF
END_MSG_LEN equ $ - end_msg

BUF_LEN equ 1024

section .bss

buf resb BUF_LEN

section .text

_start:
    mov rdi, prompt
    mov rsi, PROMPT_LEN
    call stdout_write

    mov rbx, 0
read_input:
    mov rax, SYS_read
    mov rdi, STDIN
    lea rsi, [buf+rbx]
    mov rdx, 1
    syscall

    cmp byte [buf+rbx], LF
    je read_input_done

    inc rbx
    jmp read_input
read_input_done:

    mov rdi, msg
    mov rsi, MSG_LEN
    call stdout_write

    mov rdi, buf
    mov rsi, rbx
    call stdout_write

    mov rdi, end_msg
    mov rsi, END_MSG_LEN
    call stdout_write

    mov rax, SYS_exit
    mov rdi, 0
    syscall

; stdout_write(rdi buf *b, rsi len q) rax q
stdout_write:
    push rsi
    push rdi

    mov rax, SYS_write
    mov rdi, STDOUT
    pop rsi
    pop rdx
    syscall

    ret
