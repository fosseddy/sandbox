import mysql from "mysql2/promise";

let conn = null;

async function init() {
    conn = await mysql.createConnection({
        host: "localhost",
        user: "root",
        database: "local_library",
        dateStrings: true
    });
}

function getSocketAddr() {
    const { host, port } = conn.config;
    return `${host}:${port}`;
}

function getConnection() {
    return conn;
}

const Model = { table: "" };

Model.create = function(table) {
    const m = Object.create(this);
    m.table = table;
    return m;
}

Model.findAll = async function() {
    const [rows] = await conn.execute(`SELECT * FROM ${this.table}`);
    return rows;
}

Model.find = async function (query) {
    const [key, value] = Object.entries(query)[0];

    const [rows] = await conn.execute(
        `SELECT * FROM ${this.table} WHERE ${key} = ?`,
        [value]
    );

    return rows.length > 0 ? rows[0] : null;
}

Model.update = async function (id, data) {
    let values = [];
    let fields = [];

    for (const [key, value] of Object.entries(data)) {
        fields.push(`${key} = ?`);
        values.push(value);
    }

    let query = `UPDATE ${this.table} SET ${fields.join(", ")} WHERE id = ?`;
    values.push(id);

    return conn.execute(query, values);
}

Model.remove = async function (id) {
    return conn.execute(`DELETE FROM ${this.table} WHERE id = ?`, [id]);
}

Model.insert = async function (data) {
    let fields = []
    let placeholders = [];
    let values = [];

    for (const [key, value] of Object.entries(data)) {
        fields.push(key);
        placeholders.push("?");
        values.push(value);
    }

    let query = `INSERT INTO ${this.table} (${fields.join(", ")}) ` +
                `VALUES (${placeholders.join(", ")})`;

    const [res] = await conn.execute(query, values);
    return res.insertId;
}

Model.count = async function () {
    const [rows] = await conn.execute(`SELECT COUNT(id) FROM ${this.table}`);
    const [count] = Object.values(rows[0]);

    return count;
}

export { Model, init, getSocketAddr, getConnection };
