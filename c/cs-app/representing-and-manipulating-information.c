#include <stdio.h>
#include <limits.h>
#include <assert.h>

int is_little_endian()
{
    int a = 0xFF;
    unsigned char *ptr = (unsigned char *) &a;

    return ptr[0] == 0xFF;
}

void ex_2_58()
{
    assert(is_little_endian() == 1);
}

void ex_2_59()
{
    int x = 0x89ABCDEF;
    int y = 0x76543210;

    assert((y ^ (y & 0xFF)) | (x & 0xFF) == 0x765432EF);
}

unsigned replace_byte(unsigned x, int i, unsigned char b)
{
    unsigned char *ptr = (unsigned char *) &x;
    ptr[i] = b;

    return x;
}

void ex_2_60()
{
    assert(replace_byte(0x12345678, 2, 0xAB) == 0x12AB5678);
    assert(replace_byte(0x12345678, 0, 0xAB) == 0x123456AB);
}

void ex_2_61()
{
    assert(!!0xFF == 1);
    assert(!!0x00 == 0);

    assert(!!~0x00 == 1);
    assert(!!~0xF0 == 1);
    assert(!!~UINT_MAX == 0);

    assert(!!(0xFFFF & 0xFF) == 1);
    assert(!!(0xFF00 & 0xFF) == 0);

    int v = (sizeof(int) - 1) << 3;
    assert(!!((~0x00 >> v) & 0xFF) == 1);
    assert(!!((~0xF0FFFFFF >> v) & 0xFF) == 1);
    assert(!!((~UINT_MAX >> v) & 0xFF) == 0);
}

int int_shifts_are_arithmetic()
{
    int x = -69;

    x = x >> ((sizeof(int) << 3) - 1);
    return x == -1;
}

void ex_2_62()
{
    assert(int_shifts_are_arithmetic() == 1);
}

int main(void)
{
    ex_2_58();
    ex_2_59();
    ex_2_60();
    ex_2_61();
    ex_2_62();

    return 0;
}
