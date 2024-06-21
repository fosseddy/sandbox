import crypto from "crypto";

async function randomBytes(len) {
    return new Promise((resolve, reject) => {
        crypto.randomBytes(len, (err, buf) => {
            if (err) reject(err);
            resolve(buf);
        });
    });
}

async function pbkdf2(pass, salt) {
    return new Promise((resolve, reject) => {
        crypto.pbkdf2(pass, salt, 100000, 64, 'sha512', (err, buf) => {
            if (err) reject(err);
            resolve(buf);
        });
    });
}

export { randomBytes, pbkdf2 };
