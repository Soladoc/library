#include <tchattator413/slice.h>

#include <limits.h>

int slice_leni(slice_t slice) {
    return slice.len > INT_MAX ? INT_MAX : slice.len;
}