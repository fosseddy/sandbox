const bitsizePicker = {
    8: document.querySelector("#bitsize-8"),
    16: document.querySelector("#bitsize-16"),
    32: document.querySelector("#bitsize-32"),
    64: document.querySelector("#bitsize-64")
};

const inputs = {
    uint: document.querySelector("#uint-input"),
    sint: document.querySelector("#sint-input"),
    hex: document.querySelector("#hex-input"),
    bin: document.querySelector("#bin-input"),
    oct: document.querySelector("#oct-input")
};

const info = {
    uint: document.querySelector("#uint-info"),
    sint: document.querySelector("#sint-info"),
    hex: document.querySelector("#hex-info"),
    bin: document.querySelector("#bin-info"),
    oct: document.querySelector("#oct-info")
}

const sizes = {
    8: {
        width: BigInt(2**8),
        uint: [0n, BigInt(2**8-1)],
        sint: [-BigInt(2**7), BigInt(2**7-1)]
    },
    16: {
        width: BigInt(2**16),
        uint: [0n, BigInt(2**16-1)],
        sint: [-BigInt(2**15), BigInt(2**15-1)]
    },
    32: {
        width: BigInt(2**32),
        uint: [0n, BigInt(2**32-1)],
        sint: [-BigInt(2**31), BigInt(2**31-1)]
    },
    64: {
        width: BigInt(2**64),
        uint: [0n, BigInt(2**64)-1n],
        sint: [-BigInt(2**63), BigInt(2**63)-1n]
    }
};

let lastActiveInput = inputs.uint;
let bitsize = 32;
let hasError = false;

bitsizePicker[bitsize].classList.add("active");

for (const [size, btn] of Object.entries(bitsizePicker)) {
    btn.addEventListener("click", () => {
        bitsizePicker[bitsize].classList.remove("active");

        bitsize = Number(size);
        bitsizePicker[bitsize].classList.add("active");

        lastActiveInput.dispatchEvent(new Event("input"));
    });
}

for (const [key, input] of Object.entries(inputs)) {
    input.addEventListener("input", () => {
        clearErrors();

        lastActiveInput = input;
        let val = input.value.trim();

        if (val === "") {
            clearInputs();
            setInfo(0);
            return;
        }

        switch (key) {
        case "hex":
            val = "0x" + val;
            break;
        case "bin":
            val = "0b" + val;
            break;
        case "oct":
            val = "0o" + val;
            break;
        }

        if (isNaN(Number(val))) {
            input.classList.add("error");
            hasError = true;
            return;
        }

        val = BigInt(val);
        if (!validRange(key, val)) {
            input.classList.add("error");
            hasError = true;
            return;
        }

        if (key === "sint") {
            val = s2u(val);
        }

        setInputs(key, val)
        setInfo(val);
    });
}

function clearErrors() {
    if (!hasError) return;

    for (const [_, input] of Object.entries(inputs)) {
        input.classList.remove("error");
    }
    hasError = false;
}

function clearInputs() {
    for (const [_, input] of Object.entries(inputs)) {
        input.value = "";
    }
}

function setInputs(current, value) {
    for (const [key, input] of Object.entries(inputs)) {
        if (key === current) continue;

        switch (key) {
        case "uint":
            input.value = value.toString(10);
            break;
        case "sint":
            input.value = u2s(value).toString(10);
            break;
        case "hex":
            input.value = value.toString(16);
            break;
        case "bin":
            input.value = value.toString(2);
            break;
        case "oct":
            input.value = value.toString(8);
            break;
        default:
            console.assert(false, "unreachable", key, input);
        }
    }
}

function setInfo(value) {
    for (const [key, input] of Object.entries(inputs)) {
        let v;

        switch (key) {
        case "uint":
            info.uint.textContent = separate(value.toString(10), 3);
            break;
        case "sint":
            info.sint.textContent = separate(u2s(value).toString(10), 3);
            break;
        case "hex":
            v = value.toString(16).padStart(bitsize / 4, "0")
            v = separate(v, 2);
            info.hex.textContent = "0x" + v.toUpperCase();
            break;
        case "bin":
            v = value.toString(2).padStart(bitsize, "0")
            info.bin.textContent = separate(v, 8);
            break;
        case "oct":
            v = value.toString(8).padStart(Math.ceil(bitsize / 3), "0")
            info.oct.textContent = separate(v, 3);
            break;
        default:
            console.assert(false, "unreachable", key, input);
        }
    }
}

function separate(s, len) {
    if (s.length <= len) return s;

    let sign = false;
    let buf = s.split("");

    if (buf[0] === "-") {
        sign = true;
        buf = buf.slice(1);
    }

    buf = buf.reverse();
    let newbuf = [];

    for (let i = 0; i < buf.length; i++) {
        if (i % len === 0 && i > 0) newbuf.push(" ");
        newbuf.push(buf[i]);
    }

    if (sign) newbuf.push("-");

    return newbuf.reverse().join("");
}

function validRange(kind, val) {
    if (kind !== "sint") {
        kind = "uint";
    }

    const [min, max] = sizes[bitsize][kind];
    return val >= min && val <= max;
}

function s2u(val) {
    if (val >= 0) {
        return val;
    }

    return val + sizes[bitsize].width;
}

function u2s(val) {
    const [min, max] = sizes[bitsize].sint;

    if (val <= max) {
        return val;
    }

    return val - sizes[bitsize].width;
}

setInfo(0);
