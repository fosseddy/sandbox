    .global itoa

    .text
# void itoa(rdi num, rsi *buf)
itoa:
    movq $0, %r8 # buf len
    movq $0, %r9 # is negative
    movq %rdi, %rax

    movq $10, %rcx
    cmpq $0, %rax
    jge 1f

    movq $1, %r9
    neg %rax
1: # slice_number
    cqto
    idivq %rcx

    push %rdx
    incq %r8

    cmpq $0, %rax
    jne 1b

    movq $0, %rcx
    cmpq $1, %r9
    jne 2f

    movb $'-', (%rsi, %rcx)
    incq %rcx
    incq %r8
2: # to_char
    pop %rax
    addb $'0', %al
    movb %al, (%rsi, %rcx)

    incq %rcx
    cmpq %r8, %rcx
    jl 2b

    movb $0, (%rsi, %r8)
    ret
