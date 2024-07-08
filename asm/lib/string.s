    .global strlen

    .text
# rax len strlen(rdi *s)
strlen:
    movq $0, %rax
    jmp 2f
1:
    incq %rax
2:
    cmpb $0, (%rdi, %rax)
    jne 1b
    ret
