    .global print, println
    .global STDIN, STDOUT, STDERR

    .extern strlen

    .equ STDIN, 0
    .equ STDOUT, 1
    .equ STDERR, 2

    .equ SYS_write, 1

    .text
# rax written print(rdi fd, rsi *buf)
print:
    movq %rdi, %r8

    movq %rsi, %rdi
    call strlen

    movq %r8, %rdi
    movq %rax, %rdx
    movq $SYS_write, %rax
    syscall
    ret

# rax written println(rdi fd, rsi *buf)
println:
    subq $8, %rsp
    movb $10, (%rsp)

    call print
    movq %rax, %rcx

    movq $SYS_write, %rax
    movq %rsp, %rsi
    movq $1, %rdx
    syscall

    addq %rcx, %rax

    addq $8, %rsp
    ret
