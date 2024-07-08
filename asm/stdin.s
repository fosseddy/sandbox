    .global _start

    .equ SYS_read, 0
    .equ SYS_write, 1
    .equ SYS_exit, 60
    .equ STDIN, 0
    .equ STDOUT, 1
    .equ LF, 10

    .data
prompt:
    .ascii "Enter your name: "
prompt_len = . - prompt

msg:
    .ascii "Hello, "
msg_len = . - msg

end_msg:
    .ascii "!\n"
end_msg_len = . - end_msg

buf:
    .skip 1024

    .text
_start:
    movq $prompt, %rdi
    movq $prompt_len, %rsi
    call write

    movq $0, %rbx
read_input:
    movq $SYS_read, %rax
    movq $STDIN, %rdi
    leaq buf(, %rbx), %rsi
    movq $1, %rdx
    syscall

    cmpb $LF, buf(, %rbx)
    je read_input_done

    incq %rbx
    jmp read_input
read_input_done:

    movq $msg, %rdi
    movq $msg_len, %rsi
    call write

    movq $buf, %rdi
    movq %rbx, %rsi
    call write

    movq $end_msg, %rdi
    movq $end_msg_len, %rsi
    call write

    movq $SYS_exit, %rax
    movq $0, %rdi
    syscall

# rax write(rdi byte *buf, rsi len) 
write:
    push %rsi
    push %rdi

    movq $SYS_write, %rax
    movq $STDOUT, %rdi
    pop %rsi
    pop %rdx
    syscall

    ret
